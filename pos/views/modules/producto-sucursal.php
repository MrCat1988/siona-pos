<!-- Content -->
<div class="w-full lg:ps-64">
    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- Card -->
        <div class="flex flex-col">
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                <div class="min-w-full inline-block align-middle">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
                        <!-- Header -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                                    Inventario por Sucursal
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    Gestiona precios y stock específicos de productos por sucursal.
                                </p>
                            </div>

                            <div>
                                <div class="inline-flex gap-x-2">
                                    <button id="btn-ver-todos" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <span id="btn-ver-todos-text">Ver todos</span>
                                    </button>

                                    <button type="button" id="btn-agregar-producto-sucursal" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                        Asignar producto
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Controles de filtros y búsqueda -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Búsqueda -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="search" id="filtro-busqueda" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-gray-400 dark:text-white" placeholder="Buscar por código o descripción...">
                                </div>

                                <!-- Filtro por sucursal -->
                                <div>
                                    <select id="filtro-sucursal" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="">🏪 Todas las sucursales</option>
                                        <!-- Se cargan dinámicamente -->
                                    </select>
                                </div>

                                <!-- Filtro por categoría -->
                                <div>
                                    <select id="filtro-categoria" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="">🏷️ Todas las categorías</option>
                                        <!-- Se cargan dinámicamente -->
                                    </select>
                                </div>

                                <!-- Filtro por estado -->
                                <div>
                                    <select id="filtro-estado" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="1" selected>✅ Solo activos</option>
                                        <option value="0">❌ Solo inactivos</option>
                                        <option value="">🔄 Todos los estados</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Container para las cards -->
                        <div class="p-6">
                            <!-- Loading state -->
                            <div id="loading-productos-sucursal" class="flex items-center justify-center py-12">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <span class="ml-3 text-gray-600">Cargando inventario...</span>
                            </div>

                            <!-- Contenedor de productos -->
                            <div id="contenedor-productos-sucursal" class="hidden">
                                <!-- Se llena dinámicamente con JavaScript -->
                            </div>

                            <!-- Estado vacío -->
                            <div id="estado-vacio" class="hidden text-center py-12">
                                <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-neutral-700 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No hay productos asignados</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-6">Comienza asignando productos a tus sucursales</p>
                                <button type="button" id="btn-agregar-desde-vacio"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Asignar primer producto
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>

<!-- Modal Agregar/Editar Producto-Sucursal -->
<div id="modal-producto-sucursal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-neutral-800 dark:border-neutral-700">

            <!-- Header -->
            <div class="py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 id="modal-titulo" class="font-bold text-gray-800 dark:text-white">
                    Asignar Producto a Sucursal
                </h3>
            </div>

            <!-- Body -->
            <div class="p-4 overflow-y-auto">
                <form id="form-producto-sucursal">

                    <!-- ID oculto para edición -->
                    <input type="hidden" id="idproducto_sucursal" name="idproducto_sucursal">

                    <div class="space-y-4">

                        <!-- Selección de sucursal -->
                        <div>
                            <label for="sucursal_idsucursal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Sucursal <span class="text-red-500">*</span>
                            </label>
                            <select id="sucursal_idsucursal" name="sucursal_idsucursal" required
                                    class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <option value="">Seleccionar sucursal...</option>
                                <!-- Se cargan dinámicamente -->
                            </select>
                        </div>

                        <!-- Selección de producto -->
                        <div>
                            <label for="productos_idproducto" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Producto <span class="text-red-500">*</span>
                            </label>
                            <select id="productos_idproducto" name="productos_idproducto" required
                                    class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <option value="">Seleccionar producto...</option>
                                <!-- Se cargan dinámicamente según la sucursal -->
                            </select>
                        </div>

                        <!-- Precio en sucursal -->
                        <div>
                            <label for="precio_sucursal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Precio en Sucursal <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">$</span>
                                <input type="number" id="precio_sucursal" name="precio_sucursal"
                                       step="0.00001" min="0" max="99999.99999" placeholder="0.00000" required
                                       class="py-3 pl-8 pr-4 block w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Precio específico para esta sucursal</p>
                        </div>

                        <!-- Stock en sucursal -->
                        <div>
                            <label for="stock_sucursal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Stock Actual
                            </label>
                            <input type="number" id="stock_sucursal" name="stock_sucursal"
                                   min="0" max="999999" placeholder="0"
                                   class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                        </div>

                        <!-- Stock mínimo y máximo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="stock_minimo_sucursal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Stock Mínimo
                                </label>
                                <input type="number" id="stock_minimo_sucursal" name="stock_minimo_sucursal"
                                       min="0" max="999999" placeholder="0"
                                       class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                            </div>
                            <div>
                                <label for="stock_maximo_sucursal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Stock Máximo
                                </label>
                                <input type="number" id="stock_maximo_sucursal" name="stock_maximo_sucursal"
                                       min="0" max="999999" placeholder="0"
                                       class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                            </div>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Estado
                            </label>
                            <select id="estado" name="estado"
                                    class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                <button type="button" id="btn-cancelar-modal"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-800">
                    Cancelar
                </button>
                <button type="button" id="btn-guardar-producto-sucursal"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-green-600 text-white hover:bg-green-700 disabled:opacity-50 disabled:pointer-events-none">
                    <span id="texto-boton-guardar">Asignar Producto</span>
                </button>
            </div>

        </div>
    </div>
</div>
</div>

<!-- Notificaciones Toast -->
<div id="toast-container" class="fixed top-4 right-4 z-[100] space-y-2"></div>

<!-- Script -->
<script src="views/js/producto-sucursal.js"></script>