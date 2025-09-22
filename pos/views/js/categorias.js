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

            let statusClass, statusText, statusIcon;

            if (isDeleted) {
                statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                statusText = 'Eliminada';
                statusIcon = '🗑️';
            } else {
                statusClass = categoria.estado == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                statusText = categoria.estado == 1 ? 'Activa' : 'Inactiva';
                statusIcon = categoria.estado == 1 ? '✅' : '❌';
            }

            // Formatear fecha
            const fechaCreacion = categoria.created_at ? new Date(categoria.created_at).toLocaleDateString('es-ES') : 'N/A';

            return $(`
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-700 hover:-translate-y-1">
                    <!-- Header -->
                    <div class="p-6 pb-4 flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 flex items-center justify-center border-2 border-white dark:border-neutral-700 shadow-sm">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4zM14 4h6v6h-6V4zM4 14h6v6H4v-6zM14 14h6v6h-6v-6z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 truncate">${categoria.nombre || 'N/A'}</h3>
                            <p class="text-sm text-gray-600 dark:text-neutral-400 truncate">${categoria.descripcion || 'Sin descripción'}</p>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 pb-4 space-y-3">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Fecha:</span>
                            <span class="text-gray-900 dark:text-white">${fechaCreacion}</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Estado:</span>
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full ${statusClass}">
                                ${statusIcon} ${statusText}
                            </span>
                        </div>
                    </div>

                    <!-- Footer con acciones -->
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/50">
                        <div class="flex items-center justify-end gap-2">
                            ${isDeleted ? `
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-gray-500 bg-gray-100 dark:text-neutral-400 dark:bg-neutral-700">
                                    🗑️ Categoría eliminada
                                </span>
                            ` : `
                                <button class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 hover:text-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:text-amber-400 dark:bg-amber-900/20 dark:hover:bg-amber-900/30 transition-colors duration-200 btnEditarCategoria" data-id="${categoria.idcategoria}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </button>
                                <button class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:text-red-400 dark:bg-red-900/20 dark:hover:bg-red-900/30 transition-colors duration-200 btnEliminarCategoria" data-id="${categoria.idcategoria}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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