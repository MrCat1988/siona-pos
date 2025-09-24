<!-- Content -->
<div class="w-full lg:ps-64">
    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- Card -->
        <div class="flex flex-col">
            <div class="overflow-x-hidden md:overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                <div class="w-full inline-block align-middle">
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
                        <div class="px-2 py-2 sm:px-3 sm:py-3 md:px-6 md:py-4 border-b border-gray-200 dark:border-neutral-700">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 md:gap-4">
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
                                        <option value="deleted">🗑️ Solo eliminados</option>
                                        <option value="">🔄 Todos los estados</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de herramientas con toggle de vista -->
                        <div class="px-3 py-3 md:px-6 md:py-4 border-b border-gray-200 dark:border-neutral-700">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0">
                                <div class="flex items-center gap-2 md:gap-4">
                                    <span class="text-sm font-medium text-gray-700 dark:text-neutral-300">Vista:</span>
                                    <div class="flex bg-gray-100 dark:bg-neutral-800 rounded-lg p-1">
                                        <button id="btn-vista-cards" class="vista-toggle active px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm font-medium rounded-md transition-all duration-200 bg-white dark:bg-neutral-700 text-blue-600 dark:text-blue-400 shadow-sm">
                                            <svg class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            <span class="hidden sm:inline">Cards</span>
                                            <span class="sm:hidden">💳</span>
                                        </button>
                                        <button id="btn-vista-tabla" class="vista-toggle px-2 py-1.5 md:px-3 md:py-2 text-xs md:text-sm font-medium rounded-md transition-all duration-200 text-gray-500 dark:text-neutral-400 hover:text-gray-700 dark:hover:text-neutral-300">
                                            <svg class="w-3 h-3 md:w-4 md:h-4 mr-1 md:mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M8 6h13M8 18h13"></path>
                                            </svg>
                                            <span class="hidden sm:inline">Tabla</span>
                                            <span class="sm:hidden">📋</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500 dark:text-neutral-400">
                                    <span id="total-productos-sucursal">0 productos</span>
                                </div>
                            </div>
                        </div>

                        <!-- Container para las vistas -->
                        <div class="p-6">
                            <!-- Loading state -->
                            <div id="loading-productos-sucursal" class="flex items-center justify-center py-12">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <span class="ml-3 text-gray-600">Cargando inventario...</span>
                            </div>

                            <!-- Vista Cards -->
                            <div id="vista-cards" class="hidden">
                                <!-- Se llena dinámicamente con JavaScript -->
                            </div>

                            <!-- Vista Tabla -->
                            <div id="vista-tabla" class="hidden overflow-x-hidden">
                                <div class="shadow-sm md:rounded-lg overflow-hidden">
                                    <table class="w-full max-w-full divide-y divide-gray-300 dark:divide-neutral-600 table-fixed md:table-auto">
                                        <thead class="bg-gray-50 dark:bg-neutral-800">
                                            <tr>
                                                <!-- Móvil: Header con indicador de expansión -->
                                                <th scope="col" class="md:hidden px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                                    <div class="flex items-center justify-between">
                                                        <span>Productos por Sucursal</span>
                                                        <span class="text-xs font-normal normal-case text-gray-400">Toca para expandir</span>
                                                    </div>
                                                </th>

                                                <!-- Desktop: Headers compactos -->
                                                <th scope="col" class="hidden md:table-cell px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                                    Producto
                                                </th>
                                                <th scope="col" class="hidden md:table-cell w-28 px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                                    Sucursal
                                                </th>
                                                <th scope="col" class="hidden md:table-cell w-24 px-2 py-2 text-right text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                                    Precios
                                                </th>
                                                <th scope="col" class="hidden md:table-cell w-20 px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                                    Stock
                                                </th>
                                                <th scope="col" class="hidden md:table-cell w-20 px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">
                                                    Estado
                                                </th>
                                                <th scope="col" class="hidden md:table-cell w-20 relative px-2 py-2 text-center">
                                                    <span class="text-xs font-medium text-gray-500 dark:text-neutral-400 uppercase tracking-wider">Acciones</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-productos-body" class="bg-white dark:bg-neutral-900 divide-y divide-gray-200 dark:divide-neutral-700">
                                            <!-- Se llena dinámicamente con JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Controles de paginación -->
                            <div id="paginacion-controles-sucursal" class="hidden mt-6 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                                <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-neutral-400">
                                    <span>Mostrando</span>
                                    <span id="paginacion-desde-sucursal" class="font-medium text-gray-900 dark:text-white">1</span>
                                    <span>a</span>
                                    <span id="paginacion-hasta-sucursal" class="font-medium text-gray-900 dark:text-white">20</span>
                                    <span>de</span>
                                    <span id="paginacion-total-sucursal" class="font-medium text-gray-900 dark:text-white">100</span>
                                    <span>resultados</span>
                                </div>

                                <div class="flex items-center gap-1">
                                    <!-- Botón anterior -->
                                    <button id="btn-pag-anterior-sucursal" class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Números de página -->
                                    <div id="paginacion-numeros-sucursal" class="flex items-center gap-1">
                                        <!-- Se generan dinámicamente -->
                                    </div>

                                    <!-- Botón siguiente -->
                                    <button id="btn-pag-siguiente-sucursal" class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Selector de items por página -->
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500 dark:text-neutral-400">Items por página:</span>
                                    <div class="relative">
                                        <select id="items-por-pagina-sucursal" class="appearance-none bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded pl-2 pr-8 py-1 text-xs text-gray-900 dark:text-neutral-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: none;">
                                            <option value="12" selected>12</option>
                                            <option value="24">24</option>
                                            <option value="48">48</option>
                                            <option value="96">96</option>
                                        </select>
                                        <!-- Custom dropdown arrow -->
                                        <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                                            <svg class="w-3 h-3 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
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
<div id="modal-producto-sucursal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white shadow-2xl rounded-2xl pointer-events-auto dark:bg-neutral-800 dark:shadow-neutral-700/70 overflow-hidden">

            <!-- Header mejorado -->
            <div class="flex justify-between items-center py-6 px-8 bg-gradient-to-r from-purple-50 to-blue-50 dark:from-neutral-800 dark:to-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-xl dark:bg-purple-900/20">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 id="modal-titulo" class="text-xl font-bold text-gray-900 dark:text-white">
                            Asignar Producto a Sucursal
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Gestionar precio y stock específico por sucursal
                        </p>
                    </div>
                </div>
                <button type="button" id="btn-cerrar-modal" class="flex justify-center items-center w-10 h-10 text-sm font-semibold rounded-xl border border-transparent text-gray-500 hover:bg-white hover:text-gray-800 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white">
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
                <form id="form-producto-sucursal" class="space-y-6">

                    <!-- ID oculto para edición -->
                    <input type="hidden" id="idproducto_sucursal" name="idproducto_sucursal">

                    <!-- Sección: Asignación -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Asignación Producto-Sucursal
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Selección de sucursal -->
                            <div>
                                <label for="sucursal_idsucursal" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    🏪 Sucursal <span class="text-red-500">*</span>
                                </label>
                                <select id="sucursal_idsucursal" name="sucursal_idsucursal" required
                                        class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                    <option value="">Seleccionar sucursal...</option>
                                    <!-- Se cargan dinámicamente -->
                                </select>
                            </div>

                            <!-- Selección de producto con búsqueda -->
                            <div class="relative">
                                <label for="productos_idproducto_input" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    📦 Producto <span class="text-red-500">*</span>
                                </label>
                                <!-- Input oculto para el valor seleccionado -->
                                <input type="hidden" id="productos_idproducto" name="productos_idproducto" required>

                                <!-- Input de búsqueda -->
                                <div class="relative">
                                    <input type="text" id="productos_idproducto_input"
                                           placeholder="Primero selecciona una sucursal..."
                                           autocomplete="off"
                                           class="py-3 px-4 pr-10 block w-full border-gray-300 rounded-lg text-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500"
                                           readonly>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Dropdown de productos -->
                                <div id="productos-dropdown" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-neutral-800 border border-gray-300 dark:border-neutral-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <!-- Loading state -->
                                    <div id="productos-loading" class="hidden p-4 text-center">
                                        <div class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-neutral-400">
                                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-600"></div>
                                            Buscando productos...
                                        </div>
                                    </div>

                                    <!-- Lista de productos -->
                                    <div id="productos-list" class="divide-y divide-gray-200 dark:divide-neutral-700">
                                        <!-- Se llena dinámicamente -->
                                    </div>

                                    <!-- Estado vacío -->
                                    <div id="productos-empty" class="hidden p-4 text-center text-sm text-gray-500 dark:text-neutral-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-8 h-8 text-gray-300 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                            </svg>
                                            <span>No se encontraron productos</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información del producto seleccionado -->
                                <div id="producto-info" class="hidden mt-2 p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-purple-900 dark:text-purple-100" id="producto-selected-name">
                                                <!-- Nombre del producto seleccionado -->
                                            </p>
                                            <p class="text-xs text-purple-600 dark:text-purple-300" id="producto-selected-price">
                                                <!-- Precio base del producto -->
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Precios -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Gestión de Precios
                        </h4>

                        <!-- Precio en sucursal -->
                        <div>
                            <label for="precio_sucursal" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                💰 Precio en Sucursal <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 dark:text-neutral-400">$</span>
                                <input type="number" id="precio_sucursal" name="precio_sucursal"
                                       step="0.00001" min="0" max="99999.99999" placeholder="0.00000" required
                                       class="py-3 pl-8 pr-4 block w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                            </div>
                            <p class="text-xs text-gray-500 dark:text-neutral-400 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Precio específico para esta sucursal (se autocompletará con el precio base)
                            </p>
                        </div>
                    </div>

                    <!-- Sección: Stock e Inventario -->
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Stock e Inventario
                        </h4>
                        <div class="space-y-4">
                            <!-- Indicador visual del nivel de stock -->
                            <div id="stock-level-indicator-sucursal" class="hidden bg-white dark:bg-neutral-700 border border-gray-200 dark:border-neutral-600 rounded-lg p-4">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                                    <h5 class="text-sm font-medium text-gray-800 dark:text-neutral-200">Nivel de Stock en Sucursal</h5>
                                </div>
                                <div class="space-y-3">
                                    <div class="relative">
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div id="stock-bar-sucursal" class="bg-gray-400 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                                        </div>
                                        <div class="flex justify-between items-center mt-2 text-xs">
                                            <span class="text-orange-600 dark:text-orange-400">
                                                Mín: <span id="min-display-sucursal">0</span>
                                            </span>
                                            <span id="stock-status-sucursal" class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300">
                                                Configurar stock
                                            </span>
                                            <span class="text-green-600 dark:text-green-400">
                                                Máx: <span id="max-display-sucursal">0</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Stock actual -->
                                <div>
                                    <label for="stock_sucursal" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        📊 Stock Actual
                                    </label>
                                    <input type="number" id="stock_sucursal" name="stock_sucursal"
                                           min="0" max="999999" placeholder="0" step="1"
                                           class="stock-input-sucursal py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                                </div>

                                <!-- Stock mínimo -->
                                <div>
                                    <label for="stock_minimo_sucursal" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        ⚠️ Stock Mínimo
                                    </label>
                                    <input type="number" id="stock_minimo_sucursal" name="stock_minimo_sucursal"
                                           min="0" max="999999" placeholder="0" step="1"
                                           class="stock-input-sucursal py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                                </div>

                                <!-- Stock máximo -->
                                <div>
                                    <label for="stock_maximo_sucursal" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                        ✅ Stock Máximo
                                    </label>
                                    <input type="number" id="stock_maximo_sucursal" name="stock_maximo_sucursal"
                                           min="0" max="999999" placeholder="0" step="1"
                                           class="stock-input-sucursal py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500">
                                </div>
                            </div>

                            <!-- Mensajes de validación -->
                            <div id="stock-validation-sucursal" class="hidden space-y-2">
                                <div id="stock-error-min-max-sucursal" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3">
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
                                <div id="stock-warning-actual-sucursal" class="hidden bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-3">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Advertencia de inventario</p>
                                            <p class="text-xs text-yellow-600 dark:text-yellow-400" id="stock-warning-message-sucursal">El stock actual está fuera del rango recomendado</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Estado -->
                    <div class="bg-gradient-to-r from-gray-50 to-slate-50 dark:from-neutral-800/50 dark:to-neutral-800/50 rounded-xl p-6 border border-gray-200 dark:border-neutral-700">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-600 dark:text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Estado de la Asignación
                        </h4>
                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                🔄 Estado
                            </label>
                            <select id="estado" name="estado"
                                    class="py-3 px-4 block w-full border-gray-300 rounded-lg text-sm focus:border-gray-500 focus:ring-gray-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                <option value="1">✅ Activo - Producto disponible en sucursal</option>
                                <option value="0">❌ Inactivo - Producto no disponible temporalmente</option>
                            </select>
                        </div>
                    </div>

                </form>
            </div>
            <!-- End Body -->

            <!-- Footer -->
            <div class="flex justify-end items-center gap-x-3 py-4 px-8 bg-gray-50 dark:bg-neutral-800/50">
                <button type="button" id="btn-cancelar-modal"
                        class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:border-neutral-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                <button type="button" id="btn-guardar-producto-sucursal"
                        class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gradient-to-r from-purple-600 to-blue-600 text-white shadow-lg hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span id="texto-boton-guardar">Asignar Producto</span>
                </button>
            </div>
            <!-- End Footer -->

        </div>
    </div>
</div>
</div>

<!-- Notificaciones Toast -->
<div id="toast-container" class="fixed top-4 right-4 z-[100] space-y-2"></div>

<!-- Script -->
<script src="views/js/producto-sucursal.js"></script>