// categorias.js - Gestión de categorías

// Función para mostrar notificaciones toast
function showToast(toastHtml) {
    // Crear contenedor si no existe
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-3';
        document.body.appendChild(container);
    }

    const container = document.getElementById('toast-container');
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = toastHtml;
    const toast = tempDiv.firstElementChild;

    // Añadir clases de animación
    toast.style.transform = 'translateX(100%)';
    toast.style.opacity = '0';
    toast.style.transition = 'all 0.3s ease-in-out';

    container.appendChild(toast);

    // Animar entrada
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
    }, 10);

    // Auto-remover después de 4 segundos
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        toast.style.opacity = '0';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 4000);
}

// Esperar a que el DOM y jQuery estén listos
(function() {
    function initCategorias() {

    // Sistema de gestión principal de categorías
    const CategoriasManager = {
        data: [],
        filteredData: [],
        incluyeEliminadas: false, // Nueva propiedad para controlar si mostrar eliminadas
        pagination: {
            currentPage: 1,
            totalPages: 0,
            total: 0,
            limit: 6,
            hasPrevious: false,
            hasNext: false
        },
        filters: {
            buscar: '',
            estado: '1' // Inicializar mostrando solo categorías activas
        },

        init: function() {
            // Solo inicializar si existe el contenedor Y el usuario está logueado
            if ($('#categorias-grid').length && window.TENANT_ID) {
                this.bindEvents();
                this.cargarCategorias();
            }
        },

        bindEvents: function() {
            const self = this;
            let searchTimeout;

            // Filtro de búsqueda con debounce
            $('#buscar-categoria').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    self.filters.buscar = $(this).val().toLowerCase();
                    self.pagination.currentPage = 1;
                    self.cargarCategorias();
                }, 300);
            });

            // Filtro de estado
            $(document).on('change', '#filtro-estado', function() {
                self.filters.estado = $(this).val();
                self.pagination.currentPage = 1;
                self.cargarCategorias();
            });

            // Limpiar filtros
            $('#btn-limpiar-filtros').on('click', function() {
                self.limpiarFiltros();
            });
        },

        cargarCategorias: function(page = null) {
            const self = this;

            // Si se especifica página, actualizar el estado
            if (page !== null) {
                this.pagination.currentPage = page;
            }

            console.log('🔄 Cargando categorías...', {
                page: this.pagination.currentPage,
                limit: this.pagination.limit,
                estado: this.filters.estado,
                incluir_eliminadas: this.incluyeEliminadas,
                tenantId: window.TENANT_ID
            });

            $.ajax({
                url: 'ajax/categorias.ajax.php',
                type: 'POST',
                data: {
                    accion: 'obtener_categorias',
                    page: this.pagination.currentPage,
                    limit: this.pagination.limit,
                    estado: this.filters.estado,
                    incluir_eliminadas: this.incluyeEliminadas
                },
                dataType: 'json',
                beforeSend: function() {
                    console.log('📤 Enviando petición AJAX...');
                    self.mostrarLoading();
                },
                success: function(respuesta) {
                    console.log('📥 Respuesta recibida:', respuesta);

                    if (respuesta.status === 'success' && respuesta.data) {
                        console.log('✅ Datos válidos recibidos:', respuesta.data);

                        // Actualizar datos de paginación
                        self.data = respuesta.data.categorias || [];
                        self.pagination.total = respuesta.data.total || 0;
                        self.pagination.totalPages = respuesta.data.total_pages || 0;
                        self.pagination.hasPrevious = respuesta.data.has_previous || false;
                        self.pagination.hasNext = respuesta.data.has_next || false;

                        console.log('📊 Datos procesados:', {
                            categorias: self.data.length,
                            total: self.pagination.total,
                            pages: self.pagination.totalPages
                        });

                        // Aplicar filtros locales
                        self.aplicarFiltros();

                        // Mostrar resultados
                        if (self.filteredData.length > 0) {
                            console.log('🎨 Mostrando categorías...');
                            self.mostrarCategorias();
                        } else {
                            console.log('📭 Mostrando estado vacío...');
                            self.mostrarEmpty();
                        }

                        self.actualizarFooterPaginacion();
                    } else {
                        console.error('❌ Error en la respuesta:', respuesta.message || respuesta);
                        self.mostrarError();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ Error AJAX:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    self.mostrarError();
                }
            });
        },

        aplicarFiltros: function() {

            this.filteredData = this.data.filter(categoria => {
                // Filtro de búsqueda
                if (this.filters.buscar && this.filters.buscar.length > 0) {
                    const searchText = this.filters.buscar;
                    if (!categoria.nombre.toLowerCase().includes(searchText) &&
                        (!categoria.descripcion || !categoria.descripcion.toLowerCase().includes(searchText))) {
                        return false;
                    }
                }

                return true;
            });
        },

        mostrarCategorias: function() {
            const container = $('#categorias-grid');

            // Limpiar contenido anterior
            container.empty();

            // Ocultar otros estados
            $('#categorias-empty').addClass('hidden');
            $('#categorias-loading').addClass('hidden');

            // Mostrar grid
            container.removeClass('hidden').addClass('grid');

            // Crear cards para cada categoría
            this.filteredData.forEach((categoria, index) => {
                const card = this.crearCard(categoria);
                container.append(card);
            });

            this.actualizarFooterPaginacion();
        },

        crearCard: function(categoria) {
            // Verificar si la categoría está eliminada
            const isDeleted = categoria.deleted_at !== null && categoria.deleted_at !== undefined;

            let statusClass, statusText, statusIcon, headerClass, cardClass;

            if (isDeleted) {
                statusClass = 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300';
                statusText = 'Eliminada';
                statusIcon = '🗑️';
                headerClass = 'bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 dark:from-gray-800 dark:via-gray-900 dark:to-black';
                cardClass = 'opacity-75 saturate-50';
            } else {
                statusClass = categoria.estado == 1 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                statusText = categoria.estado == 1 ? 'Activa' : 'Inactiva';
                statusIcon = categoria.estado == 1 ? '✅' : '❌';
                headerClass = 'bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 dark:from-orange-900/20 dark:via-amber-900/20 dark:to-yellow-900/20';
                cardClass = '';
            }

            // Contenido del icono de categoría
            const categoriaIconContent = isDeleted ?
                `<div class="w-20 h-20 mx-auto mb-4 relative">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center shadow-lg relative overflow-hidden border-4 border-white dark:border-neutral-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                        <div class="relative">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                </div>` :
                `<div class="w-20 h-20 mx-auto mb-4 relative">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-400 to-yellow-500 flex items-center justify-center shadow-lg relative overflow-hidden border-4 border-white dark:border-neutral-700">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                        <div class="relative">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <!-- Indicador de estado -->
                        <div class="absolute -bottom-1 -right-1 w-6 h-6 ${categoria.estado == 1 ? 'bg-green-500' : 'bg-red-500'} rounded-full border-2 border-white dark:border-neutral-800 flex items-center justify-center">
                            <div class="w-2 h-2 ${categoria.estado == 1 ? 'bg-green-600' : 'bg-red-600'} rounded-full"></div>
                        </div>
                    </div>
                </div>`;

            return $(`
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden group hover:-translate-y-2 ${cardClass}" data-categoria-id="${categoria.idcategoria}">
                    <!-- Header con gradiente -->
                    <div class="relative p-6 ${headerClass}">
                        <div class="text-center">
                            ${categoriaIconContent}

                            <!-- Información principal -->
                            <div class="space-y-2">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                                    ${categoria.nombre || 'N/A'}
                                </h3>
                                <p class="text-sm font-medium text-gray-600 dark:text-neutral-400 bg-white/50 dark:bg-black/20 rounded-lg px-3 py-1 inline-block">
                                    ${categoria.descripcion || 'Sin descripción'}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Información de la categoría -->
                    <div class="p-6 space-y-4">
                        <!-- Estado -->
                        <div class="bg-white dark:bg-neutral-900/50 rounded-xl p-4 border border-gray-200 dark:border-neutral-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-2">Estado</p>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium ${statusClass} shadow-sm">
                                        ${statusIcon} ${statusText}
                                    </span>
                                </div>
                                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        ${categoria.created_at ? `
                        <!-- Fecha de creación -->
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400">Fecha de creación</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${categoria.created_at ? new Date(categoria.created_at).toLocaleDateString('es-ES') : 'N/A'}</p>
                                </div>
                            </div>
                        </div>` : ''}
                    </div>

                    <!-- Botones de acción -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-100 dark:border-neutral-700">
                        <div class="flex gap-2">
                            ${isDeleted ? `
                                <div class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-xl text-gray-500 bg-gray-200 cursor-not-allowed dark:text-gray-400 dark:bg-gray-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                    </svg>
                                    Categoría eliminada
                                </div>
                            ` : `
                                <button class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-xl text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-300 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 transition-all duration-200 transform hover:scale-105 btnEditarCategoria" data-id="${categoria.idcategoria}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </button>
                                <button class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-xl text-red-700 bg-red-100 hover:bg-red-200 dark:text-red-300 dark:bg-red-900/30 dark:hover:bg-red-900/50 transition-all duration-200 transform hover:scale-105 btnEliminarCategoria" data-id="${categoria.idcategoria}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Eliminar
                                </button>
                            `}
                        </div>
                    </div>
                </div>
            `);
        },

        mostrarLoading: function() {
            $('#categorias-grid').addClass('hidden');
            $('#categorias-empty').addClass('hidden');
            $('#categorias-loading').removeClass('hidden');
        },

        mostrarEmpty: function() {
            $('#categorias-grid').addClass('hidden');
            $('#categorias-loading').addClass('hidden');
            $('#categorias-empty').removeClass('hidden');
        },

        mostrarError: function() {
            $('#categorias-grid').addClass('hidden');
            $('#categorias-loading').addClass('hidden');
            $('#categorias-empty').removeClass('hidden');
        },

        actualizarFooterPaginacion: function() {
            // Actualizar información de paginación
            const totalMostradas = this.filteredData.length;
            $('#pagination-info').text(`Mostrando ${totalMostradas} de ${this.pagination.total} resultados`);
            $('#categorias-count').text(`${this.pagination.total} categorías`);

            // Actualizar botones de paginación
            $('#btn-previous').prop('disabled', !this.pagination.hasPrevious);
            $('#btn-next').prop('disabled', !this.pagination.hasNext);
        },

        paginaAnterior: function() {
            if (this.pagination.hasPrevious) {
                this.cargarCategorias(this.pagination.currentPage - 1);
            }
        },

        paginaSiguiente: function() {
            if (this.pagination.hasNext) {
                this.cargarCategorias(this.pagination.currentPage + 1);
            }
        },

        limpiarFiltros: function() {
            this.filters.buscar = '';
            this.filters.estado = '1'; // Mostrar solo activas por defecto

            $('#buscar-categoria').val('');
            $('#filtro-estado').val('1');

            // Resetear página
            this.pagination.currentPage = 1;

            // Recargar datos
            this.cargarCategorias();
        }
    };

    // Exponer CategoriasManager globalmente
    window.CategoriasManager = CategoriasManager;

    // Sistema de manejo de formulario agregar categoría
    const FormAgregarCategoria = {
        init: function() {
            this.bindEvents();
            this.resetForm();
        },

        bindEvents: function() {
            // Usar delegación de eventos desde document para asegurar que funcione
            $(document).off('submit', '#form-agregar-categoria').on('submit', '#form-agregar-categoria', this.handleSubmit.bind(this));
        },

        validateForm: function() {
            let isValid = true;

            // Validar nombre
            const nombre = $('#nombre-categoria').val().trim();
            if (!nombre) {
                this.showFieldError($('#nombre-categoria'), 'El nombre es requerido');
                isValid = false;
            } else {
                this.hideFieldError($('#nombre-categoria'));
            }

            return isValid;
        },

        showFieldError: function(field, message) {
            // Remover errores anteriores
            field.removeClass('border-green-500').addClass('border-red-500');
            field.siblings('.field-error').remove();

            // Agregar nuevo error
            field.after(`<p class="field-error text-xs text-red-600 mt-1">${message}</p>`);
        },

        hideFieldError: function(field) {
            field.removeClass('border-red-500').addClass('border-gray-300');
            field.siblings('.field-error').remove();
        },

        resetForm: function() {
            // Reset del formulario HTML
            $('#form-agregar-categoria')[0].reset();

            // Limpiar errores
            $('.field-error').remove();
            $('#form-agregar-categoria input, #form-agregar-categoria textarea, #form-agregar-categoria select')
                .removeClass('border-red-500 border-green-500').addClass('border-gray-300');

            // Reset specific values
            $('#nombre-categoria').val('');
            $('#descripcion-categoria').val('');
            $('#estado-categoria').val('1');
        },

        handleSubmit: function(e) {
            e.preventDefault();

            if (!this.validateForm()) {
                return;
            }

            const formData = new FormData();
            formData.append('accion', 'crear_categoria');
            formData.append('nombre', $('#nombre-categoria').val().trim());
            formData.append('descripcion', $('#descripcion-categoria').val().trim());
            formData.append('estado', $('#estado-categoria').val());

            // Agregar CSRF token si está disponible
            const csrfToken = $('input[name="csrf_token"]').val();
            if (csrfToken) {
                formData.append('csrf_token', csrfToken);
            }

            this.submitForm(formData);
        },

        submitForm: function(formData) {
            // Deshabilitar botón durante el envío
            const submitButton = $('button[type="submit"][form="form-agregar-categoria"]');
            const originalText = submitButton.html();
            submitButton.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Creando...</span>
            `);

            $.ajax({
                url: 'ajax/categorias.ajax.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(respuesta) {
                    if (respuesta.status === 'success') {
                        // Toast de éxito
                        const toastHtml = `
                            <div class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                                <div class="flex p-4">
                                    <div class="shrink-0">
                                        <svg class="shrink-0 size-4 text-teal-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                        </svg>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-sm text-gray-700 dark:text-neutral-400">
                                            ${respuesta.message}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                        showToast(toastHtml);

                        // Cerrar modal
                        const modal = document.getElementById('modal-agregar-categoria');
                        if (modal && window.HSOverlay) {
                            window.HSOverlay.close(modal);
                        }

                        // Recargar lista de categorías
                        if (window.CategoriasManager) {
                            window.CategoriasManager.cargarCategorias();
                        }

                        // Resetear formulario
                        FormAgregarCategoria.resetForm();

                    } else {
                        console.error('FormAgregarCategoria: Error del servidor:', respuesta.message);
                        const toastHtml = `
                            <div class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                                <div class="flex p-4">
                                    <div class="shrink-0">
                                        <svg class="shrink-0 size-4 text-red-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                        </svg>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-sm text-gray-700 dark:text-neutral-400">
                                            ${respuesta.message || 'Error al crear categoría'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                        showToast(toastHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormAgregarCategoria: Error AJAX:', {xhr, status, error});
                    const toastHtml = `
                        <div class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                            <div class="flex p-4">
                                <div class="shrink-0">
                                    <svg class="shrink-0 size-4 text-red-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                    </svg>
                                </div>
                                <div class="ms-3">
                                    <p class="text-sm text-gray-700 dark:text-neutral-400">
                                        Error de conexión. Intenta nuevamente.
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    showToast(toastHtml);
                },
                complete: function() {
                    // Restaurar botón
                    submitButton.prop('disabled', false).html(originalText);
                }
            });
        }
    };

    // Sistema de manejo de formulario editar categoría
    const FormEditarCategoria = {
        datosOriginales: null,

        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            // Manejar envío del formulario
            $('#form-editar-categoria').off('submit').on('submit', this.handleSubmit.bind(this));
        },

        cargarCategoria: function(categoriaId) {
            // Mostrar loading y ocultar formulario
            $('#editar-loading').removeClass('hidden');
            $('#form-editar-categoria').addClass('hidden');

            $.ajax({
                url: 'ajax/categorias.ajax.php',
                type: 'POST',
                data: {
                    accion: 'obtener_categoria',
                    idcategoria: categoriaId
                },
                dataType: 'json',
                success: function(respuesta) {
                    if (respuesta.status === 'success' && respuesta.data) {
                        FormEditarCategoria.poblarFormulario(respuesta.data);
                    } else {
                        console.error('FormEditarCategoria: Error al cargar categoría:', respuesta.message);
                        alert('Error al cargar los datos de la categoría');
                        // Ocultar loading en caso de error
                        $('#editar-loading').addClass('hidden');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormEditarCategoria: Error AJAX:', {xhr, status, error});
                    alert('Error de conexión al cargar la categoría');
                    // Ocultar loading en caso de error
                    $('#editar-loading').addClass('hidden');
                }
            });
        },

        poblarFormulario: function(categoria) {
            // Guardar datos originales para comparación
            this.datosOriginales = { ...categoria };

            // Poblar campos
            $('#editar-categoria-id').val(categoria.idcategoria);
            $('#editar-nombre-categoria').val(categoria.nombre);
            $('#editar-descripcion-categoria').val(categoria.descripcion || '');
            $('#editar-estado-categoria').val(categoria.estado);

            // Limpiar errores previos
            this.resetValidation();

            // Ocultar loading y mostrar formulario
            $('#editar-loading').addClass('hidden');
            $('#form-editar-categoria').removeClass('hidden');
        },

        handleSubmit: function(e) {
            e.preventDefault();

            if (!this.validateForm()) {
                return;
            }

            const formData = new FormData();
            formData.append('accion', 'editar_categoria');
            formData.append('idcategoria', $('#editar-categoria-id').val());
            formData.append('nombre', $('#editar-nombre-categoria').val().trim());
            formData.append('descripcion', $('#editar-descripcion-categoria').val().trim());
            formData.append('estado', $('#editar-estado-categoria').val());

            // Agregar CSRF token si está disponible
            const csrfToken = $('input[name="csrf_token"]').val();
            if (csrfToken) {
                formData.append('csrf_token', csrfToken);
            }

            this.submitForm(formData);
        },

        submitForm: function(formData) {
            // Deshabilitar botón durante el envío
            const submitButton = $('button[type="submit"][form="form-editar-categoria"]');
            const originalText = submitButton.html();
            submitButton.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Actualizando...</span>
            `);

            $.ajax({
                url: 'ajax/categorias.ajax.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(respuesta) {
                    if (respuesta.status === 'success') {
                        // Toast de éxito
                        const toastHtml = `
                            <div class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                                <div class="flex p-4">
                                    <div class="shrink-0">
                                        <svg class="shrink-0 size-4 text-teal-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                        </svg>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-sm text-gray-700 dark:text-neutral-400">
                                            ${respuesta.message}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                        showToast(toastHtml);

                        // Cerrar modal
                        const modal = document.getElementById('modal-editar-categoria');
                        if (modal && window.HSOverlay) {
                            window.HSOverlay.close(modal);
                        }

                        // Recargar lista de categorías
                        if (window.CategoriasManager) {
                            window.CategoriasManager.cargarCategorias();
                        }

                    } else {
                        console.error('FormEditarCategoria: Error del servidor:', respuesta.message);
                        const toastHtml = `
                            <div class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                                <div class="flex p-4">
                                    <div class="shrink-0">
                                        <svg class="shrink-0 size-4 text-red-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                        </svg>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-sm text-gray-700 dark:text-neutral-400">
                                            ${respuesta.message || 'Error al actualizar categoría'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                        showToast(toastHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormEditarCategoria: Error AJAX:', {xhr, status, error});
                    const toastHtml = `
                        <div class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                            <div class="flex p-4">
                                <div class="shrink-0">
                                    <svg class="shrink-0 size-4 text-red-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                    </svg>
                                </div>
                                <div class="ms-3">
                                    <p class="text-sm text-gray-700 dark:text-neutral-400">
                                        Error de conexión. Intenta nuevamente.
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    showToast(toastHtml);
                },
                complete: function() {
                    // Restaurar botón
                    submitButton.prop('disabled', false).html(originalText);
                }
            });
        },

        validateForm: function() {
            let isValid = true;

            // Validar nombre
            const nombre = $('#editar-nombre-categoria').val().trim();
            if (!nombre) {
                this.showFieldError($('#editar-nombre-categoria'), 'El nombre es requerido');
                isValid = false;
            } else {
                this.hideFieldError($('#editar-nombre-categoria'));
            }

            return isValid;
        },

        showFieldError: function(field, message) {
            // Remover errores anteriores
            field.removeClass('border-green-500').addClass('border-red-500');
            field.siblings('.field-error').remove();

            // Agregar nuevo error
            field.after(`<p class="field-error text-xs text-red-600 mt-1">${message}</p>`);
        },

        hideFieldError: function(field) {
            field.removeClass('border-red-500').addClass('border-gray-300');
            field.siblings('.field-error').remove();
        },

        resetValidation: function() {
            // Limpiar errores
            $('.field-error').remove();
            $('#form-editar-categoria input, #form-editar-categoria textarea, #form-editar-categoria select')
                .removeClass('border-red-500').addClass('border-gray-300');
        },

        resetForm: function() {
            $('#form-editar-categoria')[0].reset();
            this.resetValidation();
            this.datosOriginales = null;
        }
    };

    // Inicializar componentes cuando el DOM esté listo
    console.log('Inicializando categorías...', {
        tenantId: window.TENANT_ID,
        location: window.location.href,
        includes: window.location.href.includes('categorias')
    });

    if (window.TENANT_ID && window.location.href.includes('categorias')) {
        console.log('✅ Inicializando CategoriasManager...');
        CategoriasManager.init();

        // Inicializar FormAgregarCategoria inmediatamente (no esperar al modal)
        FormAgregarCategoria.init();

        // Inicializar FormEditarCategoria inmediatamente
        FormEditarCategoria.init();
    } else {
        console.log('❌ No se puede inicializar categorías:', {
            tenantId: window.TENANT_ID,
            isCategoriasPage: window.location.href.includes('categorias')
        });
    }

    // Event handlers para los modales
    // Botón Editar Categoría en las cards
    $(document).on('click', '.btnEditarCategoria', function() {
        const categoriaId = $(this).data('id');

        // Cargar datos de la categoría específica
        FormEditarCategoria.cargarCategoria(categoriaId);

        // Abrir modal usando Preline UI
        const modal = document.getElementById('modal-editar-categoria');
        if (modal && window.HSOverlay) {
            window.HSOverlay.open(modal);
        }
    });

    // Botón Eliminar Categoría en las cards
    $(document).on('click', '.btnEliminarCategoria', function() {
        const categoriaId = $(this).data('id');

        // Obtener el nombre de la categoría del botón
        const categoriaNombre = $(this).closest('.bg-white').find('h3').text().trim();

        // Confirmar eliminación con SweetAlert
        Swal.fire({
            title: '¿Eliminar categoría?',
            html: `
                <div class="text-left">
                    <p class="mb-3"><strong>Categoría:</strong> ${categoriaNombre}</p>
                    <p class="text-sm text-gray-600">Esta acción:</p>
                    <ul class="text-sm text-gray-600 mt-2 space-y-1">
                        <li>• Verificará si la categoría tiene productos asociados</li>
                        <li>• Si tiene productos, será <strong>desactivada</strong> para auditoría</li>
                        <li>• Si no tiene productos, será <strong>eliminada</strong> permanentemente</li>
                    </ul>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, proceder',
            cancelButtonText: 'Cancelar',
            backdrop: true,
            allowOutsideClick: false,
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                eliminarCategoria(categoriaId);
            }
        });
    });

    // Reset del formulario cuando se cierre el modal de agregar
    $(document).on('hidden.hs.overlay', '#modal-agregar-categoria', function() {
        FormAgregarCategoria.resetForm();
    });

    // Reset del formulario cuando se cierre el modal de editar
    $(document).on('hidden.hs.overlay', '#modal-editar-categoria', function() {
        FormEditarCategoria.resetForm();
    });

    // Inicializar FormEditarCategoria cuando se abra el modal de editar
    $(document).on('shown.hs.overlay', '#modal-editar-categoria', function() {
        // Mostrar loading state por defecto
        $('#editar-loading').removeClass('hidden');
        $('#form-editar-categoria').addClass('hidden');
        FormEditarCategoria.init();
    });

    // Event handlers para paginación
    $(document).on('click', '.btn-previous', function(e) {
        e.preventDefault();
        if (window.CategoriasManager) {
            window.CategoriasManager.paginaAnterior();
        }
    });

    $(document).on('click', '.btn-next', function(e) {
        e.preventDefault();
        if (window.CategoriasManager) {
            window.CategoriasManager.paginaSiguiente();
        }
    });

    // Función para eliminar categoría
    function eliminarCategoria(categoriaId) {
        // Agregar CSRF token si está disponible
        const dataToSend = {
            accion: 'eliminar_categoria',
            idcategoria: categoriaId
        };

        const csrfToken = $('input[name="csrf_token"]').val();
        if (csrfToken) {
            dataToSend.csrf_token = csrfToken;
        }

        $.ajax({
            url: 'ajax/categorias.ajax.php',
            type: 'POST',
            data: dataToSend,
            dataType: 'json',
            beforeSend: function() {
                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Verificando dependencias de la categoría',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Mostrar mensaje diferenciado según el tipo de eliminación
                    let iconType = response.tipo_eliminacion === 'desactivacion' ? 'warning' : 'success';
                    let titleText = response.tipo_eliminacion === 'desactivacion' ? 'Categoría Desactivada' : 'Categoría Eliminada';

                    // Preparar mensaje adicional si hay dependencias
                    let mensajeAdicional = '';
                    if (response.dependencias && response.dependencias.tiene_dependencias) {
                        mensajeAdicional = `<br><br><small class="text-gray-600">La categoría tenía ${response.dependencias.productos} producto(s) asociado(s), por lo que se desactivó para mantener la integridad de los datos.</small>`;
                    }

                    Swal.fire({
                        title: titleText,
                        html: response.message + mensajeAdicional,
                        icon: iconType,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        // Mostrar toast adicional de confirmación
                        const toastHtml = `
                            <div class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                                <div class="flex p-4">
                                    <div class="shrink-0">
                                        <svg class="shrink-0 size-4 text-teal-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                        </svg>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-sm text-gray-700 dark:text-neutral-400">
                                            ${titleText.toLowerCase()} exitosamente
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                        showToast(toastHtml);

                        // Recargar la lista de categorías
                        if (window.CategoriasManager) {
                            window.CategoriasManager.cargarCategorias();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Error al eliminar categoría',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX eliminar categoría:', {xhr, status, error});
                Swal.fire({
                    title: 'Error de Conexión',
                    text: 'No se pudo conectar con el servidor. Intenta nuevamente.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            }
        });
    }

    // Funcionalidad del botón "Ver todas"
    $(document).on('click', '#btn-ver-todas', function(e) {
        e.preventDefault();

        if (window.CategoriasManager) {
            const isShowingAll = window.CategoriasManager.incluyeEliminadas;
            const newState = !isShowingAll;

            // Cambiar el estado
            window.CategoriasManager.incluyeEliminadas = newState;

            // Actualizar texto del botón
            const btnText = $('#btn-ver-todas-text');
            btnText.text(newState ? 'Ver activas' : 'Ver todas');

            // Actualizar el filtro de estado automáticamente
            if (newState) {
                // Al activar "Ver todas", mostrar todos los estados (activas, inactivas y eliminadas)
                $('#filtro-estado').val(''); // Todos los estados
                window.CategoriasManager.filters.estado = '';
            } else {
                // Al desactivar "Ver todas", volver a mostrar solo activas
                $('#filtro-estado').val('1'); // Solo activas
                window.CategoriasManager.filters.estado = '1';
            }

            // Resetear página a 1 y recargar
            window.CategoriasManager.pagination.currentPage = 1;
            window.CategoriasManager.cargarCategorias();
        }
    });

    } // Fin de initCategorias

    // Verificar si jQuery está disponible
    if (typeof $ !== 'undefined') {
        $(document).ready(initCategorias);
    } else {
        // Esperar a que jQuery se cargue
        var checkJQuery = setInterval(function() {
            if (typeof $ !== 'undefined') {
                clearInterval(checkJQuery);
                $(document).ready(initCategorias);
            }
        }, 50);

        // Timeout de seguridad
        setTimeout(function() {
            clearInterval(checkJQuery);
            if (typeof $ === 'undefined') {
                console.error('jQuery no se pudo cargar en categorias.js');
            }
        }, 5000);
    }
})();