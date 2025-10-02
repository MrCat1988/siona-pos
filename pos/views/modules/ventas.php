<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar sesión
if (!isset($_SESSION['tenant_id'])) {
    header('Location: login');
    exit;
}

// Generar CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- Content -->
<div class="w-full lg:ps-64">
    <div class="p-2 sm:p-4 md:p-6 space-y-3 sm:space-y-4">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-neutral-200">Nueva Venta</h1>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-neutral-400">Crear y gestionar facturas de venta</p>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="button" id="btn-ver-facturas" class="flex-1 sm:flex-none py-2 px-3 sm:px-4 inline-flex justify-center items-center gap-x-2 text-xs sm:text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="hidden sm:inline">Ver Facturas</span>
                    <span class="sm:hidden">Facturas</span>
                </button>
            </div>
        </div>

        <!-- Main Sales Interface -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">

            <!-- Left Panel - Product Search & Cart -->
            <div class="lg:col-span-2 space-y-3 sm:space-y-4">

                <!-- Search Products Card -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="p-3 sm:p-4 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-neutral-200">Buscar Productos</h3>
                    </div>
                    <div class="p-3 sm:p-4 space-y-3 sm:space-y-4">
                        <!-- Search Input -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="buscar-producto" class="block w-full pl-9 sm:pl-10 pr-3 py-2.5 sm:py-3 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-900 dark:border-neutral-600 dark:text-white" placeholder="Buscar producto...">
                        </div>

                        <!-- Product Results -->
                        <div id="productos-resultado" class="hidden max-h-60 sm:max-h-64 overflow-y-auto border border-gray-200 rounded-lg dark:border-neutral-700">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>
                </div>

                <!-- Shopping Cart -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="p-3 sm:p-4 border-b border-gray-200 dark:border-neutral-700">
                        <div class="flex justify-between items-center">
                            <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-neutral-200">Carrito de Venta</h3>
                            <button type="button" id="btn-limpiar-carrito" class="text-xs sm:text-sm text-red-600 hover:text-red-800 font-medium dark:text-red-400 dark:hover:text-red-300">
                                Limpiar
                            </button>
                        </div>
                    </div>
                    <div class="p-2 sm:p-4">
                        <!-- Cart Table - Desktop -->
                        <div class="hidden sm:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead class="bg-gray-50 dark:bg-neutral-900">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Producto</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Cant.</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">P. Unit.</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Desc.</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Subtotal</th>
                                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-400">Acc.</th>
                                    </tr>
                                </thead>
                                <tbody id="carrito-body" class="divide-y divide-gray-200 dark:divide-neutral-700">
                                    <tr id="carrito-vacio">
                                        <td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-neutral-400">
                                            <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <p class="mt-2 text-sm">El carrito está vacío</p>
                                            <p class="text-xs text-gray-400 dark:text-neutral-500">Busca y agrega productos</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Cart Cards - Mobile -->
                        <div class="sm:hidden" id="carrito-mobile">
                            <div id="carrito-vacio-mobile" class="py-8 text-center text-gray-500 dark:text-neutral-400">
                                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="mt-2 text-sm">El carrito está vacío</p>
                                <p class="text-xs text-gray-400">Busca y agrega productos</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Panel - Customer Info & Totals -->
            <div class="space-y-3 sm:space-y-4">

                <!-- Customer Selection -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="p-3 sm:p-4 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-neutral-200">Cliente</h3>
                    </div>
                    <div class="p-3 sm:p-4 space-y-3">
                        <div class="relative">
                            <label class="block text-xs sm:text-sm font-medium mb-1 dark:text-white">Buscar Cliente</label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="text" id="buscar-cliente" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-900 dark:border-neutral-600 dark:text-white" placeholder="Nombre o identificación...">
                                    <input type="hidden" id="cliente-seleccionado-id" value="">
                                    <!-- Cliente Results Dropdown -->
                                    <div id="clientes-resultado" class="hidden absolute z-10 mt-1 w-full max-h-48 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-neutral-800 dark:border-neutral-700">
                                        <!-- Se llenará dinámicamente -->
                                    </div>
                                </div>
                                <button type="button" id="btn-nuevo-cliente-venta" class="flex-shrink-0 px-3 py-2 inline-flex items-center gap-x-1 text-xs font-medium rounded-lg border border-blue-600 bg-blue-600 text-white hover:bg-blue-700 dark:border-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600" title="Agregar nuevo cliente">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Selected Customer Info -->
                        <div id="cliente-info" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
                            <div class="flex justify-between items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm sm:text-base text-gray-800 dark:text-white truncate" id="cliente-nombre">-</p>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-neutral-400 truncate" id="cliente-identificacion">-</p>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-neutral-400 truncate" id="cliente-email">-</p>
                                </div>
                                <button type="button" id="btn-cambiar-cliente" class="flex-shrink-0 text-xs text-blue-600 hover:text-blue-800 font-medium dark:text-blue-400 dark:hover:text-blue-300">
                                    Cambiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700">
                    <div class="p-3 sm:p-4 border-b border-gray-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-neutral-200">Método de Pago</h3>
                    </div>
                    <div class="p-3 sm:p-4">
                        <select id="metodo-pago" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-900 dark:border-neutral-600 dark:text-white">
                            <option value="01">Efectivo</option>
                            <option value="15">Compensación de deudas</option>
                            <option value="16">Tarjeta de débito</option>
                            <option value="17">Dinero electrónico</option>
                            <option value="18">Tarjeta prepago</option>
                            <option value="19">Tarjeta de crédito</option>
                            <option value="20">Otros con utilización del sistema financiero</option>
                            <option value="21">Endoso de títulos</option>
                        </select>
                    </div>
                </div>

                <!-- Totals -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl shadow-sm dark:from-neutral-800 dark:to-neutral-800 dark:border-neutral-700">
                    <div class="p-3 sm:p-4 border-b border-blue-200 dark:border-neutral-700">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-neutral-200">Resumen</h3>
                    </div>
                    <div class="p-3 sm:p-4 space-y-2">
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600 dark:text-neutral-400">Subtotal 0%:</span>
                            <span class="font-medium text-gray-800 dark:text-white" id="subtotal-0">$0.00</span>
                        </div>
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600 dark:text-neutral-400">Subtotal 15%:</span>
                            <span class="font-medium text-gray-800 dark:text-white" id="subtotal-15">$0.00</span>
                        </div>
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600 dark:text-neutral-400">IVA 15%:</span>
                            <span class="font-medium text-gray-800 dark:text-white" id="iva-15">$0.00</span>
                        </div>
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600 dark:text-neutral-400">Descuento:</span>
                            <span class="font-medium text-red-600 dark:text-red-400" id="descuento-total">$0.00</span>
                        </div>
                        <div class="pt-2 border-t border-blue-200 dark:border-neutral-700">
                            <div class="flex justify-between items-center">
                                <span class="text-base sm:text-lg font-bold text-gray-800 dark:text-white">TOTAL:</span>
                                <span class="text-xl sm:text-2xl font-bold text-blue-600 dark:text-blue-400" id="total-general">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <button type="button" id="btn-procesar-venta" disabled class="w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-blue-500 dark:hover:bg-blue-600">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Procesar Venta
                    </button>
                    <button type="button" id="btn-guardar-borrador" disabled class="w-full py-2.5 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Guardar Borrador
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>
<!-- End Content -->

<!-- Modal Agregar Cliente desde Ventas -->
<div id="modal-nuevo-cliente-venta" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white shadow-2xl rounded-2xl pointer-events-auto dark:bg-neutral-800 dark:shadow-neutral-700/70 overflow-hidden">
            <!-- Header -->
            <div class="flex justify-between items-center py-4 px-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-neutral-800 dark:to-neutral-800">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-xl dark:bg-blue-900/20">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Nuevo Cliente</h3>
                        <p class="text-xs text-gray-600 dark:text-neutral-400">Agregar cliente rápidamente</p>
                    </div>
                </div>
                <button type="button" class="flex justify-center items-center w-8 h-8 text-sm font-semibold rounded-lg border border-transparent text-gray-500 hover:bg-white hover:text-gray-800 transition-all duration-200 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" data-hs-overlay="#modal-nuevo-cliente-venta">
                    <span class="sr-only">Close</span>
                    <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 6-12 12"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4 sm:p-6 overflow-y-auto max-h-[70vh]">
                <form id="form-nuevo-cliente-venta" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tipo de identificación -->
                        <div>
                            <label for="venta_tipo_identificacion_sri" class="block text-sm font-medium mb-1 dark:text-white">
                                Tipo de Identificación <span class="text-red-500">*</span>
                            </label>
                            <select id="venta_tipo_identificacion_sri" name="tipo_identificacion_sri" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <option value="04">RUC</option>
                                <option value="05" selected>Cédula</option>
                                <option value="06">Pasaporte</option>
                                <option value="08">Identificación del Exterior</option>
                            </select>
                        </div>

                        <!-- Número de identificación -->
                        <div>
                            <label for="venta_numero_identificacion" class="block text-sm font-medium mb-1 dark:text-white">
                                Número de Identificación <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="venta_numero_identificacion" name="numero_identificacion" required maxlength="13" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="Ej: 1234567890">
                            <div id="venta_error_identificacion" class="hidden mt-1 text-xs text-red-600"></div>
                            <div id="venta_error_duplicado" class="hidden mt-1 text-xs text-red-600"></div>
                        </div>

                        <!-- Nombres -->
                        <div>
                            <label for="venta_nombres" class="block text-sm font-medium mb-1 dark:text-white">
                                Nombres <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="venta_nombres" name="nombres" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="Ej: Juan Carlos">
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <label for="venta_apellidos" class="block text-sm font-medium mb-1 dark:text-white">
                                Apellidos <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="venta_apellidos" name="apellidos" required class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="Ej: Pérez García">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="venta_email" class="block text-sm font-medium mb-1 dark:text-white">
                                Email
                            </label>
                            <input type="email" id="venta_email" name="email" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="ejemplo@correo.com">
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label for="venta_telefono" class="block text-sm font-medium mb-1 dark:text-white">
                                Teléfono
                            </label>
                            <input type="text" id="venta_telefono" name="telefono" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="Ej: 0987654321">
                        </div>

                        <!-- Dirección -->
                        <div class="sm:col-span-2">
                            <label for="venta_direccion" class="block text-sm font-medium mb-1 dark:text-white">
                                Dirección <span class="text-xs text-gray-500">(Por defecto: Quito)</span>
                            </label>
                            <textarea id="venta_direccion" name="direccion" rows="2" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400" placeholder="Ej: Av. Principal 123 y Calle Secundaria"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 sm:px-6 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                <button type="button" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 dark:bg-neutral-900 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800" data-hs-overlay="#modal-nuevo-cliente-venta">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                <button type="button" id="btn-guardar-cliente-venta" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
