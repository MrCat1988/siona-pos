/*=============================================
MÓDULO DE CLIENTES
=============================================*/

// Variables globales
let paginaActual = 1;
let registrosPorPagina = 50;
let filtrosActuales = {};

$(document).ready(function() {
    // Cargar clientes al iniciar
    cargarClientes();

    // Botón nuevo cliente - Preline maneja la apertura del modal
    $('#btn-nuevo-cliente').on('click', function() {
        abrirModalNuevoCliente();
    });

    // Enviar formulario con botón guardar
    $('#btn-guardar-cliente').on('click', function(e) {
        e.preventDefault();
        guardarCliente();
    });

    // Búsqueda con debounce
    let busquedaTimeout;
    $('#buscar-cliente').on('keyup', function() {
        clearTimeout(busquedaTimeout);
        busquedaTimeout = setTimeout(function() {
            paginaActual = 1;
            cargarClientes();
        }, 500);
    });

    // Filtros
    $('#filtro-tipo-identificacion, #filtro-estado').on('change', function() {
        paginaActual = 1;
        cargarClientes();
    });

    // Limpiar filtros
    $('#btn-limpiar-filtros').on('click', function() {
        $('#buscar-cliente').val('');
        $('#filtro-tipo-identificacion').val('');
        $('#filtro-estado').val('1');
        paginaActual = 1;
        cargarClientes();
    });

    // Cambiar registros por página
    $('#registros-por-pagina').on('change', function() {
        registrosPorPagina = parseInt($(this).val());
        paginaActual = 1;
        cargarClientes();
    });

    // Validación en tiempo real del número de identificación
    $('#tipo_identificacion_sri').on('change', function() {
        actualizarPlaceholderIdentificacion();
        validarNumeroIdentificacion();
    });

    $('#numero_identificacion').on('input', function() {
        let valor = $(this).val();

        // Remover cualquier carácter no numérico (para permitir detección automática)
        valor = valor.replace(/\D/g, '');
        $(this).val(valor);

        // Limpiar borde rojo cuando empieza a escribir
        if (valor.length > 0) {
            $(this).removeClass('border-red-500');
        }

        // Detección automática del tipo de identificación
        detectarTipoIdentificacion();
        validarNumeroIdentificacion();

        // Verificar duplicados en tiempo real
        verificarDuplicado();
    });

    // Validación en tiempo real de nombres y apellidos
    $('#nombres').on('blur', function() {
        const valor = $(this).val().trim();
        $(this).removeClass('border-red-500 border-green-500');

        if (valor === '') {
            $(this).addClass('border-red-500');
        } else {
            $(this).addClass('border-green-500');
        }
    });

    $('#apellidos').on('blur', function() {
        const valor = $(this).val().trim();
        $(this).removeClass('border-red-500 border-green-500');

        if (valor === '') {
            $(this).addClass('border-red-500');
        } else {
            $(this).addClass('border-green-500');
        }
    });

    // Limpiar borde rojo cuando empiezan a escribir
    $('#nombres, #apellidos').on('input', function() {
        $(this).removeClass('border-red-500');
    });
});

/*=============================================
CARGAR CLIENTES CON FILTROS Y PAGINACIÓN
=============================================*/
function cargarClientes() {
    // Construir datos de filtros
    const datos = {
        accion: 'obtener_clientes',
        csrf_token: $('input[name="csrf_token"]').val(),
        busqueda: $('#buscar-cliente').val(),
        tipo_identificacion: $('#filtro-tipo-identificacion').val(),
        estado: $('#filtro-estado').val(),
        page: paginaActual,
        limit: registrosPorPagina
    };

    filtrosActuales = datos;

    $.ajax({
        url: 'ajax/clientes.ajax.php',
        method: 'POST',
        data: datos,
        dataType: 'json',
        beforeSend: function() {
            $('#tabla-clientes-body').html(`
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="animate-spin h-10 w-10 text-blue-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400">Cargando clientes...</p>
                        </div>
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            if (response.status === 'success') {
                mostrarClientes(response.data);
                actualizarPaginacion(response.pagination);
                $('#total-clientes').text(response.pagination.total);
            } else {
                mostrarError('Error al cargar clientes');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar clientes:', error);
            mostrarError('Error al conectar con el servidor');
        }
    });
}

/*=============================================
MOSTRAR CLIENTES EN LA TABLA
=============================================*/
function mostrarClientes(clientes) {
    const tbody = $('#tabla-clientes-body');

    if (clientes.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-gray-600 dark:text-gray-400 text-lg font-medium">No se encontraron clientes</p>
                        <p class="text-gray-500 dark:text-gray-500 text-sm mt-1">Intenta ajustar los filtros de búsqueda</p>
                    </div>
                </td>
            </tr>
        `);
        return;
    }

    let html = '';
    clientes.forEach(function(cliente) {
        const tipoIdTexto = obtenerTextoTipoIdentificacion(cliente.tipo_identificacion_sri);
        const nombreCompleto = `${cliente.nombres} ${cliente.apellidos}`.trim();
        const estadoBadge = cliente.estado == 1
            ? '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Activo</span>'
            : '<span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Inactivo</span>';

        html += `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-xs font-medium text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">${tipoIdTexto}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">${cliente.numero_identificacion || '-'}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                            ${nombreCompleto.charAt(0).toUpperCase()}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${nombreCompleto}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">${cliente.email || '-'}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-600 dark:text-gray-400">${cliente.telefono || '-'}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${estadoBadge}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center gap-2">
                        ${cliente.tipo_identificacion_sri !== '07' ? `
                            <button onclick="editarCliente(${cliente.idcliente})" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors" title="Editar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <button onclick="eliminarCliente(${cliente.idcliente}, '${nombreCompleto}')" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors" title="Eliminar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        ` : `
                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">Cliente del sistema</span>
                        `}
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.html(html);
}

/*=============================================
ACTUALIZAR PAGINACIÓN
=============================================*/
function actualizarPaginacion(pagination) {
    const container = $('#paginacion-botones');
    let html = '';

    const totalPaginas = pagination.pages;
    const paginaActualNum = pagination.page;

    // Botón anterior
    html += `
        <button onclick="cambiarPagina(${paginaActualNum - 1})"
                ${paginaActualNum === 1 ? 'disabled' : ''}
                class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
    `;

    // Números de página
    let inicioRango = Math.max(1, paginaActualNum - 2);
    let finRango = Math.min(totalPaginas, paginaActualNum + 2);

    if (inicioRango > 1) {
        html += `<button onclick="cambiarPagina(1)" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">1</button>`;
        if (inicioRango > 2) {
            html += `<span class="px-2 text-gray-500">...</span>`;
        }
    }

    for (let i = inicioRango; i <= finRango; i++) {
        const esActual = i === paginaActualNum;
        html += `
            <button onclick="cambiarPagina(${i})"
                    class="px-4 py-2 rounded-lg border ${esActual ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'} transition-colors">
                ${i}
            </button>
        `;
    }

    if (finRango < totalPaginas) {
        if (finRango < totalPaginas - 1) {
            html += `<span class="px-2 text-gray-500">...</span>`;
        }
        html += `<button onclick="cambiarPagina(${totalPaginas})" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">${totalPaginas}</button>`;
    }

    // Botón siguiente
    html += `
        <button onclick="cambiarPagina(${paginaActualNum + 1})"
                ${paginaActualNum === totalPaginas ? 'disabled' : ''}
                class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    `;

    container.html(html);
}

/*=============================================
CAMBIAR PÁGINA
=============================================*/
function cambiarPagina(pagina) {
    paginaActual = pagina;
    cargarClientes();
}

/*=============================================
ABRIR MODAL NUEVO CLIENTE
=============================================*/
function abrirModalNuevoCliente() {
    $('#modal-cliente-label').text('Nuevo Cliente');
    $('#modal-cliente-subtitle').text('Crear un nuevo cliente en el sistema');
    $('#btn-guardar-texto').text('Guardar Cliente');
    $('#accion-modal').val('crear');
    $('#form-cliente')[0].reset();
    $('#idcliente').val('');
    $('#estado').prop('checked', true);

    // Limpiar estilos de validación
    $('#numero_identificacion, #nombres, #apellidos').removeClass('border-red-500 border-green-500');
    $('#numero_identificacion').parent().find('.error-message, .error-duplicado').remove();
}

/*=============================================
EDITAR CLIENTE
=============================================*/
function editarCliente(idcliente) {
    $.ajax({
        url: 'ajax/clientes.ajax.php',
        method: 'POST',
        data: {
            accion: 'obtener_cliente',
            idcliente: idcliente,
            csrf_token: $('input[name="csrf_token"]').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const cliente = response.data;

                $('#modal-cliente-label').text('Editar Cliente');
                $('#modal-cliente-subtitle').text('Actualizar información del cliente');
                $('#btn-guardar-texto').text('Actualizar Cliente');
                $('#accion-modal').val('editar');
                $('#idcliente').val(cliente.idcliente);
                $('#tipo_identificacion_sri').val(cliente.tipo_identificacion_sri);
                $('#numero_identificacion').val(cliente.numero_identificacion);
                $('#nombres').val(cliente.nombres);
                $('#apellidos').val(cliente.apellidos);
                $('#email').val(cliente.email);
                $('#telefono').val(cliente.telefono);
                $('#direccion').val(cliente.direccion);
                $('#estado').prop('checked', cliente.estado == 1);

                // Limpiar estilos de validación
                $('#numero_identificacion, #nombres, #apellidos').removeClass('border-red-500 border-green-500');
                $('#numero_identificacion').parent().find('.error-message, .error-duplicado').remove();

                // Abrir modal con Preline
                window.HSOverlay.open('#modal-cliente');

                // Validar el número de identificación cargado
                setTimeout(function() {
                    validarNumeroIdentificacion();
                    // NO verificar duplicado al cargar para edición (el registro actual es válido)
                }, 100);
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener cliente:', error);
            showNotification('Error al cargar los datos del cliente', 'error');
        }
    });
}

/*=============================================
GUARDAR CLIENTE
=============================================*/
function guardarCliente() {
    // Limpiar bordes de error previos
    $('#nombres, #apellidos, #numero_identificacion').removeClass('border-red-500');

    // Validar nombres (obligatorio)
    const nombres = $('#nombres').val().trim();
    if (nombres === '') {
        showNotification('El campo Nombres es obligatorio', 'error');
        $('#nombres').addClass('border-red-500').focus();
        return;
    }

    // Validar apellidos (obligatorio)
    const apellidos = $('#apellidos').val().trim();
    if (apellidos === '') {
        showNotification('El campo Apellidos es obligatorio', 'error');
        $('#apellidos').addClass('border-red-500').focus();
        return;
    }

    // Validar número de identificación (obligatorio)
    const tipo = $('#tipo_identificacion_sri').val();
    const numero = $('#numero_identificacion').val().trim();

    if (numero === '') {
        showNotification('El número de identificación es obligatorio', 'error');
        $('#numero_identificacion').addClass('border-red-500').focus();
        return;
    }

    // Validar estructura según el tipo (Cédula o RUC)
    if (tipo === '05' || tipo === '04') {
        if (!validarNumeroIdentificacion()) {
            showNotification('Por favor, corrija el número de identificación antes de continuar', 'error');
            return;
        }
    }

    // Verificar si existe mensaje de error de duplicado
    if ($('#numero_identificacion').parent().find('.error-duplicado').length > 0) {
        showNotification('Este número de identificación ya está registrado. Por favor, use uno diferente.', 'error');
        $('#numero_identificacion').focus();
        return;
    }

    // Establecer dirección por defecto si está vacía
    let direccion = $('#direccion').val().trim();
    if (direccion === '') {
        direccion = 'Quito';
        $('#direccion').val(direccion);
    }

    const formData = new FormData($('#form-cliente')[0]);
    const accion = $('#accion-modal').val();

    formData.append('accion', accion === 'crear' ? 'crear_cliente' : 'actualizar_cliente');
    formData.set('estado', $('#estado').is(':checked') ? 1 : 0);
    formData.set('direccion', direccion); // Asegurar que se envíe la dirección

    const btnGuardar = $('#btn-guardar-cliente');
    const textoOriginal = btnGuardar.html();

    btnGuardar.prop('disabled', true).html(`
        <svg class="animate-spin h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Guardando...
    `);

    $.ajax({
        url: 'ajax/clientes.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            btnGuardar.prop('disabled', false).html(textoOriginal);

            if (response.status === 'success') {
                showNotification(response.message, 'success');
                // Cerrar modal con Preline
                window.HSOverlay.close('#modal-cliente');
                cargarClientes();
            } else {
                showNotification(response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            btnGuardar.prop('disabled', false).html(textoOriginal);
            console.error('Error al guardar cliente:', error);
            showNotification('Error al guardar el cliente', 'error');
        }
    });
}

/*=============================================
ELIMINAR CLIENTE
=============================================*/
function eliminarCliente(idcliente, nombre) {
    if (confirm(`¿Está seguro que desea eliminar al cliente "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        $.ajax({
            url: 'ajax/clientes.ajax.php',
            method: 'POST',
            data: {
                accion: 'eliminar_cliente',
                idcliente: idcliente,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showNotification(response.message, 'success');
                    cargarClientes();
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al eliminar cliente:', error);
                showNotification('Error al eliminar el cliente', 'error');
            }
        });
    }
}

/*=============================================
OBTENER TEXTO DE TIPO DE IDENTIFICACIÓN
=============================================*/
function obtenerTextoTipoIdentificacion(codigo) {
    const tipos = {
        '04': 'RUC',
        '05': 'CED',
        '06': 'PAS',
        '07': 'CF',
        '08': 'IDE'
    };
    return tipos[codigo] || codigo;
}

/*=============================================
MOSTRAR ERROR
=============================================*/
function mostrarError(mensaje) {
    $('#tabla-clientes-body').html(`
        <tr>
            <td colspan="7" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-16 h-16 text-red-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-600 dark:text-gray-400 text-lg font-medium">${mensaje}</p>
                </div>
            </td>
        </tr>
    `);
}

/*=============================================
MOSTRAR NOTIFICACIÓN BREVE (PARA DETECCIÓN AUTOMÁTICA)
=============================================*/
function mostrarNotificacionBreve(message, type = 'info') {
    const bgColor = type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' :
                    type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';

    const icon = type === 'success' ? '✓' :
                 type === 'error' ? '✗' :
                 type === 'warning' ? '⚠' : 'ℹ';

    const notification = $(`
        <div class="fixed top-4 right-4 z-50 ${bgColor} text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 animate-fade-in-down text-sm">
            <span class="text-lg">${icon}</span>
            <span class="font-medium">${message}</span>
        </div>
    `);

    $('body').append(notification);

    setTimeout(function() {
        notification.fadeOut(300, function() {
            $(this).remove();
        });
    }, 1500); // Más breve que las notificaciones normales
}

/*=============================================
MOSTRAR NOTIFICACIONES
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
DETECTAR AUTOMÁTICAMENTE EL TIPO DE IDENTIFICACIÓN
=============================================*/
function detectarTipoIdentificacion() {
    const numero = $('#numero_identificacion').val().trim();
    const tipoActual = $('#tipo_identificacion_sri').val();

    // Solo detectar si el número contiene solo dígitos
    if (!/^\d+$/.test(numero)) {
        return;
    }

    // Si tiene 10 dígitos y valida como cédula
    if (numero.length === 10) {
        if (validateCedula(numero)) {
            // Solo cambiar si no está ya en Cédula
            if (tipoActual !== '05') {
                $('#tipo_identificacion_sri').val('05');
                actualizarPlaceholderIdentificacion(false); // No limpiar el campo
                // Mostrar notificación breve
                mostrarNotificacionBreve('Detectado: Cédula', 'info');
            }
        }
    }

    // Si tiene 13 dígitos y valida como RUC
    else if (numero.length === 13) {
        if (validateRuc(numero)) {
            // Solo cambiar si no está ya en RUC
            if (tipoActual !== '04') {
                $('#tipo_identificacion_sri').val('04');
                actualizarPlaceholderIdentificacion(false); // No limpiar el campo
                // Mostrar notificación breve
                mostrarNotificacionBreve('Detectado: RUC', 'info');
            }
        }
    }
}

/*=============================================
ACTUALIZAR PLACEHOLDER Y MAXLENGTH SEGÚN TIPO
=============================================*/
function actualizarPlaceholderIdentificacion(limpiarCampo = true) {
    const tipo = $('#tipo_identificacion_sri').val();
    const $input = $('#numero_identificacion');

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

    // Solo limpiar el campo si se solicita explícitamente
    if (limpiarCampo) {
        $input.val('');
        $input.removeClass('border-red-500 border-green-500');
        $input.parent().find('.error-message').remove();
    }
}

/*=============================================
VERIFICAR SI EL NÚMERO DE IDENTIFICACIÓN YA EXISTE
=============================================*/
let timeoutDuplicado = null;
function verificarDuplicado() {
    const numero = $('#numero_identificacion').val().trim();
    const idcliente = $('#idcliente').val();

    // Si está vacío, no verificar
    if (numero === '') {
        return;
    }

    // Limpiar timeout anterior
    clearTimeout(timeoutDuplicado);

    // Esperar 800ms después de que el usuario deje de escribir
    timeoutDuplicado = setTimeout(function() {
        $.ajax({
            url: 'ajax/clientes.ajax.php',
            method: 'POST',
            data: {
                accion: 'verificar_duplicado',
                numero_identificacion: numero,
                idcliente: idcliente,
                csrf_token: $('input[name="csrf_token"]').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const $input = $('#numero_identificacion');
                    const $container = $input.parent();

                    // Remover mensaje previo de duplicado
                    $container.find('.error-duplicado').remove();

                    if (response.existe) {
                        // Número duplicado
                        $input.addClass('border-red-500');
                        $input.removeClass('border-green-500');
                        $container.append(`
                            <p class="error-duplicado text-xs text-red-600 mt-1 font-semibold">
                                ⚠️ Este número de identificación ya está registrado
                            </p>
                        `);
                    } else {
                        // Número disponible - remover borde rojo si existía
                        $container.find('.error-duplicado').remove();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al verificar duplicado:', error);
            }
        });
    }, 800);
}

/*=============================================
VALIDAR NÚMERO DE IDENTIFICACIÓN EN TIEMPO REAL
=============================================*/
function validarNumeroIdentificacion() {
    const tipo = $('#tipo_identificacion_sri').val();
    const numero = $('#numero_identificacion').val().trim();
    const $input = $('#numero_identificacion');
    const $container = $input.parent();

    // Remover mensajes de error previos
    $container.find('.error-message').remove();
    $input.removeClass('border-red-500 border-green-500');

    // Si está vacío, no validar aún
    if (numero === '') {
        return true;
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
    } else if (!esValido) {
        $input.addClass('border-red-500');
        $container.append(`<p class="error-message text-xs text-red-600 mt-1">${mensaje}</p>`);
    }

    return esValido;
}

// ===============================================
// ================= FUNCTIONS ===================
// ===============================================
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
          total += parseInt(cad.charAt(i)); // parseInt o concatenará en lugar de sumar
        }
      }
      total = total % 10 ? 10 - total % 10 : 0;

      if (cad.charAt(longitud-1) == total) {
        return true;
      }else{
        return false;
      }
    }
}

function validateRuc(id_number) {          
    numero = id_number.trim();
  /* alert(numero); */

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
    //    return false;
    }
                
    if (numero.length < 10 ){              
       return false;                  
    //    return false;
    }
   
    /* Los primeros dos digitos corresponden al codigo de la provincia */
    provincia = numero.substr(0,2);      
    if (provincia < 1 || provincia > numeroProvincias){           
       return false;
//    return false;       
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
    /* 9 para sociedades privadas y extranjeros   */         
    /* 6 para sociedades publicas */         
    /* menor que 6 (0,1,2,3,4,5) para personas naturales */ 

    if (d3==7 || d3==8){           
       return false;                     
    //    return false;
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
        //   return false;
       }                  
       /* El ruc de las empresas del sector publico terminan con 0001*/         
       if ( numero.substr(9,4) != '0001' ){                    
          return false;
        //   return false;
       }
    }         
    else if(pri == true){         
       if (digitoVerificador != d10){                          
          return false;
        //   return false;
       }         
       if ( numero.substr(10,3) != '001' ){                    
          return false
        //   return false;
       }
    }      

    else if(nat == true){         
       if (digitoVerificador != d10){                          
          return false;
        //   return false;
       }         
       if (numero.length >10 && numero.substr(10,3) != '001' ){                    
          return false;
        //   return false;
       }
    }      
    return true;
    // return true;   
} 