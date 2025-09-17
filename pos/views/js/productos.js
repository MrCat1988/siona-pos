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
                return generarCodigoFallback();
            }
        } catch (error) {
            return generarCodigoFallback();
        }
    }

    /**
     * Genera un código de fallback cuando no se puede conectar a la base de datos
     * @returns {string} Código de producto de respaldo
     */
    function generarCodigoFallback() {
        const timestamp = Date.now().toString().slice(-6);
        const random = Math.floor(Math.random() * 100).toString().padStart(2, '0');
        return `PROD-${timestamp}${random}`;
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
        let codigo;
        let intentos = 0;
        const maxIntentos = 10;

        do {
            codigo = await obtenerSiguienteCodigoSecuencial();
            const existe = await verificarCodigoExiste(codigo);

            if (!existe) {
                ultimoCodigoGenerado = codigo;
                return codigo;
            }

            intentos++;

            // Si el código secuencial existe, usar fallback
            if (intentos >= 3) {
                codigo = generarCodigoFallback();
                const existeFallback = await verificarCodigoExiste(codigo);
                if (!existeFallback) {
                    ultimoCodigoGenerado = codigo;
                    return codigo;
                }
            }
        } while (intentos < maxIntentos);

        const timestampPreciso = Date.now().toString();
        codigo = `PROD-${timestampPreciso.slice(-8)}`;
        ultimoCodigoGenerado = codigo;
        return codigo;
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
 * Carga de datos inicial de productos
 */
function cargarDatosProductos() {
    // Simular carga de datos
    setTimeout(function() {
        const loadingElement = document.getElementById('productos-loading');
        const gridElement = document.getElementById('productos-grid');

        if (loadingElement) loadingElement.classList.add('hidden');
        if (gridElement) gridElement.classList.remove('hidden');
    }, 800);
}

/**
 * Inicializa la validación del código auxiliar
 */
function inicializarValidacionCodigoAuxiliar() {
    const codigoAuxiliarInput = document.getElementById('codigo-auxiliar');

    if (!codigoAuxiliarInput) return;

    // Permitir solo números, guiones y espacios
    codigoAuxiliarInput.addEventListener('input', function(e) {
        let value = e.target.value;

        // Remover caracteres no permitidos (solo números, guiones y espacios)
        value = value.replace(/[^0-9\-\s]/g, '');

        // Limitar longitud máxima a 20 caracteres
        if (value.length > 20) {
            value = value.substring(0, 20);
        }

        e.target.value = value;
    });

    // Validar formato al perder el foco
    codigoAuxiliarInput.addEventListener('blur', function(e) {
        const value = e.target.value.trim();

        if (value && value.length > 0) {
            // Validar que tenga al menos 8 caracteres para códigos de barras válidos
            if (value.length < 8) {
                mostrarMensajeValidacion(e.target, 'Los códigos de barras deben tener al menos 8 dígitos', 'warning');
            } else {
                ocultarMensajeValidacion(e.target);
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
            if (value.length > 20) {
                value = value.substring(0, 20);
            }
            e.target.value = value;
        }, 10);
    });
}

/**
 * Muestra un mensaje de validación debajo del input
 */
function mostrarMensajeValidacion(input, mensaje, tipo = 'error') {
    // Remover mensaje anterior si existe
    ocultarMensajeValidacion(input);

    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = `mt-1 text-xs ${tipo === 'error' ? 'text-red-500' : 'text-yellow-500'} flex items-center gap-1`;
    mensajeDiv.setAttribute('data-validation-message', 'true');

    const icono = tipo === 'error' ?
        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.664-.833-2.464 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>';

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
        const response = await fetch('ajax/categorias.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `accion=obtener_categorias&tenant_id=${window.TENANT_ID || 1}`
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const responseText = await response.text();
        console.log('Response from server:', responseText); // Debug log

        if (!responseText.trim()) {
            throw new Error('Respuesta vacía del servidor');
        }

        const data = JSON.parse(responseText);

        if (data.status === 'success' && data.data && data.data.categorias) {
            // Limpiar opciones existentes (mantener la primera opción de "Seleccionar")
            const primerOption = selectCategoria.querySelector('option[value=""]');
            selectCategoria.innerHTML = '';
            if (primerOption) {
                selectCategoria.appendChild(primerOption);
            } else {
                selectCategoria.innerHTML = '<option value="">Seleccionar categoría</option>';
            }

            // Agregar categorías dinámicamente
            data.data.categorias.forEach(categoria => {
                if (categoria.estado == 1) { // Solo categorías activas
                    const option = document.createElement('option');
                    option.value = categoria.idcategoria;
                    option.textContent = categoria.nombre;
                    option.title = categoria.descripcion || categoria.nombre;
                    selectCategoria.appendChild(option);
                }
            });

            // Actualizar también el filtro de categorías en la vista principal
            actualizarFiltroCategorias(data.data.categorias);
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
    const submitButton = formulario.querySelector('button[type="submit"]');

    if (!formulario) return;

    // Deshabilitar botón y mostrar loading
    const originalButtonText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
        <span>Creando...</span>
    `;

    try {
        const formData = new FormData(formulario);
        formData.append('action', 'crear');
        formData.append('tenant_id', window.TENANT_ID || 1);

        const response = await fetch('ajax/productos.ajax.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const responseText = await response.text();
        const data = JSON.parse(responseText);

        if (data.success) {
            // Éxito - mostrar mensaje y cerrar modal
            alert('Producto creado exitosamente');

            // Cerrar modal
            const modal = document.getElementById('modal-agregar-producto');
            if (modal) {
                modal.classList.add('hidden');
            }

            // Limpiar formulario
            formulario.reset();

            // Recargar vista de productos
            location.reload();
        } else {
            // Error del servidor
            alert('Error al crear el producto: ' + (data.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error al enviar formulario:', error);
        alert('Error de conexión al crear el producto');
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

// Iniciar carga de datos
cargarDatosProductos();