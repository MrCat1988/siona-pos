// Encapsular en IIFE para evitar conflictos globales
(function() {
    'use strict';

    // Variables locales del módulo
    let productosSucursalData = [];
    let sucursalesSucursal = [];
    let categoriasSucursal = [];
    let paginacionSucursal = {};
    let vistaActualSucursal = localStorage.getItem('vistaProductosSucursal') || 'cards';
    let paginaActualSucursal = 1;
    let itemsPorPaginaSucursal = 12;

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        inicializarModuloProductoSucursal();
    });

    function inicializarModuloProductoSucursal() {
        configurarEventosSucursal();
        cargarSucursalesSucursal();
        cargarCategoriasSucursal();
        configurarVistaSucursal();
        cargarProductosSucursal();
    }

    // Configurar eventos
    function configurarEventosSucursal() {
        // Botones de vista
        const btnVistaCards = document.getElementById('btn-vista-cards');
        const btnVistaTabla = document.getElementById('btn-vista-tabla');

        if (btnVistaCards) {
            btnVistaCards.addEventListener('click', () => cambiarVista('cards'));
        }
        if (btnVistaTabla) {
            btnVistaTabla.addEventListener('click', () => cambiarVista('tabla'));
        }

        // Filtros
        const filtroSucursal = document.getElementById('filtro-sucursal');
        const filtroCategoria = document.getElementById('filtro-categoria');
        const filtroEstado = document.getElementById('filtro-estado');
        const filtroBusqueda = document.getElementById('filtro-busqueda');

        // Botón "Ver todos"
        const btnVerTodos = document.getElementById('btn-ver-todos');

        if (filtroSucursal) filtroSucursal.addEventListener('change', () => {
            paginaActualSucursal = 1; // Reset paginación al cambiar filtros
            cargarProductosSucursal();
        });
        if (filtroCategoria) filtroCategoria.addEventListener('change', () => {
            paginaActualSucursal = 1;
            cargarProductosSucursal();
        });
        if (filtroEstado) filtroEstado.addEventListener('change', () => {
            paginaActualSucursal = 1;
            cargarProductosSucursal();
        });
        if (filtroBusqueda) {
            let timeoutId;
            filtroBusqueda.addEventListener('input', () => {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    paginaActualSucursal = 1; // Reset paginación al buscar
                    cargarProductosSucursal();
                }, 300);
            });
        }

        // Botón "Ver todos" - resetear filtros
        if (btnVerTodos) {
            btnVerTodos.addEventListener('click', () => {
                resetearFiltros();
            });
        }

        // Botón agregar
        const btnAgregar = document.getElementById('btn-agregar-producto-sucursal');
        if (btnAgregar) {
            btnAgregar.addEventListener('click', function() {
                abrirModalProductoSucursal();
            });
        }

        // Controles de paginación
        const btnAnterior = document.getElementById('btn-pag-anterior-sucursal');
        const btnSiguiente = document.getElementById('btn-pag-siguiente-sucursal');
        const selectItemsPorPagina = document.getElementById('items-por-pagina-sucursal');

        if (btnAnterior) {
            btnAnterior.addEventListener('click', () => {
                if (paginaActualSucursal > 1) {
                    paginaActualSucursal--;
                    cargarProductosSucursal();
                }
            });
        }
        if (btnSiguiente) {
            btnSiguiente.addEventListener('click', () => {
                const totalPaginas = Math.ceil(paginacionSucursal.total / itemsPorPaginaSucursal);
                if (paginaActualSucursal < totalPaginas) {
                    paginaActualSucursal++;
                    cargarProductosSucursal();
                }
            });
        }
        if (selectItemsPorPagina) {
            selectItemsPorPagina.addEventListener('change', () => {
                itemsPorPaginaSucursal = parseInt(selectItemsPorPagina.value);
                paginaActualSucursal = 1; // Reset a primera página
                cargarProductosSucursal();
            });
        }
    }

    // Cargar sucursales
    async function cargarSucursalesSucursal() {
        try {
            const response = await fetch('ajax/sucursales.ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                credentials: 'same-origin',
                body: 'accion=obtener_sucursales'
            });

            const data = await response.json();

            if (data.status === 'success') {
                sucursalesSucursal = data.data.sucursales || [];
                llenarSelectSucursalesSucursal();
            }
        } catch (error) {
            console.error('Error al cargar sucursales:', error);
        }
    }

    // Cargar categorías
    async function cargarCategoriasSucursal() {
        try {
            const response = await fetch('ajax/producto-sucursal.ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                credentials: 'same-origin',
                body: 'accion=obtener_categorias_para_filtros'
            });

            const data = await response.json();

            if (data.success) {
                categoriasSucursal = data.categorias;
                llenarSelectCategoriasSucursal();
            }
        } catch (error) {
            console.error('Error al cargar categorías:', error);
        }
    }

    // Llenar select de sucursales
    function llenarSelectSucursalesSucursal() {
        const filtroSucursal = document.getElementById('filtro-sucursal');
        const modalSucursal = document.getElementById('sucursal_idsucursal');

        if (filtroSucursal) {
            // Mantener la primera opción existente y agregar las demás
            const primeraOpcion = filtroSucursal.querySelector('option');
            filtroSucursal.innerHTML = '';
            if (primeraOpcion) filtroSucursal.appendChild(primeraOpcion);

            sucursalesSucursal.forEach(sucursal => {
                const option = document.createElement('option');
                option.value = sucursal.idsucursal;
                option.textContent = sucursal.sri_nombre;
                filtroSucursal.appendChild(option);
            });
        }

        if (modalSucursal) {
            modalSucursal.innerHTML = '<option value="">Seleccionar sucursal</option>';
            sucursalesSucursal.forEach(sucursal => {
                const option = document.createElement('option');
                option.value = sucursal.idsucursal;
                option.textContent = sucursal.sri_nombre;
                modalSucursal.appendChild(option);
            });
        }
    }

    // Llenar select de categorías
    function llenarSelectCategoriasSucursal() {
        const filtroCategoria = document.getElementById('filtro-categoria');

        if (filtroCategoria) {
            // Mantener la primera opción existente y agregar las demás
            const primeraOpcion = filtroCategoria.querySelector('option');
            filtroCategoria.innerHTML = '';
            if (primeraOpcion) filtroCategoria.appendChild(primeraOpcion);

            categoriasSucursal.forEach(categoria => {
                const option = document.createElement('option');
                option.value = categoria.idcategoria;
                option.textContent = categoria.nombre;
                filtroCategoria.appendChild(option);
            });
        }
    }

    // Configurar vista inicial
    function configurarVistaSucursal() {
        const btnVistaCards = document.getElementById('btn-vista-cards');
        const btnVistaTabla = document.getElementById('btn-vista-tabla');
        const vistaCards = document.getElementById('vista-cards');
        const vistaTabla = document.getElementById('vista-tabla');
        const loading = document.getElementById('loading-productos-sucursal');

        // Ocultar loading
        if (loading) loading.classList.add('hidden');

        // Configurar vista
        if (vistaActualSucursal === 'tabla') {
            if (vistaCards) vistaCards.classList.add('hidden');
            if (vistaTabla) vistaTabla.classList.remove('hidden');

            // Actualizar botones
            if (btnVistaCards) {
                btnVistaCards.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                btnVistaCards.classList.add('text-gray-500', 'hover:text-gray-700');
            }
            if (btnVistaTabla) {
                btnVistaTabla.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                btnVistaTabla.classList.remove('text-gray-500', 'hover:text-gray-700');
            }
        } else {
            if (vistaCards) vistaCards.classList.remove('hidden');
            if (vistaTabla) vistaTabla.classList.add('hidden');

            // Actualizar botones
            if (btnVistaCards) {
                btnVistaCards.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                btnVistaCards.classList.remove('text-gray-500', 'hover:text-gray-700');
            }
            if (btnVistaTabla) {
                btnVistaTabla.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                btnVistaTabla.classList.add('text-gray-500', 'hover:text-gray-700');
            }
        }
    }

    // Cambiar vista
    function cambiarVista(nuevaVista) {
        vistaActualSucursal = nuevaVista;
        localStorage.setItem('vistaProductosSucursal', vistaActualSucursal);
        configurarVistaSucursal();
        mostrarProductosSucursal();
    }

    // Resetear todos los filtros
    function resetearFiltros() {
        const filtroSucursal = document.getElementById('filtro-sucursal');
        const filtroCategoria = document.getElementById('filtro-categoria');
        const filtroEstado = document.getElementById('filtro-estado');
        const filtroBusqueda = document.getElementById('filtro-busqueda');

        // Resetear todos los filtros a valores por defecto
        if (filtroSucursal) filtroSucursal.value = '';
        if (filtroCategoria) filtroCategoria.value = '';
        if (filtroEstado) filtroEstado.value = ''; // Mostrar todos los estados
        if (filtroBusqueda) filtroBusqueda.value = '';
        paginaActualSucursal = 1; // Reset paginación

        // Recargar productos con filtros reseteados
        cargarProductosSucursal();
    }

    // Cargar productos con filtros
    async function cargarProductosSucursal() {
        // Mostrar indicador de carga
        const loading = document.getElementById('loading-productos-sucursal');
        if (loading) loading.classList.remove('hidden');

        try {
            const filtroSucursal = document.getElementById('filtro-sucursal')?.value || '';
            const filtroCategoria = document.getElementById('filtro-categoria')?.value || '';
            const filtroEstado = document.getElementById('filtro-estado')?.value || '';
            const filtroBusqueda = document.getElementById('filtro-busqueda')?.value || '';


            const formData = new FormData();
            formData.append('accion', 'obtener_productos_sucursal');
            formData.append('sucursal', filtroSucursal);
            formData.append('categoria', filtroCategoria);
            formData.append('estado', filtroEstado);
            formData.append('busqueda', filtroBusqueda);
            formData.append('limite', itemsPorPaginaSucursal.toString());
            formData.append('offset', ((paginaActualSucursal - 1) * itemsPorPaginaSucursal).toString());

            const response = await fetch('ajax/producto-sucursal.ajax.php', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            });

            const data = await response.json();


            if (data.success) {
                productosSucursalData = data.productos_sucursal || [];
                paginacionSucursal = data.paginacion || {};

                // Primero actualizar contador
                actualizarContadorSucursal();
                // Actualizar controles de paginación
                actualizarPaginacionSucursal();

                // Luego mostrar productos o estado vacío
                if (productosSucursalData.length > 0) {
                    ocultarEstadoVacio();
                    configurarVistaSucursal(); // Asegurar que la vista esté visible
                    mostrarProductosSucursal();
                } else {
                    mostrarEstadoVacio();
                }
            } else {
                productosSucursalData = [];
                mostrarEstadoVacio();
            }
        } catch (error) {
            console.error('Error al cargar productos:', error);
            productosSucursalData = [];
            mostrarEstadoVacio();
        } finally {
            // Ocultar indicador de carga
            if (loading) loading.classList.add('hidden');
        }
    }

    // Mostrar productos según la vista
    function mostrarProductosSucursal() {
        if (vistaActualSucursal === 'cards') {
            mostrarVistaCardsSucursal();
        } else {
            mostrarVistaTablaSucursal();
        }
    }

    // Vista cards
    function mostrarVistaCardsSucursal() {
        const contenedorCards = document.getElementById('vista-cards');

        if (!contenedorCards) {
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">';

        productosSucursalData.forEach(producto => {
            const imagen = producto.imagen ? producto.imagen : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIiBmaWxsPSIjOTk5Ij5Qcm9kdWN0bzwvdGV4dD48L3N2Zz4=';
            const stockStatus = getStockStatusSucursal(producto);

            html += `
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-200">
                    <!-- Imagen del producto -->
                    <div class="relative h-48 overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
                        <img src="${imagen}" alt="${producto.descripcion}"
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                        <div class="absolute top-3 right-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold ${producto.estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                ${producto.estado == 1 ? 'Activo' : 'Inactivo'}
                            </span>
                        </div>
                    </div>

                    <!-- Contenido de la card -->
                    <div class="p-6">
                        <!-- Título y código -->
                        <div class="mb-4">
                            <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2">${producto.descripcion}</h3>
                            <div class="flex flex-col text-sm text-gray-600 space-y-1">
                                <span><i class="fas fa-barcode mr-2"></i>Código: ${producto.codigo}</span>
                                <span><i class="fas fa-hashtag mr-2"></i>Cód. Auxiliar: ${producto.codigo_auxiliar}</span>
                                <span><i class="fas fa-store mr-2"></i>Sucursal: ${producto.sucursal_nombre}</span>
                            </div>
                        </div>

                        <!-- Precios -->
                        <div class="mb-4 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Precio Base:</span>
                                <span class="text-sm font-bold text-gray-900">$${parseFloat(producto.precio_base).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-sm font-medium text-gray-700">Precio Sucursal:</span>
                                <span class="text-lg font-bold text-blue-600">$${parseFloat(producto.precio_sucursal).toFixed(2)}</span>
                            </div>
                        </div>

                        <!-- Stock con barra de progreso -->
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Stock:</span>
                                <span class="text-sm font-bold ${stockStatus.color}">${producto.stock_sucursal}</span>
                            </div>
                            ${stockStatus.progress}
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Mín: ${producto.stock_minimo_sucursal}</span>
                                <span>Máx: ${producto.stock_maximo_sucursal}</span>
                            </div>
                        </div>

                        <!-- Fechas -->
                        <div class="mb-4 text-xs text-gray-500">
                            <div class="flex justify-between">
                                <span>Creado: ${formatearFechaSucursal(producto.created_at)}</span>
                                <span>Categoría: ${producto.categoria_nombre}</span>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex space-x-2">
                            <button onclick="editarProductoSucursal(${producto.idproducto_sucursal})"
                                    class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-edit mr-2"></i>Editar
                            </button>
                            <button onclick="eliminarProductoSucursal(${producto.idproducto_sucursal})"
                                    class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-trash mr-2"></i>Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        contenedorCards.innerHTML = html;
    }

    // Vista tabla
    function mostrarVistaTablaSucursal() {
        const tbody = document.getElementById('tabla-productos-body');

        if (!tbody) {
            return;
        }

        let html = '';

        productosSucursalData.forEach(producto => {
            const imagen = producto.imagen ? producto.imagen : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIiBmaWxsPSIjOTk5Ij5Qcm9kdWN0bzwvdGV4dD48L3N2Zz4=';
            const stockStatus = getStockStatusSucursal(producto);

            html += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <!-- Móvil: Row expandible -->
                    <td class="md:hidden px-3 py-4">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3">
                                <img src="${imagen}" alt="${producto.descripcion}" class="w-12 h-12 object-cover rounded-lg flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">${producto.descripcion}</div>
                                    <div class="text-sm text-gray-500">${producto.codigo} | ${producto.sucursal_nombre}</div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-blue-600">$${parseFloat(producto.precio_sucursal).toFixed(2)}</span>
                                <span class="font-bold ${stockStatus.color}">Stock: ${producto.stock_sucursal}</span>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold ${producto.estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${producto.estado == 1 ? 'Activo' : 'Inactivo'}
                                </span>
                            </div>

                            <div class="flex space-x-2">
                                <button onclick="editarProductoSucursal(${producto.idproducto_sucursal})"
                                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </button>
                                <button onclick="eliminarProductoSucursal(${producto.idproducto_sucursal})"
                                        class="flex-1 bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">
                                    <i class="fas fa-trash mr-1"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </td>

                    <!-- Desktop: Columnas separadas -->
                    <td class="hidden md:table-cell px-3 py-4">
                        <div class="flex items-center space-x-3">
                            <img src="${imagen}" alt="${producto.descripcion}" class="w-12 h-12 object-cover rounded-lg flex-shrink-0">
                            <div class="min-w-0">
                                <div class="font-medium text-gray-900 truncate">${producto.descripcion}</div>
                                <div class="text-sm text-gray-500">${producto.codigo} | ${producto.codigo_auxiliar}</div>
                            </div>
                        </div>
                    </td>
                    <td class="hidden md:table-cell px-2 py-4 text-sm font-medium text-gray-900">${producto.sucursal_nombre}</td>
                    <td class="hidden md:table-cell px-2 py-4 text-right">
                        <div class="text-sm text-gray-500">$${parseFloat(producto.precio_base).toFixed(2)}</div>
                        <div class="text-sm font-bold text-blue-600">$${parseFloat(producto.precio_sucursal).toFixed(2)}</div>
                    </td>
                    <td class="hidden md:table-cell px-2 py-4 text-center">
                        <span class="font-bold ${stockStatus.color}">${producto.stock_sucursal}</span>
                        <div class="text-xs text-gray-500">${producto.stock_minimo_sucursal}-${producto.stock_maximo_sucursal}</div>
                    </td>
                    <td class="hidden md:table-cell px-2 py-4 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold ${producto.estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${producto.estado == 1 ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td class="hidden md:table-cell px-2 py-4 text-center">
                        <div class="flex space-x-1 justify-center">
                            <button onclick="editarProductoSucursal(${producto.idproducto_sucursal})"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="eliminarProductoSucursal(${producto.idproducto_sucursal})"
                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // Mostrar/ocultar estado vacío
    function mostrarEstadoVacio() {
        const estadoVacio = document.getElementById('estado-vacio');
        const vistaCards = document.getElementById('vista-cards');
        const vistaTabla = document.getElementById('vista-tabla');

        if (estadoVacio) estadoVacio.classList.remove('hidden');
        if (vistaCards) vistaCards.classList.add('hidden');
        if (vistaTabla) vistaTabla.classList.add('hidden');
    }

    function ocultarEstadoVacio() {
        const estadoVacio = document.getElementById('estado-vacio');
        if (estadoVacio) {
            estadoVacio.classList.add('hidden');
        }

        // Restaurar visibilidad de los contenedores de productos
        const vistaCards = document.getElementById('vista-cards');
        const vistaTabla = document.getElementById('vista-tabla');

        if (vistaActualSucursal === 'cards') {
            if (vistaCards) vistaCards.classList.remove('hidden');
            if (vistaTabla) vistaTabla.classList.add('hidden');
        } else {
            if (vistaCards) vistaCards.classList.add('hidden');
            if (vistaTabla) vistaTabla.classList.remove('hidden');
        }
    }

    // Funciones de paginación
    function actualizarPaginacionSucursal() {
        const totalItems = paginacionSucursal.total || 0;
        const totalPaginas = Math.ceil(totalItems / itemsPorPaginaSucursal);

        // Mostrar/ocultar controles de paginación
        const controlesContainer = document.getElementById('paginacion-controles-sucursal');
        if (totalItems > itemsPorPaginaSucursal) {
            if (controlesContainer) controlesContainer.classList.remove('hidden');

            // Actualizar información de paginación
            const desde = ((paginaActualSucursal - 1) * itemsPorPaginaSucursal) + 1;
            const hasta = Math.min(paginaActualSucursal * itemsPorPaginaSucursal, totalItems);

            const elemDesde = document.getElementById('paginacion-desde-sucursal');
            const elemHasta = document.getElementById('paginacion-hasta-sucursal');
            const elemTotal = document.getElementById('paginacion-total-sucursal');

            if (elemDesde) elemDesde.textContent = desde;
            if (elemHasta) elemHasta.textContent = hasta;
            if (elemTotal) elemTotal.textContent = totalItems;

            // Actualizar botones anterior/siguiente
            const btnAnterior = document.getElementById('btn-pag-anterior-sucursal');
            const btnSiguiente = document.getElementById('btn-pag-siguiente-sucursal');

            if (btnAnterior) {
                btnAnterior.disabled = paginaActualSucursal <= 1;
                btnAnterior.classList.toggle('opacity-50', paginaActualSucursal <= 1);
                btnAnterior.classList.toggle('cursor-not-allowed', paginaActualSucursal <= 1);
            }
            if (btnSiguiente) {
                btnSiguiente.disabled = paginaActualSucursal >= totalPaginas;
                btnSiguiente.classList.toggle('opacity-50', paginaActualSucursal >= totalPaginas);
                btnSiguiente.classList.toggle('cursor-not-allowed', paginaActualSucursal >= totalPaginas);
            }

            // Generar números de página
            generarNumerosPaginaSucursal(totalPaginas);
        } else {
            if (controlesContainer) controlesContainer.classList.add('hidden');
        }
    }

    function generarNumerosPaginaSucursal(totalPaginas) {
        const contenedor = document.getElementById('paginacion-numeros-sucursal');
        if (!contenedor) return;

        let html = '';
        const maxBotones = 5;
        let inicio = Math.max(1, paginaActualSucursal - Math.floor(maxBotones / 2));
        let fin = Math.min(totalPaginas, inicio + maxBotones - 1);

        // Ajustar inicio si fin está en el límite
        if (fin - inicio < maxBotones - 1) {
            inicio = Math.max(1, fin - maxBotones + 1);
        }

        // Botón primera página si no está visible
        if (inicio > 1) {
            html += `<button onclick="cambiarPaginaSucursal(1)" class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white">1</button>`;
            if (inicio > 2) {
                html += `<span class="px-2 text-gray-400">...</span>`;
            }
        }

        // Números de página
        for (let i = inicio; i <= fin; i++) {
            const isActive = i === paginaActualSucursal;
            html += `<button onclick="cambiarPaginaSucursal(${i})" class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium rounded-lg ${isActive
                ? 'bg-blue-600 text-white border border-blue-600'
                : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white'}">${i}</button>`;
        }

        // Botón última página si no está visible
        if (fin < totalPaginas) {
            if (fin < totalPaginas - 1) {
                html += `<span class="px-2 text-gray-400">...</span>`;
            }
            html += `<button onclick="cambiarPaginaSucursal(${totalPaginas})" class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-neutral-800 dark:border-neutral-600 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white">${totalPaginas}</button>`;
        }

        contenedor.innerHTML = html;
    }

    function cambiarPaginaSucursal(nuevaPagina) {
        paginaActualSucursal = nuevaPagina;
        cargarProductosSucursal();
    }

    // Funciones auxiliares
    function getStockStatusSucursal(producto) {
        const stock = parseInt(producto.stock_sucursal);
        const min = parseInt(producto.stock_minimo_sucursal);
        const max = parseInt(producto.stock_maximo_sucursal);

        let color, progress = '';

        if (stock === 0) {
            color = 'text-red-600';
            progress = '<div class="w-full bg-red-200 rounded-full h-2"><div class="bg-red-500 h-2 rounded-full" style="width: 0%"></div></div>';
        } else if (stock <= min) {
            color = 'text-orange-600';
            const percent = max > 0 ? (stock / max * 100).toFixed(0) : 0;
            progress = `<div class="w-full bg-orange-200 rounded-full h-2"><div class="bg-orange-500 h-2 rounded-full" style="width: ${percent}%"></div></div>`;
        } else if (stock <= max) {
            color = 'text-green-600';
            const percent = max > 0 ? (stock / max * 100).toFixed(0) : 100;
            progress = `<div class="w-full bg-green-200 rounded-full h-2"><div class="bg-green-500 h-2 rounded-full" style="width: ${percent}%"></div></div>`;
        } else {
            color = 'text-blue-600';
            progress = '<div class="w-full bg-blue-200 rounded-full h-2"><div class="bg-blue-500 h-2 rounded-full" style="width: 100%"></div></div>';
        }

        return { color, progress };
    }

    function formatearFechaSucursal(fecha) {
        if (!fecha) return 'N/A';
        return new Date(fecha).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function actualizarContadorSucursal() {
        const contador = document.getElementById('total-productos-sucursal');
        if (contador) {
            const total = paginacionSucursal.total || 0;
            contador.textContent = `${total} productos`;
        }
    }

    // Funciones de modal y CRUD (implementar según necesidad)
    function abrirModalProductoSucursal() {
        // Modal functionality to be implemented
    }

    function editarProductoSucursal(id) {
        // Edit functionality to be implemented
    }

    function eliminarProductoSucursal(id) {
        // Delete functionality to be implemented
    }

    // Exponer funciones globalmente para que las pueda llamar el HTML
    window.abrirModalProductoSucursal = abrirModalProductoSucursal;
    window.editarProductoSucursal = editarProductoSucursal;
    window.eliminarProductoSucursal = eliminarProductoSucursal;
    window.cambiarPaginaSucursal = cambiarPaginaSucursal;

})(); // Cerrar IIFE