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
        setupModalEventListeners();
        setupModalEditar();

    });

    function inicializarModuloProductoSucursal() {
        configurarEventosSucursal();
        cargarSucursalesSucursal();
        cargarCategoriasSucursal();
        configurarVistaSucursal();
        cargarProductosSucursal();
    }

    // Configurar event listeners del modal
    function setupModalEventListeners() {
        // Los botones de abrir modal ya tienen data-hs-overlay="#modal-producto-sucursal"
        // Solo necesitamos configurar eventos para cuando se abra el modal
        const modalElement = document.querySelector('#modal-producto-sucursal');
        if (modalElement) {
            modalElement.addEventListener('open.hs.overlay', function(e) {
                // Verificar si se hizo clic en un botón de editar
                // Buscar el elemento que tiene data-edit-id en los últimos elementos activos
                let triggerElement = e.detail?.trigger || document.activeElement;

                // Si no encontramos data-edit-id en el elemento activo, buscar en elementos con data-hs-overlay recientes
                if (!triggerElement?.getAttribute('data-edit-id')) {
                    const allTriggers = document.querySelectorAll('[data-hs-overlay="#modal-producto-sucursal"][data-edit-id]');
                    triggerElement = Array.from(allTriggers).find(el => el === document.activeElement) || null;
                }

                const editId = triggerElement?.getAttribute('data-edit-id');


                if (editId) {
                    // Modo editar
                    isEditMode = true;
                    currentEditId = editId;
                    resetForm();
                    updateModalForMode();
                    cargarDatosParaEditar(editId);
                } else {
                    // Modo crear nuevo
                    isEditMode = false;
                    currentEditId = null;
                    resetForm();
                    updateModalForMode();
                    cargarSucursalesEnModal();
                }
            });
        }

        // Botón guardar
        const btnGuardar = document.getElementById('btn-guardar-producto-sucursal');
        if (btnGuardar) {
            btnGuardar.addEventListener('click', guardarProductoSucursal);
        }

        // Cambio de sucursal
        const sucursalSelect = document.getElementById('sucursal_idsucursal');
        if (sucursalSelect) {
            sucursalSelect.addEventListener('change', function() {
                const productoInput = document.getElementById('productos_idproducto_input');
                if (this.value && !isEditMode) {
                    productoInput.placeholder = 'Buscar producto...';
                    productoInput.readOnly = false;
                    productoInput.style.backgroundColor = '';
                    productoInput.style.cursor = '';
                } else if (!isEditMode) {
                    productoInput.placeholder = 'Primero selecciona una sucursal...';
                    productoInput.readOnly = true;
                    productoInput.value = '';
                    document.getElementById('productos_idproducto').value = '';
                    document.getElementById('producto-info').classList.add('hidden');
                }
            });
        }

        // Búsqueda de productos
        const productoInput = document.getElementById('productos_idproducto_input');
        if (productoInput) {
            let searchTimeout;
            productoInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                const sucursalId = document.getElementById('sucursal_idsucursal').value;

                if (query.length >= 2 && sucursalId && !isEditMode) {
                    searchTimeout = setTimeout(() => {
                        buscarProductos(query, sucursalId);
                    }, 300);
                } else {
                    document.getElementById('productos-dropdown').classList.add('hidden');
                }
            });

            productoInput.addEventListener('focus', function() {
                if (this.value.length >= 2 && !isEditMode) {
                    const sucursalId = document.getElementById('sucursal_idsucursal').value;
                    if (sucursalId) {
                        buscarProductos(this.value, sucursalId);
                    }
                }
            });

            // Ocultar dropdown al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#productos_idproducto_input') && !e.target.closest('#productos-dropdown')) {
                    document.getElementById('productos-dropdown').classList.add('hidden');
                }
            });
        }

        // Event listeners para stock
        const stockInputs = document.querySelectorAll('.stock-input-sucursal');
        stockInputs.forEach(input => {
            input.addEventListener('input', updateStockIndicator);
        });

        // Cerrar modal al hacer clic fuera (reutilizar modalElement de arriba)
        if (modalElement) {
            modalElement.addEventListener('click', function(e) {
                // Solo cerrar si se hace clic en el overlay (no en el contenido del modal)
                if (e.target === modalElement) {
                    // Usar el botón de cerrar estándar
                    const closeButton = modalElement.querySelector('[data-hs-overlay="#modal-producto-sucursal"]');
                    if (closeButton) closeButton.click();
                }
            });
        }
    }

    // Buscar productos
    function buscarProductos(query, sucursalId) {
        const dropdown = document.getElementById('productos-dropdown');
        const loading = document.getElementById('productos-loading');
        const list = document.getElementById('productos-list');
        const empty = document.getElementById('productos-empty');

        // Mostrar loading
        dropdown.classList.remove('hidden');
        loading.classList.remove('hidden');
        list.innerHTML = '';
        empty.classList.add('hidden');

        fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `accion=obtener_productos_disponibles&search=${encodeURIComponent(query)}&sucursal_id=${sucursalId}`
        })
        .then(response => response.json())
        .then(data => {
            loading.classList.add('hidden');

            if (data.success && data.productos.length > 0) {
                list.innerHTML = '';
                data.productos.forEach(producto => {
                    const item = document.createElement('div');
                    item.className = 'p-3 hover:bg-gray-50 dark:hover:bg-neutral-700 cursor-pointer border-b border-gray-100 dark:border-neutral-600 last:border-b-0';
                    item.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    ${producto.codigo_auxiliar} - ${producto.descripcion}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-neutral-400">
                                    Precio: $${parseFloat(producto.precio_venta || 0).toFixed(2)} | Categoría: ${producto.categoria}
                                </div>
                            </div>
                        </div>
                    `;

                    item.addEventListener('click', () => {
                        seleccionarProducto(producto);
                    });

                    list.appendChild(item);
                });
            } else {
                empty.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error al buscar productos:', error);
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
        });
    }

    // Seleccionar producto
    function seleccionarProducto(producto) {
        document.getElementById('productos_idproducto').value = producto.idproducto;
        document.getElementById('productos_idproducto_input').value = `${producto.codigo_auxiliar} - ${producto.descripcion}`;
        document.getElementById('productos-dropdown').classList.add('hidden');

        showProductInfo(producto);
    }

    // Guardar producto en sucursal
    function guardarProductoSucursal() {
        const form = document.getElementById('form-producto-sucursal');
        const formData = new FormData(form);

        // Validaciones
        const sucursalId = formData.get('sucursal_idsucursal');
        const productoId = formData.get('productos_idproducto');
        const precio = formData.get('precio_sucursal');

        if (!sucursalId) {
            mostrarToast('error', 'Por favor selecciona una sucursal');
            return;
        }

        if (!productoId) {
            mostrarToast('error', 'Por favor selecciona un producto');
            return;
        }

        if (!precio || parseFloat(precio) <= 0) {
            mostrarToast('error', 'Por favor ingresa un precio válido');
            return;
        }

        // Validación de stock
        const stockActual = parseInt(formData.get('stock_sucursal')) || 0;
        const stockMinimo = parseInt(formData.get('stock_minimo_sucursal')) || 0;
        const stockMaximo = parseInt(formData.get('stock_maximo_sucursal')) || 0;

        if (stockMinimo > 0 && stockMaximo > 0 && stockMinimo >= stockMaximo) {
            mostrarToast('error', 'El stock mínimo debe ser menor al stock máximo');
            return;
        }

        // Deshabilitar botón
        const btnGuardar = document.getElementById('btn-guardar-producto-sucursal');
        const textoOriginal = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            ${isEditMode ? 'Actualizando...' : 'Guardando...'}
        `;

        // Preparar datos
        const data = new URLSearchParams();
        data.append('accion', isEditMode ? 'actualizar_producto_sucursal' : 'crear_producto_sucursal');
        if (isEditMode) {
            data.append('id', currentEditId);
        }
        for (const [key, value] of formData.entries()) {
            data.append(key, value);
        }

        fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                mostrarToast('success', result.message || (isEditMode ? 'Producto actualizado exitosamente' : 'Producto asignado exitosamente'));

                // Cerrar modal usando estándar Preline
                const modalElement = document.querySelector('#modal-producto-sucursal');
                if (modalElement) {
                    const closeButton = modalElement.querySelector('[data-hs-overlay="#modal-producto-sucursal"]');
                    if (closeButton) closeButton.click();
                }

                cargarProductosSucursal();
            } else {
                mostrarToast('error', result.message || 'Error al guardar el producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarToast('error', 'Error al guardar el producto');
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = textoOriginal;
        });
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

        // El botón agregar ya tiene data-hs-overlay="#modal-producto-sucursal"

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

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';

        productosSucursalData.forEach(producto => {
            const stockStatus = getStockStatusSucursal(producto);

            // Verificar si el producto está eliminado (soft delete)
            const esEliminado = producto.deleted_at && producto.deleted_at !== null;

            // Estados y estilos
            let estadoActivo, estadoClass, estadoText, headerClass, cardClass;

            if (esEliminado) {
                estadoActivo = false;
                estadoClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                estadoText = 'Eliminado';
                headerClass = 'bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 dark:from-gray-800 dark:via-gray-900 dark:to-black';
                cardClass = 'opacity-75 saturate-50';
            } else {
                estadoActivo = producto.estado == 1;
                estadoClass = estadoActivo ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                estadoText = estadoActivo ? 'Activo' : 'Inactivo';
                headerClass = 'bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-900/20 dark:via-indigo-900/20 dark:to-purple-900/20';
                cardClass = '';
            }

            html += `
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 dark:bg-neutral-800 dark:border-neutral-700 overflow-hidden group hover:-translate-y-2 ${cardClass}" data-producto-id="${producto.idproducto_sucursal}">
                    <!-- Header con imagen del producto -->
                    <div class="relative ${headerClass} p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full ${estadoClass}">
                                ${estadoText}
                            </span>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                    ${producto.sucursal_nombre}
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
                                        <!-- Caja/Producto tachado -->
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
                                producto.imagen ?
                                    `<div class="w-20 h-20 mx-auto mb-4 rounded-2xl overflow-hidden shadow-lg">
                                        <img src="${producto.imagen}" alt="${producto.descripcion}" class="w-full h-full object-cover">
                                    </div>` :
                                    `<div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center shadow-lg relative overflow-hidden">
                                        <!-- Patrón de fondo -->
                                        <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>

                                        <!-- Icono de producto -->
                                        <div class="relative">
                                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>

                                        <!-- Badge de sucursal -->
                                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center shadow-sm">
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                    </div>`
                            }
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 leading-tight">${producto.descripcion}</h3>
                            <p class="text-sm text-gray-600 dark:text-neutral-400">📦 ${producto.categoria_nombre || 'Sin categoría'}</p>
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
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Base</p>
                                    <p class="text-lg font-bold text-gray-800 dark:text-neutral-200">$${parseFloat(producto.precio_base).toFixed(2)}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-medium text-gray-600 dark:text-neutral-400 mb-1">Precio Sucursal</p>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">$${parseFloat(producto.precio_sucursal).toFixed(2)}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Stock con indicadores visuales -->
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 border border-purple-200 dark:border-purple-800">
                            <div class="mb-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-gray-600 dark:text-neutral-400">Stock Actual</span>
                                    <span class="text-lg font-bold ${stockStatus.color}">${producto.stock_sucursal}</span>
                                </div>
                                ${stockStatus.progress}
                                <div class="flex justify-between text-xs text-gray-500 mt-2">
                                    <span>Mín: ${producto.stock_minimo_sucursal}</span>
                                    <span>Máx: ${producto.stock_maximo_sucursal}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer con metadatos -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-neutral-700">
                            <div class="text-xs text-gray-500 dark:text-neutral-400">
                                ${formatearFechaSucursal(producto.created_at)}
                            </div>
                            <div class="text-xs font-medium text-gray-600 dark:text-neutral-300">
                                #${producto.idproducto_sucursal}
                            </div>
                        </div>

                        <!-- Acciones (estilo productos) -->
                        <div class="flex gap-2 pt-4 border-t border-gray-100 dark:border-neutral-700">
                            ${esEliminado ? `
                                <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Eliminado el ${new Date(producto.deleted_at).toLocaleDateString()}
                                </div>
                            ` : `
                                <button onclick="abrirModalEditarProductoSucursal(${producto.idproducto_sucursal})"
                                        class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 transition-all duration-200 dark:text-blue-400 dark:bg-blue-900/20 dark:border-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </button>
                                <button onclick="eliminarProductoSucursal(${producto.idproducto_sucursal})"
                                        class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 hover:border-red-300 transition-all duration-200 dark:text-red-400 dark:bg-red-900/20 dark:border-red-800">
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
            const esEliminado = producto.deleted_at && producto.deleted_at !== null;

            html += `
                <tr class="hover:bg-gray-50 transition-colors ${esEliminado ? 'opacity-75 saturate-50' : ''}">
                    <!-- Móvil: Row expandible -->
                    <td class="md:hidden px-3 py-4">
                        <div class="space-y-3">
                            <!-- Header principal con botón expandir -->
                            <div class="flex items-center space-x-3">
                                <img src="${imagen}" alt="${producto.descripcion}" class="w-12 h-12 object-cover rounded-lg flex-shrink-0">
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">${producto.descripcion}</div>
                                    <div class="text-sm text-gray-500">${producto.codigo} | ${producto.sucursal_nombre}</div>
                                </div>
                                <button onclick="toggleExpandRow(${producto.idproducto_sucursal})"
                                        class="p-2 text-gray-400 hover:text-gray-600 transition-colors"
                                        title="Ver más detalles">
                                    <svg id="expand-icon-${producto.idproducto_sucursal}" class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Info básica siempre visible -->
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-blue-600">$${parseFloat(producto.precio_sucursal).toFixed(2)}</span>
                                <span class="font-bold ${stockStatus.color}">Stock: ${producto.stock_sucursal}</span>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold ${esEliminado ? 'bg-red-100 text-red-700' : (producto.estado == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')}">
                                    ${esEliminado ? 'Eliminado' : (producto.estado == 1 ? 'Activo' : 'Inactivo')}
                                </span>
                            </div>

                            <!-- Detalles expandibles (ocultos por defecto) -->
                            <div id="details-${producto.idproducto_sucursal}" class="hidden space-y-3 pt-2 border-t border-gray-200">
                                <!-- Stock detalles -->
                                ${!esEliminado && producto.stock_sucursal !== null ? `
                                    <div class="bg-blue-50 rounded-lg p-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-gray-700">Gestión de Stock</span>
                                            <span class="text-lg font-bold ${stockStatus.color}">${producto.stock_sucursal}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="${stockStatus.bgColor} h-2 rounded-full transition-all duration-300" style="width: ${Math.min(100, ((producto.stock_sucursal || 0) / Math.max(1, producto.stock_maximo_sucursal || 100)) * 100)}%"></div>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                                            <span>Mín: ${producto.stock_minimo_sucursal || 0}</span>
                                            <span>Máx: ${producto.stock_maximo_sucursal || 0}</span>
                                        </div>
                                    </div>
                                ` : ''}

                                <!-- Información adicional -->
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <div class="text-xs text-gray-500 mb-1">Código Auxiliar</div>
                                        <div class="font-medium">${producto.codigo_auxiliar || 'N/A'}</div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-2">
                                        <div class="text-xs text-gray-500 mb-1">ID Asignación</div>
                                        <div class="font-medium">#${producto.idproducto_sucursal}</div>
                                    </div>
                                </div>

                                <!-- Fechas -->
                                <div class="text-xs text-gray-500 flex justify-between">
                                    <span>Creado: ${new Date(producto.created_at).toLocaleDateString()}</span>
                                    ${esEliminado ? `<span>Eliminado: ${new Date(producto.deleted_at).toLocaleDateString()}</span>` : ''}
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="flex space-x-2 pt-2">
                                ${esEliminado ? `
                                    <div class="flex-1 flex items-center justify-center gap-2 px-3 py-2 text-xs text-gray-500 bg-gray-50 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Eliminado el ${new Date(producto.deleted_at).toLocaleDateString()}
                                    </div>
                                ` : `
                                    <button onclick="abrirModalEditarProductoSucursal(${producto.idproducto_sucursal})"
                                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm transition-colors inline-flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </button>
                                    <button onclick="eliminarProductoSucursal(${producto.idproducto_sucursal})"
                                            class="flex-1 bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm transition-colors inline-flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Eliminar
                                    </button>
                                `}
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
                        ${esEliminado ? `
                            <div class="flex items-center justify-center gap-2 px-2 py-1 text-xs text-gray-500">
                                <i class="fas fa-clock"></i>
                                <span>Eliminado</span>
                            </div>
                        ` : `
                            <div class="flex space-x-1 justify-center">
                                <button onclick="abrirModalEditarProductoSucursal(${producto.idproducto_sucursal})"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs transition-colors inline-flex items-center justify-center"
                                        title="Editar producto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="eliminarProductoSucursal(${producto.idproducto_sucursal})"
                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors inline-flex items-center justify-center"
                                        title="Eliminar producto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        `}
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

    // Variables globales para el modal
    let isEditMode = false;
    let currentEditId = null;

    // La función editarProductoSucursal ya no es necesaria
    // Los botones usan data-hs-overlay="#modal-producto-sucursal" y data-edit-id

    /**
     * Elimina un producto de sucursal (soft delete)
     * @param {number} idProductoSucursal - ID del producto-sucursal a eliminar
     */
    window.eliminarProductoSucursal = async function(idProductoSucursal) {
        try {
            console.log('=== ELIMINAR PRODUCTO SUCURSAL ===');
            console.log('ID a eliminar:', idProductoSucursal);

            // Buscar el producto en los datos actuales para mostrar información
            const producto = productosSucursalData.find(p => p.idproducto_sucursal == idProductoSucursal);

            if (!producto) {
                mostrarToast('error', 'Producto no encontrado');
                return;
            }

            // Mostrar confirmación
            const confirmacion = await mostrarConfirmacionEliminacionSucursal(producto);
            if (!confirmacion) {
                return;
            }

            // Deshabilitar botones del producto específico
            deshabilitarBotonesProducto(idProductoSucursal, true);

            // Preparar datos
            const data = new URLSearchParams();
            data.append('accion', 'eliminar_producto_sucursal');
            data.append('idproducto_sucursal', idProductoSucursal);

            console.log('Datos a enviar:', data.toString());

            const response = await fetch('ajax/producto-sucursal.ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: data
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            console.log('Resultado del servidor:', result);

            if (result.success) {
                mostrarToast('success', result.message || 'Producto eliminado exitosamente de la sucursal');
                cargarProductosSucursal();
            } else {
                mostrarToast('error', result.message || 'Error al eliminar el producto');
                // Re-habilitar botones en caso de error
                deshabilitarBotonesProducto(idProductoSucursal, false);
            }

        } catch (error) {
            console.error('Error al eliminar:', error);
            mostrarToast('error', 'Error de conexión al eliminar el producto');
            // Re-habilitar botones en caso de error
            deshabilitarBotonesProducto(idProductoSucursal, false);
        }
    }

    /**
     * Deshabilita o habilita los botones de un producto específico
     * @param {number} idProductoSucursal - ID del producto-sucursal
     * @param {boolean} deshabilitar - True para deshabilitar, false para habilitar
     */
    function deshabilitarBotonesProducto(idProductoSucursal, deshabilitar) {
        // Buscar la card del producto
        const card = document.querySelector(`[data-producto-id="${idProductoSucursal}"]`);
        if (!card) return;

        // Buscar todos los botones en la card
        const botones = card.querySelectorAll('button');

        botones.forEach(boton => {
            if (deshabilitar) {
                boton.disabled = true;
                boton.style.opacity = '0.5';
                boton.style.cursor = 'not-allowed';
                boton.style.pointerEvents = 'none';

                // Agregar spinner al botón de eliminar si es el botón de eliminar
                if (boton.onclick && boton.onclick.toString().includes('eliminarProductoSucursal')) {
                    const originalHTML = boton.innerHTML;
                    boton.setAttribute('data-original-html', originalHTML);
                    boton.innerHTML = `
                        <svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Eliminando...
                    `;
                }
            } else {
                boton.disabled = false;
                boton.style.opacity = '1';
                boton.style.cursor = 'pointer';
                boton.style.pointerEvents = 'auto';

                // Restaurar HTML original si existe
                const originalHTML = boton.getAttribute('data-original-html');
                if (originalHTML) {
                    boton.innerHTML = originalHTML;
                    boton.removeAttribute('data-original-html');
                }
            }
        });

        // También aplicar estilo a la card completa durante eliminación
        if (deshabilitar) {
            card.style.opacity = '0.7';
            card.style.transform = 'scale(0.98)';
            card.style.transition = 'all 0.3s ease';
        } else {
            card.style.opacity = '1';
            card.style.transform = 'scale(1)';
        }
    }

    /**
     * Muestra modal de confirmación para eliminar producto de sucursal
     * @param {Object} producto - Datos del producto
     * @returns {Promise<boolean>} - True si el usuario confirma
     */
    function mostrarConfirmacionEliminacionSucursal(producto) {
        return new Promise((resolve) => {
            // Crear modal de confirmación
            const modalHtml = `
                <div id="modal-confirmar-eliminacion-sucursal" class="hs-overlay fixed top-0 start-0 z-[60] w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
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
                                    ¿Estás seguro de que deseas eliminar <strong>"${producto.descripcion}"</strong> de la sucursal <strong>"${producto.sucursal_nombre}"</strong>?
                                </p>
                                <p class="text-sm text-gray-500 dark:text-neutral-400 mt-2">
                                    La asignación del producto a esta sucursal será eliminada, pero el producto seguirá disponible para otras sucursales.
                                </p>
                            </div>

                            <div class="flex gap-3 justify-end">
                                <button type="button" id="btn-cancelar-eliminacion-sucursal" class="py-2 px-4 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-600">
                                    Cancelar
                                </button>
                                <button type="button" id="btn-confirmar-eliminacion-sucursal" class="py-2 px-4 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Eliminar de sucursal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Agregar modal al DOM
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            const modal = document.getElementById('modal-confirmar-eliminacion-sucursal');
            const btnCancelar = document.getElementById('btn-cancelar-eliminacion-sucursal');
            const btnConfirmar = document.getElementById('btn-confirmar-eliminacion-sucursal');

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

    // Resetear formulario
    function resetForm() {
        const form = document.getElementById('form-producto-sucursal');
        if (form) {
            form.reset();
            document.getElementById('idproducto_sucursal').value = '';
            document.getElementById('productos_idproducto').value = '';
            document.getElementById('productos_idproducto_input').value = '';
            document.getElementById('productos_idproducto_input').placeholder = 'Primero selecciona una sucursal...';
            document.getElementById('productos_idproducto_input').readOnly = true;

            // Ocultar elementos
            document.getElementById('productos-dropdown').classList.add('hidden');
            document.getElementById('producto-info').classList.add('hidden');
            document.getElementById('stock-level-indicator-sucursal').classList.add('hidden');
            document.getElementById('stock-validation-sucursal').classList.add('hidden');
        }
    }

    // Actualizar modal según modo (crear/editar)
    function updateModalForMode() {
        const titulo = document.getElementById('modal-titulo');
        const textoBoton = document.getElementById('texto-boton-guardar');
        const productoInput = document.getElementById('productos_idproducto_input');

        if (isEditMode) {
            titulo.textContent = 'Editar Producto en Sucursal';
            textoBoton.textContent = 'Actualizar Producto';
        } else {
            titulo.textContent = 'Asignar Producto a Sucursal';
            textoBoton.textContent = 'Asignar Producto';
        }
    }

    // Cargar datos para editar
    function cargarDatosParaEditar(id) {
        fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `accion=obtener_producto_sucursal_por_id&id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.producto_sucursal) {
                const item = data.producto_sucursal;

                // Llenar formulario
                document.getElementById('idproducto_sucursal').value = item.idproducto_sucursal;
                document.getElementById('sucursal_idsucursal').value = item.sucursal_idsucursal;
                document.getElementById('precio_sucursal').value = item.precio_sucursal;
                document.getElementById('stock_sucursal').value = item.stock_sucursal || 0;
                document.getElementById('stock_minimo_sucursal').value = item.stock_minimo_sucursal || 0;
                document.getElementById('stock_maximo_sucursal').value = item.stock_maximo_sucursal || 0;
                document.getElementById('estado').value = item.estado;

                // Configurar producto (modo solo lectura en edición)
                document.getElementById('productos_idproducto').value = item.productos_idproducto;
                document.getElementById('productos_idproducto_input').value = `${item.codigo_auxiliar} - ${item.descripcion}`;
                document.getElementById('productos_idproducto_input').readOnly = true;
                document.getElementById('productos_idproducto_input').style.backgroundColor = '#f3f4f6';
                document.getElementById('productos_idproducto_input').style.cursor = 'not-allowed';

                // Mostrar info del producto
                showProductInfo({
                    descripcion: item.descripcion,
                    precio_venta: item.precio_base || 0
                });

                // Cargar sucursales después de establecer el valor
                cargarSucursalesEnModal(() => {
                    document.getElementById('sucursal_idsucursal').value = item.sucursal_idsucursal;
                });

                // Actualizar indicadores de stock
                updateStockIndicator();

            } else {
                mostrarToast('error', data.message || 'Error al cargar los datos del producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarToast('error', 'Error de conexión al cargar los datos');
        });
    }

    // Cargar sucursales en el modal
    function cargarSucursalesEnModal(callback = null) {
        fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'accion=obtener_sucursales_disponibles'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('sucursal_idsucursal');
                select.innerHTML = '<option value="">Seleccionar sucursal...</option>';

                data.sucursales.forEach(sucursal => {
                    const option = document.createElement('option');
                    option.value = sucursal.idsucursal;
                    option.textContent = sucursal.nombre;
                    select.appendChild(option);
                });

                if (callback) callback();
            } else {
                console.error('Error al cargar sucursales:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al cargar sucursales:', error);
        });
    }

    // Mostrar información del producto seleccionado
    function showProductInfo(producto) {
        const infoDiv = document.getElementById('producto-info');
        const nameSpan = document.getElementById('producto-selected-name');
        const priceSpan = document.getElementById('producto-selected-price');

        if (producto) {
            nameSpan.textContent = producto.descripcion;
            priceSpan.textContent = `Precio base: $${parseFloat(producto.precio_venta || 0).toFixed(5)}`;
            infoDiv.classList.remove('hidden');

            // Auto-llenar precio si está vacío
            const precioInput = document.getElementById('precio_sucursal');
            if (!precioInput.value && producto.precio_venta) {
                precioInput.value = parseFloat(producto.precio_venta).toFixed(5);
            }
        } else {
            infoDiv.classList.add('hidden');
        }
    }

    // Actualizar indicador de stock
    function updateStockIndicator() {
        const actual = parseInt(document.getElementById('stock_sucursal').value) || 0;
        const minimo = parseInt(document.getElementById('stock_minimo_sucursal').value) || 0;
        const maximo = parseInt(document.getElementById('stock_maximo_sucursal').value) || 0;

        const indicator = document.getElementById('stock-level-indicator-sucursal');
        const bar = document.getElementById('stock-bar-sucursal');
        const status = document.getElementById('stock-status-sucursal');
        const minDisplay = document.getElementById('min-display-sucursal');
        const maxDisplay = document.getElementById('max-display-sucursal');
        const validation = document.getElementById('stock-validation-sucursal');
        const errorMinMax = document.getElementById('stock-error-min-max-sucursal');
        const warningActual = document.getElementById('stock-warning-actual-sucursal');
        const warningMessage = document.getElementById('stock-warning-message-sucursal');

        // Mostrar indicador si hay valores
        if (actual > 0 || minimo > 0 || maximo > 0) {
            indicator.classList.remove('hidden');
        }

        // Actualizar displays
        minDisplay.textContent = minimo;
        maxDisplay.textContent = maximo;

        // Validaciones
        validation.classList.remove('hidden');
        errorMinMax.classList.add('hidden');
        warningActual.classList.add('hidden');

        if (minimo > 0 && maximo > 0 && minimo >= maximo) {
            errorMinMax.classList.remove('hidden');
            bar.className = 'bg-red-500 h-2 rounded-full transition-all duration-500';
            bar.style.width = '100%';
            status.className = 'px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-600 dark:bg-red-900/20 dark:text-red-400';
            status.textContent = 'Error configuración';
            return;
        }

        if (maximo > 0) {
            const percent = Math.min((actual / maximo) * 100, 100);
            bar.style.width = `${percent}%`;

            if (minimo > 0 && actual < minimo) {
                bar.className = 'bg-red-500 h-2 rounded-full transition-all duration-500';
                status.className = 'px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-600 dark:bg-red-900/20 dark:text-red-400';
                status.textContent = 'Stock bajo';
                warningActual.classList.remove('hidden');
                warningMessage.textContent = `Stock actual (${actual}) está por debajo del mínimo (${minimo})`;
            } else if (actual > maximo) {
                bar.className = 'bg-blue-500 h-2 rounded-full transition-all duration-500';
                status.className = 'px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400';
                status.textContent = 'Sobrestock';
                warningActual.classList.remove('hidden');
                warningMessage.textContent = `Stock actual (${actual}) excede el máximo recomendado (${maximo})`;
            } else if (minimo > 0 && actual <= (minimo * 1.2)) {
                bar.className = 'bg-orange-500 h-2 rounded-full transition-all duration-500';
                status.className = 'px-2 py-1 rounded text-xs font-medium bg-orange-100 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400';
                status.textContent = 'Stock limitado';
            } else {
                bar.className = 'bg-green-500 h-2 rounded-full transition-all duration-500';
                status.className = 'px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-600 dark:bg-green-900/20 dark:text-green-400';
                status.textContent = 'Stock óptimo';
            }
        } else {
            bar.style.width = '0%';
            status.className = 'px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300';
            status.textContent = 'Sin configurar';
        }
    }

    // Función para mostrar toast notifications
    function mostrarToast(type, message) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };

        const toast = document.createElement('div');
        toast.className = `${colors[type] || colors.info} text-white p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
        toast.textContent = message;

        container.appendChild(toast);

        // Animar entrada
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 5000);
    }

    /*=============================================
    MODAL EDITAR PRODUCTO POR SUCURSAL
    =============================================*/

    // Abrir modal editar producto sucursal (función global)
    window.abrirModalEditarProductoSucursal = async function(idProductoSucursal) {
        try {
            // Primero cargar las sucursales en el modal de editar
            await cargarSucursalesEnModalEditar();

            // Luego obtener datos del producto-sucursal
            const response = await fetch('ajax/producto-sucursal.ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `accion=obtener_producto_sucursal_por_id&id=${idProductoSucursal}`
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('Respuesta del servidor al obtener producto-sucursal:', data);

            if (data.success && (data.producto || data.producto_sucursal)) {
                const producto = data.producto || data.producto_sucursal;
                // Cargar datos en el formulario
                cargarDatosEnFormularioEditar(producto);

                // Abrir modal usando Preline
                const modalElement = document.querySelector('#modal-editar-producto-sucursal');
                if (modalElement && window.HSOverlay) {
                    window.HSOverlay.open(modalElement);
                }
            } else {
                throw new Error(data.message || 'Error al obtener datos del producto');
            }
        } catch (error) {
            console.error('Error al abrir modal editar:', error);
            mostrarToast('Error al cargar datos del producto: ' + error.message, 'error');
        }
    }

    // Cargar datos en formulario editar
    function cargarDatosEnFormularioEditar(item) {
        console.log('Cargando datos en formulario editar:', item);

        // Elementos del formulario
        const editId = document.getElementById('edit_idproducto_sucursal');
        const editSucursal = document.getElementById('edit_sucursal_idsucursal');
        const editPrecio = document.getElementById('edit_precio_sucursal');
        const editStock = document.getElementById('edit_stock_sucursal');
        const editStockMin = document.getElementById('edit_stock_minimo_sucursal');
        const editStockMax = document.getElementById('edit_stock_maximo_sucursal');
        const editEstado = document.getElementById('edit_estado');
        const editProductoInfo = document.getElementById('edit_producto_info');

        // Llenar campos
        if (editId) editId.value = item.idproducto_sucursal;
        if (editSucursal) editSucursal.value = item.sucursal_idsucursal;
        if (editPrecio) editPrecio.value = item.precio_sucursal;
        if (editStock) editStock.value = item.stock_sucursal || 0;
        if (editStockMin) editStockMin.value = item.stock_minimo_sucursal || 0;
        if (editStockMax) editStockMax.value = item.stock_maximo_sucursal || 0;
        if (editEstado) editEstado.value = item.estado;

        // Mostrar información del producto (solo lectura)
        if (editProductoInfo) {
            const productoTexto = `${item.codigo_auxiliar || item.codigo} - ${item.descripcion}`;

            // Verificar si es input o span y asignar el valor apropiadamente
            if (editProductoInfo.tagName.toLowerCase() === 'input') {
                editProductoInfo.value = productoTexto;
            } else {
                editProductoInfo.textContent = productoTexto;
            }
        }

        // Configurar controles de stock para editar
        configurarControlesStockEditar();
    }

    // Configurar controles de stock para modal editar
    function configurarControlesStockEditar() {
        const editStock = document.getElementById('edit_stock_sucursal');
        const editStockMin = document.getElementById('edit_stock_minimo_sucursal');
        const editStockMax = document.getElementById('edit_stock_maximo_sucursal');

        if (editStock && editStockMin && editStockMax) {
            // Agregar eventos para actualizar indicadores
            [editStock, editStockMin, editStockMax].forEach(input => {
                input.addEventListener('input', updateStockIndicatorEditar);
            });

            // Actualizar indicadores iniciales
            updateStockIndicatorEditar();
        }
    }

    // Actualizar indicador de stock para modal editar
    function updateStockIndicatorEditar() {
        const actual = parseInt(document.getElementById('edit_stock_sucursal').value) || 0;
        const minimo = parseInt(document.getElementById('edit_stock_minimo_sucursal').value) || 0;
        const maximo = parseInt(document.getElementById('edit_stock_maximo_sucursal').value) || 0;

        // Buscar elementos del modal editar (si existen)
        const indicator = document.getElementById('edit-stock-level-indicator');
        const bar = document.getElementById('edit-stock-bar');
        const status = document.getElementById('edit-stock-status');

        // Si no existen los elementos de indicadores, no hacer nada
        if (!indicator || !bar || !status) {
            return;
        }

        // Lógica similar a updateStockIndicator pero para elementos del modal editar
        if (actual > 0 || minimo > 0 || maximo > 0) {
            indicator.classList.remove('hidden');
        }

        // Calcular porcentaje y color según estado
        let percentage = 0;
        let colorClass = 'bg-gray-400';
        let statusText = 'Sin datos';

        if (maximo > 0) {
            percentage = Math.min((actual / maximo) * 100, 100);

            if (actual <= minimo) {
                colorClass = 'bg-red-500';
                statusText = 'Stock crítico';
            } else if (actual <= minimo * 1.5) {
                colorClass = 'bg-orange-500';
                statusText = 'Stock bajo';
            } else if (actual >= maximo * 0.8) {
                colorClass = 'bg-blue-500';
                statusText = 'Stock alto';
            } else {
                colorClass = 'bg-green-500';
                statusText = 'Stock normal';
            }
        }

        // Actualizar barra de progreso
        bar.style.width = `${percentage}%`;
        bar.className = `h-2 rounded-full transition-all duration-300 ${colorClass}`;

        // Actualizar texto de estado
        status.textContent = statusText;
    }

    // Configurar botón actualizar
    function setupModalEditar() {
        const btnActualizar = document.getElementById('btn-actualizar-producto-sucursal');
        if (btnActualizar) {
            btnActualizar.addEventListener('click', actualizarProductoSucursal);
        }
    }

    // Cargar datos del producto-sucursal para editar
    function cargarDatosProductoSucursal(id) {
        console.log('cargarDatosProductoSucursal iniciado con ID:', id);

        fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `accion=obtener_producto_sucursal_por_id&id=${id}`
        })
        .then(response => {
            console.log('Respuesta del servidor - Status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos del servidor:', data);

            if (data.success && data.producto_sucursal) {
                const item = data.producto_sucursal;
                console.log('Datos del producto-sucursal:', item);

                // Llenar formulario
                console.log('Llenando formulario con datos...');

                const editId = document.getElementById('edit_idproducto_sucursal');
                const editPrecio = document.getElementById('edit_precio_sucursal');
                const editStock = document.getElementById('edit_stock_sucursal');
                const editStockMin = document.getElementById('edit_stock_minimo_sucursal');
                const editStockMax = document.getElementById('edit_stock_maximo_sucursal');
                const editEstado = document.getElementById('edit_estado');
                const editProductoInfo = document.getElementById('edit_producto_info');

                console.log('Elementos del formulario:', {
                    editId: editId ? 'Encontrado' : 'NO ENCONTRADO',
                    editPrecio: editPrecio ? 'Encontrado' : 'NO ENCONTRADO',
                    editStock: editStock ? 'Encontrado' : 'NO ENCONTRADO',
                    editProductoInfo: editProductoInfo ? 'Encontrado' : 'NO ENCONTRADO'
                });

                if (editId) editId.value = item.idproducto_sucursal;
                if (editPrecio) editPrecio.value = item.precio_sucursal;
                if (editStock) editStock.value = item.stock_sucursal || 0;
                if (editStockMin) editStockMin.value = item.stock_minimo_sucursal || 0;
                if (editStockMax) editStockMax.value = item.stock_maximo_sucursal || 0;
                if (editEstado) editEstado.value = item.estado;

                // Mostrar información del producto (solo lectura)
                if (editProductoInfo) {
                    const productoTexto = `${item.codigo_auxiliar} - ${item.descripcion}`;
                    editProductoInfo.textContent = productoTexto;
                    console.log('Información del producto cargada:', productoTexto);
                }

                // Cargar sucursales y seleccionar la actual
                console.log('Cargando sucursales...');
                cargarSucursalesEnModalEditar(() => {
                    const editSucursal = document.getElementById('edit_sucursal_idsucursal');
                    if (editSucursal) {
                        editSucursal.value = item.sucursal_idsucursal;
                        console.log('Sucursal seleccionada:', item.sucursal_idsucursal);
                    }
                });

            } else {
                mostrarToast('error', data.message || 'Error al cargar los datos del producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarToast('error', 'Error de conexión al cargar los datos');
        });
    }

    // Cargar sucursales en modal editar
    function cargarSucursalesEnModalEditar(callback = null) {
        console.log('cargarSucursalesEnModalEditar iniciado');

        fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'accion=obtener_sucursales_disponibles'
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta sucursales para modal editar:', data);

            if (data.success) {
                const select = document.getElementById('edit_sucursal_idsucursal');
                console.log('Select sucursales encontrado:', select ? 'SÍ' : 'NO');

                if (select) {
                    select.innerHTML = '<option value="">Seleccionar sucursal...</option>';

                    data.sucursales.forEach(sucursal => {
                        const option = document.createElement('option');
                        option.value = sucursal.idsucursal;
                        option.textContent = sucursal.nombre;
                        select.appendChild(option);
                    });

                    console.log('Sucursales cargadas en modal editar:', data.sucursales.length);
                }

                if (callback) {
                    console.log('Ejecutando callback...');
                    callback();
                }
            } else {
                console.error('Error al cargar sucursales:', data.message);
            }
        })
        .catch(error => {
            console.error('Error al cargar sucursales:', error);
        });
    }

    // Actualizar producto en sucursal
    function actualizarProductoSucursal() {
        console.log('=== ACTUALIZAR PRODUCTO SUCURSAL ===');
        const form = document.getElementById('form-editar-producto-sucursal');
        console.log('Form encontrado:', form);

        if (!form) {
            mostrarToast('error', 'Formulario no encontrado');
            return;
        }

        const formData = new FormData(form);
        console.log('FormData creado, datos:');
        for (const [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Validaciones
        const sucursalId = formData.get('sucursal_idsucursal');
        const precio = formData.get('precio_sucursal');
        const id = formData.get('idproducto_sucursal');

        if (!id) {
            mostrarToast('error', 'ID de producto no encontrado');
            return;
        }

        if (!sucursalId) {
            mostrarToast('error', 'Por favor selecciona una sucursal');
            return;
        }

        if (!precio || parseFloat(precio) <= 0) {
            mostrarToast('error', 'Por favor ingresa un precio válido');
            return;
        }

        // Validación de stock
        const stockActual = parseInt(formData.get('stock_sucursal')) || 0;
        const stockMinimo = parseInt(formData.get('stock_minimo_sucursal')) || 0;
        const stockMaximo = parseInt(formData.get('stock_maximo_sucursal')) || 0;

        if (stockMinimo > 0 && stockMaximo > 0 && stockMinimo >= stockMaximo) {
            mostrarToast('error', 'El stock mínimo debe ser menor al stock máximo');
            return;
        }

        // Deshabilitar botón
        const btnActualizar = document.getElementById('btn-actualizar-producto-sucursal');
        const textoOriginal = btnActualizar.innerHTML;
        btnActualizar.disabled = true;
        btnActualizar.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Actualizando...
        `;

        // Preparar datos
        const data = new URLSearchParams();
        data.append('accion', 'actualizar_producto_sucursal');
        for (const [key, value] of formData.entries()) {
            data.append(key, value);
        }

        fetch('ajax/producto-sucursal.ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: data
        })
        .then(response => {
            console.log('Respuesta HTTP:', response.status);
            return response.json();
        })
        .then(result => {
            console.log('Resultado del servidor:', result);
            if (result.success) {
                mostrarToast('success', result.message || 'Producto actualizado exitosamente');

                // Cerrar modal
                const modalElement = document.querySelector('#modal-editar-producto-sucursal');
                if (modalElement && window.HSOverlay) {
                    window.HSOverlay.close(modalElement);
                }

                cargarProductosSucursal();
            } else {
                mostrarToast('error', result.message || 'Error al actualizar el producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarToast('error', 'Error al actualizar el producto');
        })
        .finally(() => {
            btnActualizar.disabled = false;
            btnActualizar.innerHTML = textoOriginal;
        });
    }

    // Función para expandir/contraer filas en vista móvil
    function toggleExpandRow(idProductoSucursal) {
        const detailsRow = document.getElementById(`details-${idProductoSucursal}`);
        const expandIcon = document.getElementById(`expand-icon-${idProductoSucursal}`);

        if (detailsRow && expandIcon) {
            const isHidden = detailsRow.classList.contains('hidden');

            if (isHidden) {
                // Mostrar detalles
                detailsRow.classList.remove('hidden');
                // Rotar icono hacia abajo
                expandIcon.style.transform = 'rotate(180deg)';
            } else {
                // Ocultar detalles
                detailsRow.classList.add('hidden');
                // Restaurar icono hacia arriba
                expandIcon.style.transform = 'rotate(0deg)';
            }
        }
    }

    // Exponer funciones globalmente para que las pueda llamar el HTML
    window.cambiarPaginaSucursal = cambiarPaginaSucursal;
    window.toggleExpandRow = toggleExpandRow;

})(); // Cerrar IIFE