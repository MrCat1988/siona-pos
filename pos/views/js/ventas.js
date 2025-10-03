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

    // Cargar Consumidor Final desde base de datos por defecto
    cargarConsumidorFinal();

    // Event Listeners
    inicializarEventos();
});

/*=============================================
CARGAR CONSUMIDOR FINAL POR DEFECTO
=============================================*/
function cargarConsumidorFinal() {
    console.log('📡 Iniciando carga de Consumidor Final...');
    $.ajax({
        url: 'ajax/clientes.ajax.php',
        method: 'POST',
        data: {
            accion: 'obtener_clientes',
            csrf_token: $('input[name="csrf_token"]').val(),
            tipo_identificacion: '07', // Consumidor Final
            estado: 1,
            limit: 1
        },
        dataType: 'json',
        success: function(response) {
            console.log('📥 Respuesta recibida:', response);
            if (response.status === 'success' && response.data.length > 0) {
                const consumidorFinal = response.data[0];
                console.log('👤 Consumidor Final encontrado:', consumidorFinal);

                // Cargar datos en formulario
                clienteSeleccionado = consumidorFinal;
                $('#cliente-seleccionado-id').val(consumidorFinal.idcliente);
                $('#cliente_tipo_identificacion_sri').val('07');
                $('#cliente_numero_identificacion').val(consumidorFinal.numero_identificacion);
                $('#cliente_nombres').val(consumidorFinal.nombres);
                $('#cliente_apellidos').val(consumidorFinal.apellidos);
                $('#cliente_email').val(consumidorFinal.email || '');
                $('#cliente_telefono').val(consumidorFinal.telefono || '');
                $('#cliente_direccion').val(consumidorFinal.direccion || 'Quito');
                $('#cliente_estado').val('existente');

                // Hacer todos los campos de solo lectura (Consumidor Final no es editable)
                bloquearEdicionCliente();

                console.log('✅ Consumidor Final cargado (solo lectura)');
                showNotification('✅ Consumidor Final cargado', 'success');
            } else {
                console.warn('⚠️ No se encontró Consumidor Final en la base de datos');
                console.warn('Respuesta completa:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ Error al cargar Consumidor Final:', error);
            console.error('Estado:', status);
            console.error('Respuesta completa:', xhr.responseText);
        }
    });
}

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

    // Búsqueda automática por número de identificación (6+ dígitos)
    let timeoutBusquedaNumero;
    $('#cliente_numero_identificacion').on('input', function() {
        let valor = $(this).val();

        // Permitir solo números
        valor = valor.replace(/[^0-9]/g, '');
        $(this).val(valor);

        // Limpiar timeout anterior
        clearTimeout(timeoutBusquedaNumero);

        // Auto-detectar tipo según longitud
        if (valor.length === 10) {
            $('#cliente_tipo_identificacion_sri').val('05'); // Cédula
            // Validar cédula completa
            validarIdentificacionCliente();
        } else if (valor.length === 13) {
            $('#cliente_tipo_identificacion_sri').val('04'); // RUC
            // Validar RUC completo
            validarIdentificacionCliente();
        } else {
            // Limpiar validación visual si tiene menos de 10/13 dígitos
            $('#cliente_numero_identificacion').removeClass('border-red-500 border-green-500');
            $('#cliente_error_identificacion').addClass('hidden');
        }

        // Buscar si tiene 6+ dígitos
        if (valor.length >= 6) {
            timeoutBusquedaNumero = setTimeout(function() {
                buscarClientePorIdentificacion(valor);
            }, 500);
        } else {
            $('#clientes-resultado').addClass('hidden');
        }

        actualizarEstadoBotones();
    });

    // Búsqueda automática por apellidos (3+ caracteres)
    let timeoutBusquedaApellidos;
    $('#cliente_apellidos').on('input', function() {
        const termino = $(this).val().trim();

        // Limpiar timeout anterior
        clearTimeout(timeoutBusquedaApellidos);

        // Buscar si tiene 3+ caracteres
        if (termino.length >= 3) {
            timeoutBusquedaApellidos = setTimeout(function() {
                buscarClientePorApellidos(termino);
            }, 500);
        } else {
            $('#clientes-resultado').addClass('hidden');
        }

        actualizarEstadoBotones();
    });

    // Actualizar botones cuando cambia el formulario de cliente
    $('#cliente_nombres').on('input', function() {
        actualizarEstadoBotones();
    });

    // Validar cuando cambia el tipo manualmente
    $('#cliente_tipo_identificacion_sri').on('change', function() {
        const tipo = $(this).val();

        if (tipo === '07') {
            // Si selecciona Consumidor Final, cargar desde DB
            cargarConsumidorFinal();
        } else {
            // Si cambia a otro tipo, habilitar edición
            desbloquearEdicionCliente();
            limpiarFormularioCliente();
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
        if (!$(e.target).closest('#cliente_numero_identificacion, #cliente_apellidos, #clientes-resultado').length) {
            $('#clientes-resultado').addClass('hidden');
        }
    });
}

/*=============================================
BUSCAR PRODUCTOS EN BASE DE DATOS
=============================================*/
function buscarProductos(termino) {
    console.log('🔍 Buscando productos:', termino);

    // Buscar en base de datos real
    $.ajax({
        url: 'ajax/producto-sucursal.ajax.php',
        method: 'POST',
        data: {
            accion: 'obtener_productos_sucursal',
            busqueda: termino,
            estado: 1, // Solo productos activos
            limite: 20 // Limitar resultados para búsqueda rápida
        },
        dataType: 'json',
        success: function(response) {
            console.log('📦 Productos encontrados:', response);

            if (response.success && response.data && response.data.length > 0) {
                // Filtrar productos con stock disponible
                const productosConStock = response.data.filter(p => p.stock_actual > 0);
                mostrarResultadosProductos(productosConStock);
            } else {
                mostrarResultadosProductos([]);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al buscar productos:', error);
            showNotification('Error al buscar productos', 'error');
            mostrarResultadosProductos([]);
        }
    });
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
BUSCAR CLIENTE POR NÚMERO DE IDENTIFICACIÓN
=============================================*/
function buscarClientePorIdentificacion(numero) {
    console.log('🔍 Buscando cliente por identificación:', numero);

    $.ajax({
        url: 'ajax/clientes.ajax.php',
        method: 'POST',
        data: {
            accion: 'obtener_clientes',
            csrf_token: $('input[name="csrf_token"]').val(),
            busqueda: numero,
            estado: 1,
            limit: 10
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.data.length > 0) {
                mostrarResultadosClientes(response.data, 'numero_identificacion');
            } else {
                $('#clientes-resultado').addClass('hidden');
                // No se encontró - dejar campos vacíos para crear nuevo
                console.log('ℹ️ Cliente no encontrado con ese número');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al buscar clientes:', error);
        }
    });
}

/*=============================================
BUSCAR CLIENTE POR APELLIDOS
=============================================*/
function buscarClientePorApellidos(termino) {
    console.log('🔍 Buscando cliente por apellidos:', termino);

    $.ajax({
        url: 'ajax/clientes.ajax.php',
        method: 'POST',
        data: {
            accion: 'obtener_clientes',
            csrf_token: $('input[name="csrf_token"]').val(),
            busqueda: termino,
            estado: 1,
            limit: 10
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.data.length > 0) {
                mostrarResultadosClientes(response.data, 'apellidos');
            } else {
                $('#clientes-resultado').addClass('hidden');
                // No se encontró - dejar campos para crear nuevo
                console.log('ℹ️ Cliente no encontrado con ese apellido');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al buscar clientes:', error);
        }
    });
}


/*=============================================
MOSTRAR RESULTADOS DE CLIENTES
=============================================*/
function mostrarResultadosClientes(clientes, campoOrigen) {
    const container = $('#clientes-resultado');

    if (clientes.length === 0) {
        container.addClass('hidden');
        return;
    }

    // Posicionar el dropdown relativo al campo que activó la búsqueda
    let $campoReferencia;
    if (campoOrigen === 'numero_identificacion') {
        $campoReferencia = $('#cliente_numero_identificacion');
    } else {
        $campoReferencia = $('#cliente_apellidos');
    }

    const offset = $campoReferencia.offset();
    const height = $campoReferencia.outerHeight();

    container.css({
        'position': 'absolute',
        'top': offset.top + height + 'px',
        'left': offset.left + 'px',
        'width': $campoReferencia.outerWidth() + 'px',
        'max-height': '300px',
        'overflow-y': 'auto'
    });

    let html = '<div class="py-1">';
    clientes.forEach(cliente => {
        html += `
            <div class="cliente-item p-3 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer border-b border-gray-200 dark:border-neutral-700 last:border-b-0" data-cliente='${JSON.stringify(cliente)}'>
                <p class="font-semibold text-sm text-gray-800 dark:text-white">${cliente.nombres} ${cliente.apellidos}</p>
                <p class="text-xs text-gray-600 dark:text-neutral-400">${cliente.numero_identificacion}</p>
                ${cliente.email ? `<p class="text-xs text-gray-500 dark:text-neutral-500">${cliente.email}</p>` : ''}
            </div>
        `;
    });
    html += '</div>';

    container.html(html).removeClass('hidden');

    // Event listener para seleccionar cliente
    $('.cliente-item').on('click', function() {
        const cliente = JSON.parse($(this).attr('data-cliente'));
        cargarClienteEnFormulario(cliente);
        $('#clientes-resultado').addClass('hidden');
    });
}

/*=============================================
CARGAR CLIENTE EN FORMULARIO (Cliente Existente)
=============================================*/
function cargarClienteEnFormulario(cliente) {
    console.log('👤 Cargando cliente en formulario:', cliente);

    clienteSeleccionado = cliente;

    // Llenar campos del formulario
    $('#cliente-seleccionado-id').val(cliente.idcliente);
    $('#cliente_tipo_identificacion_sri').val(cliente.tipo_identificacion_sri);
    $('#cliente_numero_identificacion').val(cliente.numero_identificacion);
    $('#cliente_nombres').val(cliente.nombres);
    $('#cliente_apellidos').val(cliente.apellidos);
    $('#cliente_email').val(cliente.email || '');
    $('#cliente_telefono').val(cliente.telefono || '');
    $('#cliente_direccion').val(cliente.direccion || 'Quito');

    // Marcar como cliente existente
    $('#cliente_estado').val('existente');

    // Si es Consumidor Final (tipo_identificacion_sri = '07'), hacer campos de solo lectura
    if (cliente.tipo_identificacion_sri === '07') {
        bloquearEdicionCliente();
        showNotification('✅ Consumidor Final cargado', 'success');
    } else {
        // Para clientes normales: solo lectura (no editable desde ventas)
        bloquearEdicionCliente();
        showNotification('✅ Cliente cargado: ' + cliente.nombres + ' ' + cliente.apellidos, 'success');
    }

    actualizarEstadoBotones();
}

/*=============================================
BLOQUEAR EDICIÓN DE CLIENTE (Para Consumidor Final)
=============================================*/
function bloquearEdicionCliente() {
    $('#cliente_tipo_identificacion_sri').prop('disabled', true);
    $('#cliente_numero_identificacion').prop('readonly', true).addClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_nombres').prop('readonly', true).addClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_apellidos').prop('readonly', true).addClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_email').prop('readonly', true).addClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_telefono').prop('readonly', true).addClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_direccion').prop('readonly', true).addClass('bg-gray-100 cursor-not-allowed');
}

/*=============================================
DESBLOQUEAR EDICIÓN DE CLIENTE (Para clientes normales)
=============================================*/
function desbloquearEdicionCliente() {
    $('#cliente_tipo_identificacion_sri').prop('disabled', false);
    $('#cliente_numero_identificacion').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_nombres').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_apellidos').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_email').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_telefono').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
    $('#cliente_direccion').prop('readonly', false).removeClass('bg-gray-100 cursor-not-allowed');
}

/*=============================================
LIMPIAR FORMULARIO DE CLIENTE
=============================================*/
function limpiarFormularioCliente() {
    $('#cliente-seleccionado-id').val('');
    $('#cliente_numero_identificacion').val('');
    $('#cliente_nombres').val('');
    $('#cliente_apellidos').val('');
    $('#cliente_email').val('');
    $('#cliente_telefono').val('');
    $('#cliente_direccion').val('');
    $('#cliente_estado').val('nuevo');

    // Limpiar validaciones visuales
    $('#cliente_numero_identificacion').removeClass('border-red-500 border-green-500');
    $('#cliente_error_identificacion').addClass('hidden');

    clienteSeleccionado = null;
    actualizarEstadoBotones();
}


/*=============================================
ACTUALIZAR ESTADO DE BOTONES
=============================================*/
function actualizarEstadoBotones() {
    // Validar si hay productos en el carrito
    const hayProductos = carrito.length > 0;

    // Validar si los campos de cliente están visibles y completos
    const camposVisibles = !$('#field-numero-id').hasClass('hidden');
    const numeroIdentificacion = $('#cliente_numero_identificacion').val().trim();
    const tieneNombres = $('#cliente_nombres').val().trim() !== '';
    const tieneApellidos = $('#cliente_apellidos').val().trim() !== '';

    // Validar que la identificación sea válida (sin error visible)
    const identificacionValida = numeroIdentificacion !== '' &&
                                 !$('#cliente_numero_identificacion').hasClass('border-red-500');

    const clienteCompleto = camposVisibles && identificacionValida && tieneNombres && tieneApellidos;

    // Habilitar botones solo si hay productos Y cliente completo con identificación válida
    const habilitarBotones = hayProductos && clienteCompleto;

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
    cargarConsumidorFinal(); // Volver a Consumidor Final
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
VALIDAR IDENTIFICACIÓN DEL CLIENTE
=============================================*/
function validarIdentificacionCliente() {
    const tipo = $('#cliente_tipo_identificacion_sri').val();
    const numero = $('#cliente_numero_identificacion').val().trim();
    const $input = $('#cliente_numero_identificacion');
    const $error = $('#cliente_error_identificacion');

    // Si es Consumidor Final, no validar (ya está precargado)
    if (tipo === '07') {
        return true;
    }

    // Limpiar estilos previos
    $input.removeClass('border-red-500 border-green-500');
    $error.addClass('hidden').text('');

    if (numero.length === 0) {
        return false;
    }

    let esValido = true;
    let mensaje = '';

    // Validación para Cédula (05)
    if (tipo === '05') {
        if (numero.length !== 10) {
            esValido = false;
            mensaje = 'La cédula debe tener exactamente 10 dígitos';
        } else if (!validateCedula(numero)) {
            esValido = false;
            mensaje = 'Número de cédula inválido';
        }
    }
    // Validación para RUC (04)
    else if (tipo === '04') {
        if (numero.length !== 13) {
            esValido = false;
            mensaje = 'El RUC debe tener exactamente 13 dígitos';
        } else if (!validateRuc(numero)) {
            esValido = false;
            mensaje = 'Número de RUC inválido';
        }
    }

    // Mostrar feedback visual
    if (esValido && numero.length > 0) {
        $input.addClass('border-green-500');
        $error.addClass('hidden');
    } else if (!esValido) {
        $input.addClass('border-red-500');
        $error.removeClass('hidden').text(mensaje);
    }

    return esValido;
}

/*=============================================
VALIDAR CÉDULA ECUATORIANA
=============================================*/
function validateCedula(id_number) {
    var cad = id_number.trim();
    var total = 0;
    var longitud = cad.length;
    var longcheck = longitud - 1;

    if (cad !== "" && longitud === 10){
        for(i = 0; i < longcheck; i++){
            if (i%2 === 0) {
                var aux = cad.charAt(i) * 2;
                if (aux > 9) aux -= 9;
                total += aux;
            } else {
                total += parseInt(cad.charAt(i));
            }
        }
        total = total % 10 ? 10 - total % 10 : 0;

        if (cad.charAt(longitud-1) == total) {
            return true;
        }else{
            return false;
        }
    }
    return false;
}

/*=============================================
VALIDAR RUC ECUATORIANO
=============================================*/
function validateRuc(id_number) {
    numero = id_number.trim();

    var suma = 0;
    var residuo = 0;
    var pri = false;
    var pub = false;
    var nat = false;
    var numeroProvincias = 22;
    var modulo = 11;

    /* Verifico que el campo no contenga letras */
    var ok=1;
    for (i=0; i<numero.length && ok==1 ; i++){
        var n = parseInt(numero.charAt(i));
        if (isNaN(n)) ok=0;
    }
    if (ok==0){
        return false;
    }

    if (numero.length < 10 ){
        return false;
    }

    /* Los primeros dos digitos corresponden al codigo de la provincia */
    provincia = numero.substr(0,2);
    if (provincia < 1 || provincia > numeroProvincias){
        return false;
    }

    /* Aqui almacenamos los digitos de la cedula en variables. */
    d1  = numero.substr(0,1);
    d2  = numero.substr(1,1);
    d3  = numero.substr(2,1);
    d4  = numero.substr(3,1);
    d5  = numero.substr(4,1);
    d6  = numero.substr(5,1);
    d7  = numero.substr(6,1);
    d8  = numero.substr(7,1);
    d9  = numero.substr(8,1);
    d10 = numero.substr(9,1);

    /* El tercer digito es: */
    /* 9 para sociedades privadas y extranjeros */
    /* 6 para sociedades publicas */
    /* menor a 6 (0,1,2,3,4,5) para personas naturales */

    if (d3==7 || d3==8){
        return false;
    }

    /* Solo para personas naturales (modulo 10) */
    if (d3 < 6){
        nat = true;
        p1 = d1 * 2;  if (p1 >= 10) p1 -= 9;
        p2 = d2 * 1;  if (p2 >= 10) p2 -= 9;
        p3 = d3 * 2;  if (p3 >= 10) p3 -= 9;
        p4 = d4 * 1;  if (p4 >= 10) p4 -= 9;
        p5 = d5 * 2;  if (p5 >= 10) p5 -= 9;
        p6 = d6 * 1;  if (p6 >= 10) p6 -= 9;
        p7 = d7 * 2;  if (p7 >= 10) p7 -= 9;
        p8 = d8 * 1;  if (p8 >= 10) p8 -= 9;
        p9 = d9 * 2;  if (p9 >= 10) p9 -= 9;
        modulo = 10;
    }

    /* Solo para sociedades publicas (modulo 11) */
    /* Aqui el digito verficador esta en la posicion 9, en las otras 2 en la pos. 10 */
    else if(d3 == 6){
        pub = true;
        p1 = d1 * 3;
        p2 = d2 * 2;
        p3 = d3 * 7;
        p4 = d4 * 6;
        p5 = d5 * 5;
        p6 = d6 * 4;
        p7 = d7 * 3;
        p8 = d8 * 2;
        p9 = 0;
    }

    /* Solo para entidades privadas (modulo 11) */
    else if(d3 == 9) {
        pri = true;
        p1 = d1 * 4;
        p2 = d2 * 3;
        p3 = d3 * 2;
        p4 = d4 * 7;
        p5 = d5 * 6;
        p6 = d6 * 5;
        p7 = d7 * 4;
        p8 = d8 * 3;
        p9 = d9 * 2;
    }

    suma = p1 + p2 + p3 + p4 + p5 + p6 + p7 + p8 + p9;
    residuo = suma % modulo;

    /* Si residuo=0, dig.ver.=0, caso contrario 10 - residuo*/
    digitoVerificador = residuo==0 ? 0: modulo - residuo;

    /* ahora comparamos el elemento de la posicion 10 con el dig. ver.*/
    if (pub==true){
        if (digitoVerificador != d9){
            return false;
        }
        /* El ruc de las empresas del sector publico terminan con 0001*/
        if ( numero.substr(9,4) != '0001' ){
            return false;
        }
    }
    else if(pri == true){
        if (digitoVerificador != d10){
            return false;
        }
        if ( numero.substr(10,3) != '001' ){
            return false;
        }
    }
    else if(nat == true){
        if (digitoVerificador != d10){
            return false;
        }
        if (numero.length >10 && numero.substr(10,3) != '001' ){
            return false;
        }
    }
    return true;
}
