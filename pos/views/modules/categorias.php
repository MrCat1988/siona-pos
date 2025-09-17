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
                                    Categorías
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    Agregar categorías, editar y más.
                                </p>
                            </div>

                            <div>
                                <div class="inline-flex gap-x-2">
                                    <button id="btn-ver-todas" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <span id="btn-ver-todas-text">Ver todas</span>
                                    </button>

                                    <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" data-hs-overlay="#modal-agregar-categoria">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                        Agregar categoría
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Controles de filtros y búsqueda -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Búsqueda -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="search" id="buscar-categoria" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-gray-400 dark:text-white" placeholder="Buscar por nombre o descripción...">
                                </div>

                                <!-- Columna vacía para mantener el layout -->
                                <div></div>

                                <!-- Filtro por estado -->
                                <div>
                                    <select id="filtro-estado" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="1" selected>✅ Solo activas</option>
                                        <option value="0">❌ Solo inactivas</option>
                                        <option value="">🔄 Todos los estados</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Container para las cards -->
                        <div class="p-6">
                            <!-- Loading state -->
                            <div id="categorias-loading" class="flex items-center justify-center py-12">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <span class="ml-3 text-gray-600">Cargando categorías...</span>
                            </div>

                            <!-- Grid de cards -->
                            <div id="categorias-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
                                <!-- Las cards se cargarán aquí dinámicamente -->
                            </div>

                            <!-- Estado vacío -->
                            <div id="categorias-empty" class="hidden text-center py-16">
                                <!-- Ilustración de categoría vacía -->
                                <div class="mx-auto mb-8">
                                    <div class="relative">
                                        <!-- Contenedor principal -->
                                        <div class="mx-auto w-32 h-32 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-2xl shadow-lg flex flex-col items-center justify-center border-2 border-dashed border-blue-300 dark:border-blue-700">
                                            <!-- Grid de categorías -->
                                            <div class="grid grid-cols-2 gap-2">
                                                <div class="w-8 h-8 bg-blue-300 dark:bg-blue-600/50 rounded-lg flex items-center justify-center">
                                                    <div class="w-3 h-3 bg-blue-500 dark:bg-blue-400 rounded"></div>
                                                </div>
                                                <div class="w-8 h-8 bg-blue-300 dark:bg-blue-600/50 rounded-lg flex items-center justify-center">
                                                    <div class="w-3 h-3 bg-blue-500 dark:bg-blue-400 rounded"></div>
                                                </div>
                                                <div class="w-8 h-8 bg-blue-300 dark:bg-blue-600/50 rounded-lg flex items-center justify-center">
                                                    <div class="w-3 h-3 bg-blue-500 dark:bg-blue-400 rounded"></div>
                                                </div>
                                                <div class="w-8 h-8 bg-blue-300 dark:bg-blue-600/50 rounded-lg flex items-center justify-center">
                                                    <div class="w-3 h-3 bg-blue-500 dark:bg-blue-400 rounded"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Elementos flotantes -->
                                        <div class="absolute -top-2 -right-2 w-6 h-6 bg-blue-500 rounded-full animate-bounce opacity-60"></div>
                                        <div class="absolute -bottom-1 -left-1 w-4 h-4 bg-blue-400 rounded-full animate-pulse opacity-40"></div>
                                    </div>
                                </div>

                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                                    No hay categorías disponibles
                                </h3>
                                <p class="text-gray-600 dark:text-neutral-400 mb-8">
                                    Organiza tus productos creando categorías. Esto te ayudará a gestionar mejor tu inventario.
                                </p>
                                <button type="button" class="inline-flex items-center gap-x-2 py-3 px-6 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200" data-hs-overlay="#modal-agregar-categoria">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                            Crear Primera Categoría
                        </button>
                    </div>

                    <!-- Grid de Categorías -->
                    <div id="categorias-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
                        <!-- Las cards se generarán dinámicamente -->
                    </div>
                </div>

                <!-- Footer con paginación -->
                <div class="bg-gray-50 dark:bg-neutral-800/50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500 dark:text-neutral-400">
                            <span id="pagination-info">Mostrando 0 de 0 resultados</span>
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
        <!-- End Card -->
    </div>
</div>
<!-- End Content -->

<!-- Modal Agregar Categoría -->
<div id="modal-agregar-categoria" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white shadow-2xl rounded-2xl pointer-events-auto dark:bg-neutral-800 dark:shadow-neutral-700/70 overflow-hidden">
            <!-- Header mejorado -->
            <div class="flex justify-between items-center py-6 px-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-neutral-800 dark:to-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl dark:bg-blue-900/20">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4zM14 4h6v6h-6V4zM4 14h6v6H4v-6zM14 14h6v6h-6v-6z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            Agregar Categoría
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Crear una nueva categoría de productos
                        </p>
                    </div>
                </div>
                <button type="button" class="flex justify-center items-center w-10 h-10 text-sm font-semibold rounded-xl border border-transparent text-gray-500 hover:bg-white hover:text-gray-800 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" data-hs-overlay="#modal-agregar-categoria">
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
                <form id="form-agregar-categoria" method="POST" class="space-y-6">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                    <!-- Sección: Información de la categoría -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4zM14 4h6v6h-6V4zM4 14h6v6H4v-6zM14 14h6v6h-6v-6z"></path>
                            </svg>
                            Información de la Categoría
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="nombre-categoria" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Nombre de la categoría <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nombre-categoria" name="nombre" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                       placeholder="Ej: Bebidas, Comida, Electrónicos">
                            </div>
                            <div class="md:col-span-2">
                                <label for="descripcion-categoria" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Descripción
                                </label>
                                <textarea id="descripcion-categoria" name="descripcion" rows="3"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                          placeholder="Descripción opcional de la categoría..."></textarea>
                            </div>
                            <div>
                                <label for="estado-categoria" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select id="estado-categoria" name="estado" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                    <option value="1">✅ Activa</option>
                                    <option value="0">❌ Inactiva</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End Body -->

            <!-- Footer -->
            <div class="flex justify-end items-center gap-x-3 py-4 px-8 bg-gray-50 dark:bg-neutral-800/50">
                <button type="button" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:border-neutral-500" data-hs-overlay="#modal-agregar-categoria">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                <button type="submit" form="form-agregar-categoria" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Crear Categoría</span>
                </button>
            </div>
            <!-- End Footer -->
        </div>
    </div>
</div>
<!-- End Modal Agregar Categoría -->

<!-- Modal Editar Categoría -->
<div id="modal-editar-categoria" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white shadow-2xl rounded-2xl pointer-events-auto dark:bg-neutral-800 dark:shadow-neutral-700/70 overflow-hidden">
            <!-- Header mejorado -->
            <div class="flex justify-between items-center py-6 px-8 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-neutral-800 dark:to-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-amber-100 rounded-xl dark:bg-amber-900/20">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            Editar Categoría
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Modificar información de la categoría
                        </p>
                    </div>
                </div>
                <button type="button" class="flex justify-center items-center w-10 h-10 text-sm font-semibold rounded-xl border border-transparent text-gray-500 hover:bg-white hover:text-gray-800 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" data-hs-overlay="#modal-editar-categoria">
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
                <!-- Loading state -->
                <div id="editar-loading" class="text-center py-16">
                    <div class="w-20 h-20 mx-auto bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center mb-6 shadow-lg dark:from-amber-900/20 dark:to-orange-900/20">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Cargando datos de la categoría...</h3>
                    <p class="text-gray-600 dark:text-neutral-400">
                        Obteniendo información actual de la categoría para edición.
                    </p>
                </div>

                <!-- Formulario de edición (oculto inicialmente) -->
                <form id="form-editar-categoria" method="POST" class="space-y-6 hidden">
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input type="hidden" id="editar-categoria-id" name="idcategoria">

                    <!-- Sección: Información de la categoría -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4zM14 4h6v6h-6V4zM4 14h6v6H4v-6zM14 14h6v6h-6v-6z"></path>
                            </svg>
                            Información de la Categoría
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="editar-nombre-categoria" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Nombre de la categoría <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="editar-nombre-categoria" name="nombre" required
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                       placeholder="Ej: Bebidas, Comida, Electrónicos">
                            </div>
                            <div class="md:col-span-2">
                                <label for="editar-descripcion-categoria" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Descripción
                                </label>
                                <textarea id="editar-descripcion-categoria" name="descripcion" rows="3"
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                          placeholder="Descripción opcional de la categoría..."></textarea>
                            </div>
                            <div>
                                <label for="editar-estado-categoria" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select id="editar-estado-categoria" name="estado" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-amber-500 focus:border-amber-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                    <option value="1">✅ Activa</option>
                                    <option value="0">❌ Inactiva</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End Body -->

            <!-- Footer -->
            <div class="flex justify-end items-center gap-x-3 py-4 px-8 bg-gray-50 dark:bg-neutral-800/50">
                <button type="button" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:border-neutral-500" data-hs-overlay="#modal-editar-categoria">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                <button type="submit" form="form-editar-categoria" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg hover:from-amber-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Actualizar Categoría</span>
                </button>
            </div>
            <!-- End Footer -->
        </div>
    </div>
</div>
<!-- End Modal Editar Categoría -->

<script>
    // Pasar datos de sesión al JavaScript
    window.TENANT_ID = <?php echo isset($_SESSION['tenant_id']) ? $_SESSION['tenant_id'] : 'null'; ?>;
    window.currentUserId = <?php echo isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 'null'; ?>;
</script>

<script src="views/js/categorias.js"></script>