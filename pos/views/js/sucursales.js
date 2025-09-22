$(document).ready(function() {

    // Función para mostrar toast notifications
    function showToast(htmlContent) {
        // Crear el contenedor de toast si no existe
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-[70] space-y-3';
            document.body.appendChild(toastContainer);
        }

        // Crear el toast
        const toast = document.createElement('div');
        toast.innerHTML = htmlContent;
        toast.className = 'transform translate-x-full opacity-0 transition-all duration-300';

        // Agregar al contenedor
        toastContainer.appendChild(toast);

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

    // Sistema de gestión de sucursales con cards
    const SucursalesManager = {
        data: [],
        filteredData: [],
        incluyeEliminadas: false, // Nueva propiedad para controlar si mostrar eliminadas
        pagination: {
            currentPage: 1,
            limit: 6,
            total: 0,
            totalPages: 0,
            hasPrevious: false,
            hasNext: false
        },
        filters: {
            buscar: '',
            estado: '1' // Inicializar mostrando solo sucursales activas
        },

        init: function() {
            // Solo inicializar si existe el contenedor Y el usuario está logueado
            if ($('#sucursales-grid').length && window.TENANT_ID) {
                this.bindEvents();
                this.cargarSucursales();
            }
        },

        bindEvents: function() {
            const self = this;

            // Filtro de búsqueda con debounce
            let searchTimeout;
            $(document).on('input', '#buscar-sucursal', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    self.filters.buscar = $(this).val().toLowerCase();
                    self.pagination.currentPage = 1;
                    self.cargarSucursales();
                }, 300);
            });

            // Filtro por estado
            $(document).on('change', '#filtro-estado', function() {
                self.filters.estado = $(this).val();
                self.pagination.currentPage = 1;
                self.cargarSucursales();
            });

            // Limpiar filtros
            $(document).on('click', '#limpiar-filtros', function() {
                self.limpiarFiltros();
            });
        },

        cargarSucursales: function(page = null) {
            const self = this;

            // Si se especifica página, actualizar el estado
            if (page !== null) {
                this.pagination.currentPage = page;
            }

            $.ajax({
                url: 'ajax/sucursales.ajax.php',
                type: 'POST',
                data: {
                    accion: 'obtener_sucursales',
                    page: this.pagination.currentPage,
                    limit: this.pagination.limit,
                    estado: this.filters.estado, // Mantener el filtro de estado
                    incluir_eliminadas: this.incluyeEliminadas ? 'true' : 'false'
                },
                dataType: 'json',
                beforeSend: function() {
                    self.mostrarLoading();
                },
                success: function(respuesta) {
                    if (respuesta.status === 'success' && respuesta.data) {
                        // Actualizar datos de paginación
                        self.data = respuesta.data.sucursales || [];
                        self.pagination.total = respuesta.data.total || 0;
                        self.pagination.totalPages = respuesta.data.total_pages || 0;
                        self.pagination.hasPrevious = respuesta.data.has_previous || false;
                        self.pagination.hasNext = respuesta.data.has_next || false;

                        // Aplicar filtros locales
                        self.aplicarFiltros();

                        // Mostrar resultados
                        if (self.filteredData.length > 0) {
                            self.mostrarSucursales();
                        } else {
                            self.mostrarVacio();
                        }
                    } else {
                        console.error('SucursalesManager: Error en respuesta:', respuesta);
                        self.mostrarError(respuesta.message || 'Error al cargar sucursales');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('SucursalesManager: Error AJAX:', {xhr, status, error});
                    console.error('SucursalesManager: Respuesta del servidor:', xhr.responseText);
                    console.error('SucursalesManager: Status HTTP:', xhr.status);
                    self.mostrarError('Error de conexión al cargar sucursales');
                },
                complete: function() {
                    self.ocultarLoading();
                }
            });
        },

        aplicarFiltros: function() {

            this.filteredData = this.data.filter(sucursal => {
                // Filtro de búsqueda
                if (this.filters.buscar && this.filters.buscar.length > 0) {
                    const busqueda = this.filters.buscar.toLowerCase();
                    const nombre = (sucursal.sri_nombre || '').toLowerCase();
                    const direccion = (sucursal.sri_direccion || '').toLowerCase();
                    const codigo = (sucursal.sri_codigo || '').toLowerCase();

                    if (!nombre.includes(busqueda) && !direccion.includes(busqueda) && !codigo.includes(busqueda)) {
                        return false;
                    }
                }

                return true;
            });
        },

        mostrarSucursales: function() {
            const container = $('#sucursales-grid');
            // console.log('mostrarSucursales: Iniciando con container:', container.length);

            // Limpiar contenido anterior
            container.empty();

            // Ocultar otros estados
            $('#sucursales-empty').addClass('hidden');
            $('#sucursales-loading').addClass('hidden');

            // console.log('mostrarSucursales: Renderizando', this.filteredData.length, 'sucursales');

            // Mostrar grid
            container.removeClass('hidden').addClass('grid');

            // Crear cards para cada sucursal
            this.filteredData.forEach((sucursal, index) => {
                // console.log('SucursalesManager: Creando card', index + 1, 'para sucursal:', sucursal.sri_nombre);
                const card = this.crearCard(sucursal);
                container.append(card);
            });

            // Verificar estado final del contenedor
            // console.log('mostrarSucursales: Cards agregadas al DOM');
            // console.log('- container.children().length:', container.children().length);
            // console.log('- container.is(":visible"):', container.is(':visible'));
            // console.log('- container.hasClass("hidden"):', container.hasClass('hidden'));
            // console.log('- container.hasClass("grid"):', container.hasClass('grid'));

            this.actualizarFooterPaginacion();
        },

        crearCard: function(sucursal) {
            // Verificar si la sucursal está eliminada
            const isDeleted = sucursal.deleted_at !== null && sucursal.deleted_at !== undefined;

            let statusClass, statusText, statusIcon;

            if (isDeleted) {
                statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                statusText = 'Eliminada';
                statusIcon = '🗑️';
            } else {
                statusClass = sucursal.estado == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
                statusText = sucursal.estado == 1 ? 'Activa' : 'Inactiva';
                statusIcon = sucursal.estado == 1 ? '✅' : '❌';
            }

            return $(`
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-700 hover:-translate-y-1">
                    <!-- Header -->
                    <div class="p-6 pb-4 flex items-start gap-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xl font-bold border-2 border-white dark:border-neutral-700 shadow-sm">
                            🏢
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 truncate">${sucursal.sri_nombre || 'N/A'}</h3>
                            <p class="text-sm text-gray-600 dark:text-neutral-400 truncate">Código SRI: ${sucursal.sri_codigo || 'N/A'}</p>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 pb-4 space-y-3">
                        <div class="flex items-start gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Dirección:</span>
                            <span class="text-gray-900 dark:text-white truncate">${sucursal.sri_direccion || 'No especificada'}</span>
                        </div>

                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Estado:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                ${statusIcon} ${statusText}
                            </span>
                        </div>


                        ${sucursal.created_at ? `
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h6a2 2 0 012 2v4m-4 6h.01M15 11v6m0 0v.01M15 17.99h3M12 11h.01M9 11v6m0 0v.01M9 17.99h3m-6-6.99h12a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8a2 2 0 012-2z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Creada:</span>
                            <span class="text-gray-900 dark:text-white">${new Date(sucursal.created_at).toLocaleDateString('es-EC')}</span>
                        </div>
                        ` : ''}
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/50">
                        <div class="flex gap-2 justify-end">
                            ${isDeleted ? `
                                <div class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-gray-500 bg-gray-100 cursor-not-allowed dark:text-gray-400 dark:bg-gray-800">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                    </svg>
                                    Sucursal eliminada
                                </div>
                            ` : `
                                <button class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:text-blue-400 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 transition-colors duration-200 btnEditarSucursal" data-id="${sucursal.idsucursal}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </button>
                                <button class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 dark:text-red-400 dark:bg-red-900/20 dark:hover:bg-red-900/30 transition-colors duration-200 btnEliminarSucursal" data-id="${sucursal.idsucursal}">
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
            $('#sucursales-grid').addClass('hidden');
            $('#sucursales-empty').addClass('hidden');
            $('#sucursales-loading').removeClass('hidden');
            $('#sucursales-footer').addClass('hidden');
        },

        ocultarLoading: function() {
            $('#sucursales-loading').addClass('hidden');
        },

        mostrarVacio: function() {
            $('#sucursales-grid').addClass('hidden');
            $('#sucursales-loading').addClass('hidden');
            $('#sucursales-empty').removeClass('hidden');
            $('#sucursales-footer').addClass('hidden');
        },

        mostrarError: function(mensaje) {
            console.error('SucursalesManager: Error:', mensaje);
            this.mostrarVacio();
        },

        actualizarFooterPaginacion: function() {
            const footer = $('#sucursales-footer');

            // Siempre mostrar el footer cuando hay sucursales
            footer.removeClass('hidden');

            // Calcular elementos mostrados
            const start = Math.min(((this.pagination.currentPage - 1) * this.pagination.limit) + 1, this.pagination.total);
            const end = Math.min(this.pagination.currentPage * this.pagination.limit, this.pagination.total);

            // Actualizar textos
            $('#pagination-start').text(start);
            $('#pagination-end').text(end);
            $('#pagination-total').text(this.pagination.total);
            $('#pagination-pages').text(`Página ${this.pagination.currentPage} de ${this.pagination.totalPages}`);

            // Habilitar/deshabilitar botones según paginación
            $('.btn-previous').prop('disabled', !this.pagination.hasPrevious);
            $('.btn-next').prop('disabled', !this.pagination.hasNext);
        },

        paginaAnterior: function() {
            if (this.pagination.hasPrevious) {
                this.cargarSucursales(this.pagination.currentPage - 1);
            }
        },

        paginaSiguiente: function() {
            if (this.pagination.hasNext) {
                this.cargarSucursales(this.pagination.currentPage + 1);
            }
        },

        limpiarFiltros: function() {
            // Resetear campos de filtros
            $('#buscar-sucursal').val('');
            $('#filtro-estado').val('1');

            // Resetear estado interno
            this.filters.buscar = '';
            this.filters.estado = '1';
            this.pagination.currentPage = 1;

            // Recargar datos
            this.cargarSucursales();
        }
    };

    // Exponer el manager globalmente para uso externo
    window.SucursalesManager = SucursalesManager;

    // Sistema de manejo de formulario agregar sucursal
    const FormAgregarSucursal = {
        init: function() {
            this.bindEvents();
            this.resetForm();
        },

        bindEvents: function() {
            // Usar delegación de eventos desde document para asegurar que funcione
            $(document).off('submit', '#form-agregar-sucursal').on('submit', '#form-agregar-sucursal', this.handleSubmit.bind(this));

            // Validación en tiempo real del código SRI usando delegación de eventos
            $(document).off('input', '#sri-codigo-sucursal').on('input', '#sri-codigo-sucursal', this.validateSriCodigo.bind(this));
            $(document).off('blur', '#sri-codigo-sucursal').on('blur', '#sri-codigo-sucursal', this.checkSriCodigoAvailability.bind(this));
        },

        validateSriCodigo: function(e) {
            const input = $(e.target);
            const valor = input.val();
            const errorDiv = $('#sri-codigo-error');
            const successDiv = $('#sri-codigo-success');

            // Limpiar mensajes previos
            errorDiv.addClass('hidden');
            successDiv.addClass('hidden');

            // Solo permitir números
            const valorLimpio = valor.replace(/[^0-9]/g, '');
            if (valorLimpio !== valor) {
                input.val(valorLimpio);
            }

            // Validar formato
            if (valorLimpio.length === 0) {
                input.removeClass('border-green-500 border-red-500').addClass('border-gray-300');
                return;
            }

            if (valorLimpio.length !== 3) {
                input.removeClass('border-green-500 border-gray-300').addClass('border-red-500');
                errorDiv.text('El código debe tener exactamente 3 dígitos').removeClass('hidden');
                return;
            }

            const numero = parseInt(valorLimpio);
            if (numero < 1 || numero > 999) {
                input.removeClass('border-green-500 border-gray-300').addClass('border-red-500');
                errorDiv.text('El código debe estar entre 001 y 999').removeClass('hidden');
                return;
            }

            // Formato válido
            input.removeClass('border-red-500 border-gray-300').addClass('border-green-500');

            // Formatear con ceros a la izquierda
            const codigoFormateado = valorLimpio.padStart(3, '0');
            if (input.val() !== codigoFormateado) {
                input.val(codigoFormateado);
            }
        },

        checkSriCodigoAvailability: function(e) {
            const input = $(e.target);
            const codigo = input.val();
            const errorDiv = $('#sri-codigo-error');
            const successDiv = $('#sri-codigo-success');

            console.log('FormAgregarSucursal: Verificando disponibilidad del código:', codigo);

            if (!codigo || codigo.length !== 3) {
                console.log('FormAgregarSucursal: Código no válido para verificación');
                return;
            }

            // Verificar disponibilidad en el servidor
            $.ajax({
                url: 'ajax/sucursales.ajax.php',
                type: 'POST',
                data: {
                    accion: 'verificar_codigo_sri',
                    sri_codigo: codigo
                },
                dataType: 'json',
                success: function(respuesta) {
                    console.log('FormAgregarSucursal: Respuesta verificación código:', respuesta);
                    if (respuesta.status === 'success') {
                        if (respuesta.disponible) {
                            input.removeClass('border-red-500 border-gray-300').addClass('border-green-500');
                            errorDiv.addClass('hidden');
                            successDiv.removeClass('hidden');
                            console.log('FormAgregarSucursal: Código disponible');
                        } else {
                            input.removeClass('border-green-500 border-gray-300').addClass('border-red-500');
                            successDiv.addClass('hidden');
                            errorDiv.text('Este código ya está en uso').removeClass('hidden');
                            console.log('FormAgregarSucursal: Código no disponible');
                        }
                    } else {
                        console.error('FormAgregarSucursal: Error en respuesta:', respuesta);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormAgregarSucursal: Error AJAX verificar código:', {xhr, status, error});
                    console.error('FormAgregarSucursal: Respuesta del servidor:', xhr.responseText);
                }
            });
        },

        handleSubmit: function(e) {
            e.preventDefault();
            console.log('FormAgregarSucursal: ¡SUBMIT EVENT CAPTURADO!');
            console.log('FormAgregarSucursal: Procesando envío...');
            console.log('FormAgregarSucursal: Event target:', e.target);
            console.log('FormAgregarSucursal: Form ID:', e.target.id);

            if (!this.validateForm()) {
                console.log('FormAgregarSucursal: Validación fallida');
                return;
            }

            // Preparar datos del formulario
            const formData = {
                accion: 'crear_sucursal',
                sri_codigo: $('#sri-codigo-sucursal').val().trim(),
                sri_nombre: $('#nombre-sucursal').val().trim(),
                sri_direccion: $('#direccion-sucursal').val().trim(),
                estado: $('#estado-sucursal').val() || '1'
            };

            // Agregar CSRF token si está disponible
            const csrfToken = $('input[name="csrf_token"]').val();
            if (csrfToken) {
                formData.csrf_token = csrfToken;
            }

            console.log('FormAgregarSucursal: Datos finales a enviar:', formData);

            this.submitForm(formData);
        },

        submitForm: function(formData) {
            console.log('FormAgregarSucursal: Enviando datos al servidor...');

            // Debug: mostrar todos los datos que se van a enviar
            console.log('FormAgregarSucursal: Datos del formulario:');
            Object.entries(formData).forEach(([key, value]) => {
                console.log('  -', key, ':', value);
            });

            // Deshabilitar botón durante el envío
            const submitButton = $('button[type="submit"][form="form-agregar-sucursal"]');
            const originalText = submitButton.html();
            submitButton.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Creando...</span>
            `);

            console.log('FormAgregarSucursal: Configurando AJAX request...');

            $.ajax({
                url: 'ajax/sucursales.ajax.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function(xhr) {
                    console.log('FormAgregarSucursal: AJAX beforeSend - request iniciado');
                },
                success: function(respuesta) {
                    console.log('FormAgregarSucursal: Respuesta del servidor:', respuesta);

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
                        const modal = document.getElementById('modal-agregar-sucursal');
                        if (modal && window.HSOverlay) {
                            window.HSOverlay.close(modal);
                        }

                        // Recargar lista de sucursales
                        if (window.SucursalesManager) {
                            window.SucursalesManager.cargarSucursales();
                        }

                        // Resetear formulario
                        FormAgregarSucursal.resetForm();

                    } else {
                        console.error('FormAgregarSucursal: Error del servidor:', respuesta.message);
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
                                            ${respuesta.message || 'Error al crear sucursal'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                        showToast(toastHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormAgregarSucursal: Error AJAX:', {xhr, status, error});
                    console.error('FormAgregarSucursal: Response text:', xhr.responseText);
                    console.error('FormAgregarSucursal: HTTP status:', xhr.status);
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

            console.log('FormAgregarSucursal: Iniciando validación del formulario...');

            // Validar código SRI
            const sriCodigo = $('#sri-codigo-sucursal').val().trim();
            console.log('FormAgregarSucursal: sri_codigo =', sriCodigo);

            if (!sriCodigo) {
                this.showFieldError($('#sri-codigo-sucursal'), 'El código SRI es requerido');
                isValid = false;
            } else if (!/^[0-9]{3}$/.test(sriCodigo)) {
                this.showFieldError($('#sri-codigo-sucursal'), 'El código debe tener 3 dígitos (001-999)');
                isValid = false;
            } else if (parseInt(sriCodigo) < 1 || parseInt(sriCodigo) > 999) {
                this.showFieldError($('#sri-codigo-sucursal'), 'El código debe estar entre 001 y 999');
                isValid = false;
            } else {
                this.hideFieldError($('#sri-codigo-sucursal'));
            }

            // Validar nombre
            const nombre = $('#nombre-sucursal').val().trim();
            console.log('FormAgregarSucursal: sri_nombre =', nombre);

            if (!nombre) {
                this.showFieldError($('#nombre-sucursal'), 'El nombre es requerido');
                isValid = false;
            } else {
                this.hideFieldError($('#nombre-sucursal'));
            }

            // Validar dirección
            const direccion = $('#direccion-sucursal').val().trim();
            console.log('FormAgregarSucursal: sri_direccion =', direccion);

            if (!direccion) {
                this.showFieldError($('#direccion-sucursal'), 'La dirección es requerida');
                isValid = false;
            } else {
                this.hideFieldError($('#direccion-sucursal'));
            }

            console.log('FormAgregarSucursal: Validación completada. Válido:', isValid);
            return isValid;
        },

        showFieldError: function(field, message) {
            // Remover errores anteriores
            field.removeClass('border-gray-300').addClass('border-red-500');
            field.siblings('.field-error').remove();

            // Agregar nuevo error
            field.after(`<p class="field-error text-xs text-red-600 mt-1">${message}</p>`);
        },

        hideFieldError: function(field) {
            field.removeClass('border-red-500').addClass('border-gray-300');
            field.siblings('.field-error').remove();
        },

        resetForm: function() {
            // console.log('FormAgregarSucursal: Reseteando formulario...');

            // Reset del formulario HTML
            $('#form-agregar-sucursal')[0].reset();

            // Limpiar errores de validación
            $('.field-error').remove();

            // Resetear estilos de campos
            $('#form-agregar-sucursal input, #form-agregar-sucursal textarea, #form-agregar-sucursal select')
                .removeClass('border-red-500 border-green-500').addClass('border-gray-300');

            // Limpiar mensajes específicos de código SRI
            $('#sri-codigo-error').addClass('hidden');
            $('#sri-codigo-success').addClass('hidden');

            // Resetear valores específicos por si acaso
            $('#sri-codigo-sucursal').val('');
            $('#nombre-sucursal').val('');
            $('#direccion-sucursal').val('');
            $('#estado-sucursal').val('1');

            // console.log('FormAgregarSucursal: Formulario reseteado completamente');
        }
    };

    // Sistema de manejo de formulario editar sucursal
    const FormEditarSucursal = {
        datosOriginales: null,

        init: function() {
            // console.log('FormEditarSucursal: Inicializando...');
            this.bindEvents();
        },

        bindEvents: function() {
            // Manejar envío del formulario
            $('#form-editar-sucursal').off('submit').on('submit', this.handleSubmit.bind(this));

            // Validación en tiempo real del código SRI para editar
            $('#editar-sri-codigo-sucursal').off('input').on('input', this.validateSriCodigo.bind(this));
            $('#editar-sri-codigo-sucursal').off('blur').on('blur', this.checkSriCodigoAvailability.bind(this));
        },

        validateSriCodigo: function(e) {
            const input = $(e.target);
            const valor = input.val();
            const errorDiv = $('#editar-sri-codigo-error');
            const successDiv = $('#editar-sri-codigo-success');

            // Limpiar mensajes previos
            errorDiv.addClass('hidden');
            successDiv.addClass('hidden');

            // Solo permitir números
            const valorLimpio = valor.replace(/[^0-9]/g, '');
            if (valorLimpio !== valor) {
                input.val(valorLimpio);
            }

            // Validar formato
            if (valorLimpio.length === 0) {
                input.removeClass('border-green-500 border-red-500').addClass('border-gray-300');
                return;
            }

            if (valorLimpio.length !== 3) {
                input.removeClass('border-green-500 border-gray-300').addClass('border-red-500');
                errorDiv.text('El código debe tener exactamente 3 dígitos').removeClass('hidden');
                return;
            }

            const numero = parseInt(valorLimpio);
            if (numero < 1 || numero > 999) {
                input.removeClass('border-green-500 border-gray-300').addClass('border-red-500');
                errorDiv.text('El código debe estar entre 001 y 999').removeClass('hidden');
                return;
            }

            // Formato válido
            input.removeClass('border-red-500 border-gray-300').addClass('border-green-500');

            // Formatear con ceros a la izquierda
            const codigoFormateado = valorLimpio.padStart(3, '0');
            if (input.val() !== codigoFormateado) {
                input.val(codigoFormateado);
            }
        },

        checkSriCodigoAvailability: function(e) {
            const input = $(e.target);
            const codigo = input.val();
            const errorDiv = $('#editar-sri-codigo-error');
            const successDiv = $('#editar-sri-codigo-success');
            const sucursalId = $('#editar-sucursal-id').val();

            if (!codigo || codigo.length !== 3) {
                return;
            }

            // Si es el mismo código original, no validar disponibilidad
            if (this.datosOriginales && this.datosOriginales.sri_codigo === codigo) {
                input.removeClass('border-red-500 border-gray-300').addClass('border-green-500');
                errorDiv.addClass('hidden');
                successDiv.removeClass('hidden');
                return;
            }

            // Verificar disponibilidad en el servidor
            $.ajax({
                url: 'ajax/sucursales.ajax.php',
                type: 'POST',
                data: {
                    accion: 'verificar_codigo_sri',
                    sri_codigo: codigo,
                    excluir_id: sucursalId // Excluir la sucursal actual de la validación
                },
                dataType: 'json',
                success: function(respuesta) {
                    if (respuesta.status === 'success') {
                        if (respuesta.disponible) {
                            input.removeClass('border-red-500 border-gray-300').addClass('border-green-500');
                            errorDiv.addClass('hidden');
                            successDiv.removeClass('hidden');
                        } else {
                            input.removeClass('border-green-500 border-gray-300').addClass('border-red-500');
                            successDiv.addClass('hidden');
                            errorDiv.text('Este código ya está en uso').removeClass('hidden');
                        }
                    }
                },
                error: function() {
                    console.error('Error al verificar disponibilidad del código SRI');
                }
            });
        },

        cargarSucursal: function(sucursalId) {
            console.log('FormEditarSucursal: Cargando datos para sucursal ID:', sucursalId);

            // Mostrar loading y ocultar formulario
            $('#editar-loading').removeClass('hidden');
            $('#form-editar-sucursal').addClass('hidden');

            $.ajax({
                url: 'ajax/sucursales.ajax.php',
                type: 'POST',
                data: {
                    accion: 'obtener_sucursal',
                    idsucursal: sucursalId
                },
                dataType: 'json',
                success: function(respuesta) {
                    console.log('FormEditarSucursal: Datos recibidos:', respuesta);

                    if (respuesta.status === 'success' && respuesta.data) {
                        FormEditarSucursal.poblarFormulario(respuesta.data);
                    } else {
                        console.error('FormEditarSucursal: Error al cargar sucursal:', respuesta.message);
                        alert('Error al cargar los datos de la sucursal');
                        // Ocultar loading en caso de error
                        $('#editar-loading').addClass('hidden');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormEditarSucursal: Error AJAX:', {xhr, status, error});
                    alert('Error de conexión al cargar la sucursal');
                    // Ocultar loading en caso de error
                    $('#editar-loading').addClass('hidden');
                }
            });
        },

        poblarFormulario: function(sucursal) {
            console.log('FormEditarSucursal: Poblando formulario con:', sucursal);

            // Guardar datos originales para comparación
            this.datosOriginales = { ...sucursal };

            // Poblar campos
            $('#editar-sucursal-id').val(sucursal.idsucursal);
            $('#editar-sri-codigo-sucursal').val(sucursal.sri_codigo);
            $('#editar-nombre-sucursal').val(sucursal.sri_nombre);
            $('#editar-direccion-sucursal').val(sucursal.sri_direccion);
            $('#editar-estado-sucursal').val(sucursal.estado);

            // Limpiar errores previos
            this.resetValidation();

            // Ocultar loading y mostrar formulario
            $('#editar-loading').addClass('hidden');
            $('#form-editar-sucursal').removeClass('hidden');
        },

        handleSubmit: function(e) {
            e.preventDefault();
            console.log('FormEditarSucursal: Procesando envío...');

            if (!this.validateForm()) {
                console.log('FormEditarSucursal: Validación fallida');
                return;
            }

            const formData = new FormData();
            formData.append('accion', 'editar_sucursal');
            formData.append('idsucursal', $('#editar-sucursal-id').val());
            formData.append('sri_codigo', $('#editar-sri-codigo-sucursal').val().trim());
            formData.append('sri_nombre', $('#editar-nombre-sucursal').val().trim());
            formData.append('sri_direccion', $('#editar-direccion-sucursal').val().trim());
            formData.append('estado', $('#editar-estado-sucursal').val());

            // Agregar CSRF token si está disponible
            const csrfToken = $('input[name="csrf_token"]').val();
            if (csrfToken) {
                formData.append('csrf_token', csrfToken);
            }

            this.submitForm(formData);
        },

        submitForm: function(formData) {
            console.log('FormEditarSucursal: Enviando datos al servidor...');

            // Deshabilitar botón durante el envío
            const submitButton = $('button[type="submit"][form="form-editar-sucursal"]');
            const originalText = submitButton.html();
            submitButton.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Actualizando...</span>
            `);

            $.ajax({
                url: 'ajax/sucursales.ajax.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(respuesta) {
                    console.log('FormEditarSucursal: Respuesta del servidor:', respuesta);

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
                        const modal = document.getElementById('modal-editar-sucursal');
                        if (modal && window.HSOverlay) {
                            window.HSOverlay.close(modal);
                        }

                        // Recargar lista de sucursales
                        if (window.SucursalesManager) {
                            window.SucursalesManager.cargarSucursales();
                        }

                    } else {
                        console.error('FormEditarSucursal: Error del servidor:', respuesta.message);
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
                                            ${respuesta.message || 'Error al actualizar sucursal'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                        showToast(toastHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormEditarSucursal: Error AJAX:', {xhr, status, error});
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

            // Validar código SRI
            const sriCodigo = $('#editar-sri-codigo-sucursal').val().trim();
            if (!sriCodigo) {
                this.showFieldError($('#editar-sri-codigo-sucursal'), 'El código SRI es requerido');
                isValid = false;
            } else if (!/^[0-9]{3}$/.test(sriCodigo)) {
                this.showFieldError($('#editar-sri-codigo-sucursal'), 'El código debe tener 3 dígitos (001-999)');
                isValid = false;
            } else if (parseInt(sriCodigo) < 1 || parseInt(sriCodigo) > 999) {
                this.showFieldError($('#editar-sri-codigo-sucursal'), 'El código debe estar entre 001 y 999');
                isValid = false;
            } else {
                this.hideFieldError($('#editar-sri-codigo-sucursal'));
            }

            // Validar nombre
            const nombre = $('#editar-nombre-sucursal').val().trim();
            if (!nombre) {
                this.showFieldError($('#editar-nombre-sucursal'), 'El nombre es requerido');
                isValid = false;
            } else {
                this.hideFieldError($('#editar-nombre-sucursal'));
            }

            // Validar dirección
            const direccion = $('#editar-direccion-sucursal').val().trim();
            if (!direccion) {
                this.showFieldError($('#editar-direccion-sucursal'), 'La dirección es requerida');
                isValid = false;
            } else {
                this.hideFieldError($('#editar-direccion-sucursal'));
            }

            return isValid;
        },

        showFieldError: function(field, message) {
            // Remover errores anteriores
            field.removeClass('border-gray-300').addClass('border-red-500');
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
            $('#form-editar-sucursal input, #form-editar-sucursal textarea, #form-editar-sucursal select')
                .removeClass('border-red-500').addClass('border-gray-300');
        },

        resetForm: function() {
            $('#form-editar-sucursal')[0].reset();
            this.resetValidation();
            this.datosOriginales = null;
        }
    };

    // Inicializar componentes cuando el DOM esté listo
    if (window.TENANT_ID && window.location.href.includes('sucursales')) {
        // console.log('Página de sucursales detectada, inicializando componentes...');
        SucursalesManager.init();

        // Inicializar FormAgregarSucursal inmediatamente (no esperar al modal)
        // console.log('Inicializando FormAgregarSucursal inmediatamente...');
        FormAgregarSucursal.init();

        // Inicializar FormEditarSucursal inmediatamente
        // console.log('Inicializando FormEditarSucursal inmediatamente...');
        FormEditarSucursal.init();

        // Listener global para debug de cualquier submit
        $(document).on('submit', 'form', function(e) {
            console.log('GLOBAL SUBMIT LISTENER: Form submitted:', this.id || 'sin-id', this);
            if (this.id === 'form-agregar-sucursal') {
                console.log('GLOBAL: Detectado submit del formulario agregar sucursal');
            }
        });
    } else {
        console.log('Inicialización omitida - TENANT_ID:', window.TENANT_ID, 'URL includes sucursales:', window.location.href.includes('sucursales'));
    }

    // Reset del formulario cuando se cierre el modal de agregar
    $(document).on('hidden.hs.overlay', '#modal-agregar-sucursal', function() {
        FormAgregarSucursal.resetForm();
    });

    // Inicializar FormAgregarSucursal cuando se abra el modal
    $(document).on('shown.hs.overlay', '#modal-agregar-sucursal', function() {
        console.log('Modal agregar sucursal abierto, inicializando formulario...');
        FormAgregarSucursal.init();
    });

    // Event handlers para los modales
    // Botón Editar Sucursal en las cards
    $(document).on('click', '.btnEditarSucursal', function() {
        const sucursalId = $(this).data('id');
        console.log('Abriendo modal editar para sucursal ID:', sucursalId);

        // Cargar datos de la sucursal específica
        FormEditarSucursal.cargarSucursal(sucursalId);

        // Abrir modal usando Preline UI
        const modal = document.getElementById('modal-editar-sucursal');
        if (modal && window.HSOverlay) {
            window.HSOverlay.open(modal);
        }
    });

    // Botón Eliminar Sucursal en las cards
    $(document).on('click', '.btnEliminarSucursal', function() {
        const sucursalId = $(this).data('id');
        console.log('Solicitando eliminación para sucursal ID:', sucursalId);

        // Obtener el nombre de la sucursal del botón
        const sucursalNombre = $(this).closest('.bg-white').find('h3').text().trim();

        // Mostrar modal de confirmación personalizado
        mostrarConfirmacionEliminacionSucursal(sucursalNombre).then((confirmado) => {
            if (confirmado) {
                eliminarSucursal(sucursalId);
            }
        });
    });

    // Reset del formulario cuando se cierre el modal de editar
    $(document).on('hidden.hs.overlay', '#modal-editar-sucursal', function() {
        FormEditarSucursal.resetForm();
    });

    // Inicializar FormEditarSucursal cuando se abra el modal de editar
    $(document).on('shown.hs.overlay', '#modal-editar-sucursal', function() {
        // Mostrar loading state por defecto
        $('#editar-loading').removeClass('hidden');
        $('#form-editar-sucursal').addClass('hidden');
        FormEditarSucursal.init();
    });

    // Event handlers para paginación
    $(document).on('click', '.btn-previous', function(e) {
        e.preventDefault();
        if (window.SucursalesManager) {
            window.SucursalesManager.paginaAnterior();
        }
    });

    $(document).on('click', '.btn-next', function(e) {
        e.preventDefault();
        if (window.SucursalesManager) {
            window.SucursalesManager.paginaSiguiente();
        }
    });

    // Event handler para el botón "Ver todas"
    $(document).on('click', '#btn-ver-todas', function(e) {
        e.preventDefault();

        if (window.SucursalesManager) {
            const isShowingAll = window.SucursalesManager.incluyeEliminadas;
            const newState = !isShowingAll;

            // Cambiar el estado
            window.SucursalesManager.incluyeEliminadas = newState;

            // Actualizar texto del botón
            const btnText = $('#btn-ver-todas-text');
            btnText.text(newState ? 'Ver activas' : 'Ver todas');

            // Actualizar el filtro de estado automáticamente
            if (newState) {
                // Al activar "Ver todas", mostrar todos los estados (activas, inactivas y eliminadas)
                $('#filtro-estado').val(''); // Todos los estados
                window.SucursalesManager.filters.estado = '';
            } else {
                // Al desactivar "Ver todas", volver a mostrar solo activas
                $('#filtro-estado').val('1'); // Solo activas
                window.SucursalesManager.filters.estado = '1';
            }

            // Resetear página a 1 y recargar
            window.SucursalesManager.pagination.currentPage = 1;
            window.SucursalesManager.cargarSucursales();
        }
    });

    /**
     * Muestra modal de confirmación para eliminar sucursal
     * @param {string} nombreSucursal - Nombre de la sucursal
     * @returns {Promise<boolean>} - True si el usuario confirma
     */
    function mostrarConfirmacionEliminacionSucursal(nombreSucursal) {
        return new Promise((resolve) => {
            // Crear modal de confirmación
            const modalHtml = `
                <div id="modal-confirmar-eliminacion-sucursal" class="hs-overlay fixed top-0 start-0 z-[60] w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
                    <div class="bg-white dark:bg-neutral-800 rounded-xl shadow-lg max-w-md w-full mx-4">
                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-full">
                                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        Confirmar eliminación
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-neutral-400">
                                        Esta acción no se puede deshacer
                                    </p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <p class="text-gray-700 dark:text-neutral-300">
                                    ¿Estás seguro de que deseas eliminar la sucursal <strong>"${nombreSucursal}"</strong>?
                                </p>
                                <div class="text-sm text-gray-500 dark:text-neutral-400 mt-3 space-y-1">
                                    <p>Esta acción:</p>
                                    <ul class="list-disc list-inside space-y-1 ml-2">
                                        <li>Verificará si la sucursal tiene usuarios asociados</li>
                                        <li>Si tiene usuarios, será <strong>desactivada</strong> para auditoría</li>
                                        <li>Si no tiene usuarios, será <strong>eliminada</strong> permanentemente</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex gap-3 justify-end">
                                <button type="button" id="btn-cancelar-eliminacion-sucursal" class="py-2 px-4 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                                    Cancelar
                                </button>
                                <button type="button" id="btn-confirmar-eliminacion-sucursal" class="py-2 px-4 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Eliminar sucursal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Agregar modal al DOM
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const modal = document.getElementById('modal-confirmar-eliminacion-sucursal');
            const btnCancelar = document.getElementById('btn-cancelar-eliminacion-sucursal');
            const btnConfirmar = document.getElementById('btn-confirmar-eliminacion-sucursal');

            // Mostrar modal
            modal.style.display = 'flex';

            // Manejar eventos
            const cerrarModal = (resultado) => {
                modal.remove();
                resolve(resultado);
            };

            btnCancelar.addEventListener('click', () => cerrarModal(false));
            btnConfirmar.addEventListener('click', () => cerrarModal(true));

            // Cerrar con ESC o click fuera
            modal.addEventListener('click', (e) => {
                if (e.target === modal) cerrarModal(false);
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    cerrarModal(false);
                }
            });
        });
    }

    // Función para eliminar sucursal
    function eliminarSucursal(sucursalId) {
        console.log('Eliminando sucursal ID:', sucursalId);

        $.ajax({
            url: 'ajax/sucursales.ajax.php',
            type: 'POST',
            data: {
                accion: 'eliminar_sucursal',
                idsucursal: sucursalId
            },
            dataType: 'json',
            beforeSend: function() {
                // Mostrar indicador de carga
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Verificando dependencias de la sucursal',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                console.log('Respuesta eliminación sucursal:', response);

                if (response.status === 'success') {
                    // Mostrar mensaje diferenciado según el tipo de eliminación
                    let iconType = response.tipo_eliminacion === 'desactivacion' ? 'warning' : 'success';
                    let titleText = response.tipo_eliminacion === 'desactivacion' ? 'Sucursal Desactivada' : 'Sucursal Eliminada';

                    // Preparar mensaje adicional si hay dependencias
                    let mensajeAdicional = '';
                    if (response.dependencias && response.dependencias.tiene_dependencias) {
                        mensajeAdicional = `<br><br><small class="text-gray-600">La sucursal tenía ${response.dependencias.usuarios} usuario(s) asociado(s), por lo que se desactivó para mantener la integridad de los datos.</small>`;
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

                        // Recargar la lista de sucursales
                        if (window.SucursalesManager) {
                            window.SucursalesManager.cargarSucursales();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Error al eliminar sucursal',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX eliminar sucursal:', {xhr, status, error});
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
});