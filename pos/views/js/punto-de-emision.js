/**
 * Función para mostrar notificaciones
 */
function showNotification(message, type = 'info') {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-[9999] max-w-sm p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Mostrar notificación
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);

    // Ocultar y remover notificación
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

$(document).ready(function() {

    // Sistema de gestión de puntos de emisión
    const PuntosEmisionManager = {
        data: [],
        filteredData: [],
        sucursales: [],
        incluyeEliminados: false,
        codigoSriOriginal: null, // Guardar código original en modo edición
        sucursalOriginal: null, // Guardar sucursal original en modo edición
        currentPage: 1,
        itemsPerPage: 9, // 9 cards (3x3 en desktop)
        filters: {
            buscar: '',
            sucursal: '',
            estado: ''
        },

        init: function() {
            // Solo inicializar si existe el contenedor Y el usuario está logueado
            if ($('#contenedor-cards-puntos').length && window.TENANT_ID) {
                this.bindEvents();
                this.cargarSucursales();
                this.cargarPuntosEmision();
            }
        },

        bindEvents: function() {
            const self = this;

            // Filtro de búsqueda con debounce
            let searchTimeout;
            $(document).on('input', '#busqueda-puntos', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    self.filters.buscar = $(this).val().toLowerCase();
                    self.aplicarFiltros();
                }, 300);
            });

            // Filtro por sucursal
            $(document).on('change', '#filtro-sucursal', function() {
                self.filters.sucursal = $(this).val();
                self.aplicarFiltros();
            });

            // Filtro por estado
            $(document).on('change', '#filtro-estado', function() {
                self.filters.estado = $(this).val();
                if ($(this).val() === 'deleted') {
                    self.incluyeEliminados = true;
                } else {
                    self.incluyeEliminados = false;
                }
                self.cargarPuntosEmision();
            });

            // Toggle para mostrar/ocultar eliminados
            $(document).on('click', '#btn-ver-todos-puntos', function() {
                self.incluyeEliminados = !self.incluyeEliminados;
                const btn = $(this);
                const text = btn.find('#btn-ver-todos-text');

                if (self.incluyeEliminados) {
                    btn.removeClass('bg-white text-gray-800').addClass('bg-red-100 text-red-700');
                    text.text('Ocultar eliminados');
                } else {
                    btn.removeClass('bg-red-100 text-red-700').addClass('bg-white text-gray-800');
                    text.text('Ver todos');
                }

                // Resetear filtro de estado si está en "eliminados"
                if (!self.incluyeEliminados && $('#filtro-estado').val() === 'deleted') {
                    $('#filtro-estado').val('');
                    self.filters.estado = '';
                }

                self.cargarPuntosEmision();
            });

            // Guardar punto de emisión
            $(document).on('click', '#btn-guardar-punto-emision', function() {
                self.guardarPuntoEmision();
            });

            // Editar punto de emisión
            $(document).on('click', '.btn-editar-punto', function() {
                const id = $(this).data('id');
                self.editarPuntoEmision(id);
            });

            // Eliminar punto de emisión
            $(document).on('click', '.btn-eliminar-punto', function() {
                const id = $(this).data('id');
                self.eliminarPuntoEmision(id);
            });

            // Limpiar modal al cerrarlo
            $(document).on('hidden.bs.modal', '#modal-agregar-punto-emision', function() {
                self.limpiarModal();
            });

            // Reset form cuando se abre el modal para agregar
            $(document).on('click', '[data-hs-overlay="#modal-agregar-punto-emision"]', function() {
                if (!$(this).hasClass('btn-editar-punto')) {
                    self.limpiarModal();
                }
            });

            // Paginación - Botón anterior
            $(document).on('click', '#btn-puntos-previous', function() {
                if (self.currentPage > 1) {
                    self.currentPage--;
                    self.renderCards();
                    self.actualizarPaginacion();
                }
            });

            // Paginación - Botón siguiente
            $(document).on('click', '#btn-puntos-next', function() {
                const totalPages = Math.ceil(self.filteredData.length / self.itemsPerPage);
                if (self.currentPage < totalPages) {
                    self.currentPage++;
                    self.renderCards();
                    self.actualizarPaginacion();
                }
            });

            // Validación para código SRI - formato 001-999 con verificación en BD
            let codigoSriTimeout;
            let codigoSriValido = false;

            $(document).on('input', '#codigo_sri', function() {
                const input = $(this);
                let value = input.val();

                // Solo permitir números
                value = value.replace(/[^0-9]/g, '');

                // Limitar a 3 dígitos
                if (value.length > 3) {
                    value = value.substring(0, 3);
                }

                input.val(value);

                // Resetear validación visual
                self.resetCodigoSriValidacion();

                // Si tiene 3 dígitos, verificar en la base de datos
                if (value.length === 3) {
                    const sucursalId = $('#sucursal_id').val();

                    if (!sucursalId) {
                        self.mostrarErrorCodigoSri('Primero seleccione una sucursal');
                        window.codigoSriValido = false;
                        return;
                    }

                    // Verificar si estamos en modo edición y si el código NO ha cambiado
                    const action = $('#modal_action').val();
                    if (action === 'update' &&
                        value === self.codigoSriOriginal &&
                        sucursalId == self.sucursalOriginal) {
                        // El código no ha cambiado, es válido
                        self.mostrarSuccessCodigoSri();
                        window.codigoSriValido = true;
                        return;
                    }

                    // Mostrar estado "verificando"
                    $('#codigo-sri-validacion').removeClass('hidden');
                    $('#codigo-sri-checking').removeClass('hidden');

                    // Debounce para no hacer requests excesivos
                    clearTimeout(codigoSriTimeout);
                    codigoSriTimeout = setTimeout(() => {
                        self.verificarCodigoSri(value, sucursalId);
                    }, 500);
                } else {
                    window.codigoSriValido = false;
                }
            });

            // Forzar formato al perder foco
            $(document).on('blur', '#codigo_sri', function() {
                const input = $(this);
                let value = input.val();

                if (value.length > 0 && value.length < 3) {
                    // Completar con ceros a la izquierda
                    value = value.padStart(3, '0');
                    input.val(value);

                    // Verificar después de formatear
                    const sucursalId = $('#sucursal_id').val();
                    if (sucursalId) {
                        self.verificarCodigoSri(value, sucursalId);
                    }
                }
            });

            // Revalidar cuando cambie la sucursal
            $(document).on('change', '#sucursal_id', function() {
                const codigoSri = $('#codigo_sri').val();
                const sucursalId = $(this).val();

                if (codigoSri.length === 3) {
                    // Verificar si estamos en modo edición y si NADA ha cambiado
                    const action = $('#modal_action').val();
                    if (action === 'update' &&
                        codigoSri === self.codigoSriOriginal &&
                        sucursalId == self.sucursalOriginal) {
                        // Nada ha cambiado, es válido
                        self.mostrarSuccessCodigoSri();
                        window.codigoSriValido = true;
                        return;
                    }

                    self.resetCodigoSriValidacion();
                    $('#codigo-sri-validacion').removeClass('hidden');
                    $('#codigo-sri-checking').removeClass('hidden');
                    setTimeout(() => {
                        self.verificarCodigoSri(codigoSri, sucursalId);
                    }, 300);
                }
            });

            // Prevenir entrada de caracteres no numéricos en código SRI
            $(document).on('keydown', '#codigo_sri', function(e) {
                // Permitir: backspace, delete, tab, escape, enter
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
                    // Permitir: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true) ||
                    // Permitir: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                // Asegurar que es un número
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

            // Auto-seleccionar contenido cuando se hace focus (select on focus)
            // Aplica a todos los campos de texto del modal para facilitar la edición
            $(document).on('focus', '#codigo_sri, #descripcion, #secuencial_factura, #secuencial_nota_credito, #secuencial_nota_debito, #secuencial_guia_remision, #secuencial_retencion', function() {
                // Seleccionar todo el texto para facilitar el reemplazo
                $(this).select();
            });

            // Validación para inputs de secuenciales - solo números enteros
            $(document).on('input', '#secuencial_factura, #secuencial_nota_credito, #secuencial_nota_debito, #secuencial_guia_remision, #secuencial_retencion', function() {
                // Eliminar cualquier caracter que no sea número
                let value = $(this).val().replace(/[^0-9]/g, '');

                // Si está vacío, establecer como 1
                if (value === '' || value === '0') {
                    value = '1';
                }

                // Actualizar el valor del input
                $(this).val(value);
            });

            // Prevenir entrada de caracteres no numéricos
            $(document).on('keydown', '#secuencial_factura, #secuencial_nota_credito, #secuencial_nota_debito, #secuencial_guia_remision, #secuencial_retencion', function(e) {
                // Permitir: backspace, delete, tab, escape, enter
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
                    // Permitir: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true) ||
                    // Permitir: home, end, left, right
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
                }
                // Asegurar que es un número y no una tecla especial
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        },

        cargarSucursales: async function() {
            try {
                const response = await $.ajax({
                    url: 'ajax/punto-de-emision.ajax.php',
                    type: 'POST',
                    data: {
                        accion: 'obtener_sucursales',
                        csrf_token: $('input[name="csrf_token"]').val() || ''
                    }
                });

                if (response.status === 'success') {
                    this.sucursales = response.data;
                    this.actualizarSelectSucursales();
                }
            } catch (error) {
                console.error('Error cargando sucursales:', error);
            }
        },

        actualizarSelectSucursales: function() {
            const selectSucursal = $('#sucursal_id');
            const filtroSucursal = $('#filtro-sucursal');

            // Limpiar opciones existentes
            selectSucursal.find('option:not(:first)').remove();
            filtroSucursal.find('option:not(:first)').remove();

            this.sucursales.forEach(sucursal => {
                if (sucursal.estado == 1) {
                    const option = `<option value="${sucursal.idsucursal}">${sucursal.sri_nombre} (${sucursal.sri_codigo})</option>`;
                    selectSucursal.append(option);
                    filtroSucursal.append(option);
                }
            });
        },

        cargarPuntosEmision: async function() {
            try {
                const response = await $.ajax({
                    url: 'ajax/punto-de-emision.ajax.php',
                    type: 'POST',
                    data: {
                        accion: 'obtener_puntos_emision',
                        incluir_eliminados: this.incluyeEliminados,
                        csrf_token: $('input[name="csrf_token"]').val() || ''
                    }
                });

                if (response.status === 'success') {
                    this.data = response.data;
                    this.aplicarFiltros();
                } else {
                    console.error('Error al cargar puntos de emisión:', response.message);
                }
            } catch (error) {
                console.error('Error en la petición:', error);
                showNotification('❌ Error al cargar los puntos de emisión', 'error');
            }
        },

        aplicarFiltros: function() {
            this.filteredData = this.data.filter(punto => {
                const cumpleBusqueda = this.filters.buscar === '' ||
                    punto.codigo_sri.toLowerCase().includes(this.filters.buscar) ||
                    punto.descripcion.toLowerCase().includes(this.filters.buscar) ||
                    punto.sucursal_nombre.toLowerCase().includes(this.filters.buscar);

                const cumpleSucursal = this.filters.sucursal === '' ||
                    punto.sucursal_idsucursal == this.filters.sucursal;

                const cumpleEstado = this.filters.estado === '' ||
                    (this.filters.estado === 'deleted' && punto.deleted_at !== null) ||
                    (this.filters.estado !== 'deleted' && punto.estado == this.filters.estado && punto.deleted_at === null);

                return cumpleBusqueda && cumpleSucursal && cumpleEstado;
            });

            // Resetear a la página 1 cuando cambian los filtros
            this.currentPage = 1;

            this.actualizarPaginacion();
            this.renderCards();
        },

        actualizarPaginacion: function() {
            const total = this.filteredData.length;
            const totalPages = Math.ceil(total / this.itemsPerPage);
            const inicio = total === 0 ? 0 : ((this.currentPage - 1) * this.itemsPerPage) + 1;
            const fin = Math.min(this.currentPage * this.itemsPerPage, total);

            // Actualizar contador
            $('#puntos-inicio').text(inicio);
            $('#puntos-fin').text(fin);
            $('#puntos-total').text(total);

            // Actualizar estado de botones
            $('#btn-puntos-previous').prop('disabled', this.currentPage <= 1);
            $('#btn-puntos-next').prop('disabled', this.currentPage >= totalPages || total === 0);
        },

        renderCards: function() {
            const container = $('#contenedor-cards-puntos');
            const emptyState = $('#puntos-empty');

            container.empty();

            if (this.filteredData.length === 0) {
                container.addClass('hidden');
                emptyState.removeClass('hidden');
                return;
            }

            container.removeClass('hidden');
            emptyState.addClass('hidden');

            // Calcular el rango de items para la página actual
            const inicio = (this.currentPage - 1) * this.itemsPerPage;
            const fin = inicio + this.itemsPerPage;
            const paginatedData = this.filteredData.slice(inicio, fin);

            // Renderizar solo los items de la página actual
            paginatedData.forEach(punto => {
                const card = this.createCard(punto);
                container.append(card);
            });
        },

        createCard: function(punto) {
            const isDeleted = punto.deleted_at !== null;
            const isActive = punto.estado == 1;

            // Estilos condicionales para cards eliminadas
            const cardClasses = isDeleted
                ? 'bg-white rounded-2xl shadow-lg border-2 border-red-300 dark:border-red-700 overflow-hidden opacity-75 grayscale relative'
                : 'bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden group hover:-translate-y-2';

            const headerClass = isDeleted ?
                'bg-gradient-to-br from-red-50 via-red-100 to-red-50 dark:from-red-900/20 dark:via-red-800/20 dark:to-red-900/20' :
                'bg-gradient-to-br from-purple-50 via-indigo-50 to-blue-50 dark:from-purple-900/20 dark:via-indigo-900/20 dark:to-blue-900/20';

            const statusBadge = isDeleted ?
                '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-600 text-white shadow-lg animate-pulse dark:bg-red-700">🗑️ ELIMINADO</span>' :
                (isActive ?
                    '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">✅ Activo</span>' :
                    '<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">⚠️ Inactivo</span>');

            const iconGradient = isDeleted ? 'from-gray-300 to-gray-500' :
                (isActive ? 'from-blue-400 to-blue-600' : 'from-gray-400 to-gray-600');

            // Marca de agua diagonal para cards eliminadas
            const watermark = isDeleted ? `
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10 overflow-hidden">
                    <div class="transform rotate-[-45deg] opacity-10 dark:opacity-20">
                        <span class="text-6xl md:text-7xl font-black text-red-600 dark:text-red-500 whitespace-nowrap">
                            ELIMINADO
                        </span>
                    </div>
                </div>
            ` : '';

            // Información de eliminación
            const deletedInfo = isDeleted ? `
                <div class="px-6 py-3 bg-red-50 dark:bg-red-900/20 border-t border-red-200 dark:border-red-800">
                    <div class="flex items-center gap-2 text-sm text-red-700 dark:text-red-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">Eliminado el: ${new Date(punto.deleted_at).toLocaleString('es-EC', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</span>
                    </div>
                    <p class="text-xs text-red-600 dark:text-red-500 mt-1 ml-6">
                        Este punto de emisión ha sido eliminado y ya no está disponible para uso.
                    </p>
                </div>
            ` : '';

            const actions = isDeleted ? '' : `
                <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700">
                    <div class="flex gap-2">
                        <button class="btn-editar-punto flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 border border-amber-200 hover:border-amber-300 transition-all duration-200 dark:text-amber-400 dark:bg-amber-900/20 dark:border-amber-800" data-id="${punto.idpunto_de_emision}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </button>
                        <button class="btn-eliminar-punto flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 transition-all duration-200 dark:text-red-400 dark:bg-red-900/20 dark:border-red-800" data-id="${punto.idpunto_de_emision}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </button>
                    </div>
                </div>
            `;

            return `
                <div class="${cardClasses}">
                    ${watermark}
                    <!-- Header con imagen/icono -->
                    <div class="relative ${headerClass} p-6">
                        <div class="flex items-center justify-between mb-4">
                            ${statusBadge}
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                Punto de Emisión
                            </span>
                        </div>

                        <div class="text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br ${iconGradient} flex items-center justify-center shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">${punto.codigo_sri}</h3>
                            <p class="text-sm text-gray-600 dark:text-neutral-400 mb-3">${punto.descripcion}</p>
                        </div>
                    </div>

                    <!-- Información principal -->
                    <div class="p-6 space-y-4">
                        <!-- Sucursal -->
                        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400">Sucursal</p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">${punto.sucursal_nombre}</p>
                                    <p class="text-xs text-gray-500 dark:text-neutral-400">Código SRI: ${punto.sucursal_codigo}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Secuenciales de documentos -->
                        <div>
                            <h4 class="text-xs font-semibold text-gray-700 dark:text-neutral-300 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Secuenciales de Documentos Electrónicos
                            </h4>
                            <div class="grid grid-cols-12 gap-2">
                                <!-- Factura -->
                                <div class="col-span-12 sm:col-span-6 lg:col-span-12 text-center bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 border border-blue-200 dark:border-blue-800">
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">📄 Factura</p>
                                    <p class="text-base font-bold text-blue-600 dark:text-blue-400 font-mono">${String(punto.secuencial_factura).padStart(9, '0')}</p>
                                </div>
                                <!-- Nota de Crédito -->
                                <div class="col-span-12 sm:col-span-6 lg:col-span-12 text-center bg-green-50 dark:bg-green-900/20 rounded-lg p-3 border border-green-200 dark:border-green-800">
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">📝 Nota de Crédito</p>
                                    <p class="text-base font-bold text-green-600 dark:text-green-400 font-mono">${String(punto.secuencial_nota_credito).padStart(9, '0')}</p>
                                </div>
                                <!-- Nota de Débito -->
                                <div class="col-span-12 sm:col-span-6 lg:col-span-12 text-center bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 border border-orange-200 dark:border-orange-800">
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">📋 Nota de Débito</p>
                                    <p class="text-base font-bold text-orange-600 dark:text-orange-400 font-mono">${String(punto.secuencial_nota_debito).padStart(9, '0')}</p>
                                </div>
                                <!-- Guía de Remisión -->
                                <div class="col-span-12 sm:col-span-6 lg:col-span-12 text-center bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 border border-amber-200 dark:border-amber-800">
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">📦 Guía de Remisión</p>
                                    <p class="text-base font-bold text-amber-600 dark:text-amber-400 font-mono">${String(punto.secuencial_guia_remision).padStart(9, '0')}</p>
                                </div>
                                <!-- Retención -->
                                <div class="col-span-12 sm:col-span-6 lg:col-span-12 text-center bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 border border-purple-200 dark:border-purple-800">
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">🧾 Retención</p>
                                    <p class="text-base font-bold text-purple-600 dark:text-purple-400 font-mono">${String(punto.secuencial_retencion).padStart(9, '0')}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="flex items-center justify-between text-sm py-2 border-t border-gray-200 dark:border-neutral-700">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-gray-600 dark:text-neutral-400">Creado: ${new Date(punto.created_at).toLocaleDateString()}</span>
                            </div>
                            ${punto.updated_at ? `
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="text-sm text-gray-500 dark:text-neutral-400">${new Date(punto.updated_at).toLocaleDateString()}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Información de eliminación -->
                    ${deletedInfo}

                    <!-- Footer con acciones -->
                    ${actions}
                </div>
            `;
        },

        editarPuntoEmision: function(id) {
            const punto = this.data.find(p => p.idpunto_de_emision == id);
            if (!punto) {
                console.error('Punto de emisión no encontrado:', id);
                return;
            }

            console.log('Editando punto de emisión:', punto);

            // Guardar valores originales para comparación
            this.codigoSriOriginal = punto.codigo_sri;
            this.sucursalOriginal = punto.sucursal_idsucursal;

            // Cargar datos en el modal
            $('#punto_emision_id').val(punto.idpunto_de_emision);
            $('#modal_action').val('update');
            $('#sucursal_id').val(punto.sucursal_idsucursal);
            $('#codigo_sri').val(punto.codigo_sri);
            $('#descripcion').val(punto.descripcion);
            $('#secuencial_factura').val(punto.secuencial_factura);
            $('#secuencial_nota_credito').val(punto.secuencial_nota_credito);
            $('#secuencial_nota_debito').val(punto.secuencial_nota_debito);
            $('#secuencial_guia_remision').val(punto.secuencial_guia_remision);
            $('#secuencial_retencion').val(punto.secuencial_retencion);
            $('#estado').val(punto.estado);

            // Cambiar título y subtítulo del modal
            $('#modal-titulo').text('Editar Punto de Emisión');
            $('#modal-titulo').next('p').text('Modificar información del punto de emisión');
            $('#btn-guardar-texto').text('Actualizar Punto de Emisión');

            // Resetear validación del código SRI
            this.resetCodigoSriValidacion();
            $('#codigo_sri').removeClass('border-red-500 border-green-500').addClass('border-gray-300');

            // Marcar el código como válido inicialmente (es el código actual)
            window.codigoSriValido = true;

            // Abrir modal usando HSOverlay
            const modalEl = document.getElementById('modal-agregar-punto-emision');
            if (modalEl && window.HSOverlay) {
                window.HSOverlay.open(modalEl);
            }
        },

        limpiarModal: function() {
            $('#form-punto-emision')[0].reset();
            $('#punto_emision_id').val('');
            $('#modal_action').val('create');
            $('#modal-titulo').text('Agregar Punto de Emisión');
            $('#modal-titulo').next('p').text('Crear un nuevo punto de emisión para facturación electrónica');
            $('#btn-guardar-texto').text('Crear Punto de Emisión');

            // Limpiar valores originales
            this.codigoSriOriginal = null;
            this.sucursalOriginal = null;

            // Resetear validación del código SRI
            this.resetCodigoSriValidacion();
            $('#codigo_sri').removeClass('border-red-500 border-green-500').addClass('border-gray-300');
            window.codigoSriValido = false;
        },

        resetCodigoSriValidacion: function() {
            $('#codigo-sri-checking, #codigo-sri-error, #codigo-sri-success').addClass('hidden');
            $('#codigo-sri-validacion').addClass('hidden');
        },

        mostrarErrorCodigoSri: function(mensaje) {
            $('#codigo-sri-checking, #codigo-sri-success').addClass('hidden');
            $('#codigo-sri-error-text').text(mensaje);
            $('#codigo-sri-error').removeClass('hidden');
            $('#codigo-sri-validacion').removeClass('hidden');

            // Cambiar borde del input a rojo
            $('#codigo_sri').removeClass('border-gray-300 border-green-500').addClass('border-red-500');
        },

        mostrarSuccessCodigoSri: function() {
            $('#codigo-sri-checking, #codigo-sri-error').addClass('hidden');
            $('#codigo-sri-success').removeClass('hidden');
            $('#codigo-sri-validacion').removeClass('hidden');

            // Cambiar borde del input a verde
            $('#codigo_sri').removeClass('border-gray-300 border-red-500').addClass('border-green-500');
        },

        verificarCodigoSri: async function(codigoSri, sucursalId) {
            try {
                const puntoEmisionId = $('#punto_emision_id').val();

                const response = await $.ajax({
                    url: 'ajax/punto-de-emision.ajax.php',
                    type: 'POST',
                    data: {
                        accion: 'verificar_codigo_sri',
                        codigo_sri: codigoSri,
                        sucursal_id: sucursalId,
                        excluir_id: puntoEmisionId || null,
                        csrf_token: $('input[name="csrf_token"]').val() || ''
                    }
                });

                $('#codigo-sri-checking').addClass('hidden');

                if (response.status === 'success') {
                    if (response.disponible) {
                        this.mostrarSuccessCodigoSri();
                        window.codigoSriValido = true;
                    } else {
                        this.mostrarErrorCodigoSri('Este código ya existe en la sucursal seleccionada');
                        window.codigoSriValido = false;
                    }
                } else {
                    this.mostrarErrorCodigoSri('Error al verificar el código');
                    window.codigoSriValido = false;
                }
            } catch (error) {
                console.error('Error verificando código SRI:', error);
                $('#codigo-sri-checking').addClass('hidden');
                this.mostrarErrorCodigoSri('Error de conexión al verificar el código');
                window.codigoSriValido = false;
            }
        },

        guardarPuntoEmision: function() {
            console.log('PuntosEmisionManager: Iniciando proceso de guardado...');
            const action = $('#modal_action').val();

            // Validaciones básicas
            if (!this.validateForm()) {
                console.log('PuntosEmisionManager: Validación de formulario fallida');
                return;
            }

            console.log('PuntosEmisionManager: Validación exitosa, preparando datos...');

            // Preparar datos del formulario
            const formData = {
                accion: action === 'create' ? 'crear_punto_emision' : 'editar_punto_emision',
                sucursal_idsucursal: $('#sucursal_id').val().trim(),
                codigo_sri: $('#codigo_sri').val().trim(),
                descripcion: $('#descripcion').val().trim(),
                secuencial_factura: $('#secuencial_factura').val() || '1',
                secuencial_nota_credito: $('#secuencial_nota_credito').val() || '1',
                secuencial_nota_debito: $('#secuencial_nota_debito').val() || '1',
                secuencial_guia_remision: $('#secuencial_guia_remision').val() || '1',
                secuencial_retencion: $('#secuencial_retencion').val() || '1',
                estado: $('#estado').val()
            };

            // Si es edición, agregar el ID
            if (action === 'update') {
                formData.idpunto_de_emision = $('#punto_emision_id').val();
            }

            // Agregar CSRF token si está disponible
            const csrfToken = $('input[name="csrf_token"]').val();
            if (csrfToken) {
                formData.csrf_token = csrfToken;
            }

            console.log('PuntosEmisionManager: Datos finales a enviar:', formData);

            this.submitForm(formData, action);
        },

        validateForm: function() {
            console.log('PuntosEmisionManager: Validando formulario...');

            // Validar sucursal
            if (!$('#sucursal_id').val()) {
                this.showError('Debe seleccionar una sucursal');
                return false;
            }

            // Validar código SRI
            const codigoSri = $('#codigo_sri').val().trim();
            if (!codigoSri) {
                this.showError('El código SRI es requerido');
                return false;
            }

            // Validar formato del código SRI
            if (codigoSri.length !== 3 || !/^[0-9]{3}$/.test(codigoSri)) {
                this.showError('El código SRI debe tener exactamente 3 dígitos (Ej: 001)');
                return false;
            }

            // Validar que el código esté verificado
            const action = $('#modal_action').val();
            const sucursalId = $('#sucursal_id').val();

            // En modo creación: siempre debe estar verificado
            if (action === 'create' && window.codigoSriValido !== true) {
                this.showError('Por favor espere a que se verifique el código SRI o corrija los errores');
                return false;
            }

            // En modo edición: solo verificar si cambió el código o la sucursal
            if (action === 'update') {
                const codigoCambio = codigoSri !== this.codigoSriOriginal;
                const sucursalCambio = sucursalId != this.sucursalOriginal;

                if ((codigoCambio || sucursalCambio) && window.codigoSriValido !== true) {
                    this.showError('Por favor espere a que se verifique el código SRI o corrija los errores');
                    return false;
                }
            }

            // Validar descripción
            if (!$('#descripcion').val().trim()) {
                this.showError('La descripción es requerida');
                return false;
            }

            console.log('PuntosEmisionManager: Validación exitosa');
            return true;
        },

        submitForm: function(formData, action) {
            console.log('PuntosEmisionManager: Enviando datos al servidor...');

            // Debug: mostrar todos los datos que se van a enviar
            console.log('PuntosEmisionManager: Datos del formulario:');
            Object.entries(formData).forEach(([key, value]) => {
                console.log('  -', key, ':', value);
            });

            // Deshabilitar botón durante el envío
            const submitButton = $('#btn-guardar-punto-emision');
            const originalHtml = submitButton.html();
            const mensajeBoton = action === 'create' ? 'Creando...' : 'Actualizando...';

            submitButton.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>${mensajeBoton}</span>
            `);

            console.log('PuntosEmisionManager: Configurando AJAX request...');

            const self = this;

            $.ajax({
                url: 'ajax/punto-de-emision.ajax.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('PuntosEmisionManager: Respuesta recibida:', response);
                    console.log('PuntosEmisionManager: Tipo de respuesta:', typeof response);
                    console.log('PuntosEmisionManager: response.status:', response.status);

                    if (response.status === 'success') {
                        console.log('PuntosEmisionManager: Guardado exitoso');
                        console.log('PuntosEmisionManager: Intentando mostrar toast...');

                        // Cerrar modal usando HSOverlay
                        const modalEl = document.getElementById('modal-agregar-punto-emision');
                        if (modalEl && window.HSOverlay) {
                            window.HSOverlay.close(modalEl);
                        }

                        // Mostrar mensaje de éxito
                        const mensaje = action === 'create' ?
                            '✅ Punto de emisión creado exitosamente' :
                            '✅ Punto de emisión actualizado exitosamente';

                        showNotification(mensaje, 'success');

                        // Recargar lista de puntos de emisión
                        self.cargarPuntosEmision();

                        // Limpiar formulario
                        self.limpiarModal();
                    } else {
                        console.error('PuntosEmisionManager: Error del servidor:', response.message);
                        self.showError(response.message || 'Error al guardar el punto de emisión');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('PuntosEmisionManager: Error AJAX:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    self.showError('Error de conexión al guardar el punto de emisión. Por favor, intente nuevamente.');
                },
                complete: function() {
                    // Restaurar botón
                    submitButton.prop('disabled', false).html(originalHtml);
                    console.log('PuntosEmisionManager: Request completado');
                }
            });
        },

        eliminarPuntoEmision: async function(id) {
            const punto = this.data.find(p => p.idpunto_de_emision == id);
            if (!punto) {
                console.error('Punto de emisión no encontrado:', id);
                return;
            }

            // Mostrar modal de confirmación
            const confirmacion = await this.mostrarConfirmacionEliminacion(punto.descripcion, punto.codigo_sri);
            if (!confirmacion) {
                return;
            }

            try {
                const response = await $.ajax({
                    url: 'ajax/punto-de-emision.ajax.php',
                    type: 'POST',
                    data: {
                        accion: 'eliminar_punto_emision',
                        idpunto_de_emision: id,
                        csrf_token: $('input[name="csrf_token"]').val() || ''
                    },
                    dataType: 'json'
                });

                if (response.status === 'success') {
                    showNotification('✅ Punto de emisión eliminado exitosamente', 'success');
                    this.cargarPuntosEmision();
                } else {
                    this.showError(response.message || 'Error al eliminar el punto de emisión');
                }
            } catch (error) {
                console.error('Error al eliminar punto de emisión:', error);
                this.showError('Error de conexión al eliminar el punto de emisión');
            }
        },

        mostrarConfirmacionEliminacion: function(descripcion, codigoSri) {
            return new Promise((resolve) => {
                // Crear modal de confirmación
                const modalHtml = `
                    <div id="modal-confirmar-eliminacion-punto" class="hs-overlay fixed top-0 start-0 z-[80] w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
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
                                        ¿Estás seguro de que deseas eliminar el punto de emisión <strong>"${descripcion}"</strong> (código: ${codigoSri})?
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2">
                                        El punto de emisión será marcado como eliminado y no aparecerá en las listas, pero se conservará para fines de auditoría.
                                    </p>
                                </div>

                                <div class="flex gap-3 justify-end">
                                    <button type="button" id="btn-cancelar-eliminacion-punto" class="py-2 px-4 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                                        Cancelar
                                    </button>
                                    <button type="button" id="btn-confirmar-eliminacion-punto" class="py-2 px-4 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                        Eliminar punto de emisión
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Agregar modal al DOM
                document.body.insertAdjacentHTML('beforeend', modalHtml);

                const modal = document.getElementById('modal-confirmar-eliminacion-punto');
                const btnCancelar = document.getElementById('btn-cancelar-eliminacion-punto');
                const btnConfirmar = document.getElementById('btn-confirmar-eliminacion-punto');

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

                document.addEventListener('keydown', function handleEscape(e) {
                    if (e.key === 'Escape') {
                        cerrarModal(false);
                        document.removeEventListener('keydown', handleEscape);
                    }
                });
            });
        },

        showError: function(message) {
            showNotification('❌ ' + message, 'error');
        }
    };

    // Inicializar el sistema
    PuntosEmisionManager.init();
});