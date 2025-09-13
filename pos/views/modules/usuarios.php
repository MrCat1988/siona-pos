<!-- Content -->
<div class="w-full lg:ps-64">
    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- Grid -->
        <!-- <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        </div> -->
        <!-- End Grid -->

        <?php
        // echo "<pre>";
        //     var_dump($_SESSION);
        // echo "</pre>";
        ?>


        <!-- Card -->
        <div class="flex flex-col">
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                <div class="min-w-full inline-block align-middle">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
                        <!-- Header -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 dark:border-neutral-700">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                                    Usuarios
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    Agregar usuarios, editar y más.
                                </p>
                            </div>

                            <div>
                                <div class="inline-flex gap-x-2">
                                    <a class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800" href="#">
                                        Ver todos
                                    </a>

                                    <button type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" data-hs-overlay="#modal-agregar-usuario">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12h14" />
                                            <path d="M12 5v14" />
                                        </svg>
                                        Agregar usuario
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
                                    <input type="search" id="buscar-usuario" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-gray-400 dark:text-white" placeholder="Buscar por nombre, email o cargo...">
                                </div>
                                
                                <!-- Filtro por rol -->
                                <div>
                                    <select id="filtro-rol" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="">Todos los roles</option>
                                        <option value="Administrador">👑 Administrador</option>
                                        <option value="Vendedor">💼 Vendedor</option>
                                        <option value="Visualizador">👁️ Visualizador</option>
                                    </select>
                                </div>
                                
                                <!-- Filtro por estado -->
                                <div>
                                    <select id="filtro-estado" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                        <option value="1">✅ Solo activos</option>
                                        <option value="0">❌ Solo inactivos</option>
                                        <option value="">🔄 Todos los estados</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Container para las cards -->
                        <div class="p-6">
                            <!-- Loading state -->
                            <div id="usuarios-loading" class="flex items-center justify-center py-12">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                <span class="ml-3 text-gray-600">Cargando usuarios...</span>
                            </div>
                            
                            <!-- Grid de cards -->
                            <div id="usuarios-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 hidden">
                                <!-- Las cards se cargarán aquí dinámicamente -->
                            </div>
                            
                            <!-- Estado vacío -->
                            <div id="usuarios-empty" class="hidden text-center py-12">
                                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gray-100 dark:bg-neutral-800 mb-4">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                                        <!-- Línea cruzada para indicar "no encontrado" -->
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 3 18 18" opacity="0.5"/>
                                    </svg>
                                </div>
                                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No se encontraron usuarios</h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-neutral-400">No hay usuarios que coincidan con los filtros aplicados. Prueba ajustando los criterios de búsqueda.</p>
                                <div class="mt-4">
                                    <button id="limpiar-filtros" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition-colors duration-200 dark:text-blue-400 dark:bg-blue-900/20 dark:hover:bg-blue-900/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Limpiar filtros
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 dark:border-neutral-700">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    <span class="font-semibold text-gray-800 dark:text-neutral-200">12</span> results
                                </p>
                            </div>

                            <div>
                                <div class="inline-flex gap-x-2">
                                    <button type="button" class="py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m15 18-6-6 6-6" />
                                        </svg>
                                        Prev
                                    </button>

                                    <button type="button" class="py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-2xs hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden focus:bg-gray-50 dark:bg-transparent dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:focus:bg-neutral-800">
                                        Next
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

<!-- Modal Agregar Usuario -->
<div id="modal-agregar-usuario" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">
        <div class="flex flex-col bg-white shadow-2xl rounded-2xl pointer-events-auto dark:bg-neutral-800 dark:shadow-neutral-700/70 overflow-hidden">
            <!-- Header mejorado -->
            <div class="flex justify-between items-center py-6 px-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-neutral-800 dark:to-neutral-800">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl dark:bg-blue-900/20">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            Agregar Usuario
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Crear un nuevo usuario en el sistema
                        </p>
                    </div>
                </div>
                <button type="button" class="flex justify-center items-center w-10 h-10 text-sm font-semibold rounded-xl border border-transparent text-gray-500 hover:bg-white hover:text-gray-800 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" data-hs-overlay="#modal-agregar-usuario">
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
                <form id="formAgregarUsuario" class="space-y-6">
                    <!-- Sección: Foto de perfil -->
                    <div class="text-center">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Foto de perfil</h4>
                        <div id="dropzone-container" class="mx-auto w-32 h-32 relative group cursor-pointer">
                            <!-- Dropzone para imagen -->
                            <div id="image-dropzone" class="w-full h-full border-2 border-dashed border-gray-300 rounded-full flex items-center justify-center bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-600 dark:hover:bg-neutral-700">
                                <div id="dropzone-content" class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">Subir foto</p>
                                </div>
                                <img id="image-preview" class="hidden w-full h-full object-cover rounded-full" />
                            </div>
                            <!-- Input oculto para archivos -->
                            <input type="file" id="usuario-imagen" name="usuario_imagen" accept="image/*" class="hidden">
                            <!-- Botón para quitar imagen -->
                            <button type="button" id="remove-image" class="hidden absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-neutral-400">JPG, PNG hasta 2MB</p>
                    </div>

                    <!-- Sección: Información personal -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Información Personal
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="nombre-usuario" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Nombre completo <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nombre-usuario" name="nombre" required 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                       placeholder="Ej: Juan Pérez Gómez">
                            </div>
                            <div>
                                <label for="cargo-usuario" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Cargo <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="cargo-usuario" name="cargo" required maxlength="45"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                       placeholder="Ej: Gerente de ventas">
                            </div>
                        </div>
                    </div>

                    <!-- Sección: Información de contacto -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Información de Contacto
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="email-usuario" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email-usuario" name="email" required maxlength="150"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                       placeholder="Ej: juan.perez@empresa.com">
                            </div>
                            <div>
                                <label for="telefono-usuario" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Teléfono <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="telefono-usuario" name="telefono" required maxlength="13"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                       placeholder="Ej: 0999999999">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="direccion-usuario" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                Dirección
                            </label>
                            <input type="text" id="direccion-usuario" name="direccion" maxlength="150" value="Quito"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                   placeholder="Ej: Av. Amazonas y Naciones Unidas">
                        </div>
                    </div>

                    <!-- Sección: Acceso al sistema -->
                    <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Acceso al Sistema
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="rol-usuario" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Rol <span class="text-red-500">*</span>
                                </label>
                                <select id="rol-usuario" name="rol" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                    <option value="">Seleccionar rol</option>
                                    <option value="Administrador">👑 Administrador</option>
                                    <option value="Vendedor">💼 Vendedor</option>
                                    <option value="Visualizador">👁️ Visualizador</option>
                                </select>
                            </div>
                            <div>
                                <label for="sucursal-usuario" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                    Sucursal <span class="text-red-500">*</span>
                                </label>
                                <select id="sucursal-usuario" name="sucursal_idsucursal" required
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white">
                                    <option value="">Cargando sucursales...</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="password-usuario" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" id="password-usuario" name="password" required
                                       class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:placeholder-neutral-400 dark:text-white"
                                       placeholder="Contraseña segura">
                                <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600">
                                    <!-- Icono ojo cerrado (contraseña oculta) -->
                                    <svg id="eye-closed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88"></path>
                                    </svg>
                                    <!-- Icono ojo abierto (contraseña visible) -->
                                    <svg id="eye-open" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-neutral-400">
                                Mínimo 8 caracteres, incluye mayúsculas, minúsculas, números y caracteres especiales
                            </p>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End Body -->

            <!-- Footer -->
            <div class="flex justify-end items-center gap-x-3 py-4 px-8 bg-gray-50 dark:bg-neutral-800/50">
                <button type="button" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:border-neutral-500" data-hs-overlay="#modal-agregar-usuario">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                <button type="button" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Crear Usuario</span>
                </button>
            </div>
            <!-- End Footer -->
        </div>
    </div>
</div>
<!-- End Modal Agregar Usuario -->

<!-- Modal Editar Usuario -->
<div id="modal-editar-usuario" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
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
                            Editar Usuario
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Modificar información del usuario
                        </p>
                    </div>
                </div>
                <button type="button" class="flex justify-center items-center w-10 h-10 text-sm font-semibold rounded-xl border border-transparent text-gray-500 hover:bg-white hover:text-gray-800 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white" data-hs-overlay="#modal-editar-usuario">
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
                <div class="text-center py-16">
                    <div class="w-20 h-20 mx-auto bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center mb-6 shadow-lg dark:from-amber-900/20 dark:to-orange-900/20">
                        <svg class="w-10 h-10 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Editar Usuario</h3>
                    <p class="text-gray-600 dark:text-neutral-400 max-w-sm mx-auto leading-relaxed mb-6">
                        Aquí se mostrará el formulario para editar la información de este usuario con todos sus datos actuales.
                    </p>
                    
                    <!-- Info del usuario -->
                    <div class="inline-flex items-center gap-3 px-4 py-3 bg-amber-50 rounded-lg border border-amber-100 dark:bg-amber-900/10 dark:border-amber-800/20">
                        <div class="flex items-center justify-center w-8 h-8 bg-amber-100 rounded-full dark:bg-amber-900/20">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Usuario seleccionado</p>
                            <p class="text-xs text-amber-600 dark:text-amber-400">ID: <span id="usuario-id-display" class="font-mono font-semibold">-</span></p>
                        </div>
                    </div>
                    
                    <div class="mt-8 p-4 bg-amber-50 rounded-lg border border-amber-100 dark:bg-amber-900/10 dark:border-amber-800/20">
                        <div class="flex items-center justify-center gap-2 text-amber-700 dark:text-amber-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm font-medium">Formulario en desarrollo</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Body -->

            <!-- Footer -->
            <div class="flex justify-end items-center gap-x-3 py-4 px-8 bg-gray-50 dark:bg-neutral-800/50">
                <button type="button" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-medium rounded-xl border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:border-neutral-500" data-hs-overlay="#modal-editar-usuario">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar
                </button>
                <button type="button" class="py-3 px-6 inline-flex items-center gap-x-2 text-sm font-semibold rounded-xl border border-transparent bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-lg hover:from-amber-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none transition-all duration-200 transform hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Actualizar Usuario</span>
                </button>
            </div>
            <!-- End Footer -->
        </div>
    </div>
</div>
<!-- End Modal Editar Usuario -->