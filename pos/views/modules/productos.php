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
                        <div id="dropzone-container" class="mx-auto w-40 h-40 relative group cursor-pointer">
                            <div id="image-dropzone" class="w-full h-full border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:hover:bg-neutral-700 relative overflow-hidden">
                                <div id="dropzone-content" class="text-center">
                                    <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z"></path>
                                    </svg>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">Subir imagen</p>
                                    <p class="text-xs text-gray-400 dark:text-neutral-500">o arrastra aquí</p>
                                </div>

                                <!-- Estados de carga -->
                                <div id="upload-loading" class="hidden absolute inset-0 bg-white/90 dark:bg-neutral-800/90 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                                        <p class="mt-2 text-xs text-blue-600 dark:text-blue-400 font-medium">Procesando...</p>
                                        <div id="upload-progress" class="w-24 bg-gray-200 rounded-full h-1 mt-2 mx-auto">
                                            <div id="upload-progress-bar" class="bg-blue-600 h-1 rounded-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>

                                <img id="image-preview" class="hidden w-full h-full object-cover rounded-xl" />
                            </div>

                            <input type="file" id="producto-imagen" name="producto_imagen" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden">

                            <!-- Botón remover imagen -->
                            <button type="button" id="remove-image" class="hidden absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs hover:bg-red-600 shadow-lg transition-all duration-200 hover:scale-110">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>

                            <!-- Badge de estado -->
                            <div id="image-status-badge" class="hidden absolute -bottom-2 left-1/2 transform -translate-x-1/2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    ✓ Imagen cargada
                                </span>
                            </div>
                        </div>

                        <!-- Información del archivo -->
                        <div id="file-info" class="hidden mt-3 text-xs text-gray-600 dark:text-neutral-400">
                            <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-2 inline-block">
                                <div class="flex items-center gap-2">
                                    <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span id="file-name">archivo.jpg</span>
                                    <span id="file-size" class="text-gray-400">• 1.2 MB</span>
                                    <span id="file-dimensions" class="text-gray-400">• 800×600</span>
                                </div>
                            </div>
                        </div>

                        <p class="mt-2 text-xs text-gray-500 dark:text-neutral-400">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                JPG, PNG, WebP, GIF hasta 2MB • Min: 300×300px (opcional)
                            </span>
                        </p>

                        <!-- Mensajes de validación -->
                        <div id="image-validation" class="hidden mt-2 space-y-2">
                            <div id="image-error-size" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2">
                                <div class="flex items-start gap-2">
                                    <svg class="w-3 h-3 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-medium text-red-800 dark:text-red-300">Archivo demasiado grande</p>
                                        <p class="text-xs text-red-600 dark:text-red-400">El tamaño máximo permitido es 2MB</p>
                                    </div>
                                </div>
                            </div>
                            <div id="image-error-type" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2">
                                <div class="flex items-start gap-2">
                                    <svg class="w-3 h-3 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-medium text-red-800 dark:text-red-300">Formato no válido</p>
                                        <p class="text-xs text-red-600 dark:text-red-400">Solo se permiten archivos JPG, PNG, WebP y GIF</p>
                                    </div>
                                </div>
                            </div>
                            <div id="image-error-dimensions" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2">
                                <div class="flex items-start gap-2">
                                    <svg class="w-3 h-3 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-xs font-medium text-red-800 dark:text-red-300">Dimensiones insuficientes</p>
                                        <p class="text-xs text-red-600 dark:text-red-400">Las dimensiones mínimas son 300×300 píxeles</p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                    <input type="number" id="descuento-cantidad" name="descuento_por_cantidad" placeholder="0" min="1" class="stock-input py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-yellow-500 focus:ring-yellow-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
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
                                <input type="checkbox" id="maneja-stock" name="maneja_stock" value="1" class="shrink-0 mt-0.5 border-gray-200 rounded text-purple-600 focus:ring-purple-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-purple-500 dark:checked:border-purple-500 dark:focus:ring-offset-gray-800">
                                <label for="maneja-stock" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Manejar control de stock</label>
                            </div>
                            <div id="stock-fields" class="space-y-4 hidden">
                                <!-- Indicador visual del nivel de stock -->
                                <div id="stock-level-indicator" class="hidden bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-lg p-4">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-3 h-3 bg-purple-500 rounded-full animate-pulse"></div>
                                        <h5 class="text-sm font-medium text-gray-800 dark:text-neutral-200">Nivel de Stock</h5>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="relative">
                                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                                <div id="stock-bar" class="bg-gray-400 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                                            </div>
                                            <div class="flex justify-between items-center mt-2 text-xs">
                                                <span class="text-orange-600 dark:text-orange-400">
                                                    Mín: <span id="min-display">0</span>
                                                </span>
                                                <span id="stock-status" class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300">
                                                    Configurar stock
                                                </span>
                                                <span class="text-green-600 dark:text-green-400">
                                                    Máx: <span id="max-display">0</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="stock-actual" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Stock Actual
                                        </label>
                                        <input type="number" id="stock-actual" name="stock_actual" placeholder="0" min="0" step="1" class="stock-input py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-purple-500 focus:ring-purple-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    </div>
                                    <div>
                                        <label for="stock-minimo" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Stock Mínimo
                                        </label>
                                        <input type="number" id="stock-minimo" name="stock_minimo" placeholder="0" min="0" step="1" class="stock-input py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-purple-500 focus:ring-purple-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    </div>
                                    <div>
                                        <label for="stock-maximo" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Stock Máximo
                                        </label>
                                        <input type="number" id="stock-maximo" name="stock_maximo" placeholder="0" min="0" step="1" class="stock-input py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-purple-500 focus:ring-purple-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    </div>
                                </div>

                                <!-- Mensajes de validación -->
                                <div id="stock-validation" class="hidden space-y-2">
                                    <div id="stock-error-min-max" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-red-800 dark:text-red-300">Error en configuración de stock</p>
                                                <p class="text-xs text-red-600 dark:text-red-400">El stock mínimo no puede ser mayor al stock máximo</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="stock-warning-actual" class="hidden bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Advertencia de inventario</p>
                                                <p class="text-xs text-yellow-600 dark:text-yellow-400" id="stock-warning-message">El stock actual está fuera del rango recomendado</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="unidad-medida" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Unidad de Medida
                                    </label>
                                    <select id="unidad-medida" name="unidad_medida" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:focus:ring-neutral-600">
                                        <optgroup label="📦 Unidades Básicas">
                                            <option value="Unidad" selected>Unidad</option>
                                            <option value="Pieza">Pieza</option>
                                            <option value="Par">Par</option>
                                            <option value="Docena">Docena</option>
                                        </optgroup>
                                        <optgroup label="⚖️ Peso">
                                            <option value="Kilogramo">Kilogramo (kg)</option>
                                            <option value="Gramo">Gramo (g)</option>
                                            <option value="Libra">Libra (lb)</option>
                                            <option value="Onza">Onza (oz)</option>
                                        </optgroup>
                                        <optgroup label="🥤 Volumen">
                                            <option value="Litro">Litro (L)</option>
                                            <option value="Mililitro">Mililitro (mL)</option>
                                            <option value="Galón">Galón (gal)</option>
                                        </optgroup>
                                        <optgroup label="📏 Longitud">
                                            <option value="Metro">Metro (m)</option>
                                            <option value="Centímetro">Centímetro (cm)</option>
                                            <option value="Pulgada">Pulgada (in)</option>
                                            <option value="Pie">Pie (ft)</option>
                                        </optgroup>
                                        <optgroup label="📐 Área">
                                            <option value="Metro cuadrado">Metro cuadrado (m²)</option>
                                            <option value="Centímetro cuadrado">Centímetro cuadrado (cm²)</option>
                                        </optgroup>
                                        <optgroup label="⏰ Tiempo/Servicios">
                                            <option value="Hora">Hora</option>
                                            <option value="Día">Día</option>
                                            <option value="Mes">Mes</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div id="peso-container">
                                    <label for="peso" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Peso
                                        <span class="text-xs text-gray-500 dark:text-neutral-400 font-normal">(Opcional)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="peso" name="peso" placeholder="0.000" step="0.001" min="0" max="99999.999" class="stock-input py-2 px-3 pr-12 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <span id="peso-unit" class="text-xs text-gray-400 dark:text-neutral-500 font-medium">kg</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span id="peso-help-text">Peso físico del producto para cálculos de envío</span>
                                        </span>
                                    </p>
                                    <!-- Validación de peso -->
                                    <div id="peso-validation" class="hidden mt-2">
                                        <div id="peso-error-max" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2">
                                            <div class="flex items-start gap-2">
                                                <svg class="w-3 h-3 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-xs font-medium text-red-800 dark:text-red-300">Peso demasiado alto</p>
                                                    <p class="text-xs text-red-600 dark:text-red-400">El peso máximo permitido es 99,999.999 kg</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                            <!-- Sección: IVA -->
                            <div class="space-y-4">
                                <div>
                                    <label for="codigo-iva" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Código IVA <span class="text-red-500">*</span>
                                    </label>
                                    <select id="codigo-iva" name="codigo_iva" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" required>
                                        <option value="">Seleccione porcentaje de IVA</option>
                                        <option value="0">0%</option>
                                        <option value="2">12%</option>
                                        <option value="3">14%</option>
                                        <option value="4" selected>15%</option>
                                        <option value="5">5%</option>
                                        <option value="6">No Objeto de Impuesto</option>
                                        <option value="7">Exento de IVA</option>
                                        <option value="8">IVA diferenciado (15%)</option>
                                        <option value="10">13%</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Código requerido por el SRI para clasificación tributaria
                                        </span>
                                    </p>
                                </div>

                                <div>
                                    <label for="porcentaje-iva" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Porcentaje IVA (%)
                                    </label>
                                    <input type="number" id="porcentaje-iva" name="porcentaje_iva" value="15.00" step="0.01" min="0" max="100" readonly class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm bg-gray-50 cursor-not-allowed disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Se actualiza automáticamente según el código seleccionado
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" id="graba-ice" name="graba_ice" value="1" class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                <label for="graba-ice" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Graba ICE (Impuesto Consumos Especiales)</label>
                            </div>
                            <div id="ice-fields" class="space-y-4 hidden">
                                <div>
                                    <label for="codigo-ice" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Código ICE <span class="text-red-500">*</span>
                                    </label>
                                    <select id="codigo-ice" name="codigo_ice" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                        <option value="">Seleccione un código ICE</option>
                                        <option value="3011">3011 - ICE Cigarrillos Rubios</option>
                                        <option value="3021">3021 - ICE Cigarrillos Negros</option>
                                        <option value="3023">3023 - ICE Productos del Tabaco y Sucedáneos del Tabaco excepto Cigarrillos</option>
                                        <option value="3031">3031 - ICE Bebidas Alcohólicas</option>
                                        <option value="3041">3041 - ICE Cerveza Industrial Gran Escala</option>
                                        <option value="3041">3041 - ICE Cerveza Industrial Mediana Escala</option>
                                        <option value="3041">3041 - ICE Cerveza Industrial Pequeña Escala</option>
                                        <option value="3073">3073 - ICE Vehículos Motorizados cuyo PVP sea hasta de 20000 USD</option>
                                        <option value="3075">3075 - ICE Vehículos Motorizados PVP entre 30000 y 40000</option>
                                        <option value="3077">3077 - ICE Vehículos Motorizados cuyo PVP superior USD 40.000 hasta 50.000</option>
                                        <option value="3078">3078 - ICE Vehículos Motorizados cuyo PVP superior USD 50.000 hasta 60.000</option>
                                        <option value="3079">3079 - ICE Vehículos Motorizados cuyo PVP superior USD 60.000 hasta 70.000</option>
                                        <option value="3080">3080 - ICE Vehículos Motorizados cuyo PVP superior USD 70.000</option>
                                        <option value="3081">3081 - ICE Aviones, Tricares, yates, Barcos de Recreo</option>
                                        <option value="3092">3092 - ICE Servicios de Televisión Prepagada</option>
                                        <option value="3610">3610 - ICE Perfumes y Aguas de Tocador</option>
                                        <option value="3620">3620 - ICE Videojuegos</option>
                                        <option value="3630">3630 - ICE Armas de Fuego, Armas deportivas y Municiones</option>
                                        <option value="3640">3640 - ICE Focos Incandescentes</option>
                                        <option value="3660">3660 - ICE Cuotas Membresías Afiliaciones Acciones</option>
                                        <option value="3093">3093 - ICE Servicios Telefonía Sociedades</option>
                                        <option value="3101">3101 - ICE Bebidas Energizantes</option>
                                        <option value="3053">3053 - ICE Bebidas Gaseosas con Alto Contenido de Azúcar</option>
                                        <option value="3054">3054 - ICE Bebidas Gaseosas con Bajo Contenido de Azúcar</option>
                                        <option value="3111">3111 - ICE Bebidas No Alcohólicas</option>
                                        <option value="3043">3043 - ICE Cerveza Artesanal</option>
                                        <option value="3033">3033 - ICE Alcohol</option>
                                        <option value="3671">3671 - ICE calefones y sistemas de calentamiento de agua a gas SRI</option>
                                        <option value="3684">3684 - ICE vehículos motorizados camionetas y de rescate cuyo PVP sea hasta DE 30.000 USD</option>
                                        <option value="3686">3686 - ICE vehículos motorizados excepto camionetas y de rescate cuyo PVP sea superior USD 20.000 hasta DE 30.000</option>
                                        <option value="3688">3688 - ICE vehículos híbridos cuyo PVP sea de hasta USD. 35.000</option>
                                        <option value="3691">3691 - ICE vehículos híbridos cuyo PVP superior USD. 35.000 hasta 40.000</option>
                                        <option value="3692">3692 - ICE vehículos híbridos cuyo PVP superior USD. 40.000 hasta 50.000</option>
                                        <option value="3695">3695 - ICE vehículos híbridos cuyo PVP superior USD. 50.000 hasta 60.000</option>
                                        <option value="3696">3696 - ICE vehículos híbridos cuyo PVP superior USD. 60.000 hasta 70.000</option>
                                        <option value="3698">3698 - ICE vehículos híbridos cuyo PVP superior a USD 70.000</option>
                                        <option value="3682">3682 - ICE consumibles tabaco calentado y líquidos con nicotina SRI</option>
                                        <option value="3681">3681 - ICE servicios de telefonía móvil personas naturales</option>
                                        <option value="3680">3680 - ICE fundas plásticas</option>
                                        <option value="3533">3533 - ICE Import. Bebidas Alcohólicas</option>
                                        <option value="3541">3541 - ICE Cerveza Gran Escala CAE</option>
                                        <option value="3541">3541 - ICE Cerveza Industrial de Mediana Escala CAE</option>
                                        <option value="3541">3541 - ICE Cerveza Industrial de Pequeña Escala CAE</option>
                                        <option value="3542">3542 - ICE Cigarrillos Rubios CAE</option>
                                        <option value="3543">3543 - ICE Cigarrillos Negros CAE</option>
                                        <option value="3544">3544 - ICE Productos del Tabaco y Sucedáneos del Tabaco Excepto Cigarrillos CAE</option>
                                        <option value="3581">3581 - ICE Aeronaves CAE</option>
                                        <option value="3582">3582 - ICE Aviones, Avionetas y Helicópteros Exct. Aquellos destinados Al Trans. CAE</option>
                                        <option value="3710">3710 - ICE Perfumes Aguas de Tocador Cae</option>
                                        <option value="3720">3720 - ICE Video Juegos CAE</option>
                                        <option value="3730">3730 - ICE Importaciones Armas de Fuego, Armas deportivas y Municiones CAE</option>
                                        <option value="3740">3740 - ICE Focos Incandescentes CAE</option>
                                        <option value="3871">3871 - ICE-vehículos motorizados cuyo PVP SEA hasta de 20000 USD SENAE</option>
                                        <option value="3873">3873 - ICE-vehículos motorizados PVP entre 30000 Y 40000 SENAE</option>
                                        <option value="3874">3874 - ICE-vehículos motorizados cuyo PVP superior USD 40.000 hasta 50.000 SENAE</option>
                                        <option value="3875">3875 - ICE-vehículos motorizados cuyo PVP superior USD 50.000 hasta 60.000 SENAE</option>
                                        <option value="3876">3876 - ICE-vehículos motorizados cuyo PVP superior USD 60.000 hasta 70.000 SENAE</option>
                                        <option value="3877">3877 - ICE-vehículos motorizados cuyo PVP superior USD 70.000 SENAE</option>
                                        <option value="3878">3878 - ICE-Aviones, Tricares, Yates, Barcos de Rec SENAE</option>
                                        <option value="3601">3601 - ICE Bebidas Energizantes SENAE</option>
                                        <option value="3552">3552 - ICE bebidas gaseosas con alto contenido de azúcar SENAE</option>
                                        <option value="3553">3553 - ICE bebidas gaseosas con bajo contenido de azúcar SENAE</option>
                                        <option value="3602">3602 - ICE bebidas no alcohólicas SENAE</option>
                                        <option value="3545">3545 - ICE cerveza artesanal SENAE</option>
                                        <option value="3532">3532 - ICE Import. alcohol SENAE</option>
                                        <option value="3771">3771 - ICE calefones y sistemas de calentamiento de agua a gas SENAE</option>
                                        <option value="3685">3685 - ICE vehículos motorizados camionetas y de rescate PVP sea hasta DE 30.000 USD SENAE</option>
                                        <option value="3687">3687 - ICE vehículos motorizados excepto camionetas y de rescate cuyo PVP sea superior USD 20.000 hasta de 30.000 SENAE</option>
                                        <option value="3689">3689 - ICE vehículos híbridos cuyo PVP sea de hasta USD. 35.000 SENAE</option>
                                        <option value="3690">3690 - ICE vehículos híbridos cuyo PVP superior USD. 35.000 hasta 40.000 SENAE</option>
                                        <option value="3693">3693 - ICE vehículos híbridos cuyo PVP superior USD. 40.000 hasta 50.000 SENAE</option>
                                        <option value="3694">3694 - ICE vehículos híbridos cuyo PVP superior USD. 50.000 hasta 60.000 SENAE</option>
                                        <option value="3697">3697 - ICE vehículos híbridos cuyo PVP superior USD. 60.000 hasta 70.000 SENAE</option>
                                        <option value="3699">3699 - ICE vehículos híbridos cuyo PVP superior a USD 70.000 SENAE</option>
                                        <option value="3683">3683 - ICE consumibles tabaco calentado y líquidos con nicotina SENAE</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Código requerido por el SRI para productos con ICE
                                        </span>
                                    </p>
                                </div>

                                <div>
                                    <label for="porcentaje-ice" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Porcentaje ICE (%)
                                    </label>
                                    <input type="number" id="porcentaje-ice" name="porcentaje_ice" placeholder="0" step="0.01" min="0" max="100" class="stock-input py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Porcentaje aplicable según código seleccionado
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Sección: Material de Construcción -->
                            <div class="space-y-4 mt-6">
                                <div class="flex items-center">
                                    <input type="checkbox" id="es-material-construccion" name="es_material_construccion" value="1" class="shrink-0 mt-0.5 border-gray-200 rounded text-orange-600 focus:ring-orange-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-orange-500 dark:checked:border-orange-500 dark:focus:ring-offset-gray-800">
                                    <label for="es-material-construccion" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Es material de construcción</label>
                                </div>

                                <div id="material-construccion-fields" class="hidden">
                                    <label for="codigo-material-construccion" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Código Material de Construcción <span class="text-red-500">*</span>
                                    </label>
                                    <select id="codigo-material-construccion" name="codigo_material_construccion" class="py-2 px-3 block w-full border border-gray-300 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                        <option value="">Seleccione un código de material</option>
                                        <option value="F010101">F010101 - VARILLA LAMINADA CORRUGADA AS42 DE 8MM, 10MM Y 12MM DE DIÁMETRO</option>
                                        <option value="F010201">F010201 - ARCILLA</option>
                                        <option value="F010202">F010202 - ARENA</option>
                                        <option value="F010203">F010203 - CAL</option>
                                        <option value="F010204">F010204 - CALIZA</option>
                                        <option value="F010205">F010205 - PÉTROS</option>
                                        <option value="F010301">F010301 - HORMIGÓN PREMEZCLADO</option>
                                        <option value="F010401">F010401 - CEMENTO Y SUS DERIVADOS</option>
                                        <option value="F010402">F010402 - RESIDUO CEMENTO</option>
                                        <option value="F010501">F010501 - CHATARRA FERROSA</option>
                                        <option value="F010601">F010601 - MORTERS</option>
                                        <option value="F010701">F010701 - CLINKER</option>
                                        <option value="F010702">F010702 - PUZOLANA</option>
                                        <option value="F010703">F010703 - YESO</option>
                                        <option value="F010801">F010801 - ADOQUÍN</option>
                                        <option value="F010802">F010802 - BLOQUES</option>
                                        <option value="F010803">F010803 - LADRILLOS</option>
                                        <option value="F010804">F010804 - PRODUCTOS DE HORMIGÓN PREFABRICADO</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3 h-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Código requerido por el SRI para materiales de construcción
                                        </span>
                                    </p>
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

<!-- Modal Editar Producto -->
<div id="modal-editar-producto" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all lg:max-w-4xl lg:w-full m-3 lg:mx-auto">
        <div class="max-h-full overflow-hidden flex flex-col bg-white border shadow-2xl rounded-2xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-black/30">
            <!-- Header mejorado -->
            <div class="flex justify-between items-center py-6 px-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-neutral-800 dark:to-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl dark:bg-blue-900/20">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            Editar Producto
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Modificar información del producto
                        </p>
                    </div>
                </div>
                <button type="button" class="flex justify-center items-center w-10 h-10 text-sm font-semibold rounded-xl border border-transparent text-gray-500 hover:bg-white hover:text-gray-800 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" data-hs-overlay="#modal-editar-producto">
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
                    <form id="formEditarProducto" class="space-y-6">
                        <!-- Imagen del producto -->
                        <div class="text-center">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Imagen del producto</h4>
                            <div id="edit-dropzone-container" class="mx-auto w-40 h-40 relative group cursor-pointer">
                                <div id="edit-image-dropzone" class="w-full h-full border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:hover:bg-neutral-700 relative overflow-hidden">
                                    <div id="edit-dropzone-content" class="text-center">
                                        <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4a2 2 0 012-2z"></path>
                                        </svg>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">Cambiar imagen</p>
                                        <p class="text-xs text-gray-400 dark:text-neutral-500">o arrastra aquí</p>
                                    </div>

                                    <!-- Estados de carga -->
                                    <div id="edit-upload-loading" class="hidden absolute inset-0 bg-white/90 dark:bg-neutral-800/90 flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                                            <p class="mt-2 text-xs text-blue-600 dark:text-blue-400 font-medium">Procesando...</p>
                                            <div id="edit-upload-progress" class="w-24 bg-gray-200 rounded-full h-1 mt-2 mx-auto">
                                                <div id="edit-upload-progress-bar" class="bg-blue-600 h-1 rounded-full transition-all duration-300" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <img id="edit-image-preview" class="hidden w-full h-full object-cover rounded-xl" />
                                </div>

                                <input type="file" id="edit-producto-imagen" name="producto_imagen" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden">

                                <!-- Botón remover imagen -->
                                <button type="button" id="edit-remove-image" class="hidden absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs hover:bg-red-600 shadow-lg transition-all duration-200 hover:scale-110">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>

                                <!-- Badge de estado -->
                                <div id="edit-image-status-badge" class="hidden absolute -bottom-2 left-1/2 transform -translate-x-1/2">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        ✓ Imagen cargada
                                    </span>
                                </div>
                            </div>

                            <!-- Información del archivo -->
                            <div id="edit-file-info" class="hidden mt-3 text-xs text-gray-600 dark:text-neutral-400">
                                <div class="bg-gray-50 dark:bg-neutral-800 rounded-lg p-2 inline-block">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span id="edit-file-name">archivo.jpg</span>
                                        <span id="edit-file-size" class="text-gray-400">• 1.2 MB</span>
                                        <span id="edit-file-dimensions" class="text-gray-400">• 800×600</span>
                                    </div>
                                </div>
                            </div>

                            <p class="mt-2 text-xs text-gray-500 dark:text-neutral-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    JPG, PNG, WebP, GIF hasta 2MB • Min: 300×300px (opcional)
                                </span>
                            </p>

                            <!-- Mensajes de validación -->
                            <div id="edit-image-validation" class="hidden mt-2 space-y-2">
                                <div id="edit-image-error-size" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-3 h-3 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-xs font-medium text-red-800 dark:text-red-300">Archivo demasiado grande</p>
                                            <p class="text-xs text-red-600 dark:text-red-400">El tamaño máximo permitido es 2MB</p>
                                        </div>
                                    </div>
                                </div>
                                <div id="edit-image-error-type" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-3 h-3 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-xs font-medium text-red-800 dark:text-red-300">Formato no válido</p>
                                            <p class="text-xs text-red-600 dark:text-red-400">Solo se permiten archivos JPG, PNG, WebP y GIF</p>
                                        </div>
                                    </div>
                                </div>
                                <div id="edit-image-error-dimensions" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-3 h-3 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-xs font-medium text-red-800 dark:text-red-300">Dimensiones insuficientes</p>
                                            <p class="text-xs text-red-600 dark:text-red-400">Las dimensiones mínimas son 300×300 píxeles</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información básica -->
                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Información Básica
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Código del producto -->
                                <div>
                                    <label for="edit-codigo" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Código del producto
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="edit-codigo" name="codigo"
                                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                               placeholder="Ej: P0000001"
                                               maxlength="12"
                                               readonly>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        🔒 El código no se puede modificar
                                    </p>
                                </div>

                                <!-- Código auxiliar (código de barras) -->
                                <div>
                                    <label for="edit-codigo-auxiliar" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Código auxiliar (Código de barras)
                                    </label>
                                    <input type="text" id="edit-codigo-auxiliar" name="codigo_auxiliar"
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                           placeholder="Ej: 7801234567890"
                                           maxlength="25">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        📊 Código de barras o SKU alternativo
                                    </p>
                                </div>

                                <!-- Descripción -->
                                <div class="md:col-span-2">
                                    <label for="edit-descripcion" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Descripción del producto
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <textarea id="edit-descripcion" name="descripcion" rows="3"
                                                  class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none dark:bg-neutral-800 dark:border-neutral-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                  placeholder="Describe tu producto de manera clara y detallada..."
                                                  maxlength="500"></textarea>
                                        <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                                            <span id="edit-descripcion-counter">0/500</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        ✍️ Descripción clara ayuda a tus clientes y empleados
                                    </p>
                                </div>

                                <!-- Categoría -->
                                <div>
                                    <label for="edit-categoria" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Categoría
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select id="edit-categoria" name="categoria_idcategoria"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="">Seleccionar categoría</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        🏷️ Organiza tus productos por categorías
                                    </p>
                                </div>

                                <!-- Tipo de producto -->
                                <div>
                                    <label for="edit-tipo-producto" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Tipo de producto
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select id="edit-tipo-producto" name="tipo_producto"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        <option value="Producto">📦 Producto</option>
                                        <option value="Servicio">🔧 Servicio</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        ⚡ Define si es un producto físico o servicio
                                    </p>
                                </div>
                            </div>
                        </div>


                        <!-- Resto del formulario similar al modal de agregar... -->
                        <!-- Para brevedad, voy a agregar una versión condensada de las secciones restantes -->

                        <!-- Precios -->
                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Precios
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="edit-precio-venta" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Precio de venta <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" id="edit-precio-venta" name="precio_de_venta" step="0.01" min="0" max="99999.99999"
                                               class="block w-full pl-7 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                               placeholder="0.00">
                                    </div>
                                </div>

                                <div>
                                    <label for="edit-precio-compra" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Precio de compra
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" id="edit-precio-compra" name="precio_de_compra" step="0.01" min="0" max="99999.99999"
                                               class="block w-full pl-7 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                               placeholder="0.00">
                                    </div>
                                </div>

                                <!-- Unidad de medida -->
                                <div>
                                    <label for="edit-unidad-medida" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Unidad de medida
                                    </label>
                                    <select id="edit-unidad-medida" name="unidad_medida"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="Unidad">📦 Unidad</option>
                                        <option value="Pieza">🔧 Pieza</option>
                                        <option value="Par">👟 Par</option>
                                        <option value="Docena">🥚 Docena</option>
                                        <option value="Kilogramo">⚖️ Kilogramo</option>
                                        <option value="Gramo">📏 Gramo</option>
                                        <option value="Libra">🏋️ Libra</option>
                                        <option value="Onza">🥄 Onza</option>
                                        <option value="Litro">🥤 Litro</option>
                                        <option value="Mililitro">💧 Mililitro</option>
                                        <option value="Galón">🛢️ Galón</option>
                                        <option value="Metro">📐 Metro</option>
                                        <option value="Centímetro">📏 Centímetro</option>
                                        <option value="Pulgada">📌 Pulgada</option>
                                        <option value="Pie">👣 Pie</option>
                                        <option value="Metro cuadrado">⬜ Metro cuadrado</option>
                                        <option value="Centímetro cuadrado">▫️ Centímetro cuadrado</option>
                                        <option value="Hora">⏰ Hora</option>
                                        <option value="Día">📅 Día</option>
                                        <option value="Mes">🗓️ Mes</option>
                                    </select>
                                </div>

                                <!-- Peso -->
                                <div>
                                    <label for="edit-peso" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Peso (kg)
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="edit-peso" name="peso" step="0.001" min="0" max="999.999"
                                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                               placeholder="0.000">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">kg</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- IVA -->
                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Impuestos - IVA
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="edit-codigo-iva" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Código IVA <span class="text-red-500">*</span>
                                    </label>
                                    <select id="edit-codigo-iva" name="codigo_iva"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="0">0%</option>
                                        <option value="2">12%</option>
                                        <option value="3">14%</option>
                                        <option value="4" selected>15%</option>
                                        <option value="6">5%</option>
                                        <option value="7">No Objeto de Impuesto</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="edit-porcentaje-iva" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Porcentaje IVA
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="edit-porcentaje-iva" name="porcentaje_iva" step="0.01" min="0" max="100"
                                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                               placeholder="15.00" readonly>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ICE (Impuesto a los Consumos Especiales) -->
                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                </svg>
                                ICE (Impuesto Consumos Especiales)
                            </h4>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="edit-graba-ice" name="graba_ice"
                                           class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 dark:focus:ring-orange-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="edit-graba-ice" class="ml-2 text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Este producto graba ICE
                                    </label>
                                </div>

                                <div id="edit-ice-fields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="edit-codigo-ice" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Código ICE
                                        </label>
                                        <select id="edit-codigo-ice" name="codigo_ice"
                                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                            <option value="">Seleccionar código ICE</option>
                                            <option value="3011">3011 - ICE Cigarrillos Rubios</option>
                                            <option value="3021">3021 - ICE Cigarrillos Negros</option>
                                            <option value="3031">3031 - ICE Bebidas Alcohólicas</option>
                                            <option value="3041">3041 - ICE Cerveza</option>
                                            <option value="3051">3051 - ICE Bebidas Gaseosas</option>
                                            <option value="3061">3061 - ICE Perfumes y Aguas de Tocador</option>
                                            <option value="3071">3071 - ICE Videojuegos</option>
                                            <option value="3081">3081 - ICE Armas de Fuego</option>
                                            <option value="3091">3091 - ICE Focos Incandescentes</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="edit-porcentaje-ice" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Porcentaje ICE
                                        </label>
                                        <div class="relative">
                                            <input type="number" id="edit-porcentaje-ice" name="porcentaje_ice" step="0.01" min="0" max="100"
                                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                                   placeholder="0.00">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Material de Construcción -->
                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Material de Construcción
                            </h4>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="edit-es-material-construccion" name="es_material_construccion"
                                           class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 dark:focus:ring-yellow-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="edit-es-material-construccion" class="ml-2 text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Es material de construcción
                                    </label>
                                </div>

                                <div id="edit-material-fields" class="hidden">
                                    <label for="edit-codigo-material-construccion" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Código material de construcción
                                    </label>
                                    <select id="edit-codigo-material-construccion" name="codigo_material_construccion"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="">Seleccionar material</option>
                                        <option value="F010101">F010101 - VARILLA LAMINADA CORRUGADA AS42 DE 8MM, 10MM Y 12MM DE DIÁMETRO</option>
                                        <option value="F010201">F010201 - ARCILLA</option>
                                        <option value="F010202">F010202 - ARENA</option>
                                        <option value="F010203">F010203 - CAL</option>
                                        <option value="F010204">F010204 - CALIZA</option>
                                        <option value="F010205">F010205 - PÉTROS</option>
                                        <option value="F010301">F010301 - HORMIGÓN PREMEZCLADO</option>
                                        <option value="F010401">F010401 - CEMENTO Y SUS DERIVADOS</option>
                                        <option value="F010402">F010402 - RESIDUO CEMENTO</option>
                                        <option value="F010501">F010501 - CHATARRA FERROSA</option>
                                        <option value="F010601">F010601 - MORTERS</option>
                                        <option value="F010701">F010701 - CLINKER</option>
                                        <option value="F010702">F010702 - PUZOLANA</option>
                                        <option value="F010703">F010703 - YESO</option>
                                        <option value="F010801">F010801 - ADOQUÍN</option>
                                        <option value="F010802">F010802 - BLOQUES</option>
                                        <option value="F010803">F010803 - LADRILLOS</option>
                                        <option value="F010804">F010804 - PRODUCTOS DE HORMIGÓN PREFABRICADO</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Gestión de Stock -->
                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Gestión de Stock
                            </h4>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="edit-maneja-stock" name="maneja_stock"
                                           class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="edit-maneja-stock" class="ml-2 text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Manejar inventario de stock
                                    </label>
                                </div>

                                <div id="edit-stock-fields" class="hidden grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="edit-stock-actual" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Stock actual
                                        </label>
                                        <input type="number" id="edit-stock-actual" name="stock_actual" min="0"
                                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                               placeholder="0">
                                    </div>

                                    <div>
                                        <label for="edit-stock-minimo" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Stock mínimo
                                        </label>
                                        <input type="number" id="edit-stock-minimo" name="stock_minimo" min="0"
                                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                               placeholder="0">
                                    </div>

                                    <div>
                                        <label for="edit-stock-maximo" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Stock máximo
                                        </label>
                                        <input type="number" id="edit-stock-maximo" name="stock_maximo" min="0"
                                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                               placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Descuentos por Cantidad -->
                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Descuentos por Cantidad
                            </h4>

                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="edit-tiene-descuento" name="tiene_descuento"
                                           class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500 dark:focus:ring-red-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="edit-tiene-descuento" class="ml-2 text-sm font-medium text-gray-700 dark:text-neutral-300">
                                        Aplicar descuento por cantidad
                                    </label>
                                </div>

                                <div id="edit-descuento-fields" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="edit-descuento-cantidad" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Cantidad mínima para descuento
                                        </label>
                                        <input type="number" id="edit-descuento-cantidad" name="descuento_por_cantidad" min="1"
                                               class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                               placeholder="5">
                                    </div>

                                    <div>
                                        <label for="edit-precio-descuento" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                            Precio con descuento
                                        </label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <input type="number" id="edit-precio-descuento" name="precio_con_descuento" step="0.01" min="0"
                                                   class="block w-full pl-7 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white"
                                                   placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado del Producto -->
                        <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Estado del Producto
                            </h4>

                            <div class="space-y-4">
                                <div>
                                    <label for="edit-estado" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        Estado
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select id="edit-estado" name="estado"
                                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500">
                                        <option value="1">✅ Activo</option>
                                        <option value="0">❌ Inactivo</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                        🔄 Los productos inactivos no aparecen en ventas
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>
            <!-- End Body -->

            <!-- Footer -->
            <div class="flex justify-end items-center gap-x-3 py-4 px-6 border-t border-gray-200 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800">
                <button type="button" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:border-neutral-500" data-hs-overlay="#modal-editar-producto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                <button type="submit" form="formEditarProducto" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Actualizar Producto</span>
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
            const timestamp = Date.now().toString().slice(-7); // Últimos 7 dígitos del timestamp
            const codigo = `P${timestamp}`;
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
        const codigoIvaSelect = document.getElementById('codigo-iva');
        const porcentajeIvaInput = document.getElementById('porcentaje-iva');
        const grabaIceCheckbox = document.getElementById('graba-ice');
        const iceFields = document.getElementById('ice-fields');
        const esMaterialConstruccionCheckbox = document.getElementById('es-material-construccion');
        const materialConstruccionFields = document.getElementById('material-construccion-fields');

        // Mapeo de códigos IVA a porcentajes
        const ivaCodeToPercentage = {
            '0': '0.00',
            '2': '12.00',
            '3': '14.00',
            '4': '15.00',
            '5': '5.00',
            '6': '0.00', // No Objeto de Impuesto
            '7': '0.00', // Exento de IVA
            '8': '15.00', // IVA diferenciado
            '10': '13.00'
        };

        // Función para actualizar porcentaje IVA
        function updateIvaPercentage() {
            if (codigoIvaSelect && porcentajeIvaInput) {
                const selectedCode = codigoIvaSelect.value;
                const percentage = ivaCodeToPercentage[selectedCode] || '0.00';
                porcentajeIvaInput.value = percentage;

                // Animación visual para mostrar el cambio
                porcentajeIvaInput.classList.add('bg-blue-50', 'dark:bg-blue-900/20');
                setTimeout(() => {
                    porcentajeIvaInput.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
                }, 500);
            }
        }

        // Event listener para código IVA
        if (codigoIvaSelect) {
            codigoIvaSelect.addEventListener('change', updateIvaPercentage);
        }

        // Elementos para manejo de stock
        const stockActualInput = document.getElementById('stock-actual');
        const stockMinimoInput = document.getElementById('stock-minimo');
        const stockMaximoInput = document.getElementById('stock-maximo');
        const stockLevelIndicator = document.getElementById('stock-level-indicator');
        const stockBar = document.getElementById('stock-bar');
        const stockStatus = document.getElementById('stock-status');
        const minDisplay = document.getElementById('min-display');
        const maxDisplay = document.getElementById('max-display');
        const stockValidation = document.getElementById('stock-validation');
        const stockErrorMinMax = document.getElementById('stock-error-min-max');
        const stockWarningActual = document.getElementById('stock-warning-actual');
        const stockWarningMessage = document.getElementById('stock-warning-message');

        // Función para validar stock
        function validateStock() {
            const actual = parseInt(stockActualInput.value) || 0;
            const minimo = parseInt(stockMinimoInput.value) || 0;
            const maximo = parseInt(stockMaximoInput.value) || 0;

            // Ocultar todos los mensajes de validación
            stockValidation.classList.add('hidden');
            stockErrorMinMax.classList.add('hidden');
            stockWarningActual.classList.add('hidden');

            let hasError = false;
            let hasWarning = false;

            // Validar que mínimo no sea mayor que máximo
            if (maximo > 0 && minimo > maximo) {
                stockErrorMinMax.classList.remove('hidden');
                stockValidation.classList.remove('hidden');
                hasError = true;
            }

            // Validar advertencias de stock actual
            if (!hasError && maximo > 0 && minimo >= 0) {
                if (actual < minimo) {
                    stockWarningMessage.textContent = `Stock actual (${actual}) está por debajo del mínimo recomendado (${minimo})`;
                    stockWarningActual.classList.remove('hidden');
                    stockValidation.classList.remove('hidden');
                    hasWarning = true;
                } else if (actual > maximo) {
                    stockWarningMessage.textContent = `Stock actual (${actual}) supera el máximo recomendado (${maximo})`;
                    stockWarningActual.classList.remove('hidden');
                    stockValidation.classList.remove('hidden');
                    hasWarning = true;
                }
            }

            return !hasError;
        }

        // Función para actualizar indicador visual
        function updateStockIndicator() {
            const actual = parseInt(stockActualInput.value) || 0;
            const minimo = parseInt(stockMinimoInput.value) || 0;
            const maximo = parseInt(stockMaximoInput.value) || 0;

            // Mostrar el indicador solo si hay valores
            if (maximo > 0 || minimo > 0 || actual > 0) {
                stockLevelIndicator.classList.remove('hidden');

                // Actualizar displays
                minDisplay.textContent = minimo;
                maxDisplay.textContent = maximo;

                // Calcular porcentaje y color de la barra
                let percentage = 0;
                let barColor = 'bg-gray-400';
                let statusText = 'Sin configurar';
                let statusColor = 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';

                if (maximo > 0) {
                    percentage = Math.min((actual / maximo) * 100, 100);

                    if (actual < minimo) {
                        barColor = 'bg-red-500';
                        statusText = '⚠️ Stock bajo';
                        statusColor = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                    } else if (actual >= minimo && actual <= maximo * 0.7) {
                        barColor = 'bg-yellow-500';
                        statusText = '✅ Stock normal';
                        statusColor = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
                    } else if (actual <= maximo) {
                        barColor = 'bg-green-500';
                        statusText = '📈 Stock alto';
                        statusColor = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
                    } else {
                        barColor = 'bg-blue-500';
                        statusText = '📊 Sobre stock';
                        statusColor = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                    }
                }

                // Aplicar estilos
                stockBar.style.width = `${percentage}%`;
                stockBar.className = `${barColor} h-3 rounded-full transition-all duration-500 relative`;
                stockBar.innerHTML = '<div class="absolute inset-0 bg-gradient-to-r from-transparent to-white/20 rounded-full"></div>';

                stockStatus.textContent = statusText;
                stockStatus.className = `px-2 py-1 rounded-full text-xs font-medium ${statusColor}`;
            } else {
                stockLevelIndicator.classList.add('hidden');
            }
        }

        // Mostrar/ocultar campos de stock
        if (manejaStockCheckbox && stockFields) {
            manejaStockCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    stockFields.classList.remove('hidden');
                    // Inicializar indicador
                    updateStockIndicator();
                } else {
                    stockFields.classList.add('hidden');
                    stockValidation.classList.add('hidden');
                }
            });
        }

        // Event listeners para los campos de stock
        if (stockActualInput) {
            stockActualInput.addEventListener('input', function() {
                updateStockIndicator();
                validateStock();
            });
        }

        if (stockMinimoInput) {
            stockMinimoInput.addEventListener('input', function() {
                updateStockIndicator();
                validateStock();
            });
        }

        if (stockMaximoInput) {
            stockMaximoInput.addEventListener('input', function() {
                updateStockIndicator();
                validateStock();
            });
        }

        // Mostrar/ocultar campos de ICE
        if (grabaIceCheckbox && iceFields) {
            grabaIceCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    iceFields.classList.remove('hidden');
                } else {
                    iceFields.classList.add('hidden');
                    // Limpiar los campos cuando se oculta
                    const selectCodigoIce = document.getElementById('codigo-ice');
                    const inputPorcentajeIce = document.getElementById('porcentaje-ice');
                    if (selectCodigoIce) {
                        selectCodigoIce.value = '';
                    }
                    if (inputPorcentajeIce) {
                        inputPorcentajeIce.value = '';
                    }
                }
            });
        }

        // Mostrar/ocultar campos de Material de Construcción
        if (esMaterialConstruccionCheckbox && materialConstruccionFields) {
            esMaterialConstruccionCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    materialConstruccionFields.classList.remove('hidden');
                } else {
                    materialConstruccionFields.classList.add('hidden');
                    // Limpiar la selección cuando se oculta
                    const selectCodigo = document.getElementById('codigo-material-construccion');
                    if (selectCodigo) {
                        selectCodigo.value = '';
                    }
                }
            });
        }

        // Configuración avanzada de dropzone para imagen
        const imageDropzone = document.getElementById('image-dropzone');
        const imageInput = document.getElementById('producto-imagen');
        const imagePreview = document.getElementById('image-preview');
        const dropzoneContent = document.getElementById('dropzone-content');
        const removeImageBtn = document.getElementById('remove-image');
        const uploadLoading = document.getElementById('upload-loading');
        const uploadProgressBar = document.getElementById('upload-progress-bar');
        const fileInfo = document.getElementById('file-info');
        const fileName = document.getElementById('file-name');
        const fileSize = document.getElementById('file-size');
        const fileDimensions = document.getElementById('file-dimensions');
        const imageStatusBadge = document.getElementById('image-status-badge');
        const imageValidation = document.getElementById('image-validation');
        const imageErrorSize = document.getElementById('image-error-size');
        const imageErrorType = document.getElementById('image-error-type');
        const imageErrorDimensions = document.getElementById('image-error-dimensions');

        // Configuración de validación
        const imageConfig = {
            maxSize: 2 * 1024 * 1024, // 2MB
            minWidth: 300,
            minHeight: 300,
            allowedTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
            compressionQuality: 0.8,
            maxWidth: 1200,
            maxHeight: 1200
        };

        // Función para validar archivo
        function validateImageFile(file) {
            const errors = [];

            // Validar tamaño
            if (file.size > imageConfig.maxSize) {
                errors.push('size');
            }

            // Validar tipo MIME
            if (!imageConfig.allowedTypes.includes(file.type)) {
                errors.push('type');
            }

            return errors;
        }

        // Función para mostrar errores de validación
        function showImageErrors(errors) {
            imageValidation.classList.add('hidden');
            imageErrorSize.classList.add('hidden');
            imageErrorType.classList.add('hidden');
            imageErrorDimensions.classList.add('hidden');

            if (errors.length > 0) {
                imageValidation.classList.remove('hidden');
                errors.forEach(error => {
                    if (error === 'size') imageErrorSize.classList.remove('hidden');
                    if (error === 'type') imageErrorType.classList.remove('hidden');
                    if (error === 'dimensions') imageErrorDimensions.classList.remove('hidden');
                });
                imageDropzone.classList.add('image-dropzone-error');
            } else {
                imageDropzone.classList.remove('image-dropzone-error');
            }
        }

        // Función para formatear tamaño de archivo
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Función para comprimir imagen
        function compressImage(file, maxWidth, maxHeight, quality) {
            return new Promise((resolve) => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.onload = function() {
                    // Calcular nuevas dimensiones manteniendo proporción
                    let { width, height } = img;

                    if (width > height) {
                        if (width > maxWidth) {
                            height = (height * maxWidth) / width;
                            width = maxWidth;
                        }
                    } else {
                        if (height > maxHeight) {
                            width = (width * maxHeight) / height;
                            height = maxHeight;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;

                    // Dibujar imagen redimensionada
                    ctx.drawImage(img, 0, 0, width, height);

                    // Convertir a blob
                    canvas.toBlob((blob) => {
                        // Crear archivo comprimido
                        const compressedFile = new File([blob], file.name, {
                            type: file.type,
                            lastModified: Date.now()
                        });
                        resolve({
                            file: compressedFile,
                            originalSize: file.size,
                            compressedSize: blob.size,
                            dimensions: { width, height }
                        });
                    }, file.type, quality);
                };

                img.src = URL.createObjectURL(file);
            });
        }

        // Función para procesar archivo de imagen
        async function processImageFile(file) {
            // Mostrar loading
            uploadLoading.classList.remove('hidden');
            updateProgress(20);

            try {
                // Validación inicial
                const errors = validateImageFile(file);
                if (errors.length > 0) {
                    showImageErrors(errors);
                    uploadLoading.classList.add('hidden');
                    return;
                }

                updateProgress(40);

                // Obtener dimensiones originales
                const img = new Image();
                const imageLoad = new Promise((resolve, reject) => {
                    img.onload = () => resolve({ width: img.naturalWidth, height: img.naturalHeight });
                    img.onerror = reject;
                    img.src = URL.createObjectURL(file);
                });

                const originalDimensions = await imageLoad;
                updateProgress(60);

                // Validar dimensiones mínimas
                if (originalDimensions.width < imageConfig.minWidth || originalDimensions.height < imageConfig.minHeight) {
                    showImageErrors(['dimensions']);
                    uploadLoading.classList.add('hidden');
                    return;
                }

                updateProgress(80);

                // Comprimir si es necesario
                let processedFile = file;
                let finalDimensions = originalDimensions;

                if (file.size > 500 * 1024 || originalDimensions.width > imageConfig.maxWidth || originalDimensions.height > imageConfig.maxHeight) {
                    const compressed = await compressImage(file, imageConfig.maxWidth, imageConfig.maxHeight, imageConfig.compressionQuality);
                    processedFile = compressed.file;
                    finalDimensions = compressed.dimensions;
                }

                updateProgress(100);

                // Mostrar imagen
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                    imagePreview.classList.add('image-processed');
                    dropzoneContent.classList.add('hidden');
                    removeImageBtn.classList.remove('hidden');
                    imageStatusBadge.classList.remove('hidden');

                    // Mostrar información del archivo
                    fileName.textContent = file.name;
                    fileSize.textContent = `• ${formatFileSize(processedFile.size)}`;
                    fileDimensions.textContent = `• ${finalDimensions.width}×${finalDimensions.height}`;
                    fileInfo.classList.remove('hidden');

                    // Limpiar validaciones
                    showImageErrors([]);

                    setTimeout(() => {
                        uploadLoading.classList.add('hidden');
                        updateProgress(0);
                    }, 500);
                };
                reader.readAsDataURL(processedFile);

                // Actualizar input con archivo procesado
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(processedFile);
                imageInput.files = dataTransfer.files;

            } catch (error) {
                console.error('Error procesando imagen:', error);
                uploadLoading.classList.add('hidden');
                showImageErrors(['type']);
            }
        }

        // Función para actualizar barra de progreso
        function updateProgress(percent) {
            uploadProgressBar.style.width = `${percent}%`;
        }

        // Función para limpiar imagen
        function clearImage() {
            imageInput.value = '';
            imagePreview.classList.add('hidden');
            imagePreview.classList.remove('image-processed');
            dropzoneContent.classList.remove('hidden');
            removeImageBtn.classList.add('hidden');
            imageStatusBadge.classList.add('hidden');
            fileInfo.classList.add('hidden');
            uploadLoading.classList.add('hidden');
            showImageErrors([]);
            imageDropzone.classList.remove('image-dropzone-active', 'image-dropzone-error');
            updateProgress(0);
        }

        if (imageDropzone && imageInput) {
            // Click en dropzone
            imageDropzone.addEventListener('click', function() {
                if (!uploadLoading.classList.contains('hidden')) return;
                imageInput.click();
            });

            // Cambio de archivo
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    processImageFile(file);
                }
            });

            // Remover imagen
            if (removeImageBtn) {
                removeImageBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    clearImage();
                });
            }

            // Drag and drop mejorado
            imageDropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                if (!uploadLoading.classList.contains('hidden')) return;
                this.classList.add('image-dropzone-active');
            });

            imageDropzone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('image-dropzone-active');
            });

            imageDropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('image-dropzone-active');

                if (!uploadLoading.classList.contains('hidden')) return;

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    processImageFile(files[0]);
                }
            });
        }

        // Configuración del campo peso
        const pesoInput = document.getElementById('peso');
        const pesoContainer = document.getElementById('peso-container');
        const pesoUnit = document.getElementById('peso-unit');
        const pesoHelpText = document.getElementById('peso-help-text');
        const pesoValidation = document.getElementById('peso-validation');
        const pesoErrorMax = document.getElementById('peso-error-max');
        const unidadMedidaSelect = document.getElementById('unidad-medida');

        // Mapa de unidades de medida a información de peso
        const unidadPesoMap = {
            // Unidades que usan peso físico
            'Unidad': { showPeso: true, unit: 'kg', helpText: 'Peso físico del producto para cálculos de envío' },
            'Pieza': { showPeso: true, unit: 'kg', helpText: 'Peso físico de cada pieza' },
            'Par': { showPeso: true, unit: 'kg', helpText: 'Peso físico del par completo' },
            'Docena': { showPeso: true, unit: 'kg', helpText: 'Peso físico de la docena completa' },

            // Unidades de peso (redundante pero puede ser útil para conversión)
            'Kilogramo': { showPeso: false, unit: 'kg', helpText: 'La unidad ya representa peso' },
            'Gramo': { showPeso: false, unit: 'g', helpText: 'La unidad ya representa peso' },
            'Libra': { showPeso: false, unit: 'lb', helpText: 'La unidad ya representa peso' },
            'Onza': { showPeso: false, unit: 'oz', helpText: 'La unidad ya representa peso' },

            // Volumen (puede tener peso)
            'Litro': { showPeso: true, unit: 'kg', helpText: 'Peso del producto líquido por litro' },
            'Mililitro': { showPeso: true, unit: 'kg', helpText: 'Peso del producto líquido total' },
            'Galón': { showPeso: true, unit: 'kg', helpText: 'Peso del producto líquido por galón' },

            // Longitud/Área (productos físicos)
            'Metro': { showPeso: true, unit: 'kg', helpText: 'Peso por metro del material' },
            'Centímetro': { showPeso: true, unit: 'kg', helpText: 'Peso del producto físico' },
            'Pulgada': { showPeso: true, unit: 'kg', helpText: 'Peso del producto físico' },
            'Pie': { showPeso: true, unit: 'kg', helpText: 'Peso del producto físico' },
            'Metro cuadrado': { showPeso: true, unit: 'kg', helpText: 'Peso por metro cuadrado' },
            'Centímetro cuadrado': { showPeso: true, unit: 'kg', helpText: 'Peso del producto físico' },

            // Tiempo/Servicios (no tienen peso físico)
            'Hora': { showPeso: false, unit: 'kg', helpText: 'Los servicios no requieren peso físico' },
            'Día': { showPeso: false, unit: 'kg', helpText: 'Los servicios no requieren peso físico' },
            'Mes': { showPeso: false, unit: 'kg', helpText: 'Los servicios no requieren peso físico' }
        };

        // Función para validar peso
        function validatePeso() {
            const peso = parseFloat(pesoInput.value);

            // Ocultar validaciones
            pesoValidation.classList.add('hidden');
            pesoErrorMax.classList.add('hidden');

            let hasError = false;

            // Validar peso máximo (DECIMAL(8,3) = 99999.999)
            if (peso > 99999.999) {
                pesoErrorMax.classList.remove('hidden');
                pesoValidation.classList.remove('hidden');
                hasError = true;
            }

            return !hasError;
        }

        // Función para actualizar visibilidad y contexto del peso
        function updatePesoVisibility() {
            const selectedUnidad = unidadMedidaSelect.value;
            const pesoInfo = unidadPesoMap[selectedUnidad] || unidadPesoMap['Unidad'];

            if (pesoInfo.showPeso) {
                pesoContainer.style.display = 'block';
                pesoContainer.style.opacity = '1';
            } else {
                pesoContainer.style.opacity = '0.5';
                // No ocultamos completamente para mantener la consistencia del layout
            }

            // Actualizar unidad mostrada
            pesoUnit.textContent = pesoInfo.unit;

            // Actualizar texto de ayuda
            pesoHelpText.textContent = pesoInfo.helpText;

            // Actualizar placeholder según la unidad
            if (pesoInfo.showPeso) {
                pesoInput.placeholder = '0.000';
            } else {
                pesoInput.placeholder = 'No aplicable';
                pesoInput.value = ''; // Limpiar valor si no aplica
            }
        }

        // Event listeners para peso
        if (pesoInput) {
            pesoInput.addEventListener('input', function() {
                validatePeso();
            });

            pesoInput.addEventListener('blur', function() {
                validatePeso();
            });
        }

        // Event listener para cambio de unidad de medida
        if (unidadMedidaSelect) {
            unidadMedidaSelect.addEventListener('change', function() {
                updatePesoVisibility();
                validatePeso();
            });
        }

        // Inicializar estado del peso
        updatePesoVisibility();

    });
</script>