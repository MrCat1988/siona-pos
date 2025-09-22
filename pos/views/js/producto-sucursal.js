/*=============================================
VARIABLES GLOBALES
=============================================*/
let productosData = [];
let sucursalesData = [];
let categoriasData = [];
let filtrosActivos = {};
let modoEdicion = false;

/*=============================================
INICIALIZAR APLICACIÓN
=============================================*/
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando módulo Productos por Sucursal');

    inicializarEventos();
    cargarDatosIniciales();
});

/*=============================================
INICIALIZAR EVENTOS
=============================================*/
function inicializarEventos() {
    // Botones para abrir modal
    document.getElementById('btn-agregar-producto-sucursal').addEventListener('click', abrirModalAgregar);
    document.getElementById('btn-agregar-desde-vacio').addEventListener('click', abrirModalAgregar);

    // Botones del modal
    document.getElementById('btn-cancelar-modal').addEventListener('click', cerrarModal);
    document.getElementById('btn-guardar-producto-sucursal').addEventListener('click', guardarProductoSucursal);

    // Eventos de filtros con debounce
    document.getElementById('filtro-sucursal').addEventListener('change', aplicarFiltros);
    document.getElementById('filtro-categoria').addEventListener('change', aplicarFiltros);
    document.getElementById('filtro-estado').addEventListener('change', aplicarFiltros);

    // Búsqueda con debounce
    let timeoutBusqueda;
    document.getElementById('filtro-busqueda').addEventListener('input', function() {
        clearTimeout(timeoutBusqueda);
        timeoutBusqueda = setTimeout(aplicarFiltros, 500);
    });

    // Cambio en sucursal del modal - cargar productos disponibles
    document.getElementById('sucursal_idsucursal').addEventListener('change', cargarProductosDisponibles);

    // Cambio en producto - mostrar precio base
    document.getElementById('productos_idproducto').addEventListener('change', mostrarPrecioBase);
}

/*=============================================
CARGAR DATOS INICIALES
=============================================*/
async function cargarDatosIniciales() {
    try {
        console.log('📦 Cargando datos iniciales...');

        // Cargar datos en paralelo
        await Promise.all([
            cargarSucursales(),
            cargarCategorias(),
            cargarProductosSucursal()
        ]);

        console.log('✅ Datos iniciales cargados correctamente');

    } catch (error) {
        console.error('❌ Error al cargar datos iniciales:', error);
        showNotification('Error al cargar los datos iniciales', 'error');
    }
}

/*=============================================
CARGAR SUCURSALES
=============================================*/
async function cargarSucursales() {
    try {
        const response = await fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'accion=obtener_sucursales_disponibles'
        });

        const data = await response.json();

        if (data.success) {
            sucursalesData = data.sucursales;
            llenarSelectSucursales();
        } else {
            throw new Error(data.message || 'Error al cargar sucursales');
        }

    } catch (error) {
        console.error('Error al cargar sucursales:', error);
        showNotification('Error al cargar sucursales', 'error');
    }
}

/*=============================================
CARGAR CATEGORÍAS
=============================================*/
async function cargarCategorias() {
    try {
        const response = await fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'accion=obtener_categorias_para_filtros'
        });

        const data = await response.json();

        if (data.success) {
            categoriasData = data.categorias;
            llenarSelectCategorias();
        } else {
            throw new Error(data.message || 'Error al cargar categorías');
        }

    } catch (error) {
        console.error('Error al cargar categorías:', error);
        showNotification('Error al cargar categorías', 'error');
    }
}

/*=============================================
LLENAR SELECT DE SUCURSALES
=============================================*/
function llenarSelectSucursales() {
    // Select del filtro
    const filtroSucursal = document.getElementById('filtro-sucursal');
    filtroSucursal.innerHTML = '<option value="">🏪 Todas las sucursales</option>';

    // Select del modal
    const modalSucursal = document.getElementById('sucursal_idsucursal');
    modalSucursal.innerHTML = '<option value="">Seleccionar sucursal...</option>';

    sucursalesData.forEach(sucursal => {
        const option1 = new Option(`🏪 ${sucursal.nombre}`, sucursal.idsucursal);
        const option2 = new Option(sucursal.nombre, sucursal.idsucursal);

        filtroSucursal.appendChild(option1);
        modalSucursal.appendChild(option2);
    });
}

/*=============================================
LLENAR SELECT DE CATEGORÍAS
=============================================*/
function llenarSelectCategorias() {
    const filtroCategorias = document.getElementById('filtro-categoria');
    filtroCategorias.innerHTML = '<option value="">🏷️ Todas las categorías</option>';

    categoriasData.forEach(categoria => {
        const option = new Option(`🏷️ ${categoria.nombre}`, categoria.idcategoria);
        filtroCategorias.appendChild(option);
    });
}

/*=============================================
CARGAR PRODUCTOS POR SUCURSAL
=============================================*/
async function cargarProductosSucursal() {
    try {
        mostrarLoading(true);

        // Construir parámetros de filtros
        const params = new URLSearchParams();
        params.append('accion', 'obtener_productos_sucursal');

        Object.keys(filtrosActivos).forEach(key => {
            if (filtrosActivos[key] !== '') {
                params.append(key, filtrosActivos[key]);
            }
        });

        const response = await fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params.toString()
        });

        const data = await response.json();

        if (data.success) {
            productosData = data.productos_sucursal;
            mostrarProductos();
        } else {
            throw new Error(data.message || 'Error al cargar productos');
        }

    } catch (error) {
        console.error('Error al cargar productos por sucursal:', error);
        showNotification('Error al cargar los productos', 'error');
        mostrarEstadoVacio();
    } finally {
        mostrarLoading(false);
    }
}

/*=============================================
MOSTRAR/OCULTAR LOADING
=============================================*/
function mostrarLoading(mostrar) {
    const loading = document.getElementById('loading-productos-sucursal');
    const contenedor = document.getElementById('contenedor-productos-sucursal');
    const estadoVacio = document.getElementById('estado-vacio');

    if (mostrar) {
        loading.classList.remove('hidden');
        contenedor.classList.add('hidden');
        estadoVacio.classList.add('hidden');
    } else {
        loading.classList.add('hidden');
    }
}

/*=============================================
MOSTRAR PRODUCTOS
=============================================*/
function mostrarProductos() {
    const contenedor = document.getElementById('contenedor-productos-sucursal');
    const estadoVacio = document.getElementById('estado-vacio');

    if (productosData.length === 0) {
        mostrarEstadoVacio();
        return;
    }

    estadoVacio.classList.add('hidden');
    contenedor.classList.remove('hidden');

    // Crear grid de productos similar al módulo productos
    let html = `<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">`;

    productosData.forEach(producto => {
        html += crearCardProducto(producto);
    });

    html += '</div>';

    contenedor.innerHTML = html;
}

/*=============================================
CREAR CARD DE PRODUCTO
=============================================*/
function crearCardProducto(producto) {
    const estado = parseInt(producto.estado);
    const estadoClass = estado === 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    const estadoTexto = estado === 1 ? '✅ Activo' : '❌ Inactivo';

    // Verificar stock bajo
    const stockBajo = producto.stock_sucursal <= producto.stock_minimo_sucursal && producto.stock_minimo_sucursal > 0;
    const stockClass = stockBajo ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white';

    // Generar gradiente de colores para el header
    const gradientes = [
        'from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20',
        'from-emerald-50 via-green-50 to-teal-50 dark:from-emerald-900/20 dark:via-green-900/20 dark:to-teal-900/20',
        'from-rose-50 via-pink-50 to-red-50 dark:from-rose-900/20 dark:via-pink-900/20 dark:to-red-900/20',
        'from-amber-50 via-yellow-50 to-orange-50 dark:from-amber-900/20 dark:via-yellow-900/20 dark:to-orange-900/20'
    ];
    const gradiente = gradientes[Math.abs(hashCode(producto.codigo)) % gradientes.length];

    return `
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden group hover:-translate-y-2">
            <!-- Header con información del producto -->
            <div class="relative bg-gradient-to-br ${gradiente} p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full ${estadoClass}">
                        ${estadoTexto}
                    </span>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                            🏪 ${producto.sucursal_nombre}
                        </span>
                    </div>
                </div>

                <div class="text-center">
                    ${producto.imagen && producto.imagen.trim() !== '' ?
                        `<div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-white/50 dark:bg-black/20 flex items-center justify-center shadow-lg overflow-hidden">
                            <img src="${producto.imagen}" alt="${producto.descripcion}" class="w-full h-full object-cover rounded-xl">
                        </div>` :
                        `<div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-lg">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>`
                    }
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 line-clamp-1">${producto.descripcion}</h3>
                    <p class="text-sm text-gray-600 dark:text-neutral-400 mb-3">🏷️ ${producto.categoria_nombre}</p>
                </div>
            </div>

            <!-- Información principal -->
            <div class="p-6 space-y-4">
                <!-- Códigos -->
                <div class="bg-gray-50 dark:bg-neutral-800/50 rounded-xl p-4">
                    <div class="grid grid-cols-1 gap-2 text-sm">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-neutral-400 mb-1">Código del Producto</p>
                            <p class="font-semibold text-gray-900 dark:text-white">${producto.codigo}</p>
                        </div>
                    </div>
                </div>

                <!-- Precios destacados -->
                <div class="bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Base</p>
                            <p class="text-sm font-semibold text-gray-700 dark:text-neutral-300">$${parseFloat(producto.precio_base).toFixed(2)}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Sucursal</p>
                            <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400">$${parseFloat(producto.precio_sucursal).toFixed(2)}</p>
                        </div>
                    </div>
                </div>

                <!-- Stock y límites -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div>
                            <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Stock</p>
                            <p class="text-lg font-bold ${stockClass}">
                                ${producto.stock_sucursal}
                                ${stockBajo ? ' ⚠️' : ''}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Mínimo</p>
                            <p class="text-sm font-semibold text-gray-700 dark:text-neutral-300">${producto.stock_minimo_sucursal}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Máximo</p>
                            <p class="text-sm font-semibold text-gray-700 dark:text-neutral-300">${producto.stock_maximo_sucursal}</p>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex gap-3 pt-2">
                    <button onclick="editarProductoSucursal(${producto.idproducto_sucursal})"
                            class="flex-1 py-2.5 px-4 text-sm font-medium text-blue-600 hover:text-white bg-white hover:bg-blue-600 border border-blue-300 rounded-lg transition-all duration-200 hover:shadow-md dark:bg-neutral-700 dark:border-blue-500 dark:text-blue-400 dark:hover:bg-blue-600 dark:hover:text-white">
                        ✏️ Editar
                    </button>

                    <button onclick="eliminarProductoSucursal(${producto.idproducto_sucursal}, '${producto.codigo} - ${producto.sucursal_nombre}')"
                            class="flex-1 py-2.5 px-4 text-sm font-medium text-red-600 hover:text-white bg-white hover:bg-red-600 border border-red-300 rounded-lg transition-all duration-200 hover:shadow-md dark:bg-neutral-700 dark:border-red-500 dark:text-red-400 dark:hover:bg-red-600 dark:hover:text-white">
                        🗑️ Eliminar
                    </button>
                </div>
            </div>
        </div>
    `;
}

/*=============================================
FUNCIÓN AUXILIAR PARA GENERAR HASH
=============================================*/
function hashCode(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        const char = str.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
}

/*=============================================
MOSTRAR ESTADO VACÍO
=============================================*/
function mostrarEstadoVacio() {
    const contenedor = document.getElementById('contenedor-productos-sucursal');
    const estadoVacio = document.getElementById('estado-vacio');

    contenedor.classList.add('hidden');
    estadoVacio.classList.remove('hidden');
}

/*=============================================
APLICAR FILTROS
=============================================*/
function aplicarFiltros() {
    console.log('🔍 Aplicando filtros...');

    filtrosActivos = {
        sucursal: document.getElementById('filtro-sucursal').value,
        categoria: document.getElementById('filtro-categoria').value,
        estado: document.getElementById('filtro-estado').value,
        busqueda: document.getElementById('filtro-busqueda').value.trim()
    };

    console.log('Filtros activos:', filtrosActivos);
    cargarProductosSucursal();
}

/*=============================================
ABRIR MODAL AGREGAR
=============================================*/
function abrirModalAgregar() {
    modoEdicion = false;

    document.getElementById('modal-titulo').textContent = 'Asignar Producto a Sucursal';
    document.getElementById('texto-boton-guardar').textContent = 'Asignar Producto';

    // Limpiar formulario
    document.getElementById('form-producto-sucursal').reset();
    document.getElementById('idproducto_sucursal').value = '';

    // Limpiar productos disponibles
    const selectProductos = document.getElementById('productos_idproducto');
    selectProductos.innerHTML = '<option value="">Primero selecciona una sucursal...</option>';

    // Abrir modal
    window.HSOverlay.open(document.getElementById('modal-producto-sucursal'));
}

/*=============================================
CARGAR PRODUCTOS DISPONIBLES
=============================================*/
async function cargarProductosDisponibles() {
    const sucursalId = document.getElementById('sucursal_idsucursal').value;
    const selectProductos = document.getElementById('productos_idproducto');

    if (!sucursalId) {
        selectProductos.innerHTML = '<option value="">Primero selecciona una sucursal...</option>';
        return;
    }

    try {
        selectProductos.innerHTML = '<option value="">Cargando productos...</option>';

        const response = await fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `accion=obtener_productos_disponibles&sucursal_id=${sucursalId}`
        });

        const data = await response.json();

        if (data.success) {
            selectProductos.innerHTML = '<option value="">Seleccionar producto...</option>';

            data.productos.forEach(producto => {
                const option = new Option(
                    `${producto.codigo} - ${producto.descripcion} ($${parseFloat(producto.precio_de_venta).toFixed(2)})`,
                    producto.idproducto
                );
                option.dataset.precioBase = producto.precio_de_venta;
                selectProductos.appendChild(option);
            });

        } else {
            selectProductos.innerHTML = '<option value="">No hay productos disponibles</option>';
        }

    } catch (error) {
        console.error('Error al cargar productos disponibles:', error);
        selectProductos.innerHTML = '<option value="">Error al cargar productos</option>';
    }
}

/*=============================================
MOSTRAR PRECIO BASE
=============================================*/
function mostrarPrecioBase() {
    const selectProducto = document.getElementById('productos_idproducto');
    const inputPrecio = document.getElementById('precio_sucursal');

    if (selectProducto.selectedIndex > 0) {
        const precioBase = selectProducto.options[selectProducto.selectedIndex].dataset.precioBase;
        if (precioBase && inputPrecio.value === '') {
            inputPrecio.value = parseFloat(precioBase).toFixed(5);
        }
    }
}

/*=============================================
GUARDAR PRODUCTO-SUCURSAL
=============================================*/
async function guardarProductoSucursal() {
    try {
        const form = document.getElementById('form-producto-sucursal');
        const formData = new FormData(form);

        // Agregar acción
        const accion = modoEdicion ? 'actualizar_producto_sucursal' : 'crear_producto_sucursal';
        formData.append('accion', accion);

        // Deshabilitar botón
        const btnGuardar = document.getElementById('btn-guardar-producto-sucursal');
        const textoOriginal = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '⏳ Guardando...';

        const response = await fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showNotification(data.message, 'success');
            cerrarModal();
            cargarProductosSucursal();
        } else {
            showNotification(data.message, 'error');
        }

    } catch (error) {
        console.error('Error al guardar:', error);
        showNotification('Error de conexión al guardar', 'error');
    } finally {
        // Rehabilitar botón
        const btnGuardar = document.getElementById('btn-guardar-producto-sucursal');
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = textoOriginal;
    }
}

/*=============================================
EDITAR PRODUCTO-SUCURSAL
=============================================*/
async function editarProductoSucursal(idProductoSucursal) {
    try {
        modoEdicion = true;

        document.getElementById('modal-titulo').textContent = 'Editar Producto en Sucursal';
        document.getElementById('texto-boton-guardar').textContent = 'Actualizar';

        // Cargar datos del producto-sucursal
        const response = await fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `accion=obtener_producto_sucursal_por_id&id=${idProductoSucursal}`
        });

        const data = await response.json();

        if (data.success) {
            const producto = data.producto_sucursal;

            // Llenar formulario
            document.getElementById('idproducto_sucursal').value = producto.idproducto_sucursal;
            document.getElementById('sucursal_idsucursal').value = producto.sucursal_idsucursal;
            document.getElementById('precio_sucursal').value = parseFloat(producto.precio_sucursal).toFixed(5);
            document.getElementById('stock_sucursal').value = producto.stock_sucursal;
            document.getElementById('stock_minimo_sucursal').value = producto.stock_minimo_sucursal;
            document.getElementById('stock_maximo_sucursal').value = producto.stock_maximo_sucursal;
            document.getElementById('estado').value = producto.estado;

            // Para edición, mostrar el producto actual (no productos disponibles)
            const selectProductos = document.getElementById('productos_idproducto');
            selectProductos.innerHTML = `<option value="${producto.productos_idproducto}">${producto.codigo} - ${producto.descripcion}</option>`;
            selectProductos.value = producto.productos_idproducto;
            selectProductos.disabled = true; // No permitir cambiar producto en edición

            // Abrir modal
            window.HSOverlay.open(document.getElementById('modal-producto-sucursal'));

        } else {
            showNotification(data.message, 'error');
        }

    } catch (error) {
        console.error('Error al cargar datos para edición:', error);
        showNotification('Error al cargar los datos', 'error');
    }
}

/*=============================================
ELIMINAR PRODUCTO-SUCURSAL
=============================================*/
async function eliminarProductoSucursal(idProductoSucursal, descripcion) {
    const confirmado = await mostrarConfirmacionEliminacion(descripcion);

    if (!confirmado) return;

    try {
        const response = await fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `accion=eliminar_producto_sucursal&idproducto_sucursal=${idProductoSucursal}`
        });

        const data = await response.json();

        if (data.success) {
            showNotification(data.message, 'success');
            cargarProductosSucursal();
        } else {
            showNotification(data.message, 'error');
        }

    } catch (error) {
        console.error('Error al eliminar:', error);
        showNotification('Error de conexión al eliminar', 'error');
    }
}

/*=============================================
MODAL DE CONFIRMACIÓN PARA ELIMINAR
=============================================*/
function mostrarConfirmacionEliminacion(descripcion) {
    return new Promise((resolve) => {
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
                                ¿Estás seguro de que deseas eliminar la asignación <strong>"${descripcion}"</strong>?
                            </p>
                            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2">
                                El producto será removido de la sucursal pero se conservará para fines de auditoría.
                            </p>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button type="button" id="btn-cancelar-eliminacion" class="py-2 px-4 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                                Cancelar
                            </button>
                            <button type="button" id="btn-confirmar-eliminacion" class="py-2 px-4 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Eliminar asignación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        const modal = document.getElementById('modal-confirmar-eliminacion');
        const btnCancelar = document.getElementById('btn-cancelar-eliminacion');
        const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');

        modal.style.display = 'flex';

        const cerrarModal = (resultado) => {
            modal.remove();
            resolve(resultado);
        };

        btnCancelar.addEventListener('click', () => cerrarModal(false));
        btnConfirmar.addEventListener('click', () => cerrarModal(true));

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

/*=============================================
CERRAR MODAL
=============================================*/
function cerrarModal() {
    window.HSOverlay.close(document.getElementById('modal-producto-sucursal'));

    // Rehabilitar select de productos si estaba deshabilitado
    document.getElementById('productos_idproducto').disabled = false;
}

/*=============================================
MOSTRAR NOTIFICACIÓN
=============================================*/
function showNotification(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const id = 'toast-' + Date.now();

    const typeConfig = {
        success: {
            icon: '✅',
            bgColor: 'bg-green-500',
            textColor: 'text-white'
        },
        error: {
            icon: '❌',
            bgColor: 'bg-red-500',
            textColor: 'text-white'
        },
        info: {
            icon: 'ℹ️',
            bgColor: 'bg-blue-500',
            textColor: 'text-white'
        },
        warning: {
            icon: '⚠️',
            bgColor: 'bg-yellow-500',
            textColor: 'text-white'
        }
    };

    const config = typeConfig[type] || typeConfig.info;

    const toast = document.createElement('div');
    toast.id = id;
    toast.className = `${config.bgColor} ${config.textColor} px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <span class="text-lg">${config.icon}</span>
            <span class="font-medium">${message}</span>
            <button onclick="removeToast('${id}')" class="ml-2 hover:opacity-70">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    container.appendChild(toast);

    // Animar entrada
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
    }, 100);

    // Auto-remove después de 5 segundos
    setTimeout(() => {
        removeToast(id);
    }, 5000);
}

/*=============================================
REMOVER NOTIFICACIÓN
=============================================*/
function removeToast(id) {
    const toast = document.getElementById(id);
    if (toast) {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}

console.log('✅ Producto-Sucursal JS cargado correctamente');