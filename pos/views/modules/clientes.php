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
                                    Clientes
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    Agregar clientes, editar y más.
                                </p>
                            </div>

                            <div>
                                <div class="inline-flex gap-x-2">
                                    <button type="button" id="btn-nuevo-cliente" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" data-hs-overlay="#modal-cliente">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                        Agregar cliente
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Controles de filtros y búsqueda -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Búsqueda -->
                                <div class="md:col-span-2 relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="search" id="buscar-cliente" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-gray-400 dark:text-white" placeholder="Buscar por nombre, apellido, identificación o email...">
                                </div>

                                <!-- Filtro por tipo de identificación -->
                                <div>
                                    <select id="filtro-tipo-identificacion" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="">Todos los tipos</option>
                                        <option value="04">RUC</option>
                                        <option value="05">Cédula</option>
                                        <option value="06">Pasaporte</option>
                                        <option value="08">Identificación del Exterior</option>
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

                            <!-- Contador y botón limpiar -->
                            <div class="flex justify-between items-center mt-4">
                                <div class="text-sm text-gray-600 dark:text-neutral-400">
                                    <span id="total-clientes">0</span> clientes encontrados
                                </div>
                                <button id="btn-limpiar-filtros" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                    Limpiar filtros
                                </button>
                            </div>
                        </div>

                        <!-- Tabla -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                                <thead class="bg-gray-50 dark:bg-neutral-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                Tipo ID
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                Identificación
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                Nombres y Apellidos
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                Email
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                Teléfono
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                Estado
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-center">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-neutral-200">
                                                Acciones
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-clientes-body" class="divide-y divide-gray-200 dark:divide-neutral-700">
                                    <!-- Loading inicial -->
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex items-center justify-center">
                                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                                <span class="ml-3 text-gray-600 dark:text-neutral-400">Cargando clientes...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
                            <div class="inline-flex items-center gap-x-2">
                                <span class="text-sm text-gray-600 dark:text-neutral-400">Registros por página:</span>
                                <select id="registros-por-pagina" class="py-1.5 px-2 pr-9 block border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400">
                                    <option value="25">25</option>
                                    <option value="50" selected>50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>

                            <div id="paginacion-botones" class="inline-flex gap-x-2">
                                <!-- Los botones de paginación se generarán dinámicamente -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
</div>
<!-- End Content -->

<!-- Modal Agregar/Editar Cliente -->
<div id="modal-cliente" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-3xl sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white shadow-2xl rounded-2xl pointer-events-auto dark:bg-neutral-800 dark:shadow-neutral-700/70 overflow-hidden">
            <!-- Header mejorado -->
            <div class="flex justify-between items-center py-6 px-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-neutral-800 dark:to-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl dark:bg-blue-900/20">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="modal-cliente-label" class="text-xl font-bold text-gray-900 dark:text-white">
                            Nuevo Cliente
                        </h3>
                        <p id="modal-cliente-subtitle" class="text-sm text-gray-600 dark:text-neutral-400">
                            Crear un nuevo cliente en el sistema
                        </p>
                    </div>
                </div>
                <button type="button" class="flex justify-center items-center w-10 h-10 text-sm font-semibold rounded-xl border border-transparent text-gray-500 hover:bg-white hover:text-gray-800 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" data-hs-overlay="#modal-cliente">
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
                <form id="form-cliente" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" id="idcliente" name="idcliente">
                    <input type="hidden" id="accion-modal" value="crear">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tipo de identificación -->
                        <div>
                            <label for="tipo_identificacion_sri" class="block text-sm font-medium mb-2 dark:text-white">
                                Tipo de Identificación <span class="text-red-500">*</span>
                            </label>
                            <select id="tipo_identificacion_sri" name="tipo_identificacion_sri" required class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                                <option value="04">RUC</option>
                                <option value="05" selected>Cédula</option>
                                <option value="06">Pasaporte</option>
                                <option value="08">Identificación del Exterior</option>
                            </select>
                        </div>

                        <!-- Número de identificación -->
                        <div>
                            <label for="numero_identificacion" class="block text-sm font-medium mb-2 dark:text-white">
                                Número de Identificación <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="numero_identificacion" name="numero_identificacion" required maxlength="13" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="Ej: 1234567890">
                        </div>

                        <!-- Nombres -->
                        <div>
                            <label for="nombres" class="block text-sm font-medium mb-2 dark:text-white">
                                Nombres <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nombres" name="nombres" required class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="Ej: Juan Carlos">
                        </div>

                        <!-- Apellidos -->
                        <div>
                            <label for="apellidos" class="block text-sm font-medium mb-2 dark:text-white">
                                Apellidos <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="apellidos" name="apellidos" required class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="Ej: Pérez García">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2 dark:text-white">
                                Email
                            </label>
                            <input type="email" id="email" name="email" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="ejemplo@correo.com">
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block text-sm font-medium mb-2 dark:text-white">
                                Teléfono
                            </label>
                            <input type="text" id="telefono" name="telefono" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="Ej: 0987654321">
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <label for="direccion" class="block text-sm font-medium mb-2 dark:text-white">
                                Dirección <span class="text-xs text-gray-500">(Por defecto: Quito)</span>
                            </label>
                            <textarea id="direccion" name="direccion" rows="2" class="py-3 px-4 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="Ej: Av. Principal 123 y Calle Secundaria (Si está vacío se asignará 'Quito')"></textarea>
                        </div>

                        <!-- Estado -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="estado" name="estado" value="1" checked class="shrink-0 mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800">
                                <label for="estado" class="text-sm text-gray-500 ms-3 dark:text-neutral-400">Cliente Activo</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer mejorado -->
            <div class="flex justify-end items-center gap-x-3 py-6 px-8 bg-gray-50 dark:bg-neutral-900 border-t border-gray-200 dark:border-neutral-700">
                <button type="button" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:border-neutral-500" data-hs-overlay="#modal-cliente">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                <button type="button" id="btn-guardar-cliente" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span id="btn-guardar-texto">Guardar Cliente</span>
                </button>
            </div>
            <!-- End Footer -->
        </div>
    </div>
</div>
