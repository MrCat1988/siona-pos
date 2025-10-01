<!-- Content -->
<div class="w-full lg:ps-64">
    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">

        <!-- Card -->
        <div class="flex flex-col">
            <div class="overflow-x-auto [&::-webkit-scrollbar]:h-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                <div class="min-w-full inline-block align-middle">
                    <div class="bg-white border border-gray-200 rounded-xl shadow-2xs overflow-hidden dark:bg-neutral-800 dark:border-neutral-700">
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-neutral-700">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-neutral-200">
                                    Mi Empresa
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-neutral-400">
                                    Gestiona la información legal y configuración tributaria de tu negocio.
                                </p>
                            </div>
                        </div>
                        <!-- End Header -->

                        <!-- Formulario -->
                        <div class="p-6">
                            <form id="form-empresa">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                                <input type="hidden" id="idempresa_tenant" name="idempresa_tenant">

                                <!-- Sección: Información Legal -->
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 mb-6 border border-blue-200 dark:border-blue-800">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Información Legal
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- RUC (Solo Lectura) -->
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                RUC
                                            </label>
                                            <div class="flex items-center gap-3 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/10 dark:to-indigo-900/10 border border-blue-200 dark:border-blue-800 rounded-lg">
                                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-grow">
                                                    <div class="flex items-center gap-2">
                                                        <span id="ruc-display" class="text-2xl font-bold text-gray-900 dark:text-white tracking-wide">-</span>
                                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            No editable
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Registro Único de Contribuyentes</p>
                                                </div>
                                            </div>
                                            <input type="hidden" id="ruc" name="ruc" required>
                                        </div>

                                        <!-- Razón Social -->
                                        <div class="md:col-span-2">
                                            <label for="razon_social" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Razón Social <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="razon_social" name="razon_social" required maxlength="150"
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all"
                                                   placeholder="Ingrese la razón social de la empresa">
                                        </div>

                                        <!-- Nombre Comercial -->
                                        <div>
                                            <label for="nombre_comercial" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Nombre Comercial
                                            </label>
                                            <input type="text" id="nombre_comercial" name="nombre_comercial" maxlength="100"
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all"
                                                   placeholder="Nombre comercial del negocio">
                                        </div>

                                        <!-- Tipo de Contribuyente (Solo Lectura) -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Tipo de Contribuyente
                                            </label>
                                            <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-900/10 dark:to-slate-900/10 border border-gray-200 dark:border-gray-700 rounded-lg">
                                                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-900/30 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-grow">
                                                    <span id="tipo-contibuyente-display" class="text-sm font-semibold text-gray-900 dark:text-white">Persona Natural</span>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Si desea realizar el cambio contacte al administrador</p>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Bloqueado
                                                </span>
                                            </div>
                                            <input type="hidden" id="tipo_contibuyente" name="tipo_contibuyente" value="Persona natural" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Ubicación y Contacto -->
                                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 mb-6 border border-green-200 dark:border-green-800">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Ubicación y Contacto
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Dirección Matriz -->
                                        <div class="md:col-span-2">
                                            <label for="direccion_matriz" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Dirección Matriz
                                            </label>
                                            <input type="text" id="direccion_matriz" name="direccion_matriz" maxlength="150"
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all"
                                                   placeholder="Av. Principal y Calle Secundaria">
                                        </div>

                                        <!-- Teléfono -->
                                        <div>
                                            <label for="telefono" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Teléfono
                                            </label>
                                            <input type="text" id="telefono" name="telefono" maxlength="13"
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all"
                                                   placeholder="0999999999">
                                        </div>

                                        <!-- Email -->
                                        <div>
                                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Email
                                            </label>
                                            <input type="email" id="email" name="email" maxlength="50"
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all"
                                                   placeholder="contacto@empresa.com">
                                        </div>

                                        <!-- Actividad Económica -->
                                        <div class="md:col-span-2">
                                            <label for="actividad_economica" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Actividad Económica
                                            </label>
                                            <input type="text" id="actividad_economica" name="actividad_economica" maxlength="200"
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all"
                                                   placeholder="Comercio al por menor">
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Configuración Tributaria -->
                                <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-6 mb-6 border border-purple-200 dark:border-purple-800">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        Configuración Tributaria
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Régimen -->
                                        <div>
                                            <label for="regimen" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Régimen Tributario
                                            </label>
                                            <select id="regimen" name="regimen"
                                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all">
                                                <option value="Regimen general">Régimen General</option>
                                                <option value="Rimpe negocio popular">RIMPE Negocio Popular</option>
                                                <option value="Rimpe emprendedor">RIMPE Emprendedor</option>
                                            </select>
                                        </div>

                                        <!-- Ambiente SRI -->
                                        <div>
                                            <label for="ambiente_sri" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Ambiente SRI
                                            </label>
                                            <select id="ambiente_sri" name="ambiente_sri"
                                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all">
                                                <option value="Pruebas">Pruebas</option>
                                                <option value="Produccion">Producción</option>
                                            </select>
                                        </div>

                                        <!-- Checkboxes de características tributarias -->
                                        <div class="md:col-span-2">
                                            <!-- Campos ocultos: No disponibles en versión inicial (configurables desde panel admin) -->
                                            <input type="hidden" id="contabilidad" name="contabilidad" value="0">
                                            <input type="hidden" id="agente_retencion" name="agente_retencion" value="0">
                                            <input type="hidden" id="contribuyente_especial" name="contribuyente_especial" value="0">

                                            <!-- Campo Artesano -->
                                            <div class="p-4 bg-white dark:bg-neutral-800/50 rounded-lg border border-gray-200 dark:border-neutral-700">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <label for="artesano" class="text-sm font-semibold text-gray-900 dark:text-white cursor-pointer">
                                                                Calificación Artesanal
                                                            </label>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">Activa si posees calificación del MIPRO</p>
                                                        </div>
                                                    </div>
                                                    <label for="artesano" class="inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" id="artesano" name="artesano" value="1" class="sr-only">
                                                        <div id="artesano-switch-bg" class="relative w-11 h-6 bg-gray-200 rounded-full transition-colors dark:bg-gray-700">
                                                            <div id="artesano-switch-toggle" class="absolute top-0.5 left-0.5 bg-white border border-gray-300 rounded-full h-5 w-5 transition-transform duration-200 dark:border-gray-600"></div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Número Calificación Artesanal -->
                                        <div id="campo-calificacion-artesanal" class="md:col-span-2 hidden">
                                            <label for="numero_calificacion_artesanal" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Número de Calificación Artesanal
                                            </label>
                                            <input type="text" id="numero_calificacion_artesanal" name="numero_calificacion_artesanal" maxlength="13"
                                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all"
                                                   placeholder="Número de calificación artesanal">
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Configuración de Correo (Solo Lectura) -->
                                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-6 mb-6 border border-amber-200 dark:border-amber-800">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Configuración de Correo para Facturación
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Correo Envío Factura (Solo Lectura) -->
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Correo de Envío
                                            </label>
                                            <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 border border-amber-200 dark:border-amber-700 rounded-lg">
                                                <div class="flex-shrink-0 w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-grow">
                                                    <span id="correo-envio-display" class="text-sm font-semibold text-gray-900 dark:text-white">No configurado</span>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Configurable desde panel admin</p>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Bloqueado
                                                </span>
                                            </div>
                                            <input type="hidden" id="correo_envio_factura" name="correo_envio_factura">
                                        </div>

                                        <!-- Servidor SMTP (Solo Lectura) -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Servidor SMTP
                                            </label>
                                            <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 border border-amber-200 dark:border-amber-700 rounded-lg">
                                                <div class="flex-shrink-0 w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                                    </svg>
                                                </div>
                                                <span id="servidor-smtp-display" class="text-sm font-semibold text-gray-900 dark:text-white">No configurado</span>
                                            </div>
                                            <input type="hidden" id="servidor_smtp_correo_envio_factura" name="servidor_smtp_correo_envio_factura">
                                        </div>

                                        <!-- Puerto SMTP (Solo Lectura) -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Puerto SMTP
                                            </label>
                                            <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 border border-amber-200 dark:border-amber-700 rounded-lg">
                                                <div class="flex-shrink-0 w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    </svg>
                                                </div>
                                                <span id="puerto-smtp-display" class="text-sm font-semibold text-gray-900 dark:text-white">No configurado</span>
                                            </div>
                                            <input type="hidden" id="puerto_correo_envio_factura" name="puerto_correo_envio_factura">
                                        </div>

                                        <!-- Contraseña Correo (Solo Lectura) -->
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Contraseña de Aplicación
                                            </label>
                                            <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 border border-amber-200 dark:border-amber-700 rounded-lg">
                                                <div class="flex-shrink-0 w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-grow">
                                                    <span id="password-correo-display" class="text-sm font-semibold text-gray-900 dark:text-white">••••••••</span>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Contraseña oculta por seguridad</p>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300">
                                                    Configurado
                                                </span>
                                            </div>
                                            <input type="hidden" id="password_correo_envio_factura" name="password_correo_envio_factura">
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección: Firma Electrónica -->
                                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 rounded-xl p-6 mb-6 border-2 border-cyan-200 dark:border-cyan-800">
                                    <div class="flex items-start gap-3 mb-4">
                                        <div class="flex-shrink-0">
                                            <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-grow">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                                Firma Electrónica para Facturación
                                            </h3>
                                            <div class="flex items-start gap-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg">
                                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-300">⚠️ Advertencia Importante</p>
                                                    <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">Modificar estos datos afectará directamente el funcionamiento del módulo de facturación electrónica. Asegúrese de que el certificado y contraseña sean correctos.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Archivo P12 -->
                                        <div class="md:col-span-2">
                                            <label for="p12_file" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Certificado de Firma (.p12) <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="file" id="p12_file" name="p12_file" accept=".p12"
                                                       class="block w-full text-sm text-gray-900 border border-cyan-300 rounded-lg cursor-pointer bg-white dark:text-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-100 file:text-cyan-700 hover:file:bg-cyan-200 dark:file:bg-cyan-900 dark:file:text-cyan-300">
                                                <input type="hidden" id="p12_path" name="p12_path">
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                Solo archivos .p12 del SRI. Archivo actual: <span id="p12-file-current" class="font-semibold text-cyan-700 dark:text-cyan-400">No configurado</span>
                                            </p>
                                            <!-- Fecha de caducidad del certificado -->
                                            <div id="p12-expiry-container" class="mt-3 p-3 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/10 dark:to-cyan-900/10 border border-blue-200 dark:border-blue-700 rounded-lg hidden">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <div>
                                                        <p class="text-xs font-semibold text-blue-900 dark:text-blue-300">Fecha de caducidad del certificado:</p>
                                                        <p class="text-sm font-bold text-blue-700 dark:text-blue-400" id="p12-expiry-date">Calculando...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Contraseña P12 -->
                                        <div class="md:col-span-2">
                                            <label for="p12_password" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                                Contraseña del Certificado <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="password" id="p12_password" name="p12_password" maxlength="100"
                                                       class="block w-full px-4 py-2.5 pr-12 border border-cyan-300 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 dark:bg-neutral-800 dark:border-neutral-600 dark:text-white transition-all"
                                                       placeholder="Ingrese la contraseña del certificado .p12">
                                                <button type="button" id="toggle-p12-password" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                                    <svg id="eye-icon-p12" class="w-5 h-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Contraseña proporcionada por el proveedor del certificado
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botón Guardar -->
                                <div class="flex justify-end pt-4">
                                    <button type="submit" id="btn-guardar-empresa"
                                            class="py-3.5 px-8 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none shadow-lg hover:shadow-xl transition-all duration-200">
                                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                            <polyline points="17 21 17 13 7 13 7 21"/>
                                            <polyline points="7 3 7 8 15 8"/>
                                        </svg>
                                        Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!-- End Formulario -->

                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
</div>
<!-- End Content -->
