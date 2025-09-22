/**
 * Módulo de gestión de productos
 * Funcionalidades para el manejo de productos en el sistema POS
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const modalAgregarProducto = document.getElementById('modal-agregar-producto');
    const inputCodigo = document.getElementById('codigo');
    const btnGenerarCodigo = document.getElementById('btn-generar-codigo');
    const formAgregarProducto = document.getElementById('formAgregarProducto');

    // Variables globales
    let ultimoCodigoGenerado = null;

    /**
     * Obtiene el siguiente número secuencial para el código de producto
     * @returns {Promise<string>} Código de producto único
     */
    async function obtenerSiguienteCodigoSecuencial() {
        try {
            const response = await fetch('ajax/productos.ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=obtenerSiguienteCodigo&tenant_id=' + (window.TENANT_ID || 1)
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const responseText = await response.text();
            const data = JSON.parse(responseText);

            if (data.success) {
                return data.codigo;
            } else {
                console.error('Error del servidor al obtener código:', data.message);
                throw new Error(data.message || 'Error del servidor');
            }
        } catch (error) {
            console.error('Error al obtener código secuencial:', error);
            throw error;
        }
    }

    /**
     * Genera un código de fallback cuando no se puede conectar a la base de datos
     * @returns {string} Código de producto de respaldo
     */
    function generarCodigoFallback() {
        const timestamp = Date.now().toString().slice(-6);
        const random = Math.floor(Math.random() * 10);
        const numeroFallback = parseInt(timestamp + random);
        return `P${numeroFallback.toString().padStart(7, '0')}`;
    }

    /**
     * Genera un código secuencial de emergencia basado en la fecha/hora actual
     * @returns {Promise<string>} Código secuencial de emergencia
     */
    async function generarCodigoFallbackSecuencial() {
        // Usar fecha y hora actual para generar un número secuencial
        const now = new Date();
        const year = now.getFullYear().toString().slice(-2); // Últimos 2 dígitos del año
        const month = (now.getMonth() + 1).toString().padStart(2, '0');
        const day = now.getDate().toString().padStart(2, '0');
        const hour = now.getHours().toString().padStart(2, '0');
        const minute = now.getMinutes().toString().padStart(2, '0');

        // Formato: P + YY + MM + DD + HH (7 dígitos total)
        const numeroSecuencial = year + month + day + hour.slice(-1);
        let codigo = `P${numeroSecuencial}`;

        // Verificar si existe y ajustar con minutos si es necesario
        let existe = await verificarCodigoExiste(codigo);
        if (existe) {
            // Si existe, usar los minutos para hacer único
            const numeroConMinutos = year + month + day + minute.slice(-1);
            codigo = `P${numeroConMinutos}`;
        }

        return codigo;
    }

    /**
     * Verifica si un código ya existe en la base de datos
     * @param {string} codigo - Código a verificar
     * @returns {Promise<boolean>} True si el código existe, false si está disponible
     */
    async function verificarCodigoExiste(codigo) {
        try {
            const response = await fetch('ajax/productos.ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=verificarCodigo&codigo=${encodeURIComponent(codigo)}&tenant_id=${window.TENANT_ID || 1}`
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const responseText = await response.text();
            const data = JSON.parse(responseText);

            if (data.success) {
                return data.existe || false;
            } else {
                return false;
            }
        } catch (error) {
            return false;
        }
    }

    /**
     * Genera un código único verificando que no exista en la base de datos
     * @returns {Promise<string>} Código único garantizado
     */
    async function generarCodigoUnico() {
        let intentos = 0;
        const maxIntentos = 5;

        // Intentar obtener código secuencial de la base de datos
        while (intentos < maxIntentos) {
            try {
                const codigo = await obtenerSiguienteCodigoSecuencial();
                const existe = await verificarCodigoExiste(codigo);

                if (!existe) {
                    ultimoCodigoGenerado = codigo;
                    return codigo;
                }

                // Si el código existe, incrementar y volver a intentar
                intentos++;
                console.warn(`Código ${codigo} ya existe, reintentando...`);

            } catch (error) {
                console.error(`Intento ${intentos + 1} falló:`, error);
                intentos++;

                // Si es el último intento, usar fallback secuencial
                if (intentos >= maxIntentos) {
                    console.warn('Usando código secuencial de emergencia');
                    return await generarCodigoFallbackSecuencial();
                }

                // Esperar un poco antes del siguiente intento
                await new Promise(resolve => setTimeout(resolve, 200));
            }
        }

        // Si todo falla, generar código secuencial de emergencia
        return await generarCodigoFallbackSecuencial();
    }

    /**
     * Actualiza el input de código con animaciones
     * @param {string} codigo - Nuevo código a mostrar
     */
    function actualizarInputCodigo(codigo) {
        if (inputCodigo) {
            inputCodigo.value = codigo;

            // Animación visual
            inputCodigo.classList.add('animate-pulse', 'bg-green-50', 'dark:bg-green-900/20');
            setTimeout(() => {
                inputCodigo.classList.remove('animate-pulse', 'bg-green-50', 'dark:bg-green-900/20');
            }, 1000);
        }
    }

    /**
     * Actualiza el estado visual del botón generar
     * @param {string} estado - Estado del botón: 'loading', 'success', 'normal'
     */
    function actualizarEstadoBoton(estado) {
        if (!btnGenerarCodigo) return;

        const iconos = {
            loading: `
                <svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Generando...
            `,
            success: `
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Generado
            `,
            normal: `
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Generar
            `
        };

        btnGenerarCodigo.innerHTML = iconos[estado] || iconos.normal;
    }

    // Event Listeners

    // Función para generar código y actualizar input
    async function ejecutarGeneracionCodigo() {
        actualizarEstadoBoton('loading');

        try {
            const nuevoCodigo = await generarCodigoUnico();
            actualizarInputCodigo(nuevoCodigo);
            actualizarEstadoBoton('success');

            setTimeout(() => {
                actualizarEstadoBoton('normal');
            }, 2000);
        } catch (error) {
            actualizarEstadoBoton('normal');
        }
    }

    // Generar código al abrir el modal (múltiples eventos para compatibilidad)
    if (modalAgregarProducto) {
        // Event listener de Preline
        modalAgregarProducto.addEventListener('open.hs.overlay', ejecutarGeneracionCodigo);

        // Event listener alternativo usando MutationObserver
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    if (target.classList.contains('open') && !target.classList.contains('hidden')) {
                        ejecutarGeneracionCodigo();
                    }
                }
            });
        });

        observer.observe(modalAgregarProducto, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Event listener para botones que abren el modal
        const botonesAbrir = document.querySelectorAll('[data-hs-overlay="#modal-agregar-producto"]');
        botonesAbrir.forEach(boton => {
            boton.addEventListener('click', function() {
                setTimeout(ejecutarGeneracionCodigo, 100);
            });
        });
    }

    // Botón para regenerar código manualmente
    if (btnGenerarCodigo) {
        btnGenerarCodigo.addEventListener('click', function() {
            ejecutarGeneracionCodigo();
        });
    }

    // Configuración adicional para controles interactivos
    inicializarControlesFormulario();
    inicializarDropzoneImagen();
    inicializarValidacionCodigoAuxiliar();
    inicializarTextareaDescripcion();
    cargarCategorias();
    inicializarFormularioSubmit();
    inicializarValidacionPrecios();
    inicializarDescuentos();
    inicializarFormularioEditarProducto();
    inicializarFiltros();
    cargarCategoriasFiltro();

});

/**
 * Inicializa los controles del formulario
 */
function inicializarControlesFormulario() {
    const manejaStockCheckbox = document.getElementById('maneja-stock');
    const stockFields = document.getElementById('stock-fields');
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

    // Auto-llenar porcentaje IVA cuando se selecciona código IVA
    const codigoIvaSelect = document.getElementById('codigo-iva');
    const porcentajeIvaInput = document.getElementById('porcentaje-iva');

    if (codigoIvaSelect && porcentajeIvaInput) {
        codigoIvaSelect.addEventListener('change', function() {
            actualizarPorcentajeIva(this.value, porcentajeIvaInput);
        });
    }
}

/**
 * Actualiza automáticamente el porcentaje IVA basado en el código seleccionado
 * @param {string} codigoIva - Código IVA seleccionado
 * @param {HTMLElement} porcentajeInput - Input del porcentaje IVA
 */
function actualizarPorcentajeIva(codigoIva, porcentajeInput) {
    const porcentajes = {
        '0': '0',      // 0%
        '2': '12',     // 12%
        '3': '14',     // 14%
        '4': '15',     // 15%
        '5': '5',      // 5% (código alternativo)
        '6': '5',      // 5%
        '7': '0',      // No Objeto de Impuesto
        '8': '15',     // IVA diferenciado (15%)
        '10': '13'     // 13%
    };

    if (porcentajeInput && porcentajes.hasOwnProperty(codigoIva)) {
        porcentajeInput.value = porcentajes[codigoIva];

        // Trigger change event para validaciones
        const event = new Event('change', { bubbles: true });
        porcentajeInput.dispatchEvent(event);
    }
}

/**
 * Inicializa la funcionalidad de dropzone para imágenes
 */
function inicializarDropzoneImagen() {
    const imageDropzone = document.getElementById('image-dropzone');
    const imageInput = document.getElementById('producto-imagen');
    const imagePreview = document.getElementById('image-preview');
    const dropzoneContent = document.getElementById('dropzone-content');
    const removeImageBtn = document.getElementById('remove-image');

    if (!imageDropzone || !imageInput) return;

    // Click en dropzone
    imageDropzone.addEventListener('click', function() {
        imageInput.click();
    });

    // Cambio de archivo
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño (2MB máximo)
            if (file.size > 2 * 1024 * 1024) {
                alert('El archivo es muy grande. El tamaño máximo es 2MB.');
                this.value = '';
                return;
            }

            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                alert('Por favor selecciona un archivo de imagen válido.');
                this.value = '';
                return;
            }

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
        this.classList.add('border-blue-400', 'bg-blue-50', 'dark:border-blue-500', 'dark:bg-blue-900/20');
    });

    imageDropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50', 'dark:border-blue-500', 'dark:bg-blue-900/20');
    });

    imageDropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50', 'dark:border-blue-500', 'dark:bg-blue-900/20');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            const event = new Event('change', { bubbles: true });
            imageInput.dispatchEvent(event);
        }
    });
}

/**
 * Limpia el grid y carga productos dinámicamente
 */
function limpiarYCargarProductos(productos) {
    const gridElement = document.getElementById('productos-grid');
    if (!gridElement) return;

    // Limpiar productos existentes
    gridElement.innerHTML = '';

    if (productos.length === 0) {
        // Mostrar mensaje de no productos con imagen descriptiva
        gridElement.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center py-16">
                <!-- Imagen ilustrativa más descriptiva -->
                <div class="relative mb-8">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl flex items-center justify-center shadow-lg border border-blue-100 dark:border-blue-800">
                        <!-- Caja de productos vacía -->
                        <svg class="w-16 h-16 text-blue-400 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <!-- Elementos decorativos flotantes -->
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-200 dark:bg-yellow-600/20 rounded-full flex items-center justify-center shadow-md">
                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div class="absolute -bottom-1 -left-3 w-6 h-6 bg-green-200 dark:bg-green-600/20 rounded-full flex items-center justify-center shadow-md">
                        <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="absolute top-4 -left-4 w-5 h-5 bg-purple-200 dark:bg-purple-600/20 rounded-full flex items-center justify-center shadow-md">
                        <svg class="w-2.5 h-2.5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                </div>

                <!-- Contenido del mensaje -->
                <div class="text-center max-w-md">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                        ¡Tu inventario está vacío!
                    </h3>
                    <p class="text-gray-600 dark:text-neutral-400 mb-2 leading-relaxed">
                        Comienza a construir tu catálogo de productos agregando tu primer artículo.
                    </p>
                    <p class="text-sm text-gray-500 dark:text-neutral-500 mb-8">
                        Puedes agregar productos físicos, servicios, o cualquier artículo que vendas.
                    </p>

                    <!-- Botón principal -->
                    <button type="button"
                            class="inline-flex items-center gap-x-3 py-3 px-6 text-base font-semibold rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 focus:outline-hidden focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-xl"
                            onclick="abrirModalAgregarProducto()">
                        <svg class="shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Agregar mi primer producto
                        <svg class="shrink-0 w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </button>

                    <!-- Mensaje adicional -->
                    <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 w-5 h-5 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">
                                    💡 Consejo rápido
                                </p>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Completa la información básica: código, descripción, precio y categoría para comenzar.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return;
    }

    // Crear cards para cada producto
    productos.forEach(producto => {
        const cardHTML = crearCardProducto(producto);
        gridElement.insertAdjacentHTML('beforeend', cardHTML);
    });
}

/**
 * Crea el HTML para una card de producto
 */
function crearCardProducto(producto) {
    // Verificar si el producto está eliminado
    const esEliminado = producto.deleted_at !== null && producto.deleted_at !== undefined;

    let estadoClass, estadoText;
    if (esEliminado) {
        estadoClass = 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300';
        estadoText = '🗑️ Eliminado';
    } else {
        estadoClass = producto.estado == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        estadoText = producto.estado == 1 ? '✅ Activo' : '❌ Inactivo';
    }

    const imagenUrl = producto.imagen ? producto.imagen : null;

    // Agregar clases especiales para productos eliminados
    const cardClass = esEliminado ? 'opacity-60 grayscale' : '';
    const headerClass = esEliminado ? 'bg-gradient-to-br from-gray-50 via-gray-100 to-gray-150 dark:from-gray-900/20 dark:via-gray-800/20 dark:to-gray-700/20' : 'bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20';

    return `
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden group hover:-translate-y-2 ${cardClass}">
            <!-- Header con imagen del producto -->
            <div class="relative ${headerClass} p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full ${estadoClass}">
                        ${estadoText}
                    </span>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            ${producto.tipo_producto || 'Producto'}
                        </span>
                    </div>
                </div>

                <div class="text-center">
                    ${esEliminado ?
                        // Icono especial para productos eliminados
                        `<div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center shadow-lg relative overflow-hidden">
                            <!-- Patrón de fondo para eliminados -->
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>

                            <!-- Icono de producto eliminado -->
                            <div class="relative">
                                <!-- Caja tachada -->
                                <svg class="w-10 h-10 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>

                                <!-- X grande encima -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>

                                <!-- Badge de eliminado -->
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>` :
                        // Lógica normal para productos no eliminados
                        imagenUrl ?
                            `<div class="w-20 h-20 mx-auto mb-4 rounded-2xl overflow-hidden shadow-lg">
                                <img src="${imagenUrl}" alt="${producto.descripcion}" class="w-full h-full object-cover">
                            </div>` :
                            `<div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg relative overflow-hidden">
                                <!-- Patrón de fondo sutil -->
                                <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>

                                <!-- SVG de producto con etiqueta de precio -->
                                <div class="relative">
                                    <!-- Caja del producto -->
                                    <svg class="w-12 h-12 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>

                                    <!-- Etiqueta de precio -->
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center shadow-sm">
                                        <svg class="w-2.5 h-2.5 text-yellow-800" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </div>

                                    <!-- Código de barras pequeño -->
                                    <div class="absolute -bottom-1 -left-1 flex gap-0.5 opacity-80">
                                        <div class="w-0.5 h-2 bg-white rounded-full"></div>
                                        <div class="w-0.5 h-1.5 bg-white rounded-full"></div>
                                        <div class="w-0.5 h-2 bg-white rounded-full"></div>
                                        <div class="w-0.5 h-1 bg-white rounded-full"></div>
                                        <div class="w-0.5 h-2 bg-white rounded-full"></div>
                                    </div>
                                </div>
                            </div>`
                    }
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">${producto.descripcion}</h3>
                    <p class="text-sm text-gray-600 dark:text-neutral-400 mb-3">📦 ${producto.categoria_nombre || 'Sin categoría'}</p>
                </div>
            </div>

            <!-- Información principal -->
            <div class="p-6 space-y-4">
                <!-- Códigos -->
                <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">Código</p>
                            <p class="font-semibold text-gray-900 dark:text-white">${producto.codigo}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">Código Auxiliar</p>
                            <p class="font-semibold text-gray-900 dark:text-white">${producto.codigo_auxiliar || 'N/A'}</p>
                        </div>
                    </div>
                </div>

                <!-- Precios destacados -->
                <div class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Compra</p>
                            <p class="text-lg font-bold text-gray-800 dark:text-neutral-200">$${parseFloat(producto.precio_de_compra || 0).toFixed(2)}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Venta</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">$${parseFloat(producto.precio_de_venta || 0).toFixed(2)}</p>
                        </div>
                    </div>
                    ${producto.tiene_descuento == 1 ? `
                        <div class="mt-3 pt-3 border-t border-green-200 dark:border-green-800">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-neutral-400">Precio con descuento:</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">$${parseFloat(producto.precio_con_descuento || 0).toFixed(2)}</span>
                            </div>
                        </div>
                    ` : ''}
                </div>

                <!-- Stock (si maneja stock) -->
                ${producto.maneja_stock == 1 ? `
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-neutral-300">Stock actual</span>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">${producto.stock_actual || 0} ${producto.unidad_medida || 'unidades'}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-neutral-700 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: ${Math.min(100, ((producto.stock_actual || 0) / Math.max(1, producto.stock_maximo || 100)) * 100)}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 dark:text-neutral-400 mt-1">
                            <span>Mín: ${producto.stock_minimo || 0}</span>
                            <span>Máx: ${producto.stock_maximo || 0}</span>
                        </div>
                    </div>
                ` : ''}

                <!-- Acciones -->
                <div class="flex gap-2 pt-4 border-t border-gray-100 dark:border-neutral-700">
                    ${esEliminado ? `
                        <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Eliminado el ${new Date(producto.deleted_at).toLocaleDateString()}
                        </div>
                    ` : `
                        <button onclick="abrirModalEditarProducto(${producto.idproducto})" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 transition-all duration-200 dark:text-blue-400 dark:bg-blue-900/20 dark:border-blue-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Editar
                        </button>
                        <button onclick="eliminarProducto(${producto.idproducto}, '${producto.descripcion.replace(/'/g, "\\'")}' )" class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 transition-all duration-200 dark:text-red-400 dark:bg-red-900/20 dark:border-red-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Eliminar
                        </button>
                    `}
                </div>
            </div>
        </div>
    `;
}

/**
 * Muestra un mensaje de error
 */
function mostrarMensajeError(mensaje) {
    const gridElement = document.getElementById('productos-grid');
    if (gridElement) {
        gridElement.innerHTML = `
            <div class="col-span-full flex flex-col items-center justify-center py-12">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-red-600 dark:text-red-400 font-medium">${mensaje}</p>
            </div>
        `;
    }
}

/**
 * Carga de datos de productos con filtros opcionales
 * @param {Object} filtros - Objeto con los filtros aplicar
 */
async function cargarDatosProductos(filtros = {}) {
    const loadingElement = document.getElementById('productos-loading');
    const gridElement = document.getElementById('productos-grid');

    try {
        // Mostrar loading
        if (loadingElement) loadingElement.classList.remove('hidden');
        if (gridElement) gridElement.classList.add('hidden');

        // Construir parámetros de búsqueda
        const params = new URLSearchParams({
            action: 'obtenerProductos',
            tenant_id: window.TENANT_ID || 1
        });

        // Agregar filtros si existen
        if (filtros.categoria) params.append('categoria', filtros.categoria);
        if (filtros.estado !== undefined && filtros.estado !== '') params.append('estado', filtros.estado);
        if (filtros.busqueda) params.append('busqueda', filtros.busqueda);

        // Obtener productos del servidor
        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (data.success && data.productos) {
            // Limpiar grid y cargar productos reales
            limpiarYCargarProductos(data.productos);
            actualizarContadorProductos(data.productos);
        } else {
            mostrarMensajeError('No se pudieron cargar los productos');
            actualizarContadorProductos([]);
        }

    } catch (error) {
        console.error('Error al cargar productos:', error);
        mostrarMensajeError('Error de conexión al cargar productos');
    } finally {
        // Ocultar loading y mostrar grid
        if (loadingElement) loadingElement.classList.add('hidden');
        if (gridElement) gridElement.classList.remove('hidden');
    }
}

/**
 * Inicializa la validación del código auxiliar
 */
function inicializarValidacionCodigoAuxiliar() {
    const codigoAuxiliarInput = document.getElementById('codigo-auxiliar');

    if (!codigoAuxiliarInput) return;

    let timeoutValidacion = null;

    // Permitir solo números, guiones y espacios
    codigoAuxiliarInput.addEventListener('input', function(e) {
        let value = e.target.value;

        // Remover caracteres no permitidos (solo números, guiones y espacios)
        value = value.replace(/[^0-9\-\s]/g, '');

        // Limitar longitud máxima a 25 caracteres (según base de datos)
        if (value.length > 25) {
            value = value.substring(0, 25);
        }

        e.target.value = value;

        // Limpiar timeout anterior
        if (timeoutValidacion) {
            clearTimeout(timeoutValidacion);
        }

        // Si hay contenido, validar después de 500ms de inactividad
        if (value.trim().length >= 8) {
            timeoutValidacion = setTimeout(() => {
                verificarCodigoAuxiliarEnTiempoReal(e.target, value.trim());
            }, 500);
        } else {
            // Si es muy corto, ocultar mensajes de validación
            ocultarMensajeValidacion(e.target);
        }
    });

    // Validar formato al perder el foco
    codigoAuxiliarInput.addEventListener('blur', function(e) {
        const value = e.target.value.trim();

        if (value && value.length > 0) {
            // Validar que tenga al menos 8 caracteres para códigos de barras válidos
            if (value.length < 8) {
                mostrarMensajeValidacion(e.target, 'Los códigos de barras deben tener al menos 8 dígitos', 'warning');
            } else {
                // Verificar duplicados si no se ha hecho ya
                verificarCodigoAuxiliarEnTiempoReal(e.target, value);
            }
        } else {
            ocultarMensajeValidacion(e.target);
        }
    });

    // Validar cuando el usuario pega contenido
    codigoAuxiliarInput.addEventListener('paste', function(e) {
        setTimeout(() => {
            let value = e.target.value;
            value = value.replace(/[^0-9\-\s]/g, '');
            if (value.length > 25) {
                value = value.substring(0, 25);
            }
            e.target.value = value;

            // Verificar duplicados después de limpiar
            if (value.trim().length >= 8) {
                setTimeout(() => {
                    verificarCodigoAuxiliarEnTiempoReal(e.target, value.trim());
                }, 100);
            }
        }, 10);
    });
}

/**
 * Verifica si el código auxiliar ya existe en tiempo real
 */
async function verificarCodigoAuxiliarEnTiempoReal(inputElement, codigoAuxiliar) {
    try {
        // Mostrar indicador de verificación
        mostrarIndicadorVerificacion(inputElement, true);

        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=verificarCodigoAuxiliar&codigo_auxiliar=${encodeURIComponent(codigoAuxiliar)}&tenant_id=${window.TENANT_ID || 1}`
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const responseText = await response.text();
        const data = JSON.parse(responseText);

        // Ocultar indicador de verificación
        mostrarIndicadorVerificacion(inputElement, false);

        if (data.success) {
            if (data.existe) {
                // El código ya existe
                mostrarMensajeValidacion(inputElement, '⚠️ Este código de barras ya está en uso por otro producto', 'error');
                inputElement.classList.add('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
            } else {
                // El código está disponible
                mostrarMensajeValidacion(inputElement, '✅ Código de barras disponible', 'success');
                inputElement.classList.remove('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
                inputElement.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');

                // Ocultar mensaje de éxito después de 2 segundos
                setTimeout(() => {
                    const mensaje = inputElement.parentElement.parentElement.querySelector('[data-validation-message="true"]');
                    if (mensaje && mensaje.textContent.includes('disponible')) {
                        ocultarMensajeValidacion(inputElement);
                        inputElement.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
                    }
                }, 2000);
            }
        } else {
            mostrarMensajeValidacion(inputElement, 'Error al verificar código de barras', 'warning');
        }

    } catch (error) {
        console.error('Error al verificar código auxiliar:', error);
        mostrarIndicadorVerificacion(inputElement, false);
        mostrarMensajeValidacion(inputElement, 'Error de conexión al verificar código', 'warning');
    }
}

/**
 * Muestra/oculta indicador de verificación en el input
 */
function mostrarIndicadorVerificacion(inputElement, mostrar) {
    const container = inputElement.parentElement;
    let indicador = container.querySelector('.indicador-verificacion');

    if (mostrar) {
        if (!indicador) {
            indicador = document.createElement('div');
            indicador.className = 'indicador-verificacion absolute inset-y-0 right-0 flex items-center pr-3';
            indicador.innerHTML = `
                <svg class="w-4 h-4 animate-spin text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            `;
            container.appendChild(indicador);
        }
        indicador.style.display = 'flex';
    } else {
        if (indicador) {
            indicador.style.display = 'none';
        }
    }
}

/**
 * Muestra un mensaje de validación debajo del input
 */
function mostrarMensajeValidacion(input, mensaje, tipo = 'error') {
    // Remover mensaje anterior si existe
    ocultarMensajeValidacion(input);

    const mensajeDiv = document.createElement('div');

    let colorClass = '';
    let icono = '';

    switch (tipo) {
        case 'error':
            colorClass = 'text-red-500';
            icono = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
            break;
        case 'warning':
            colorClass = 'text-yellow-500';
            icono = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>';
            break;
        case 'success':
            colorClass = 'text-green-500';
            icono = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            break;
        default:
            colorClass = 'text-gray-500';
            icono = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    }

    mensajeDiv.className = `mt-1 text-xs ${colorClass} flex items-center gap-1`;
    mensajeDiv.setAttribute('data-validation-message', 'true');
    mensajeDiv.innerHTML = `${icono}<span>${mensaje}</span>`;

    // Insertar después del input o su contenedor padre
    const container = input.parentElement;
    container.parentElement.appendChild(mensajeDiv);
}

/**
 * Oculta el mensaje de validación
 */
function ocultarMensajeValidacion(input) {
    const container = input.parentElement.parentElement;
    const mensajeExistente = container.querySelector('[data-validation-message="true"]');
    if (mensajeExistente) {
        mensajeExistente.remove();
    }
}

/**
 * Inicializa la funcionalidad del textarea de descripción
 */
function inicializarTextareaDescripcion() {
    const descripcionTextarea = document.getElementById('descripcion');
    const contadorElemento = document.getElementById('descripcion-counter');

    if (!descripcionTextarea || !contadorElemento) return;

    // Función para actualizar el contador de caracteres
    function actualizarContador() {
        const longitudActual = descripcionTextarea.value.length;
        const longitudMaxima = 500;

        contadorElemento.textContent = `${longitudActual}/${longitudMaxima}`;

        // Cambiar color según proximidad al límite
        if (longitudActual > longitudMaxima * 0.9) {
            contadorElemento.className = 'text-red-500 font-medium';
        } else if (longitudActual > longitudMaxima * 0.7) {
            contadorElemento.className = 'text-yellow-500 font-medium';
        } else {
            contadorElemento.className = '';
        }
    }

    // Función para ajustar la altura automáticamente
    function ajustarAltura() {
        descripcionTextarea.style.height = 'auto';
        const scrollHeight = descripcionTextarea.scrollHeight;
        const maxHeight = 120; // Máximo equivalente a ~6 líneas

        if (scrollHeight > maxHeight) {
            descripcionTextarea.style.height = maxHeight + 'px';
            descripcionTextarea.style.overflowY = 'auto';
        } else {
            descripcionTextarea.style.height = Math.max(scrollHeight, 60) + 'px'; // Mínimo 3 líneas
            descripcionTextarea.style.overflowY = 'hidden';
        }
    }

    // Event listeners
    descripcionTextarea.addEventListener('input', function() {
        actualizarContador();
        ajustarAltura();
    });

    descripcionTextarea.addEventListener('paste', function() {
        setTimeout(() => {
            actualizarContador();
            ajustarAltura();
        }, 10);
    });

    // Validación personalizada
    descripcionTextarea.addEventListener('blur', function() {
        const value = this.value.trim();

        if (value.length < 10 && value.length > 0) {
            mostrarMensajeValidacionDescripcion(this, 'La descripción debe tener al menos 10 caracteres para ser útil', 'warning');
        } else if (value.length === 0) {
            mostrarMensajeValidacionDescripcion(this, 'La descripción es obligatoria', 'error');
        } else {
            ocultarMensajeValidacionDescripcion(this);
        }
    });

    // Validar mientras escribe (solo para longitud máxima)
    descripcionTextarea.addEventListener('input', function() {
        if (this.value.length > 500) {
            this.value = this.value.substring(0, 500);
            actualizarContador();
        }
    });

    // Inicializar contador y altura
    actualizarContador();
    ajustarAltura();
}

/**
 * Muestra mensaje de validación específico para descripción
 */
function mostrarMensajeValidacionDescripcion(textarea, mensaje, tipo = 'error') {
    ocultarMensajeValidacionDescripcion(textarea);

    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `mt-1 text-xs ${tipo === 'error' ? 'text-red-500' : 'text-yellow-500'} flex items-center gap-1`;
    mensajeDiv.setAttribute('data-descripcion-validation', 'true');

    const icono = tipo === 'error' ?
        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>';

    mensajeDiv.innerHTML = `${icono}<span>${mensaje}</span>`;

    // Insertar después del párrafo del contador
    const contadorParrafo = textarea.parentElement.querySelector('p');
    if (contadorParrafo) {
        contadorParrafo.parentElement.insertBefore(mensajeDiv, contadorParrafo.nextSibling);
    } else {
        textarea.parentElement.appendChild(mensajeDiv);
    }
}

/**
 * Oculta mensaje de validación de descripción
 */
function ocultarMensajeValidacionDescripcion(textarea) {
    const mensajeExistente = textarea.parentElement.querySelector('[data-descripcion-validation="true"]');
    if (mensajeExistente) {
        mensajeExistente.remove();
    }
}

/**
 * Carga las categorías disponibles desde la base de datos
 */
async function cargarCategorias() {
    const selectCategoria = document.getElementById('categoria');

    if (!selectCategoria) return;

    try {
        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=obtenerCategorias&tenant_id=${window.TENANT_ID || 1}`
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const responseText = await response.text();

        if (!responseText.trim()) {
            throw new Error('Respuesta vacía del servidor');
        }

        const data = JSON.parse(responseText);

        if (data.success && data.categorias) {
            // Limpiar opciones existentes (mantener la primera opción de "Seleccionar")
            const primerOption = selectCategoria.querySelector('option[value=""]');
            selectCategoria.innerHTML = '';
            if (primerOption) {
                selectCategoria.appendChild(primerOption);
            } else {
                selectCategoria.innerHTML = '<option value="">Seleccionar categoría</option>';
            }

            // Agregar categorías dinámicamente
            data.categorias.forEach(categoria => {
                if (categoria.estado == 1) { // Solo categorías activas
                    const option = document.createElement('option');
                    option.value = categoria.idcategoria;
                    option.textContent = categoria.nombre;
                    option.title = categoria.descripcion || categoria.nombre;
                    selectCategoria.appendChild(option);
                }
            });

            // Actualizar también el filtro de categorías en la vista principal
            actualizarFiltroCategorias(data.categorias);
        } else {
            // Si no hay categorías, mostrar mensaje
            selectCategoria.innerHTML = '<option value="">No hay categorías disponibles</option>';
            console.warn('No se pudieron cargar las categorías:', data.message || 'Error desconocido');
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        selectCategoria.innerHTML = '<option value="">Error al cargar categorías</option>';
    }
}

/**
 * Carga las categorías en el modal de editar producto
 */
async function cargarCategoriasModalEditar() {
    const selectCategoria = document.getElementById('edit-categoria');

    if (!selectCategoria) return;

    try {
        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=obtenerCategorias&tenant_id=${window.TENANT_ID || 1}`
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const responseText = await response.text();

        if (!responseText.trim()) {
            throw new Error('Respuesta vacía del servidor');
        }

        const data = JSON.parse(responseText);

        if (data.success && data.categorias) {
            // Limpiar opciones existentes (mantener la primera opción de "Seleccionar")
            const primerOption = selectCategoria.querySelector('option[value=""]');
            selectCategoria.innerHTML = '';
            if (primerOption) {
                selectCategoria.appendChild(primerOption);
            } else {
                selectCategoria.innerHTML = '<option value="">Seleccionar categoría</option>';
            }

            // Agregar categorías dinámicamente
            data.categorias.forEach(categoria => {
                if (categoria.estado == 1) { // Solo categorías activas
                    const option = document.createElement('option');
                    option.value = categoria.idcategoria;
                    option.textContent = categoria.nombre;
                    option.title = categoria.descripcion || categoria.nombre;
                    selectCategoria.appendChild(option);
                }
            });
        } else {
            console.warn('No se pudieron cargar las categorías para edición:', data.message || 'Error desconocido');
        }
    } catch (error) {
        console.error('Error al cargar categorías para edición:', error);
        selectCategoria.innerHTML = '<option value="">Error al cargar categorías</option>';
    }
}

/**
 * Actualiza el filtro de categorías en la vista principal
 */
function actualizarFiltroCategorias(categorias) {
    const filtroCategoria = document.getElementById('filtro-categoria');

    if (!filtroCategoria || !categorias) return;

    // Conservar la opción "Todas las categorías"
    const todasOption = filtroCategoria.querySelector('option[value=""]');
    filtroCategoria.innerHTML = '';

    if (todasOption) {
        filtroCategoria.appendChild(todasOption);
    } else {
        filtroCategoria.innerHTML = '<option value="">🏷️ Todas las categorías</option>';
    }

    // Agregar categorías activas al filtro
    categorias.forEach(categoria => {
        if (categoria.estado == 1) {
            const option = document.createElement('option');
            option.value = categoria.idcategoria;
            option.textContent = `📁 ${categoria.nombre}`;
            filtroCategoria.appendChild(option);
        }
    });
}

/**
 * Valida el formulario antes del envío
 */
function validarFormularioProducto() {
    const formulario = document.getElementById('formAgregarProducto');
    if (!formulario) return false;

    let esValido = true;
    const errores = [];

    // Validar campos obligatorios según la base de datos
    const campos = {
        codigo: {
            elemento: document.getElementById('codigo'),
            requerido: true,
            maxLength: 12,
            nombre: 'Código'
        },
        descripcion: {
            elemento: document.getElementById('descripcion'),
            requerido: true,
            maxLength: 150,
            minLength: 10,
            nombre: 'Descripción'
        },
        categoria: {
            elemento: document.getElementById('categoria'),
            requerido: true,
            nombre: 'Categoría'
        },
        'tipo-producto': {
            elemento: document.getElementById('tipo-producto'),
            requerido: true,
            nombre: 'Tipo de producto'
        },
        'precio-venta': {
            elemento: document.getElementById('precio-venta'),
            requerido: true,
            tipo: 'number',
            min: 0,
            max: 99999.99999,
            decimales: 5,
            nombre: 'Precio de venta'
        }
    };

    // Validar campos opcionales con restricciones específicas
    const camposOpcionales = {
        'codigo-auxiliar': {
            elemento: document.getElementById('codigo-auxiliar'),
            maxLength: 25,
            nombre: 'Código auxiliar'
        },
        'precio-compra': {
            elemento: document.getElementById('precio-compra'),
            tipo: 'number',
            min: 0,
            max: 99999.99999,
            decimales: 5,
            nombre: 'Precio de compra'
        },
        'unidad-medida': {
            elemento: document.getElementById('unidad-medida'),
            maxLength: 20,
            nombre: 'Unidad de medida'
        },
        peso: {
            elemento: document.getElementById('peso'),
            tipo: 'number',
            min: 0,
            max: 999.999,
            decimales: 3,
            nombre: 'Peso'
        },
        'porcentaje-iva': {
            elemento: document.getElementById('porcentaje-iva'),
            tipo: 'number',
            min: 0,
            max: 99.99,
            decimales: 2,
            nombre: 'Porcentaje IVA'
        },
        'porcentaje-ice': {
            elemento: document.getElementById('porcentaje-ice'),
            tipo: 'number',
            min: 0,
            max: 99.99,
            decimales: 2,
            nombre: 'Porcentaje ICE'
        }
    };

    // Validar campos obligatorios
    Object.keys(campos).forEach(campo => {
        const config = campos[campo];
        const elemento = config.elemento;

        if (!elemento) return;

        const valor = elemento.value.trim();

        // Validar si es requerido
        if (config.requerido && (!valor || valor === '')) {
            errores.push(`${config.nombre} es obligatorio`);
            elemento.classList.add('border-red-500');
            esValido = false;
        } else {
            elemento.classList.remove('border-red-500');
        }

        // Validar longitud máxima
        if (valor && config.maxLength && valor.length > config.maxLength) {
            errores.push(`${config.nombre} no puede tener más de ${config.maxLength} caracteres`);
            elemento.classList.add('border-red-500');
            esValido = false;
        }

        // Validar longitud mínima
        if (valor && config.minLength && valor.length < config.minLength) {
            errores.push(`${config.nombre} debe tener al menos ${config.minLength} caracteres`);
            elemento.classList.add('border-red-500');
            esValido = false;
        }

        // Validar números
        if (valor && config.tipo === 'number') {
            const numero = parseFloat(valor);
            if (isNaN(numero)) {
                errores.push(`${config.nombre} debe ser un número válido`);
                elemento.classList.add('border-red-500');
                esValido = false;
            } else {
                if (config.min !== undefined && numero < config.min) {
                    errores.push(`${config.nombre} debe ser mayor o igual a ${config.min}`);
                    elemento.classList.add('border-red-500');
                    esValido = false;
                }
                if (config.max !== undefined && numero > config.max) {
                    errores.push(`${config.nombre} no puede ser mayor a ${config.max}`);
                    elemento.classList.add('border-red-500');
                    esValido = false;
                }
                // Validar número de decimales
                if (config.decimales !== undefined) {
                    const partes = valor.split('.');
                    if (partes.length > 1 && partes[1].length > config.decimales) {
                        errores.push(`${config.nombre} no puede tener más de ${config.decimales} decimales`);
                        elemento.classList.add('border-red-500');
                        esValido = false;
                    }
                }
            }
        }
    });

    // Validar campos opcionales
    Object.keys(camposOpcionales).forEach(campo => {
        const config = camposOpcionales[campo];
        const elemento = config.elemento;

        if (!elemento) return;

        const valor = elemento.value.trim();

        // Solo validar si tiene valor
        if (valor) {
            // Validar longitud máxima
            if (config.maxLength && valor.length > config.maxLength) {
                errores.push(`${config.nombre} no puede tener más de ${config.maxLength} caracteres`);
                elemento.classList.add('border-red-500');
                esValido = false;
            } else {
                elemento.classList.remove('border-red-500');
            }

            // Validar números
            if (config.tipo === 'number') {
                const numero = parseFloat(valor);
                if (isNaN(numero)) {
                    errores.push(`${config.nombre} debe ser un número válido`);
                    elemento.classList.add('border-red-500');
                    esValido = false;
                } else {
                    if (config.min !== undefined && numero < config.min) {
                        errores.push(`${config.nombre} debe ser mayor o igual a ${config.min}`);
                        elemento.classList.add('border-red-500');
                        esValido = false;
                    }
                    if (config.max !== undefined && numero > config.max) {
                        errores.push(`${config.nombre} no puede ser mayor a ${config.max}`);
                        elemento.classList.add('border-red-500');
                        esValido = false;
                    }
                    // Validar número de decimales
                    if (config.decimales !== undefined) {
                        const partes = valor.split('.');
                        if (partes.length > 1 && partes[1].length > config.decimales) {
                            errores.push(`${config.nombre} no puede tener más de ${config.decimales} decimales`);
                            elemento.classList.add('border-red-500');
                            esValido = false;
                        }
                    }
                }
            }
        } else {
            elemento.classList.remove('border-red-500');
        }
    });

    // Validación especial para campos de stock
    const manejaStock = document.getElementById('maneja-stock').checked;
    if (manejaStock) {
        const stockFields = ['stock-actual', 'stock-minimo', 'stock-maximo'];
        stockFields.forEach(fieldId => {
            const elemento = document.getElementById(fieldId);
            if (elemento && elemento.value.trim()) {
                const valor = parseInt(elemento.value);
                if (isNaN(valor) || valor < 0) {
                    errores.push(`${fieldId.replace('-', ' ')} debe ser un número entero positivo`);
                    elemento.classList.add('border-red-500');
                    esValido = false;
                } else {
                    elemento.classList.remove('border-red-500');
                }
            }
        });
    }

    // Validación especial para campos de descuento
    const tieneDescuento = document.getElementById('tiene-descuento').checked;
    if (tieneDescuento) {
        const descuentoCantidad = document.getElementById('descuento-cantidad');
        const precioDescuento = document.getElementById('precio-descuento');
        const precioVenta = document.getElementById('precio-venta');

        // Validar cantidad de descuento
        if (descuentoCantidad) {
            const cantidadValue = parseInt(descuentoCantidad.value);
            if (!descuentoCantidad.value.trim() || isNaN(cantidadValue) || cantidadValue < 1) {
                errores.push('La cantidad mínima para descuento debe ser un número entero mayor a 0');
                descuentoCantidad.classList.add('border-red-500');
                esValido = false;
            } else {
                descuentoCantidad.classList.remove('border-red-500');
            }
        }

        // Validar precio con descuento
        if (precioDescuento) {
            const precioDescuentoValue = parseFloat(precioDescuento.value);
            const precioVentaValue = parseFloat(precioVenta.value);

            if (!precioDescuento.value.trim() || isNaN(precioDescuentoValue)) {
                errores.push('El precio con descuento es obligatorio cuando el descuento está activo');
                precioDescuento.classList.add('border-red-500');
                esValido = false;
            } else if (precioDescuentoValue <= 0) {
                errores.push('El precio con descuento debe ser mayor a 0');
                precioDescuento.classList.add('border-red-500');
                esValido = false;
            } else if (precioDescuentoValue >= precioVentaValue) {
                errores.push('El precio con descuento debe ser menor al precio de venta');
                precioDescuento.classList.add('border-red-500');
                esValido = false;
            } else if (precioDescuentoValue > 99999.99999) {
                errores.push('El precio con descuento no puede ser mayor a 99,999.99999');
                precioDescuento.classList.add('border-red-500');
                esValido = false;
            } else {
                precioDescuento.classList.remove('border-red-500');
            }
        }
    }

    // Validación especial para código auxiliar duplicado
    const codigoAuxiliarInput = document.getElementById('codigo-auxiliar');
    if (codigoAuxiliarInput && codigoAuxiliarInput.value.trim().length > 0) {
        // Verificar si hay un mensaje de error visible
        const mensajeError = codigoAuxiliarInput.parentElement.parentElement.querySelector('[data-validation-message="true"]');
        if (mensajeError && mensajeError.textContent.includes('ya está en uso')) {
            errores.push('El código de barras ya está en uso por otro producto');
            codigoAuxiliarInput.classList.add('border-red-500');
            esValido = false;
        }
    }

    // Mostrar errores si los hay
    if (!esValido) {
        mostrarErroresValidacion(errores);
    }

    return esValido;
}

/**
 * Muestra los errores de validación en un toast o alert
 */
function mostrarErroresValidacion(errores) {
    const mensaje = 'Por favor corrige los siguientes errores:\n\n' + errores.join('\n');
    alert(mensaje);
}

/**
 * Inicializa el event listener del formulario
 */
function inicializarFormularioSubmit() {
    const formulario = document.getElementById('formAgregarProducto');

    if (formulario) {
        formulario.addEventListener('submit', function(e) {
            e.preventDefault();

            if (validarFormularioProducto()) {
                enviarFormularioProducto();
            }
        });
    }
}

/**
 * Envía el formulario al servidor
 */
async function enviarFormularioProducto() {
    const formulario = document.getElementById('formAgregarProducto');
    const submitButton = document.querySelector('button[type="submit"][form="formAgregarProducto"]');

    if (!formulario) return;

    // Deshabilitar botón y mostrar loading
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Crear Producto';
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span>Creando...</span>
    `;

    try {
        const formData = new FormData(formulario);
        formData.append('action', 'crearProducto');
        formData.append('tenant_id', window.TENANT_ID || 1);

        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const responseText = await response.text();

        // Limpiar posible contenido antes del JSON
        const jsonStart = responseText.indexOf('{');
        const cleanResponse = jsonStart !== -1 ? responseText.substring(jsonStart) : responseText;

        const data = JSON.parse(cleanResponse);

        if (data.success) {
            // Éxito - mostrar mensaje y cerrar modal
            showNotification('✅ Producto creado exitosamente', 'success');

            // Cerrar modal usando HSOverlay
            const modal = document.getElementById('modal-agregar-producto');
            if (modal && window.HSOverlay) {
                const overlay = HSOverlay.getInstance(modal, true);
                if (overlay && overlay.element) {
                    overlay.element.close();
                }
            }

            // Limpiar formulario
            formulario.reset();
            limpiarImagenProducto();

            // Recargar productos
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            // Error del servidor
            showNotification('❌ ' + (data.message || 'Error al crear el producto'), 'error');
        }
    } catch (error) {
        console.error('Error al enviar formulario:', error);
        showNotification('❌ Error de conexión al crear el producto', 'error');
    } finally {
        // Rehabilitar botón
        submitButton.disabled = false;
        submitButton.innerHTML = originalButtonText;
    }
}

/**
 * Inicializa la validación en tiempo real para campos de precio
 */
function inicializarValidacionPrecios() {
    const precioCompra = document.getElementById('precio-compra');
    const precioVenta = document.getElementById('precio-venta');

    [precioCompra, precioVenta].forEach(input => {
        if (!input) return;

        // Validación en tiempo real mientras escribe
        input.addEventListener('input', function(e) {
            let value = e.target.value;
            let cursorPosition = e.target.selectionStart;

            // Permitir solo números, punto decimal
            // Remover caracteres no válidos (mantener números, punto y coma por si copian)
            const valorAnterior = value;
            value = value.replace(/[^0-9.,]/g, '');

            // Convertir comas a puntos (por si el usuario usa coma decimal)
            value = value.replace(/,/g, '.');

            // Permitir solo un punto decimal
            const puntos = value.split('.');
            if (puntos.length > 2) {
                // Si hay más de un punto, mantener solo el primero
                value = puntos[0] + '.' + puntos.slice(1).join('');
            }

            // Limitar a 5 decimales solo si ya hay punto
            if (puntos.length === 2 && puntos[1].length > 5) {
                value = puntos[0] + '.' + puntos[1].substring(0, 5);
            }

            // Limitar a 5 dígitos enteros (99999.99999 máximo)
            if (puntos[0].length > 5) {
                value = puntos[0].substring(0, 5) + (puntos.length > 1 ? '.' + (puntos[1] || '') : '');
            }

            // Solo actualizar si el valor cambió para evitar problemas de cursor
            if (value !== valorAnterior) {
                e.target.value = value;

                // Restaurar posición del cursor si es posible
                if (value.length <= valorAnterior.length) {
                    e.target.setSelectionRange(cursorPosition, cursorPosition);
                }
            }
        });

        // Validación al perder el foco
        input.addEventListener('blur', function(e) {
            let valorTexto = e.target.value.trim();
            const fieldName = e.target.id === 'precio-compra' ? 'Precio de compra' : 'Precio de venta';

            if (valorTexto !== '') {
                // Limpiar el valor antes de validar
                valorTexto = valorTexto.replace(/,/g, '.');
                const value = parseFloat(valorTexto);

                if (isNaN(value) || valorTexto === '' || valorTexto === '.') {
                    mostrarMensajeValidacionPrecio(e.target, `${fieldName} debe ser un número válido`, 'error');
                    e.target.classList.add('border-red-500');
                } else if (value < 0) {
                    mostrarMensajeValidacionPrecio(e.target, `${fieldName} no puede ser negativo`, 'error');
                    e.target.classList.add('border-red-500');
                } else if (value > 99999.99999) {
                    mostrarMensajeValidacionPrecio(e.target, `${fieldName} no puede ser mayor a 99,999.99999`, 'error');
                    e.target.classList.add('border-red-500');
                } else {
                    ocultarMensajeValidacionPrecio(e.target);
                    e.target.classList.remove('border-red-500');

                    // Formatear el valor limpiamente
                    if (value === 0) {
                        e.target.value = '0';
                    } else {
                        // Mantener los decimales que tiene, sin forzar formato
                        e.target.value = value.toString();
                    }
                }
            } else {
                // Si está vacío, ocultar mensaje de error
                ocultarMensajeValidacionPrecio(e.target);
                e.target.classList.remove('border-red-500');

                // Para precio de venta, mostrar advertencia si está vacío
                if (e.target.id === 'precio-venta') {
                    mostrarMensajeValidacionPrecio(e.target, 'Precio de venta es obligatorio', 'error');
                    e.target.classList.add('border-red-500');
                }
            }
        });

        // Validar cuando el usuario pega contenido
        input.addEventListener('paste', function(e) {
            setTimeout(() => {
                let value = e.target.value;

                // Limpiar caracteres no válidos y convertir comas a puntos
                value = value.replace(/[^0-9.,]/g, '');
                value = value.replace(/,/g, '.');

                // Permitir solo un punto decimal
                const puntos = value.split('.');
                if (puntos.length > 2) {
                    value = puntos[0] + '.' + puntos.slice(1).join('');
                }

                // Limitar a 5 decimales
                if (puntos.length === 2 && puntos[1].length > 5) {
                    value = puntos[0] + '.' + puntos[1].substring(0, 5);
                }

                // Limitar a 5 dígitos enteros
                if (puntos[0].length > 5) {
                    value = puntos[0].substring(0, 5) + (puntos.length > 1 ? '.' + (puntos[1] || '') : '');
                }

                e.target.value = value;
            }, 10);
        });
    });
}

/**
 * Muestra mensaje de validación específico para precios
 */
function mostrarMensajeValidacionPrecio(input, mensaje, tipo = 'error') {
    ocultarMensajeValidacionPrecio(input);

    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `mt-1 text-xs ${tipo === 'error' ? 'text-red-500' : 'text-yellow-500'} flex items-center gap-1`;
    mensajeDiv.setAttribute('data-precio-validation', 'true');

    const icono = tipo === 'error' ?
        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>';

    mensajeDiv.innerHTML = `${icono}<span>${mensaje}</span>`;

    // Insertar después del input
    input.parentElement.appendChild(mensajeDiv);
}

/**
 * Oculta mensaje de validación de precios
 */
function ocultarMensajeValidacionPrecio(input) {
    const mensajeExistente = input.parentElement.querySelector('[data-precio-validation="true"]');
    if (mensajeExistente) {
        mensajeExistente.remove();
    }
}

/**
 * Inicializa la funcionalidad de descuentos por cantidad
 */
function inicializarDescuentos() {
    const tieneDescuentoCheckbox = document.getElementById('tiene-descuento');
    const descuentoFields = document.getElementById('descuento-fields');
    const descuentoPreview = document.getElementById('descuento-preview');
    const descuentoCantidad = document.getElementById('descuento-cantidad');
    const precioDescuento = document.getElementById('precio-descuento');
    const precioVenta = document.getElementById('precio-venta');

    if (!tieneDescuentoCheckbox) return;

    // Mostrar/ocultar campos de descuento
    tieneDescuentoCheckbox.addEventListener('change', function() {
        if (this.checked) {
            descuentoFields.classList.remove('hidden');
            actualizarPreviewDescuento();
        } else {
            descuentoFields.classList.add('hidden');
            descuentoPreview.classList.add('hidden');
            // Limpiar valores
            if (descuentoCantidad) descuentoCantidad.value = '';
            if (precioDescuento) precioDescuento.value = '';
        }
    });

    // Validación en tiempo real para cantidad de descuento
    if (descuentoCantidad) {
        descuentoCantidad.addEventListener('input', function(e) {
            let value = e.target.value;

            // Permitir solo números enteros
            value = value.replace(/[^0-9]/g, '');

            // Limitar a números razonables (máximo 6 dígitos)
            if (value.length > 6) {
                value = value.substring(0, 6);
            }

            e.target.value = value;
            actualizarPreviewDescuento();
        });

        descuentoCantidad.addEventListener('blur', function(e) {
            const value = parseInt(e.target.value);

            if (e.target.value.trim() !== '') {
                if (isNaN(value) || value < 1) {
                    mostrarMensajeValidacionDescuento(e.target, 'La cantidad debe ser un número entero mayor a 0', 'error');
                    e.target.classList.add('border-red-500');
                } else {
                    ocultarMensajeValidacionDescuento(e.target);
                    e.target.classList.remove('border-red-500');
                }
            } else if (tieneDescuentoCheckbox.checked) {
                mostrarMensajeValidacionDescuento(e.target, 'La cantidad mínima es obligatoria cuando el descuento está activo', 'error');
                e.target.classList.add('border-red-500');
            }
        });
    }

    // Validación en tiempo real para precio con descuento
    if (precioDescuento) {
        // Aplicar las mismas validaciones que los otros precios
        aplicarValidacionPrecio(precioDescuento, 'Precio con descuento');

        // Validación adicional específica para descuentos
        precioDescuento.addEventListener('blur', function(e) {
            const precioVentaValue = parseFloat(precioVenta.value || 0);
            const precioDescuentoValue = parseFloat(e.target.value || 0);

            if (e.target.value.trim() !== '' && !isNaN(precioDescuentoValue) && !isNaN(precioVentaValue)) {
                if (precioDescuentoValue >= precioVentaValue) {
                    mostrarMensajeValidacionDescuento(e.target, 'El precio con descuento debe ser menor al precio de venta', 'error');
                    e.target.classList.add('border-red-500');
                } else {
                    // Verificar que no sea un descuento demasiado pequeño (menos del 1%)
                    const porcentajeDescuento = ((precioVentaValue - precioDescuentoValue) / precioVentaValue) * 100;
                    if (porcentajeDescuento < 1) {
                        mostrarMensajeValidacionDescuento(e.target, 'El descuento es muy pequeño (menos del 1%)', 'warning');
                    }
                }
            } else if (e.target.value.trim() === '' && tieneDescuentoCheckbox.checked) {
                mostrarMensajeValidacionDescuento(e.target, 'El precio con descuento es obligatorio cuando el descuento está activo', 'error');
                e.target.classList.add('border-red-500');
            }

            actualizarPreviewDescuento();
        });

        precioDescuento.addEventListener('input', function() {
            actualizarPreviewDescuento();
        });
    }

    // Actualizar preview cuando cambie el precio de venta
    if (precioVenta) {
        precioVenta.addEventListener('input', function() {
            if (tieneDescuentoCheckbox.checked) {
                actualizarPreviewDescuento();
            }
        });
    }
}

/**
 * Aplica validación de precio a un input específico
 */
function aplicarValidacionPrecio(input, nombreCampo) {
    // Validación en tiempo real mientras escribe
    input.addEventListener('input', function(e) {
        let value = e.target.value;
        let cursorPosition = e.target.selectionStart;

        const valorAnterior = value;
        value = value.replace(/[^0-9.,]/g, '');
        value = value.replace(/,/g, '.');

        const puntos = value.split('.');
        if (puntos.length > 2) {
            value = puntos[0] + '.' + puntos.slice(1).join('');
        }

        if (puntos.length === 2 && puntos[1].length > 5) {
            value = puntos[0] + '.' + puntos[1].substring(0, 5);
        }

        if (puntos[0].length > 5) {
            value = puntos[0].substring(0, 5) + (puntos.length > 1 ? '.' + (puntos[1] || '') : '');
        }

        if (value !== valorAnterior) {
            e.target.value = value;
            if (value.length <= valorAnterior.length) {
                e.target.setSelectionRange(cursorPosition, cursorPosition);
            }
        }
    });

    // Validación al perder el foco
    input.addEventListener('blur', function(e) {
        let valorTexto = e.target.value.trim();

        if (valorTexto !== '') {
            valorTexto = valorTexto.replace(/,/g, '.');
            const value = parseFloat(valorTexto);

            if (isNaN(value) || valorTexto === '' || valorTexto === '.') {
                mostrarMensajeValidacionDescuento(e.target, `${nombreCampo} debe ser un número válido`, 'error');
                e.target.classList.add('border-red-500');
            } else if (value < 0) {
                mostrarMensajeValidacionDescuento(e.target, `${nombreCampo} no puede ser negativo`, 'error');
                e.target.classList.add('border-red-500');
            } else if (value > 99999.99999) {
                mostrarMensajeValidacionDescuento(e.target, `${nombreCampo} no puede ser mayor a 99,999.99999`, 'error');
                e.target.classList.add('border-red-500');
            } else {
                ocultarMensajeValidacionDescuento(e.target);
                e.target.classList.remove('border-red-500');

                if (value === 0) {
                    e.target.value = '0';
                } else {
                    e.target.value = value.toString();
                }
            }
        }
    });
}

/**
 * Actualiza la vista previa del descuento
 */
function actualizarPreviewDescuento() {
    const tieneDescuentoCheckbox = document.getElementById('tiene-descuento');
    const descuentoPreview = document.getElementById('descuento-preview');
    const precioVenta = document.getElementById('precio-venta');
    const precioDescuento = document.getElementById('precio-descuento');
    const descuentoCantidad = document.getElementById('descuento-cantidad');

    if (!tieneDescuentoCheckbox.checked || !descuentoPreview) {
        if (descuentoPreview) descuentoPreview.classList.add('hidden');
        return;
    }

    const precioVentaValue = parseFloat(precioVenta.value || 0);
    const precioDescuentoValue = parseFloat(precioDescuento.value || 0);
    const cantidadValue = parseInt(descuentoCantidad.value || 0);

    // Elementos del preview
    const previewPrecioNormal = document.getElementById('preview-precio-normal');
    const previewPrecioDescuento = document.getElementById('preview-precio-descuento');
    const previewAhorro = document.getElementById('preview-ahorro');

    if (precioVentaValue > 0 && precioDescuentoValue > 0 && cantidadValue > 0) {
        descuentoPreview.classList.remove('hidden');

        // Calcular valores
        const ahorro = precioVentaValue - precioDescuentoValue;
        const porcentajeAhorro = (ahorro / precioVentaValue) * 100;

        // Formatear valores
        const formatearPrecio = (precio) => `$${precio.toFixed(2)}`;

        // Actualizar preview
        if (previewPrecioNormal) previewPrecioNormal.textContent = formatearPrecio(precioVentaValue);
        if (previewPrecioDescuento) previewPrecioDescuento.textContent = formatearPrecio(precioDescuentoValue);
        if (previewAhorro) {
            previewAhorro.textContent = `${formatearPrecio(ahorro)} (${porcentajeAhorro.toFixed(1)}%)`;

            // Cambiar color según el porcentaje de descuento
            if (porcentajeAhorro >= 20) {
                previewAhorro.className = 'font-semibold text-green-600 dark:text-green-400';
            } else if (porcentajeAhorro >= 10) {
                previewAhorro.className = 'font-semibold text-yellow-600 dark:text-yellow-400';
            } else {
                previewAhorro.className = 'font-semibold text-orange-600 dark:text-orange-400';
            }
        }
    } else {
        descuentoPreview.classList.add('hidden');
    }
}

/**
 * Muestra mensaje de validación específico para descuentos
 */
function mostrarMensajeValidacionDescuento(input, mensaje, tipo = 'error') {
    ocultarMensajeValidacionDescuento(input);

    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `mt-1 text-xs ${tipo === 'error' ? 'text-red-500' : 'text-yellow-500'} flex items-center gap-1`;
    mensajeDiv.setAttribute('data-descuento-validation', 'true');

    const icono = tipo === 'error' ?
        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>';

    mensajeDiv.innerHTML = `${icono}<span>${mensaje}</span>`;

    // Insertar después del input o su párrafo padre
    const parentElement = input.parentElement;
    const existingP = parentElement.querySelector('p');

    if (existingP) {
        parentElement.insertBefore(mensajeDiv, existingP.nextSibling);
    } else {
        parentElement.appendChild(mensajeDiv);
    }
}

/**
 * Oculta mensaje de validación de descuentos
 */
function ocultarMensajeValidacionDescuento(input) {
    const mensajeExistente = input.parentElement.querySelector('[data-descuento-validation="true"]');
    if (mensajeExistente) {
        mensajeExistente.remove();
    }
}

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

/**
 * Función para limpiar imagen del producto
 */
function limpiarImagenProducto() {
    const imagePreview = document.getElementById('image-preview');
    const dropzoneContent = document.getElementById('dropzone-content');
    const removeImageBtn = document.getElementById('remove-image');
    const imageStatusBadge = document.getElementById('image-status-badge');
    const fileInfo = document.getElementById('file-info');
    const imageInput = document.getElementById('producto-imagen');

    if (imagePreview) imagePreview.classList.add('hidden');
    if (dropzoneContent) dropzoneContent.classList.remove('hidden');
    if (removeImageBtn) removeImageBtn.classList.add('hidden');
    if (imageStatusBadge) imageStatusBadge.classList.add('hidden');
    if (fileInfo) fileInfo.classList.add('hidden');
    if (imageInput) imageInput.value = '';
}

/**
 * Abre el modal para agregar producto
 */
function abrirModalAgregarProducto() {
    const modal = document.getElementById('modal-agregar-producto');
    if (modal) {
        // Usar HSOverlay para abrir el modal
        if (window.HSOverlay) {
            const overlay = HSOverlay.getInstance(modal, true);
            if (overlay && overlay.element) {
                overlay.element.open();
            } else {
                // Fallback si HSOverlay no está disponible
                modal.classList.remove('hidden');
                modal.classList.add('open');
            }
        } else {
            // Fallback manual
            modal.classList.remove('hidden');
            modal.classList.add('open');
            modal.style.display = 'flex';
        }
    }
}

/**
 * Abre el modal para editar producto
 */
async function abrirModalEditarProducto(idProducto) {
    try {
        // Primero cargar las categorías en el modal de editar
        await cargarCategoriasModalEditar();

        // Luego obtener datos del producto
        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=obtenerProducto&id=${idProducto}&tenant_id=${window.TENANT_ID || 1}`
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (data.success && data.producto) {
            // Poblar formulario con datos del producto
            poblarFormularioEditarProducto(data.producto);

            // Abrir modal
            const modal = document.getElementById('modal-editar-producto');
            if (modal) {
                if (window.HSOverlay) {
                    const overlay = HSOverlay.getInstance(modal, true);
                    if (overlay && overlay.element) {
                        overlay.element.open();
                    } else {
                        modal.classList.remove('hidden');
                        modal.classList.add('open');
                    }
                } else {
                    modal.classList.remove('hidden');
                    modal.classList.add('open');
                    modal.style.display = 'flex';
                }
            }
        } else {
            showNotification('❌ Error al cargar los datos del producto', 'error');
        }
    } catch (error) {
        console.error('Error al cargar producto:', error);
        showNotification('❌ Error de conexión al cargar el producto', 'error');
    }
}

/**
 * Pobla el formulario de edición con los datos del producto
 */
function poblarFormularioEditarProducto(producto) {
    // Campos básicos
    const campos = {
        'edit-codigo': producto.codigo,
        'edit-codigo-auxiliar': producto.codigo_auxiliar || '',
        'edit-descripcion': producto.descripcion || '',
        'edit-precio-venta': producto.precio_de_venta || '',
        'edit-precio-compra': producto.precio_de_compra || '',
        'edit-categoria': producto.categoria_idcategoria || '',
        'edit-tipo-producto': producto.tipo_producto || '',
        'edit-unidad-medida': producto.unidad_medida || '',
        'edit-peso': producto.peso || '',
        'edit-codigo-iva': producto.codigo_iva || '',
        'edit-porcentaje-iva': producto.porcentaje_iva || '',
        'edit-codigo-ice': producto.codigo_ice || '',
        'edit-porcentaje-ice': producto.porcentaje_ice || '',
        'edit-codigo-material-construccion': producto.codigo_material_construccion || '',
        'edit-stock-actual': producto.stock_actual || '',
        'edit-stock-minimo': producto.stock_minimo || '',
        'edit-stock-maximo': producto.stock_maximo || '',
        'edit-descuento-cantidad': producto.descuento_por_cantidad || '',
        'edit-precio-descuento': producto.precio_con_descuento || '',
        'edit-estado': producto.estado !== undefined && producto.estado !== null ? producto.estado.toString() : '1'
    };

    // Llenar campos de texto y selects (excepto los especiales)
    Object.keys(campos).forEach(campoId => {
        const elemento = document.getElementById(campoId);
        if (elemento) {
            // Saltar selects especiales por ahora
            if (campoId === 'edit-codigo-material-construccion' || campoId === 'edit-codigo-ice') {
                return; // Los manejaremos después
            } else {
                elemento.value = campos[campoId];
            }
        }
    });

    // Campos checkbox
    const checkboxes = {
        'edit-maneja-stock': producto.maneja_stock == 1,
        'edit-tiene-descuento': producto.tiene_descuento == 1,
        'edit-graba-ice': producto.graba_ice == 1,
        'edit-es-material-construccion': producto.es_material_construccion == 1
    };

    Object.keys(checkboxes).forEach(checkboxId => {
        const elemento = document.getElementById(checkboxId);
        if (elemento) {
            elemento.checked = checkboxes[checkboxId];
            // Trigger change event para mostrar/ocultar campos dependientes
            elemento.dispatchEvent(new Event('change'));
        }
    });

    // Asegurar que los campos dependientes se muestren/oculten correctamente
    mostrarCamposDependientesEdicion(producto);

    // Ahora poblar los selects especiales después de que los campos estén visibles
    poblarSelectsEspeciales(producto, campos);

    // Manejar imagen actual
    if (producto.imagen && producto.imagen.trim() !== '') {
        const imagePreview = document.getElementById('edit-image-preview');
        const dropzoneContent = document.getElementById('edit-dropzone-content');
        const removeImageBtn = document.getElementById('edit-remove-image');
        const statusBadge = document.getElementById('edit-image-status-badge');

        if (imagePreview && dropzoneContent) {
            // Mostrar la imagen actual
            imagePreview.src = producto.imagen;
            imagePreview.classList.remove('hidden');

            // Ocultar el contenido del dropzone
            dropzoneContent.classList.add('hidden');

            // Mostrar botón de remover
            if (removeImageBtn) {
                removeImageBtn.classList.remove('hidden');
            }

            // Mostrar badge de estado
            if (statusBadge) {
                statusBadge.classList.remove('hidden');
            }
        }
    } else {
        // Asegurar que se muestre el dropzone si no hay imagen
        const imagePreview = document.getElementById('edit-image-preview');
        const dropzoneContent = document.getElementById('edit-dropzone-content');
        const removeImageBtn = document.getElementById('edit-remove-image');
        const statusBadge = document.getElementById('edit-image-status-badge');

        if (imagePreview) imagePreview.classList.add('hidden');
        if (dropzoneContent) dropzoneContent.classList.remove('hidden');
        if (removeImageBtn) removeImageBtn.classList.add('hidden');
        if (statusBadge) statusBadge.classList.add('hidden');
    }

    // Guardar ID del producto para actualización
    const form = document.getElementById('formEditarProducto');
    if (form) {
        // Agregar input hidden con el ID del producto
        let hiddenInput = form.querySelector('input[name="producto_id"]');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'producto_id';
            form.appendChild(hiddenInput);
        }
        hiddenInput.value = producto.idproducto;
    }
}

/**
 * Pobla los selects especiales después de que los campos estén visibles
 */
function poblarSelectsEspeciales(producto, campos) {
    // Material de construcción
    if (producto.es_material_construccion == 1) {
        const materialSelect = document.getElementById('edit-codigo-material-construccion');
        const materialValor = campos['edit-codigo-material-construccion'];

        if (materialSelect && materialValor && materialValor.trim() !== '') {
            // Verificar si existe la opción
            const opcionExiste = materialSelect.querySelector(`option[value="${materialValor}"]`);
            if (opcionExiste) {
                materialSelect.value = materialValor;
            }
        }
    }

    // ICE
    if (producto.graba_ice == 1) {
        const iceSelect = document.getElementById('edit-codigo-ice');
        const iceValor = campos['edit-codigo-ice'];

        if (iceSelect && iceValor && iceValor.trim() !== '') {
            // Verificar si existe la opción
            const opcionExiste = iceSelect.querySelector(`option[value="${iceValor}"]`);
            if (opcionExiste) {
                iceSelect.value = iceValor;
            }
        }
    }
}

/**
 * Muestra u oculta campos dependientes en el formulario de edición
 */
function mostrarCamposDependientesEdicion(producto) {
    // Campos de ICE
    const iceFields = document.getElementById('edit-ice-fields');
    if (iceFields) {
        if (producto.graba_ice == 1) {
            iceFields.classList.remove('hidden');
        } else {
            iceFields.classList.add('hidden');
        }
    }

    // Campos de material de construcción
    const materialFields = document.getElementById('edit-material-fields');
    if (materialFields) {
        if (producto.es_material_construccion == 1) {
            materialFields.classList.remove('hidden');
        } else {
            materialFields.classList.add('hidden');
        }
    }

    // Campos de stock
    const stockFields = document.getElementById('edit-stock-fields');
    if (stockFields) {
        if (producto.maneja_stock == 1) {
            stockFields.classList.remove('hidden');
        } else {
            stockFields.classList.add('hidden');
        }
    }

    // Campos de descuento
    const descuentoFields = document.getElementById('edit-descuento-fields');
    if (descuentoFields) {
        if (producto.tiene_descuento == 1) {
            descuentoFields.classList.remove('hidden');
        } else {
            descuentoFields.classList.add('hidden');
        }
    }
}

/**
 * Inicializa los controles del formulario de edición
 */
function inicializarControlesFormularioEdicion() {
    const manejaStockCheckbox = document.getElementById('edit-maneja-stock');
    const stockFields = document.getElementById('edit-stock-fields');
    const grabaIceCheckbox = document.getElementById('edit-graba-ice');
    const iceFields = document.getElementById('edit-ice-fields');
    const tieneDescuentoCheckbox = document.getElementById('edit-tiene-descuento');
    const descuentoFields = document.getElementById('edit-descuento-fields');
    const esMaterialCheckbox = document.getElementById('edit-es-material-construccion');
    const materialFields = document.getElementById('edit-material-fields');

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

    // Mostrar/ocultar campos de descuento
    if (tieneDescuentoCheckbox && descuentoFields) {
        tieneDescuentoCheckbox.addEventListener('change', function() {
            if (this.checked) {
                descuentoFields.classList.remove('hidden');
            } else {
                descuentoFields.classList.add('hidden');
            }
        });
    }

    // Mostrar/ocultar campos de material de construcción
    if (esMaterialCheckbox && materialFields) {
        esMaterialCheckbox.addEventListener('change', function() {
            if (this.checked) {
                materialFields.classList.remove('hidden');
            } else {
                materialFields.classList.add('hidden');
            }
        });
    }

    // Auto-llenar porcentaje IVA cuando se selecciona código IVA
    const codigoIvaSelect = document.getElementById('edit-codigo-iva');
    const porcentajeIvaInput = document.getElementById('edit-porcentaje-iva');

    if (codigoIvaSelect && porcentajeIvaInput) {
        codigoIvaSelect.addEventListener('change', function() {
            actualizarPorcentajeIva(this.value, porcentajeIvaInput);
        });
    }

    // Inicializar validación del código auxiliar para edición
    inicializarValidacionCodigoAuxiliarEdicion();
}

/**
 * Inicializa la validación del código auxiliar para el modal de edición
 */
function inicializarValidacionCodigoAuxiliarEdicion() {
    const codigoAuxiliarInput = document.getElementById('edit-codigo-auxiliar');

    if (!codigoAuxiliarInput) return;

    let timeoutValidacion = null;

    // Permitir solo números, guiones y espacios
    codigoAuxiliarInput.addEventListener('input', function(e) {
        let value = e.target.value;

        // Remover caracteres no permitidos (solo números, guiones y espacios)
        value = value.replace(/[^0-9\-\s]/g, '');

        // Limitar longitud máxima a 25 caracteres (según base de datos)
        if (value.length > 25) {
            value = value.substring(0, 25);
        }

        e.target.value = value;

        // Limpiar timeout anterior
        if (timeoutValidacion) {
            clearTimeout(timeoutValidacion);
        }

        // Si hay contenido, validar después de 500ms de inactividad
        if (value.trim().length >= 8) {
            timeoutValidacion = setTimeout(() => {
                verificarCodigoAuxiliarEnTiempoRealEdicion(e.target, value.trim());
            }, 500);
        } else {
            // Si es muy corto, ocultar mensajes de validación
            ocultarMensajeValidacion(e.target);
        }
    });

    // Validar formato al perder el foco
    codigoAuxiliarInput.addEventListener('blur', function(e) {
        const value = e.target.value.trim();

        if (value && value.length > 0) {
            // Validar que tenga al menos 8 caracteres para códigos de barras válidos
            if (value.length < 8) {
                mostrarMensajeValidacion(e.target, 'Los códigos de barras deben tener al menos 8 dígitos', 'warning');
            } else {
                // Verificar duplicados si no se ha hecho ya
                verificarCodigoAuxiliarEnTiempoRealEdicion(e.target, value);
            }
        } else {
            ocultarMensajeValidacion(e.target);
        }
    });

    // Validar cuando el usuario pega contenido
    codigoAuxiliarInput.addEventListener('paste', function(e) {
        setTimeout(() => {
            let value = e.target.value;
            value = value.replace(/[^0-9\-\s]/g, '');
            if (value.length > 25) {
                value = value.substring(0, 25);
            }
            e.target.value = value;

            // Verificar duplicados después de limpiar
            if (value.trim().length >= 8) {
                setTimeout(() => {
                    verificarCodigoAuxiliarEnTiempoRealEdicion(e.target, value.trim());
                }, 100);
            }
        }, 10);
    });
}

/**
 * Verifica si el código auxiliar ya existe en tiempo real para el modal de edición
 */
async function verificarCodigoAuxiliarEnTiempoRealEdicion(inputElement, codigoAuxiliar) {
    try {
        // Mostrar indicador de verificación
        mostrarIndicadorVerificacion(inputElement, true);

        // Obtener el ID del producto que se está editando
        const form = document.getElementById('formEditarProducto');
        const productoId = form ? form.querySelector('input[name="producto_id"]')?.value : null;

        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=verificarCodigoAuxiliar&codigo_auxiliar=${encodeURIComponent(codigoAuxiliar)}&tenant_id=${window.TENANT_ID || 1}&producto_id=${productoId || ''}`
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const responseText = await response.text();
        const data = JSON.parse(responseText);

        // Ocultar indicador de verificación
        mostrarIndicadorVerificacion(inputElement, false);

        if (data.success) {
            if (data.existe) {
                // El código ya existe
                mostrarMensajeValidacion(inputElement, '⚠️ Este código de barras ya está en uso por otro producto', 'error');
                inputElement.classList.add('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
            } else {
                // El código está disponible
                mostrarMensajeValidacion(inputElement, '✅ Código de barras disponible', 'success');
                inputElement.classList.remove('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
                inputElement.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');

                // Ocultar mensaje de éxito después de 2 segundos
                setTimeout(() => {
                    const mensaje = inputElement.parentElement.parentElement.querySelector('[data-validation-message="true"]');
                    if (mensaje && mensaje.textContent.includes('disponible')) {
                        ocultarMensajeValidacion(inputElement);
                        inputElement.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
                    }
                }, 2000);
            }
        } else {
            mostrarMensajeValidacion(inputElement, 'Error al verificar código de barras', 'warning');
        }

    } catch (error) {
        console.error('Error al verificar código auxiliar:', error);
        mostrarIndicadorVerificacion(inputElement, false);
        mostrarMensajeValidacion(inputElement, 'Error de conexión al verificar código', 'warning');
    }
}

/**
 * Inicializa el formulario de edición
 */
function inicializarFormularioEditarProducto() {
    const formulario = document.getElementById('formEditarProducto');

    if (formulario) {
        formulario.addEventListener('submit', function(e) {
            e.preventDefault();

            if (validarFormularioEditarProducto()) {
                enviarFormularioEditarProducto();
            }
        });
    }

    // Inicializar controles específicos del formulario de edición
    inicializarControlesFormularioEdicion();
    inicializarDropzoneImagenEdicion();
}

/**
 * Valida el formulario de edición
 */
function validarFormularioEditarProducto() {
    // Reutilizar la misma lógica de validación pero con IDs de edición
    const formulario = document.getElementById('formEditarProducto');
    if (!formulario) return false;

    let esValido = true;
    const errores = [];

    // Validar campos obligatorios básicos
    const camposObligatorios = [
        { id: 'edit-descripcion', nombre: 'Descripción', minLength: 10, maxLength: 150 },
        { id: 'edit-categoria', nombre: 'Categoría' },
        { id: 'edit-tipo-producto', nombre: 'Tipo de producto' },
        { id: 'edit-precio-venta', nombre: 'Precio de venta', tipo: 'number', min: 0, max: 99999.99999 }
    ];

    camposObligatorios.forEach(campo => {
        const elemento = document.getElementById(campo.id);
        if (!elemento) return;

        const valor = elemento.value.trim();

        if (!valor || valor === '') {
            errores.push(`${campo.nombre} es obligatorio`);
            elemento.classList.add('border-red-500');
            esValido = false;
        } else {
            elemento.classList.remove('border-red-500');

            // Validaciones específicas
            if (campo.minLength && valor.length < campo.minLength) {
                errores.push(`${campo.nombre} debe tener al menos ${campo.minLength} caracteres`);
                elemento.classList.add('border-red-500');
                esValido = false;
            }

            if (campo.maxLength && valor.length > campo.maxLength) {
                errores.push(`${campo.nombre} no puede tener más de ${campo.maxLength} caracteres`);
                elemento.classList.add('border-red-500');
                esValido = false;
            }

            if (campo.tipo === 'number') {
                const numero = parseFloat(valor);
                if (isNaN(numero)) {
                    errores.push(`${campo.nombre} debe ser un número válido`);
                    elemento.classList.add('border-red-500');
                    esValido = false;
                } else {
                    if (campo.min !== undefined && numero < campo.min) {
                        errores.push(`${campo.nombre} debe ser mayor o igual a ${campo.min}`);
                        elemento.classList.add('border-red-500');
                        esValido = false;
                    }
                    if (campo.max !== undefined && numero > campo.max) {
                        errores.push(`${campo.nombre} no puede ser mayor a ${campo.max}`);
                        elemento.classList.add('border-red-500');
                        esValido = false;
                    }
                }
            }
        }
    });

    // Validación especial para código auxiliar duplicado en edición
    const editCodigoAuxiliarInput = document.getElementById('edit-codigo-auxiliar');
    if (editCodigoAuxiliarInput && editCodigoAuxiliarInput.value.trim().length > 0) {
        // Verificar si hay un mensaje de error visible
        const mensajeError = editCodigoAuxiliarInput.parentElement.parentElement.querySelector('[data-validation-message="true"]');
        if (mensajeError && mensajeError.textContent.includes('ya está en uso')) {
            errores.push('El código de barras ya está en uso por otro producto');
            editCodigoAuxiliarInput.classList.add('border-red-500');
            esValido = false;
        }
    }

    if (!esValido) {
        mostrarErroresValidacion(errores);
    }

    return esValido;
}

/**
 * Envía el formulario de edición al servidor
 */
async function enviarFormularioEditarProducto() {
    const formulario = document.getElementById('formEditarProducto');
    const submitButton = document.querySelector('button[type="submit"][form="formEditarProducto"]');

    if (!formulario) return;

    // Deshabilitar botón y mostrar loading
    const originalButtonText = submitButton ? submitButton.innerHTML : 'Actualizar Producto';
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Actualizando...</span>
        `;
    }

    try {
        const formData = new FormData(formulario);
        formData.append('action', 'actualizarProducto');
        formData.append('tenant_id', window.TENANT_ID || 1);

        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const responseText = await response.text();
        const jsonStart = responseText.indexOf('{');
        const cleanResponse = jsonStart !== -1 ? responseText.substring(jsonStart) : responseText;
        const data = JSON.parse(cleanResponse);

        if (data.success) {
            showNotification('✅ Producto actualizado exitosamente', 'success');

            // Cerrar modal
            const modal = document.getElementById('modal-editar-producto');
            if (modal && window.HSOverlay) {
                const overlay = HSOverlay.getInstance(modal, true);
                if (overlay && overlay.element) {
                    overlay.element.close();
                }
            }

            // Limpiar formulario y recargar productos
            formulario.reset();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification('❌ ' + (data.message || 'Error al actualizar el producto'), 'error');
        }
    } catch (error) {
        console.error('Error al enviar formulario de edición:', error);
        showNotification('❌ Error de conexión al actualizar el producto', 'error');
    } finally {
        // Rehabilitar botón
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }
}

/**
 * Inicializa la funcionalidad de dropzone para imágenes en edición
 */
function inicializarDropzoneImagenEdicion() {
    const imageDropzone = document.getElementById('edit-image-dropzone');
    const imageInput = document.getElementById('edit-producto-imagen');
    const imagePreview = document.getElementById('edit-image-preview');
    const dropzoneContent = document.getElementById('edit-dropzone-content');
    const removeImageBtn = document.getElementById('edit-remove-image');

    if (!imageDropzone || !imageInput) return;

    // Click en dropzone
    imageDropzone.addEventListener('click', function() {
        imageInput.click();
    });

    // Cambio de archivo
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño (2MB máximo)
            if (file.size > 2 * 1024 * 1024) {
                alert('El archivo es muy grande. El tamaño máximo es 2MB.');
                this.value = '';
                return;
            }

            // Validar tipo de archivo
            if (!file.type.startsWith('image/')) {
                alert('Por favor selecciona un archivo de imagen válido.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                dropzoneContent.classList.add('hidden');
                removeImageBtn.classList.remove('hidden');

                // Ocultar imagen actual si existe
                const currentImageSection = document.getElementById('edit-current-image-section');
                if (currentImageSection) {
                    currentImageSection.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Remover imagen nueva
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            imageInput.value = '';
            imagePreview.classList.add('hidden');
            dropzoneContent.classList.remove('hidden');
            this.classList.add('hidden');

            // Mostrar imagen actual si existe
            const currentImageSection = document.getElementById('edit-current-image-section');
            if (currentImageSection) {
                currentImageSection.classList.remove('hidden');
            }
        });
    }

    // Drag and drop
    imageDropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-blue-400', 'bg-blue-50', 'dark:border-blue-500', 'dark:bg-blue-900/20');
    });

    imageDropzone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50', 'dark:border-blue-500', 'dark:bg-blue-900/20');
    });

    imageDropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50', 'dark:border-blue-500', 'dark:bg-blue-900/20');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            const event = new Event('change', { bubbles: true });
            imageInput.dispatchEvent(event);
        }
    });
}

/**
 * Carga las categorías en el filtro
 */
async function cargarCategoriasFiltro() {
    const selectFiltroCategoria = document.getElementById('filtro-categoria');

    if (!selectFiltroCategoria) return;

    try {
        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=obtenerCategorias&tenant_id=${window.TENANT_ID || 1}`
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const responseText = await response.text();
        if (!responseText.trim()) {
            throw new Error('Respuesta vacía del servidor');
        }

        const data = JSON.parse(responseText);

        if (data.success && data.categorias) {
            // Limpiar opciones existentes pero mantener "Todas las categorías"
            const primerOption = selectFiltroCategoria.querySelector('option[value=""]');
            selectFiltroCategoria.innerHTML = '';
            if (primerOption) {
                selectFiltroCategoria.appendChild(primerOption);
            } else {
                // Crear opción "Todas las categorías" si no existe
                const optionTodas = document.createElement('option');
                optionTodas.value = '';
                optionTodas.textContent = '🏷️ Todas las categorías';
                selectFiltroCategoria.appendChild(optionTodas);
            }

            // Agregar categorías
            data.categorias.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.idcategoria;
                option.textContent = `${categoria.icono || '📁'} ${categoria.nombre}`;
                selectFiltroCategoria.appendChild(option);
            });
        }

    } catch (error) {
        console.error('Error al cargar categorías para filtro:', error);
    }
}

/**
 * Aplica los filtros seleccionados y recarga los productos
 */
function aplicarFiltros() {
    const filtros = {
        categoria: document.getElementById('filtro-categoria')?.value || '',
        estado: document.getElementById('filtro-estado')?.value || '',
        busqueda: document.getElementById('buscar-producto')?.value || ''
    };

    cargarDatosProductos(filtros);
}

/**
 * Limpia todos los filtros y recarga los productos
 */
function limpiarFiltros() {
    // Limpiar filtro de categoría
    const filtroCategoria = document.getElementById('filtro-categoria');
    if (filtroCategoria) {
        filtroCategoria.value = '';
    }

    // Establecer filtro de estado a "Solo activos" (valor por defecto)
    const filtroEstado = document.getElementById('filtro-estado');
    if (filtroEstado) {
        filtroEstado.value = '1';
    }

    // Limpiar búsqueda
    const buscarProducto = document.getElementById('buscar-producto');
    if (buscarProducto) {
        buscarProducto.value = '';
    }

    // Recargar productos con filtros limpios
    aplicarFiltros();
}

/**
 * Inicializa los event listeners para los filtros
 */
function inicializarFiltros() {
    // Filtro por categoría
    const filtroCategoria = document.getElementById('filtro-categoria');
    if (filtroCategoria) {
        filtroCategoria.addEventListener('change', aplicarFiltros);
    }

    // Filtro por estado
    const filtroEstado = document.getElementById('filtro-estado');
    if (filtroEstado) {
        filtroEstado.addEventListener('change', aplicarFiltros);
    }

    // Búsqueda de texto con debounce
    const buscarProducto = document.getElementById('buscar-producto');
    if (buscarProducto) {
        let timeoutId;
        buscarProducto.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                aplicarFiltros();
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });
    }
}

/**
 * Actualiza el contador de productos mostrados
 * @param {Array} productos - Array de productos
 */
function actualizarContadorProductos(productos) {
    // Esta función se puede llamar después de cargar productos para mostrar estadísticas
    const totalProductos = productos.length;
    const productosActivos = productos.filter(p => p.estado == 1 && (!p.deleted_at)).length;
    const productosInactivos = productos.filter(p => p.estado == 0 && (!p.deleted_at)).length;
    const productosEliminados = productos.filter(p => p.deleted_at !== null && p.deleted_at !== undefined).length;

    // Log para debugging (se puede remover en producción)
    console.log(`Total productos: ${totalProductos}, Activos: ${productosActivos}, Inactivos: ${productosInactivos}, Eliminados: ${productosEliminados}`);

    // Aquí se puede actualizar algún elemento del DOM si se desea mostrar estadísticas
    // Por ejemplo, en el header o en algún badge
}

/**
 * Elimina un producto (soft delete)
 * @param {number} idProducto - ID del producto a eliminar
 * @param {string} nombreProducto - Nombre del producto para confirmación
 */
async function eliminarProducto(idProducto, nombreProducto) {
    try {
        // Mostrar confirmación
        const confirmacion = await mostrarConfirmacionEliminacion(nombreProducto);
        if (!confirmacion) {
            return;
        }

        // Enviar petición de eliminación
        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=eliminarProducto&id=${idProducto}`
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        if (data.success) {
            showNotification('✅ Producto eliminado exitosamente', 'success');

            // Recargar la lista de productos
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(`❌ ${data.message}`, 'error');
        }

    } catch (error) {
        console.error('Error al eliminar producto:', error);
        showNotification('❌ Error de conexión al eliminar el producto', 'error');
    }
}

/**
 * Muestra modal de confirmación para eliminar producto
 * @param {string} nombreProducto - Nombre del producto
 * @returns {Promise<boolean>} - True si el usuario confirma
 */
function mostrarConfirmacionEliminacion(nombreProducto) {
    return new Promise((resolve) => {
        // Crear modal de confirmación
        const modalHtml = `
            <div id="modal-confirmar-eliminacion" class="hs-overlay fixed top-0 start-0 z-[60] w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
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
                                ¿Estás seguro de que deseas eliminar el producto <strong>"${nombreProducto}"</strong>?
                            </p>
                            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2">
                                El producto será marcado como eliminado y no aparecerá en las listas, pero se conservará para fines de auditoría.
                            </p>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button type="button" id="btn-cancelar-eliminacion" class="py-2 px-4 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                                Cancelar
                            </button>
                            <button type="button" id="btn-confirmar-eliminacion" class="py-2 px-4 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Eliminar producto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Agregar modal al DOM
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        const modal = document.getElementById('modal-confirmar-eliminacion');
        const btnCancelar = document.getElementById('btn-cancelar-eliminacion');
        const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');

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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModal(false);
            }
        });
    });
}

// Iniciar carga de datos
cargarDatosProductos();