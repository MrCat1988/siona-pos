/*=============================================
MÓDULO DE VENTAS - PROTOTIPO FUNCIONAL
=============================================*/

// Variables globales
let carrito = [];
let clienteSeleccionado = null;

// Datos de prueba - Productos
const productosDemo = [
    // Bebidas
    {
        idproducto: 1,
        codigo: 'P0000001',
        codigo_auxiliar: '7501234567890',
        descripcion: 'Coca Cola 2L',
        precio_de_venta: 2.50,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 50,
        imagen: null
    },
    {
        idproducto: 2,
        codigo: 'P0000002',
        codigo_auxiliar: '7501234567891',
        descripcion: 'Agua Mineral 600ml',
        precio_de_venta: 0.75,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 200,
        imagen: null
    },
    {
        idproducto: 3,
        codigo: 'P0000003',
        codigo_auxiliar: '7501234567892',
        descripcion: 'Sprite 2L',
        precio_de_venta: 2.30,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 45,
        imagen: null
    },
    {
        idproducto: 4,
        codigo: 'P0000004',
        codigo_auxiliar: '7501234567893',
        descripcion: 'Jugos Del Valle 1L',
        precio_de_venta: 1.85,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 60,
        imagen: null
    },
    // Lácteos
    {
        idproducto: 5,
        codigo: 'P0000005',
        codigo_auxiliar: '7501234567894',
        descripcion: 'Leche Entera 1L',
        precio_de_venta: 1.50,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 75,
        imagen: null
    },
    {
        idproducto: 6,
        codigo: 'P0000006',
        codigo_auxiliar: '7501234567895',
        descripcion: 'Yogurt Natural 1L',
        precio_de_venta: 2.20,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 40,
        imagen: null
    },
    {
        idproducto: 7,
        codigo: 'P0000007',
        codigo_auxiliar: '7501234567896',
        descripcion: 'Queso Fresco 500g',
        precio_de_venta: 3.50,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 25,
        imagen: null
    },
    // Abarrotes
    {
        idproducto: 8,
        codigo: 'P0000008',
        codigo_auxiliar: '7501234567897',
        descripcion: 'Arroz Premium 1kg',
        precio_de_venta: 1.80,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 100,
        imagen: null
    },
    {
        idproducto: 9,
        codigo: 'P0000009',
        codigo_auxiliar: '7501234567898',
        descripcion: 'Azúcar Refinada 1kg',
        precio_de_venta: 1.20,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 80,
        imagen: null
    },
    {
        idproducto: 10,
        codigo: 'P0000010',
        codigo_auxiliar: '7501234567899',
        descripcion: 'Aceite de Girasol 1L',
        precio_de_venta: 3.20,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 40,
        imagen: null
    },
    {
        idproducto: 11,
        codigo: 'P0000011',
        codigo_auxiliar: '7501234567900',
        descripcion: 'Sal Yodada 1kg',
        precio_de_venta: 0.85,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 120,
        imagen: null
    },
    {
        idproducto: 12,
        codigo: 'P0000012',
        codigo_auxiliar: '7501234567901',
        descripcion: 'Fideo Tallarín 500g',
        precio_de_venta: 1.10,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 90,
        imagen: null
    },
    // Panadería
    {
        idproducto: 13,
        codigo: 'P0000013',
        codigo_auxiliar: '7501234567902',
        descripcion: 'Pan Integral 500g',
        precio_de_venta: 1.20,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 30,
        imagen: null
    },
    {
        idproducto: 14,
        codigo: 'P0000014',
        codigo_auxiliar: '7501234567903',
        descripcion: 'Pan de Molde 600g',
        precio_de_venta: 1.50,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 35,
        imagen: null
    },
    // Snacks
    {
        idproducto: 15,
        codigo: 'P0000015',
        codigo_auxiliar: '7501234567904',
        descripcion: 'Papas Fritas Lay\'s 150g',
        precio_de_venta: 1.75,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 55,
        imagen: null
    },
    {
        idproducto: 16,
        codigo: 'P0000016',
        codigo_auxiliar: '7501234567905',
        descripcion: 'Doritos Nachos 140g',
        precio_de_venta: 1.85,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 48,
        imagen: null
    },
    {
        idproducto: 17,
        codigo: 'P0000017',
        codigo_auxiliar: '7501234567906',
        descripcion: 'Galletas Oreo 154g',
        precio_de_venta: 1.95,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 42,
        imagen: null
    },
    // Higiene Personal
    {
        idproducto: 18,
        codigo: 'P0000018',
        codigo_auxiliar: '7501234567907',
        descripcion: 'Jabón Dove 90g',
        precio_de_venta: 1.40,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 65,
        imagen: null
    },
    {
        idproducto: 19,
        codigo: 'P0000019',
        codigo_auxiliar: '7501234567908',
        descripcion: 'Shampoo Sedal 350ml',
        precio_de_venta: 3.50,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 38,
        imagen: null
    },
    {
        idproducto: 20,
        codigo: 'P0000020',
        codigo_auxiliar: '7501234567909',
        descripcion: 'Pasta Dental Colgate 75ml',
        precio_de_venta: 2.25,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 52,
        imagen: null
    },
    // Limpieza del Hogar
    {
        idproducto: 21,
        codigo: 'P0000021',
        codigo_auxiliar: '7501234567910',
        descripcion: 'Detergente Ariel 1kg',
        precio_de_venta: 4.50,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 30,
        imagen: null
    },
    {
        idproducto: 22,
        codigo: 'P0000022',
        codigo_auxiliar: '7501234567911',
        descripcion: 'Cloro Clorox 1L',
        precio_de_venta: 2.10,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 44,
        imagen: null
    },
    {
        idproducto: 23,
        codigo: 'P0000023',
        codigo_auxiliar: '7501234567912',
        descripcion: 'Desinfectante Fabuloso 1L',
        precio_de_venta: 2.80,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 36,
        imagen: null
    },
    // Productos sin stock (para prueba)
    {
        idproducto: 24,
        codigo: 'P0000024',
        codigo_auxiliar: '7501234567913',
        descripcion: 'Producto Agotado TEST',
        precio_de_venta: 5.00,
        codigo_tarifa_iva: '2', // 15%
        porcentaje_iva: 15,
        maneja_stock: 1,
        stock_actual: 0,
        imagen: null
    },
    {
        idproducto: 25,
        codigo: 'P0000025',
        codigo_auxiliar: '7501234567914',
        descripcion: 'Atún en Lata 140g',
        precio_de_venta: 1.65,
        codigo_tarifa_iva: '0', // 0%
        porcentaje_iva: 0,
        maneja_stock: 1,
        stock_actual: 88,
        imagen: null
    }
];

// Datos de prueba - Clientes
const clientesDemo = [
    {
        idcliente: 1,
        tipo_identificacion_sri: '07',
        numero_identificacion: '9999999999999',
        nombres: 'Consumidor',
        apellidos: 'Final',
        email: null,
        telefono: null
    },
    {
        idcliente: 2,
        tipo_identificacion_sri: '05',
        numero_identificacion: '1234567890',
        nombres: 'Juan Carlos',
        apellidos: 'Pérez García',
        email: 'juan.perez@email.com',
        telefono: '0987654321'
    },
    {
        idcliente: 3,
        tipo_identificacion_sri: '04',
        numero_identificacion: '1234567890001',
        nombres: 'María Fernanda',
        apellidos: 'González López',
        email: 'maria.gonzalez@email.com',
        telefono: '0998765432'
    }
];

$(document).ready(function() {
    console.log('🛒 Módulo de Ventas inicializado');

    // Inicializar con Consumidor Final por defecto
    seleccionarCliente(clientesDemo[0]);

    // Event Listeners
    inicializarEventos();
});

/*=============================================
INICIALIZAR EVENTOS
=============================================*/
function inicializarEventos() {

    // Búsqueda de productos con debounce
    let timeoutBusquedaProducto;
    $('#buscar-producto').on('keyup', function() {
        clearTimeout(timeoutBusquedaProducto);
        const termino = $(this).val().trim();

        if (termino.length >= 2) {
            timeoutBusquedaProducto = setTimeout(function() {
                buscarProductos(termino);
            }, 300);
        } else {
            $('#productos-resultado').addClass('hidden').empty();
        }
    });

    // Búsqueda de clientes con debounce
    let timeoutBusquedaCliente;
    $('#buscar-cliente').on('keyup', function() {
        clearTimeout(timeoutBusquedaCliente);
        const termino = $(this).val().trim();

        if (termino.length >= 2) {
            timeoutBusquedaCliente = setTimeout(function() {
                buscarClientes(termino);
            }, 300);
        } else {
            $('#clientes-resultado').addClass('hidden').empty();
        }
    });

    // Limpiar carrito
    $('#btn-limpiar-carrito').on('click', function() {
        if (carrito.length > 0) {
            if (confirm('¿Está seguro que desea limpiar todo el carrito?')) {
                limpiarCarrito();
            }
        }
    });

    // Cambiar cliente
    $('#btn-cambiar-cliente').on('click', function() {
        $('#cliente-info').addClass('hidden');
        $('#buscar-cliente').val('').focus();
        clienteSeleccionado = null;
        actualizarEstadoBotones();
    });

    // Procesar venta
    $('#btn-procesar-venta').on('click', function() {
        procesarVenta();
    });

    // Guardar borrador
    $('#btn-guardar-borrador').on('click', function() {
        guardarBorrador();
    });

    // Ver facturas
    $('#btn-ver-facturas').on('click', function() {
        alert('Funcionalidad para ver facturas - Por implementar');
    });

    // Cerrar resultados al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#buscar-producto, #productos-resultado').length) {
            $('#productos-resultado').addClass('hidden');
        }
        if (!$(e.target).closest('#buscar-cliente, #clientes-resultado').length) {
            $('#clientes-resultado').addClass('hidden');
        }
    });
}

/*=============================================
BUSCAR PRODUCTOS
=============================================*/
function buscarProductos(termino) {
    console.log('🔍 Buscando productos:', termino);

    // Filtrar productos demo
    const resultados = productosDemo.filter(producto => {
        const textoCompleto = `${producto.codigo} ${producto.codigo_auxiliar} ${producto.descripcion}`.toLowerCase();
        return textoCompleto.includes(termino.toLowerCase());
    });

    mostrarResultadosProductos(resultados);
}

/*=============================================
MOSTRAR RESULTADOS DE PRODUCTOS
=============================================*/
function mostrarResultadosProductos(productos) {
    const container = $('#productos-resultado');

    if (productos.length === 0) {
        container.html(`
            <div class="p-4 text-center text-gray-500">
                <p>No se encontraron productos</p>
            </div>
        `).removeClass('hidden');
        return;
    }

    let html = '';
    productos.forEach(producto => {
        const stockClass = producto.stock_actual <= 0 ? 'text-red-600' : 'text-green-600';
        const stockText = producto.stock_actual <= 0 ? 'Sin stock' : `Stock: ${producto.stock_actual}`;

        html += `
            <div class="producto-item p-3 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer border-b border-gray-200 dark:border-neutral-700 last:border-b-0" data-producto='${JSON.stringify(producto)}'>
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800 dark:text-white">${producto.descripcion}</p>
                        <p class="text-sm text-gray-600 dark:text-neutral-400">
                            Código: ${producto.codigo} | Aux: ${producto.codigo_auxiliar}
                        </p>
                        <p class="text-xs ${stockClass} font-medium mt-1">${stockText}</p>
                    </div>
                    <div class="text-right ml-4">
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">$${parseFloat(producto.precio_de_venta).toFixed(2)}</p>
                        <p class="text-xs text-gray-500">IVA ${producto.porcentaje_iva}%</p>
                    </div>
                </div>
            </div>
        `;
    });

    container.html(html).removeClass('hidden');

    // Event listener para seleccionar producto
    $('.producto-item').on('click', function() {
        const producto = JSON.parse($(this).attr('data-producto'));
        agregarAlCarrito(producto);
        $('#buscar-producto').val('');
        $('#productos-resultado').addClass('hidden');
    });
}

/*=============================================
AGREGAR AL CARRITO
=============================================*/
function agregarAlCarrito(producto) {
    console.log('➕ Agregando al carrito:', producto);

    // Verificar si el producto ya está en el carrito
    const index = carrito.findIndex(item => item.idproducto === producto.idproducto);

    if (index !== -1) {
        // Incrementar cantidad si ya existe
        carrito[index].cantidad++;
    } else {
        // Agregar nuevo producto
        carrito.push({
            idproducto: producto.idproducto,
            codigo: producto.codigo,
            codigo_auxiliar: producto.codigo_auxiliar,
            descripcion: producto.descripcion,
            cantidad: 1,
            precio_unitario: parseFloat(producto.precio_de_venta),
            descuento: 0,
            codigo_tarifa_iva: producto.codigo_tarifa_iva,
            porcentaje_iva: producto.porcentaje_iva,
            stock_disponible: producto.stock_actual
        });
    }

    actualizarVistaCarrito();
    calcularTotales();
    actualizarEstadoBotones();
}

/*=============================================
ACTUALIZAR VISTA DEL CARRITO
=============================================*/
function actualizarVistaCarrito() {
    // Actualizar vista desktop (tabla)
    actualizarVistaDesktop();

    // Actualizar vista móvil (cards)
    actualizarVistaMobile();
}

/*=============================================
ACTUALIZAR VISTA DESKTOP (TABLA)
=============================================*/
function actualizarVistaDesktop() {
    const tbody = $('#carrito-body');

    if (carrito.length === 0) {
        tbody.html(`
            <tr id="carrito-vacio">
                <td colspan="6" class="px-3 py-8 text-center text-gray-500 dark:text-neutral-400">
                    <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="mt-2 text-sm">El carrito está vacío</p>
                    <p class="text-xs text-gray-400 dark:text-neutral-500">Busca y agrega productos</p>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    carrito.forEach((item, index) => {
        const subtotal = (item.cantidad * item.precio_unitario) - item.descuento;

        html += `
            <tr class="hover:bg-gray-50 dark:hover:bg-neutral-700">
                <td class="px-3 py-3">
                    <p class="font-medium text-gray-800 dark:text-white text-sm">${item.descripcion}</p>
                    <p class="text-xs text-gray-500 dark:text-neutral-400">${item.codigo}</p>
                </td>
                <td class="px-3 py-3">
                    <div class="flex items-center justify-center gap-1">
                        <button onclick="cambiarCantidad(${index}, -1)" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <input type="number" value="${item.cantidad}" min="1" max="${item.stock_disponible}" onchange="actualizarCantidad(${index}, this.value)" class="w-16 text-center border border-gray-300 rounded px-2 py-1 text-sm dark:bg-neutral-900 dark:border-neutral-600 dark:text-white">
                        <button onclick="cambiarCantidad(${index}, 1)" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                </td>
                <td class="px-3 py-3 text-right text-sm font-medium text-gray-800 dark:text-white">
                    $${item.precio_unitario.toFixed(2)}
                </td>
                <td class="px-3 py-3 text-right">
                    <input type="number" value="${item.descuento.toFixed(2)}" min="0" step="0.01" onchange="actualizarDescuento(${index}, this.value)" class="w-20 text-right border border-gray-300 rounded px-2 py-1 text-sm dark:bg-neutral-900 dark:border-neutral-600 dark:text-white">
                </td>
                <td class="px-3 py-3 text-right text-sm font-bold text-blue-600 dark:text-blue-400">
                    $${subtotal.toFixed(2)}
                </td>
                <td class="px-3 py-3 text-center">
                    <button onclick="eliminarDelCarrito(${index})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.html(html);
}

/*=============================================
ACTUALIZAR VISTA MOBILE (CARDS)
=============================================*/
function actualizarVistaMobile() {
    const container = $('#carrito-mobile');

    if (carrito.length === 0) {
        container.html(`
            <div id="carrito-vacio-mobile" class="py-8 text-center text-gray-500 dark:text-neutral-400">
                <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="mt-2 text-sm">El carrito está vacío</p>
                <p class="text-xs text-gray-400">Busca y agrega productos</p>
            </div>
        `);
        return;
    }

    let html = '<div class="space-y-2">';

    carrito.forEach((item, index) => {
        const subtotal = (item.cantidad * item.precio_unitario) - item.descuento;

        html += `
            <div class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-lg p-3">
                <!-- Producto Header -->
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1 min-w-0 pr-2">
                        <h4 class="font-semibold text-sm text-gray-800 dark:text-white truncate">${item.descripcion}</h4>
                        <p class="text-xs text-gray-500 dark:text-neutral-400">${item.codigo}</p>
                    </div>
                    <button onclick="eliminarDelCarrito(${index})" class="flex-shrink-0 text-red-600 hover:text-red-800 dark:text-red-400 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>

                <!-- Detalles del producto -->
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-neutral-400">Cantidad</p>
                        <div class="flex items-center gap-1 mt-1">
                            <button onclick="cambiarCantidad(${index}, -1)" class="p-1 border border-gray-300 dark:border-neutral-600 rounded text-gray-600 dark:text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number" value="${item.cantidad}" min="1" max="${item.stock_disponible}" onchange="actualizarCantidad(${index}, this.value)" class="w-12 text-center border border-gray-300 dark:border-neutral-600 rounded px-1 py-1 text-xs dark:bg-neutral-900 dark:text-white">
                            <button onclick="cambiarCantidad(${index}, 1)" class="p-1 border border-gray-300 dark:border-neutral-600 rounded text-gray-600 dark:text-gray-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-neutral-400">P. Unitario</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white mt-1">$${item.precio_unitario.toFixed(2)}</p>
                    </div>
                </div>

                <!-- Descuento -->
                <div class="mb-3">
                    <label class="text-xs text-gray-500 dark:text-neutral-400">Descuento</label>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-500 dark:text-neutral-400">$</span>
                        <input type="number" value="${item.descuento.toFixed(2)}" min="0" step="0.01" onchange="actualizarDescuento(${index}, this.value)" class="flex-1 text-right border border-gray-300 dark:border-neutral-600 rounded px-2 py-1 text-sm dark:bg-neutral-900 dark:text-white">
                    </div>
                </div>

                <!-- Subtotal -->
                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-neutral-700">
                    <span class="text-sm font-medium text-gray-600 dark:text-neutral-400">Subtotal:</span>
                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">$${subtotal.toFixed(2)}</span>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.html(html);
}

/*=============================================
CAMBIAR CANTIDAD
=============================================*/
function cambiarCantidad(index, delta) {
    const item = carrito[index];
    const nuevaCantidad = item.cantidad + delta;

    if (nuevaCantidad >= 1 && nuevaCantidad <= item.stock_disponible) {
        carrito[index].cantidad = nuevaCantidad;
        actualizarVistaCarrito();
        calcularTotales();
    } else if (nuevaCantidad < 1) {
        eliminarDelCarrito(index);
    } else {
        alert(`Stock máximo disponible: ${item.stock_disponible}`);
    }
}

/*=============================================
ACTUALIZAR CANTIDAD
=============================================*/
function actualizarCantidad(index, nuevaCantidad) {
    nuevaCantidad = parseInt(nuevaCantidad);

    if (nuevaCantidad < 1) {
        eliminarDelCarrito(index);
        return;
    }

    const item = carrito[index];
    if (nuevaCantidad > item.stock_disponible) {
        alert(`Stock máximo disponible: ${item.stock_disponible}`);
        actualizarVistaCarrito();
        return;
    }

    carrito[index].cantidad = nuevaCantidad;
    actualizarVistaCarrito();
    calcularTotales();
}

/*=============================================
ACTUALIZAR DESCUENTO
=============================================*/
function actualizarDescuento(index, nuevoDescuento) {
    nuevoDescuento = parseFloat(nuevoDescuento) || 0;

    const item = carrito[index];
    const subtotalSinDescuento = item.cantidad * item.precio_unitario;

    if (nuevoDescuento > subtotalSinDescuento) {
        alert('El descuento no puede ser mayor al subtotal');
        actualizarVistaCarrito();
        return;
    }

    carrito[index].descuento = nuevoDescuento;
    calcularTotales();
    actualizarVistaCarrito();
}

/*=============================================
ELIMINAR DEL CARRITO
=============================================*/
function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarVistaCarrito();
    calcularTotales();
    actualizarEstadoBotones();
}

/*=============================================
LIMPIAR CARRITO
=============================================*/
function limpiarCarrito() {
    carrito = [];
    actualizarVistaCarrito();
    calcularTotales();
    actualizarEstadoBotones();
    console.log('🧹 Carrito limpiado');
}

/*=============================================
CALCULAR TOTALES
=============================================*/
function calcularTotales() {
    let subtotal0 = 0;
    let subtotal15 = 0;
    let iva15 = 0;
    let descuentoTotal = 0;

    carrito.forEach(item => {
        const subtotalItem = (item.cantidad * item.precio_unitario) - item.descuento;
        descuentoTotal += item.descuento;

        if (item.porcentaje_iva === 0) {
            subtotal0 += subtotalItem;
        } else if (item.porcentaje_iva === 15) {
            // Calcular base imponible (precio sin IVA)
            const baseImponible = subtotalItem / 1.15;
            subtotal15 += baseImponible;
            iva15 += subtotalItem - baseImponible;
        }
    });

    const total = subtotal0 + subtotal15 + iva15;

    // Actualizar vista
    $('#subtotal-0').text('$' + subtotal0.toFixed(2));
    $('#subtotal-15').text('$' + subtotal15.toFixed(2));
    $('#iva-15').text('$' + iva15.toFixed(2));
    $('#descuento-total').text('$' + descuentoTotal.toFixed(2));
    $('#total-general').text('$' + total.toFixed(2));
}

/*=============================================
BUSCAR CLIENTES
=============================================*/
function buscarClientes(termino) {
    console.log('🔍 Buscando clientes:', termino);

    // Filtrar clientes demo
    const resultados = clientesDemo.filter(cliente => {
        const textoCompleto = `${cliente.nombres} ${cliente.apellidos} ${cliente.numero_identificacion}`.toLowerCase();
        return textoCompleto.includes(termino.toLowerCase());
    });

    mostrarResultadosClientes(resultados);
}

/*=============================================
MOSTRAR RESULTADOS DE CLIENTES
=============================================*/
function mostrarResultadosClientes(clientes) {
    const container = $('#clientes-resultado');

    if (clientes.length === 0) {
        container.html(`
            <div class="p-4 text-center text-gray-500">
                <p>No se encontraron clientes</p>
            </div>
        `).removeClass('hidden');
        return;
    }

    let html = '';
    clientes.forEach(cliente => {
        html += `
            <div class="cliente-item p-3 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer border-b border-gray-200 dark:border-neutral-700 last:border-b-0" data-cliente='${JSON.stringify(cliente)}'>
                <p class="font-semibold text-gray-800 dark:text-white">${cliente.nombres} ${cliente.apellidos}</p>
                <p class="text-sm text-gray-600 dark:text-neutral-400">${cliente.numero_identificacion}</p>
                ${cliente.email ? `<p class="text-xs text-gray-500 dark:text-neutral-500">${cliente.email}</p>` : ''}
            </div>
        `;
    });

    container.html(html).removeClass('hidden');

    // Event listener para seleccionar cliente
    $('.cliente-item').on('click', function() {
        const cliente = JSON.parse($(this).attr('data-cliente'));
        seleccionarCliente(cliente);
        $('#buscar-cliente').val('');
        $('#clientes-resultado').addClass('hidden');
    });
}

/*=============================================
SELECCIONAR CLIENTE
=============================================*/
function seleccionarCliente(cliente) {
    console.log('👤 Cliente seleccionado:', cliente);

    clienteSeleccionado = cliente;

    $('#cliente-seleccionado-id').val(cliente.idcliente);
    $('#cliente-nombre').text(`${cliente.nombres} ${cliente.apellidos}`);
    $('#cliente-identificacion').text(cliente.numero_identificacion);
    $('#cliente-email').text(cliente.email || 'Sin email');
    $('#cliente-info').removeClass('hidden');

    actualizarEstadoBotones();
}

/*=============================================
ACTUALIZAR ESTADO DE BOTONES
=============================================*/
function actualizarEstadoBotones() {
    const habilitarBotones = carrito.length > 0 && clienteSeleccionado !== null;

    $('#btn-procesar-venta').prop('disabled', !habilitarBotones);
    $('#btn-guardar-borrador').prop('disabled', !habilitarBotones);
}

/*=============================================
PROCESAR VENTA
=============================================*/
function procesarVenta() {
    console.log('💰 Procesando venta...');

    if (carrito.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    if (!clienteSeleccionado) {
        alert('Debe seleccionar un cliente');
        return;
    }

    // Preparar datos de la venta
    const venta = {
        cliente: clienteSeleccionado,
        metodo_pago: $('#metodo-pago').val(),
        items: carrito,
        totales: {
            subtotal_0: parseFloat($('#subtotal-0').text().replace('$', '')),
            subtotal_15: parseFloat($('#subtotal-15').text().replace('$', '')),
            iva_15: parseFloat($('#iva-15').text().replace('$', '')),
            descuento: parseFloat($('#descuento-total').text().replace('$', '')),
            total: parseFloat($('#total-general').text().replace('$', ''))
        }
    };

    console.log('📋 Datos de venta:', venta);

    // Mostrar resumen
    const resumen = `
        Cliente: ${venta.cliente.nombres} ${venta.cliente.apellidos}
        Total de productos: ${carrito.length}
        Total a pagar: $${venta.totales.total.toFixed(2)}

        ¿Desea procesar esta venta?
    `;

    if (confirm(resumen)) {
        // TODO: Aquí se enviará al backend
        alert('✅ Venta procesada exitosamente!\n\n(Esto es un prototipo - La integración con el backend está pendiente)');

        // Limpiar todo
        limpiarVenta();
    }
}

/*=============================================
GUARDAR BORRADOR
=============================================*/
function guardarBorrador() {
    console.log('💾 Guardando borrador...');

    if (carrito.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    // TODO: Aquí se enviará al backend
    alert('✅ Borrador guardado exitosamente!\n\n(Esto es un prototipo - La integración con el backend está pendiente)');
}

/*=============================================
LIMPIAR VENTA
=============================================*/
function limpiarVenta() {
    limpiarCarrito();
    seleccionarCliente(clientesDemo[0]); // Volver a Consumidor Final
    $('#metodo-pago').val('01');
    console.log('✨ Venta limpiada - Lista para nueva venta');
}

/*=============================================
NOTIFICACIÓN (HELPER)
=============================================*/
function showNotification(message, type = 'info') {
    const bgColor = type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' :
                    type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';

    const icon = type === 'success' ? '✓' :
                 type === 'error' ? '✗' :
                 type === 'warning' ? '⚠' : 'ℹ';

    const notification = $(`
        <div class="fixed top-4 right-4 z-50 ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in-down">
            <span class="text-2xl">${icon}</span>
            <span class="font-medium">${message}</span>
        </div>
    `);

    $('body').append(notification);

    setTimeout(function() {
        notification.fadeOut(400, function() {
            $(this).remove();
        });
    }, 3000);
}

/*=============================================
FUNCIONALIDAD CREAR CLIENTE DESDE VENTAS
=============================================*/

// Abrir modal de nuevo cliente
$('#btn-nuevo-cliente-venta').on('click', function() {
    abrirModalNuevoClienteVenta();
});

// Función para abrir el modal
function abrirModalNuevoClienteVenta() {
    // Limpiar formulario
    $('#form-nuevo-cliente-venta')[0].reset();
    $('#venta_tipo_identificacion_sri').val('05'); // Cédula por defecto
    $('#venta_numero_identificacion').removeClass('border-red-500 border-green-500');
    $('#venta_error_identificacion, #venta_error_duplicado').addClass('hidden');

    // Abrir modal con Preline
    window.HSOverlay.open('#modal-nuevo-cliente-venta');
}

// Validaciones en tiempo real - Tipo de identificación
$('#venta_tipo_identificacion_sri').on('change', function() {
    actualizarPlaceholderVenta();
    validarNumeroIdentificacionVenta();
});

// Validaciones en tiempo real - Número de identificación
$('#venta_numero_identificacion').on('input', function() {
    let valor = $(this).val();

    // Remover caracteres no numéricos
    valor = valor.replace(/\D/g, '');
    $(this).val(valor);

    // Limpiar borde rojo cuando empieza a escribir
    if (valor.length > 0) {
        $(this).removeClass('border-red-500');
    }

    // Detección automática del tipo
    detectarTipoIdentificacionVenta();
    validarNumeroIdentificacionVenta();

    // Verificar duplicados
    verificarDuplicadoVenta();
});

// Validación de nombres y apellidos
$('#venta_nombres, #venta_apellidos').on('blur', function() {
    const valor = $(this).val().trim();
    $(this).removeClass('border-red-500 border-green-500');

    if (valor === '') {
        $(this).addClass('border-red-500');
    } else {
        $(this).addClass('border-green-500');
    }
});

// Guardar nuevo cliente
$('#btn-guardar-cliente-venta').on('click', function() {
    guardarNuevoClienteVenta();
});

/*=============================================
ACTUALIZAR PLACEHOLDER SEGÚN TIPO
=============================================*/
function actualizarPlaceholderVenta() {
    const tipo = $('#venta_tipo_identificacion_sri').val();
    const $input = $('#venta_numero_identificacion');

    if (tipo === '05') {
        $input.attr('placeholder', 'Ej: 1234567890 (10 dígitos)');
        $input.attr('maxlength', '10');
    } else if (tipo === '04') {
        $input.attr('placeholder', 'Ej: 1234567890001 (13 dígitos)');
        $input.attr('maxlength', '13');
    } else {
        $input.attr('placeholder', 'Ej: 1234567890');
        $input.attr('maxlength', '50');
    }
}

/*=============================================
DETECTAR TIPO DE IDENTIFICACIÓN
=============================================*/
function detectarTipoIdentificacionVenta() {
    const numero = $('#venta_numero_identificacion').val().trim();
    const tipoActual = $('#venta_tipo_identificacion_sri').val();

    if (!/^\d+$/.test(numero)) {
        return;
    }

    if (numero.length === 10 && validateCedula(numero)) {
        if (tipoActual !== '05') {
            $('#venta_tipo_identificacion_sri').val('05');
            actualizarPlaceholderVenta();
        }
    } else if (numero.length === 13 && validateRuc(numero)) {
        if (tipoActual !== '04') {
            $('#venta_tipo_identificacion_sri').val('04');
            actualizarPlaceholderVenta();
        }
    }
}

/*=============================================
VALIDAR NÚMERO DE IDENTIFICACIÓN
=============================================*/
function validarNumeroIdentificacionVenta() {
    const tipo = $('#venta_tipo_identificacion_sri').val();
    const numero = $('#venta_numero_identificacion').val().trim();
    const $input = $('#venta_numero_identificacion');
    const $error = $('#venta_error_identificacion');

    $error.addClass('hidden');
    $input.removeClass('border-red-500 border-green-500');

    if (numero === '') {
        return true;
    }

    let valido = false;
    let mensaje = '';

    if (tipo === '05') {
        valido = validateCedula(numero);
        mensaje = valido ? '' : '❌ Cédula inválida (10 dígitos requeridos)';
    } else if (tipo === '04') {
        valido = validateRuc(numero);
        mensaje = valido ? '' : '❌ RUC inválido (13 dígitos requeridos)';
    } else {
        valido = numero.length > 0;
    }

    if (numero.length > 0) {
        if (valido) {
            $input.addClass('border-green-500');
        } else {
            $input.addClass('border-red-500');
            $error.text(mensaje).removeClass('hidden');
        }
    }

    return valido;
}

/*=============================================
VERIFICAR DUPLICADO
=============================================*/
let timeoutDuplicadoVenta = null;
function verificarDuplicadoVenta() {
    const numero = $('#venta_numero_identificacion').val().trim();
    const $error = $('#venta_error_duplicado');

    $error.addClass('hidden');

    if (numero.length < 5) {
        return;
    }

    clearTimeout(timeoutDuplicadoVenta);

    timeoutDuplicadoVenta = setTimeout(function() {
        $.ajax({
            url: 'ajax/clientes.ajax.php',
            method: 'POST',
            data: {
                accion: 'verificar_duplicado',
                numero_identificacion: numero,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.existe) {
                    $error.text('⚠ Este número de identificación ya está registrado').removeClass('hidden');
                    $('#venta_numero_identificacion').addClass('border-red-500');
                }
            }
        });
    }, 800);
}

/*=============================================
VALIDACIÓN DE CÉDULA
=============================================*/
function validateCedula(cedula) {
    if (cedula.length !== 10) {
        return false;
    }

    const provincia = parseInt(cedula.substring(0, 2));
    if (provincia < 1 || provincia > 24) {
        return false;
    }

    const tercerDigito = parseInt(cedula.charAt(2));
    if (tercerDigito > 5) {
        return false;
    }

    const coeficientes = [2, 1, 2, 1, 2, 1, 2, 1, 2];
    let suma = 0;

    for (let i = 0; i < 9; i++) {
        let valor = parseInt(cedula.charAt(i)) * coeficientes[i];
        if (valor > 9) {
            valor -= 9;
        }
        suma += valor;
    }

    let resultado = suma % 10;
    let digitoVerificador = resultado === 0 ? 0 : 10 - resultado;

    return digitoVerificador === parseInt(cedula.charAt(9));
}

/*=============================================
VALIDACIÓN DE RUC
=============================================*/
function validateRuc(ruc) {
    if (ruc.length !== 13) {
        return false;
    }

    const provincia = parseInt(ruc.substring(0, 2));
    if (provincia < 1 || provincia > 24) {
        return false;
    }

    const tercerDigito = parseInt(ruc.charAt(2));

    if (tercerDigito < 6) {
        return validateCedula(ruc.substring(0, 10));
    } else if (tercerDigito === 6) {
        return validateRucPublico(ruc);
    } else if (tercerDigito === 9) {
        return validateRucJuridico(ruc);
    }

    return false;
}

function validateRucPublico(ruc) {
    const coeficientes = [3, 2, 7, 6, 5, 4, 3, 2];
    let suma = 0;

    for (let i = 0; i < 8; i++) {
        suma += parseInt(ruc.charAt(i)) * coeficientes[i];
    }

    let resultado = suma % 11;
    let digitoVerificador = resultado === 0 ? 0 : 11 - resultado;

    return digitoVerificador === parseInt(ruc.charAt(8));
}

function validateRucJuridico(ruc) {
    const coeficientes = [4, 3, 2, 7, 6, 5, 4, 3, 2];
    let suma = 0;

    for (let i = 0; i < 9; i++) {
        suma += parseInt(ruc.charAt(i)) * coeficientes[i];
    }

    let resultado = suma % 11;
    let digitoVerificador = resultado === 0 ? 0 : 11 - resultado;

    return digitoVerificador === parseInt(ruc.charAt(9));
}

/*=============================================
GUARDAR NUEVO CLIENTE DESDE VENTAS
=============================================*/
function guardarNuevoClienteVenta() {
    console.log('💾 Guardando nuevo cliente desde ventas...');

    // Validar campos requeridos
    const nombres = $('#venta_nombres').val().trim();
    const apellidos = $('#venta_apellidos').val().trim();
    const numeroIdentificacion = $('#venta_numero_identificacion').val().trim();

    if (!nombres || !apellidos || !numeroIdentificacion) {
        showNotification('Por favor complete todos los campos obligatorios', 'error');
        return;
    }

    // Validar número de identificación
    if (!validarNumeroIdentificacionVenta()) {
        showNotification('Por favor corrija el número de identificación', 'error');
        return;
    }

    // Verificar si hay error de duplicado visible
    if (!$('#venta_error_duplicado').hasClass('hidden')) {
        showNotification('Este número de identificación ya está registrado', 'error');
        return;
    }

    // Establecer dirección por defecto si está vacía
    let direccion = $('#venta_direccion').val().trim();
    if (direccion === '') {
        direccion = 'Quito';
        $('#venta_direccion').val(direccion);
    }

    const formData = new FormData($('#form-nuevo-cliente-venta')[0]);
    formData.append('accion', 'crear_cliente');
    formData.set('estado', 1); // Siempre activo

    $.ajax({
        url: 'ajax/clientes.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showNotification(response.message, 'success');

                // Cerrar modal
                window.HSOverlay.close('#modal-nuevo-cliente-venta');

                // Seleccionar automáticamente el cliente recién creado
                const nuevoCliente = {
                    idcliente: response.idcliente,
                    tipo_identificacion_sri: $('#venta_tipo_identificacion_sri').val(),
                    numero_identificacion: numeroIdentificacion,
                    nombres: nombres,
                    apellidos: apellidos,
                    email: $('#venta_email').val() || null,
                    telefono: $('#venta_telefono').val() || null
                };

                seleccionarCliente(nuevoCliente);

                console.log('✅ Cliente creado y seleccionado:', nuevoCliente);
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al crear cliente:', error);
            showNotification('Error al crear el cliente', 'error');
        }
    });
}
