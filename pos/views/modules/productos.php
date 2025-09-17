<!-- Content -->
<div class="w-full lg:ps-64">
    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- Grid -->
        <!-- <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        </div> -->
        <!-- End Grid -->

        <!-- Card -->
        <div class="flex flex-col">
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                <div class="min-w-full inline-block align-middle">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
                        <!-- Header -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                                    Productos
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    Agregar productos, editar y más.
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

                                    <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" data-hs-overlay="#modal-agregar-producto">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                        Agregar producto
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
                                    <input type="search" id="buscar-producto" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-gray-400 dark:text-white" placeholder="Buscar por nombre, código o descripción...">
                                </div>

                                <!-- Filtro por categoría -->
                                <div>
                                    <select id="filtro-categoria" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="">🏷️ Todas las categorías</option>
                                        <option value="1">📱 Electrónicos</option>
                                        <option value="2">🍔 Comida</option>
                                        <option value="3">👕 Ropa</option>
                                    </select>
                                </div>

                                <!-- Columna vacía para espaciado -->
                                <div></div>

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
                            <div id="productos-loading" class="flex items-center justify-center py-12">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <span class="ml-3 text-gray-600">Cargando productos...</span>
                            </div>

                            <!-- Grid de cards -->
                            <div id="productos-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
                                <!-- Producto 1: iPhone 15 Pro - Stock Alto -->
                                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden group hover:-translate-y-2">
                                    <!-- Header con imagen del producto -->
                                    <div class="relative bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20 p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                ✅ Activo
                                            </span>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                                    Producto
                                                </span>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">iPhone 15 Pro</h3>
                                            <p class="text-sm text-gray-600 dark:text-neutral-400 mb-3">📱 Electrónicos</p>
                                        </div>
                                    </div>

                                    <!-- Información principal -->
                                    <div class="p-6 space-y-4">
                                        <!-- Códigos -->
                                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-4">
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">Código</p>
                                                    <p class="font-semibold text-gray-900 dark:text-white">IP15P-001</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">Código Auxiliar</p>
                                                    <p class="font-semibold text-gray-900 dark:text-white">7801234567890</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Precios destacados -->
                                        <div class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Compra</p>
                                                    <p class="text-lg font-bold text-gray-800 dark:text-neutral-200">$899.00</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Venta</p>
                                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">$999.99</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 pt-3 border-t border-green-200 dark:border-green-800">
                                                <div class="flex items-center justify-between text-sm">
                                                    <span class="text-gray-600 dark:text-neutral-400">Margen:</span>
                                                    <span class="font-semibold text-green-600 dark:text-green-400">11.2%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stock y inventario -->
                                        <div class="grid grid-cols-3 gap-3">
                                            <div class="text-center bg-green-50 dark:bg-green-900/20 rounded-lg p-3 border border-green-200 dark:border-green-800">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Stock Actual</p>
                                                <p class="text-xl font-bold text-green-600 dark:text-green-400">25</p>
                                                <p class="text-xs text-gray-500 dark:text-neutral-400">Unidades</p>
                                            </div>
                                            <div class="text-center bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-3">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Mín</p>
                                                <p class="text-lg font-bold text-orange-600 dark:text-orange-400">5</p>
                                            </div>
                                            <div class="text-center bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-3">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Máx</p>
                                                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">100</p>
                                            </div>
                                        </div>

                                        <!-- Información adicional -->
                                        <div class="flex items-center justify-between text-sm py-2">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                                                </svg>
                                                <span class="text-gray-600 dark:text-neutral-400">0.18 kg</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-green-600 dark:text-green-400">IVA 15%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer con acciones -->
                                    <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-neutral-400">Unidad de medida</span>
                                            <div class="flex gap-2">
                                                <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 border border-amber-200 hover:border-amber-300 transition-all duration-200 dark:text-amber-400 dark:bg-amber-900/20 dark:border-amber-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Editar
                                                </button>
                                                <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 transition-all duration-200 dark:text-red-400 dark:bg-red-900/20 dark:border-red-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Producto 2: MacBook Pro - Stock Bajo -->
                                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden group hover:-translate-y-2">
                                    <!-- Header con imagen del producto -->
                                    <div class="relative bg-gradient-to-br from-purple-50 via-violet-50 to-pink-50 dark:from-purple-900/20 dark:via-violet-900/20 dark:to-pink-900/20 p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                ✅ Activo
                                            </span>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                                    Producto
                                                </span>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg">
                                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">MacBook Pro 16"</h3>
                                            <p class="text-sm text-gray-600 dark:text-neutral-400 mb-3">💻 Electrónicos</p>
                                        </div>
                                    </div>

                                    <!-- Información principal -->
                                    <div class="p-6 space-y-4">
                                        <!-- Códigos -->
                                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-4">
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">Código</p>
                                                    <p class="font-semibold text-gray-900 dark:text-white">MBP16-003</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">Código Auxiliar</p>
                                                    <p class="font-semibold text-gray-900 dark:text-white">7890123456789</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Precios destacados -->
                                        <div class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Compra</p>
                                                    <p class="text-lg font-bold text-gray-800 dark:text-neutral-200">$2,200.00</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Venta</p>
                                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">$2,499.00</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 pt-3 border-t border-green-200 dark:border-green-800">
                                                <div class="flex items-center justify-between text-sm">
                                                    <span class="text-gray-600 dark:text-neutral-400">Margen:</span>
                                                    <span class="font-semibold text-green-600 dark:text-green-400">13.6%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stock y inventario -->
                                        <div class="grid grid-cols-3 gap-3">
                                            <div class="text-center bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 border border-orange-200 dark:border-orange-800">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Stock Actual</p>
                                                <p class="text-xl font-bold text-orange-600 dark:text-orange-400">3</p>
                                                <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">⚠️ Bajo</p>
                                            </div>
                                            <div class="text-center bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-3">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Mín</p>
                                                <p class="text-lg font-bold text-orange-600 dark:text-orange-400">2</p>
                                            </div>
                                            <div class="text-center bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-3">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Máx</p>
                                                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">20</p>
                                            </div>
                                        </div>

                                        <!-- Información adicional -->
                                        <div class="flex items-center justify-between text-sm py-2">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                                                </svg>
                                                <span class="text-gray-600 dark:text-neutral-400">2.10 kg</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-green-600 dark:text-green-400">IVA 15%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer con acciones -->
                                    <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-neutral-400">Unidad de medida</span>
                                            <div class="flex gap-2">
                                                <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 border border-amber-200 hover:border-amber-300 transition-all duration-200 dark:text-amber-400 dark:bg-amber-900/20 dark:border-amber-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Editar
                                                </button>
                                                <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 transition-all duration-200 dark:text-red-400 dark:bg-red-900/20 dark:border-red-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Producto 3: Samsung Galaxy S20 - Sin Stock (Inactivo) -->
                                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden group hover:-translate-y-2 opacity-80">
                                    <!-- Header con imagen del producto -->
                                    <div class="relative bg-gradient-to-br from-gray-50 via-slate-50 to-gray-50 dark:from-gray-900/20 dark:via-slate-900/20 dark:to-gray-900/20 p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                ❌ Inactivo
                                            </span>
                                            <div class="text-right">
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                                    Producto
                                                </span>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center shadow-lg">
                                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">Samsung Galaxy S20</h3>
                                            <p class="text-sm text-gray-600 dark:text-neutral-400 mb-3">📱 Electrónicos</p>
                                        </div>
                                    </div>

                                    <!-- Información principal -->
                                    <div class="p-6 space-y-4">
                                        <!-- Códigos -->
                                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-4">
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">Código</p>
                                                    <p class="font-semibold text-gray-900 dark:text-white">SGS20-OLD</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">Código Auxiliar</p>
                                                    <p class="font-semibold text-gray-500 dark:text-neutral-400">Sin código</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Precios destacados -->
                                        <div class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Compra</p>
                                                    <p class="text-lg font-bold text-gray-800 dark:text-neutral-200">$350.00</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Venta</p>
                                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">$399.99</p>
                                                </div>
                                            </div>
                                            <div class="mt-3 pt-3 border-t border-green-200 dark:border-green-800">
                                                <div class="flex items-center justify-between text-sm">
                                                    <span class="text-gray-600 dark:text-neutral-400">Margen:</span>
                                                    <span class="font-semibold text-green-600 dark:text-green-400">14.3%</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stock y inventario -->
                                        <div class="grid grid-cols-3 gap-3">
                                            <div class="text-center bg-red-50 dark:bg-red-900/20 rounded-lg p-3 border border-red-200 dark:border-red-800">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Stock Actual</p>
                                                <p class="text-xl font-bold text-red-600 dark:text-red-400">0</p>
                                                <p class="text-xs text-red-600 dark:text-red-400 font-medium">🚫 Agotado</p>
                                            </div>
                                            <div class="text-center bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-3">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Mín</p>
                                                <p class="text-lg font-bold text-orange-600 dark:text-orange-400">1</p>
                                            </div>
                                            <div class="text-center bg-gray-50 dark:bg-neutral-800/50 rounded-lg p-3">
                                                <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Máx</p>
                                                <p class="text-lg font-bold text-blue-600 dark:text-blue-400">50</p>
                                            </div>
                                        </div>

                                        <!-- Información adicional -->
                                        <div class="flex items-center justify-between text-sm py-2">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l3-1m-3 1l-3-1"></path>
                                                </svg>
                                                <span class="text-gray-600 dark:text-neutral-400">0.174 kg</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-sm font-medium text-green-600 dark:text-green-400">IVA 15%</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer con acciones -->
                                    <div class="px-6 py-4 bg-gray-50 dark:bg-neutral-800/50 border-t border-gray-200 dark:border-neutral-700">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-600 dark:text-neutral-400">Unidad de medida</span>
                                            <div class="flex gap-2">
                                                <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 border border-amber-200 hover:border-amber-300 transition-all duration-200 dark:text-amber-400 dark:bg-amber-900/20 dark:border-amber-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Editar
                                                </button>
                                                <button class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 transition-all duration-200 dark:text-red-400 dark:bg-red-900/20 dark:border-red-800">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estado vacío -->
                            <div id="productos-empty" class="hidden text-center py-16">
                                <!-- Ilustración de productos vacía -->
                                <div class="mx-auto mb-8">
                                    <div class="relative">
                                        <!-- Contenedor principal -->
                                        <div class="mx-auto w-32 h-32 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-2xl shadow-lg flex flex-col items-center justify-center border-2 border-dashed border-green-300 dark:border-green-700">
                                            <!-- Caja de producto -->
                                            <div class="relative">
                                                <svg class="w-16 h-16 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v2M7 4h10l1 16H6L7 4zM12 8v4m-3-2h6"></path>
                                                </svg>
                                                <!-- Precio flotante -->
                                                <div class="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                                    <span class="text-white text-xs font-bold">$</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Elementos flotantes -->
                                        <div class="absolute -top-2 -left-2 w-6 h-6 bg-green-500 rounded-full animate-bounce opacity-60"></div>
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full animate-pulse opacity-40"></div>
                                    </div>
                                </div>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                                    No hay productos disponibles
                                </h3>
                                <p class="text-gray-600 dark:text-neutral-400 mb-8">
                                    Comienza agregando productos a tu inventario. Esto te ayudará a gestionar mejor tu negocio.
                                </p>
                                <button type="button" class="inline-flex items-center gap-x-2 py-3 px-6 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200" data-hs-overlay="#modal-agregar-producto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Agregar primer producto
                                </button>
                            </div>
                        </div>

                        <!-- Footer con paginación -->
                        <div class="bg-gray-50 dark:bg-neutral-800/50 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500 dark:text-neutral-400">
                                    <span id="pagination-info">Mostrando 3 de 3 productos</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" id="btn-previous" class="btn-previous py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" disabled>
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m15 18-6-6 6-6" />
                                        </svg>
                                        Anterior
                                    </button>

                                    <button type="button" id="btn-next" class="btn-next py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" disabled>
                                        Siguiente
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End Footer -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
</div>
<!-- End Content -->

<!-- Modal Agregar Producto -->
<div id="modal-agregar-producto" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-4xl sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white shadow-2xl rounded-2xl pointer-events-auto dark:bg-neutral-800 dark:shadow-neutral-700/70 overflow-hidden">
            <!-- Header mejorado -->
            <div class="flex justify-between items-center py-6 px-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-neutral-800 dark:to-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl dark:bg-blue-900/20">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v2M7 4h10l1 16H6L7 4zM12 8v4m-3-2h6"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            Agregar Producto
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Crear un nuevo producto en el inventario
                        </p>
                    </div>
                </div>
                <button type="button" class="flex justify-center items-center w-10 h-10 text-sm font-semibold rounded-xl border border-transparent text-gray-500 hover:bg-white hover:text-gray-800 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" data-hs-overlay="#modal-agregar-producto">
                    <span class="sr-only">Close</span>
                    <svg class="flex-shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m18 6-12 12"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <!-- End Header -->

            <!-- Body -->
            <div class="p-8 overflow-y-auto max-h-[70vh]">
                <form id="formAgregarProducto" class="space-y-6">
                    <!-- Sección: Imagen del producto -->
                    <div class="text-center">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Imagen del producto</h4>
                        <div id="dropzone-container" class="mx-auto w-32 h-32 relative group cursor-pointer">
                            <div id="image-dropzone" class="w-full h-full border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:hover:bg-neutral-700">
                                <div id="dropzone-content" class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z"></path>
                                    </svg>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">Subir imagen</p>
                                </div>
                                <img id="image-preview" class="hidden w-full h-full object-cover rounded-xl" />
                            </div>
                            <input type="file" id="producto-imagen" name="producto_imagen" accept="image/*" class="hidden">
                            <button type="button" id="remove-image" class="hidden absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-neutral-400">JPG, PNG hasta 2MB (opcional)</p>
                    </div>

                    <!-- Información básica -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Información Básica
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="codigo" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Código <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="codigo" name="codigo" placeholder="Se generará automáticamente" class="py-2 px-3 pr-24 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 bg-gray-50 dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" readonly required>
                                    <button type="button" id="btn-generar-codigo" class="absolute inset-y-0 right-0 px-3 flex items-center text-xs font-medium text-blue-600 hover:text-blue-700 focus:outline-none focus:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Generar
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Código único generado automáticamente
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label for="codigo-auxiliar" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Código Auxiliar
                                    <span class="text-xs text-gray-500 dark:text-neutral-400 font-normal">(Opcional)</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="codigo-auxiliar" name="codigo_auxiliar" placeholder="Ej: 7891234567890" class="py-2 px-3 pl-10 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4m-4 8h4M4 20h4m12 0h4M4 4h4m4 0h4m4 0h4M4 8h4m0 0h4m4 0h4m4 0h4M4 16h4m0 0h4m4 0h4m4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Código de barras del producto (EAN, UPC, etc.)
                                    </span>
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Descripción <span class="text-red-500">*</span>
                                </label>
                                <textarea id="descripcion" name="descripcion" rows="3" placeholder="Describe el producto de manera clara y detallada..." class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none resize-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" required maxlength="500"></textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Máximo 500 caracteres • <span id="descripcion-counter">0/500</span>
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label for="categoria" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Categoría <span class="text-red-500">*</span>
                                </label>
                                <select id="categoria" name="categoria_idcategoria" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:focus:ring-neutral-600" required>
                                    <option value="">Seleccionar categoría</option>
                                </select>
                            </div>
                            <div>
                                <label for="tipo-producto" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Tipo <span class="text-red-500">*</span>
                                </label>
                                <select id="tipo-producto" name="tipo_producto" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:focus:ring-neutral-600" required>
                                    <option value="Producto">Producto</option>
                                    <option value="Servicio">Servicio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Precios -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Precios
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="precio-compra" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Precio de Compra
                                </label>
                                <input type="text" id="precio-compra" name="precio_de_compra" placeholder="0.00000" inputmode="decimal" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                            </div>
                            <div>
                                <label for="precio-venta" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Precio de Venta <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="precio-venta" name="precio_de_venta" placeholder="0.00000" inputmode="decimal" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" required>
                            </div>
                        </div>
                    </div>

                    <!-- Descuentos -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Descuentos por Cantidad
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="tiene-descuento" name="tiene_descuento" value="1" class="shrink-0 mt-0.5 border-gray-200 rounded text-yellow-600 focus:ring-yellow-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-yellow-500 dark:checked:border-yellow-500 dark:focus:ring-offset-gray-800">
                                <label for="tiene-descuento" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Ofrecer descuento por cantidad</label>
                            </div>
                            <div id="descuento-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                                <div>
                                    <label for="descuento-cantidad" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Cantidad Mínima <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="descuento-cantidad" name="descuento_por_cantidad" placeholder="0" min="1" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-yellow-500 focus:ring-yellow-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Número entero mayor a 0
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <label for="precio-descuento" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Precio con Descuento <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="precio-descuento" name="precio_con_descuento" placeholder="0.00000" inputmode="decimal" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-yellow-500 focus:ring-yellow-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Debe ser menor al precio de venta
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div id="descuento-preview" class="hidden bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <h5 class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-2">Vista previa del descuento:</h5>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600 dark:text-neutral-400">Precio normal:</span>
                                        <p id="preview-precio-normal" class="font-semibold text-gray-900 dark:text-white">$0.00</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-neutral-400">Precio con descuento:</span>
                                        <p id="preview-precio-descuento" class="font-semibold text-yellow-600 dark:text-yellow-400">$0.00</p>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-neutral-400">Ahorro:</span>
                                        <p id="preview-ahorro" class="font-semibold text-green-600 dark:text-green-400">$0.00 (0%)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stock e Inventario -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Stock e Inventario
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="maneja-stock" name="maneja_stock" value="1" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                <label for="maneja-stock" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Manejar control de stock</label>
                            </div>
                            <div id="stock-fields" class="grid grid-cols-1 md:grid-cols-3 gap-4 hidden">
                                <div>
                                    <label for="stock-actual" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Stock Actual
                                    </label>
                                    <input type="number" id="stock-actual" name="stock_actual" placeholder="0" min="0" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                </div>
                                <div>
                                    <label for="stock-minimo" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Stock Mínimo
                                    </label>
                                    <input type="number" id="stock-minimo" name="stock_minimo" placeholder="0" min="0" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                </div>
                                <div>
                                    <label for="stock-maximo" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Stock Máximo
                                    </label>
                                    <input type="number" id="stock-maximo" name="stock_maximo" placeholder="0" min="0" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="unidad-medida" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Unidad de Medida
                                    </label>
                                    <input type="text" id="unidad-medida" name="unidad_medida" value="Unidad" placeholder="Unidad, Kg, Litro, etc." class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                </div>
                                <div>
                                    <label for="peso" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Peso (Kg)
                                    </label>
                                    <input type="number" id="peso" name="peso" placeholder="0.000" step="0.001" min="0" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Impuestos -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Configuración de Impuestos
                        </h4>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="graba-iva" name="graba_iva" value="1" checked class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                <label for="graba-iva" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Graba IVA</label>
                            </div>
                            <div id="iva-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="porcentaje-iva" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Porcentaje IVA (%)
                                    </label>
                                    <input type="number" id="porcentaje-iva" name="porcentaje_iva" value="15" placeholder="15" step="0.01" min="0" max="100" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="graba-ice" name="graba_ice" value="1" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                <label for="graba-ice" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Graba ICE (Impuesto Consumos Especiales)</label>
                            </div>
                            <div id="ice-fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                                <div>
                                    <label for="porcentaje-ice" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Porcentaje ICE (%)
                                    </label>
                                    <input type="number" id="porcentaje-ice" name="porcentaje_ice" placeholder="0" step="0.01" min="0" max="100" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="flex justify-end items-center gap-x-3 py-6 px-8 border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/50">
                <button type="button" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:border-neutral-500" data-hs-overlay="#modal-agregar-producto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                <button type="submit" form="formAgregarProducto" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Crear Producto</span>
                </button>
            </div>
            <!-- End Footer -->
        </div>
    </div>
</div>

<script>
    // Pasar datos de sesión al JavaScript
    window.TENANT_ID = <?php echo isset($_SESSION['tenant_id']) ? $_SESSION['tenant_id'] : 'null'; ?>;
    window.currentUserId = <?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'null'; ?>;
</script>

<script>
    // Simulación de carga de datos
    document.addEventListener('DOMContentLoaded', function() {
        // Ocultar loading y mostrar grid después de un breve delay
        setTimeout(function() {
            document.getElementById('productos-loading').classList.add('hidden');
            document.getElementById('productos-grid').classList.remove('hidden');
        }, 800);

        // Generar código automáticamente al abrir el modal
        const modalAgregarProducto = document.getElementById('modal-agregar-producto');
        const inputCodigo = document.getElementById('codigo');
        const btnGenerarCodigo = document.getElementById('btn-generar-codigo');

        // Función para generar código único
        function generarCodigoProducto() {
            const timestamp = Date.now().toString().slice(-6); // Últimos 6 dígitos del timestamp
            const random = Math.floor(Math.random() * 100).toString().padStart(2, '0'); // Número aleatorio 00-99
            const codigo = `PROD-${timestamp}${random}`;
            return codigo;
        }

        // Generar código al abrir el modal
        if (modalAgregarProducto) {
            modalAgregarProducto.addEventListener('open.hs.overlay', function() {
                const nuevoCodigo = generarCodigoProducto();
                inputCodigo.value = nuevoCodigo;

                // Animación visual para mostrar que se generó un nuevo código
                inputCodigo.classList.add('animate-pulse');
                setTimeout(() => {
                    inputCodigo.classList.remove('animate-pulse');
                }, 1000);
            });
        }

        // Botón para regenerar código manualmente
        if (btnGenerarCodigo) {
            btnGenerarCodigo.addEventListener('click', function() {
                const nuevoCodigo = generarCodigoProducto();
                inputCodigo.value = nuevoCodigo;

                // Feedback visual
                this.innerHTML = `
                    <svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Generando...
                `;

                setTimeout(() => {
                    this.innerHTML = `
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Generado
                    `;

                    // Volver al estado original después de 2 segundos
                    setTimeout(() => {
                        this.innerHTML = `
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Generar
                        `;
                    }, 2000);
                }, 800);

                // Animación en el input
                inputCodigo.classList.add('animate-pulse', 'bg-green-50', 'dark:bg-green-900/20');
                setTimeout(() => {
                    inputCodigo.classList.remove('animate-pulse', 'bg-green-50', 'dark:bg-green-900/20');
                }, 1000);
            });
        }

        // Configuración adicional para controles interactivos
        const manejaStockCheckbox = document.getElementById('maneja-stock');
        const stockFields = document.getElementById('stock-fields');
        const grabaIvaCheckbox = document.getElementById('graba-iva');
        const ivaFields = document.getElementById('iva-fields');
        const grabaIceCheckbox = document.getElementById('graba-ice');
        const iceFields = document.getElementById('ice-fields');

        // Mostrar/ocultar campos de stock
        if (manejaStockCheckbox && stockFields) {
            manejaStockCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    stockFields.classList.remove('hidden');
                } else {
                    stockFields.classList.add('hidden');
                }
            });
        }

        // Mostrar/ocultar campos de ICE
        if (grabaIceCheckbox && iceFields) {
            grabaIceCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    iceFields.classList.remove('hidden');
                } else {
                    iceFields.classList.add('hidden');
                }
            });
        }

        // Configuración de dropzone para imagen
        const imageDropzone = document.getElementById('image-dropzone');
        const imageInput = document.getElementById('producto-imagen');
        const imagePreview = document.getElementById('image-preview');
        const dropzoneContent = document.getElementById('dropzone-content');
        const removeImageBtn = document.getElementById('remove-image');

        if (imageDropzone && imageInput) {
            // Click en dropzone
            imageDropzone.addEventListener('click', function() {
                imageInput.click();
            });

            // Cambio de archivo
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        dropzoneContent.classList.add('hidden');
                        removeImageBtn.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Remover imagen
            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    imageInput.value = '';
                    imagePreview.classList.add('hidden');
                    dropzoneContent.classList.remove('hidden');
                    this.classList.add('hidden');
                });
            }

            // Drag and drop
            imageDropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-blue-400', 'bg-blue-50');
            });

            imageDropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-400', 'bg-blue-50');
            });

            imageDropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-400', 'bg-blue-50');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    imageInput.files = files;
                    const event = new Event('change', { bubbles: true });
                    imageInput.dispatchEvent(event);
                }
            });
        }
    });
</script>