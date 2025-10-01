/*=============================================
MÓDULO DE EMPRESA
=============================================*/

$(document).ready(function() {
    // Cargar información de la empresa al cargar la página
    cargarEmpresa();

    // Manejar visibilidad del campo de calificación artesanal y animación del switch
    $('#artesano').on('change', function() {
        const isChecked = $(this).is(':checked');

        if (isChecked) {
            $('#campo-calificacion-artesanal').removeClass('hidden');
            $('#artesano-switch-bg').addClass('bg-purple-600').removeClass('bg-gray-200');
            $('#artesano-switch-toggle').addClass('translate-x-5');
        } else {
            $('#campo-calificacion-artesanal').addClass('hidden');
            $('#numero_calificacion_artesanal').val('');
            $('#artesano-switch-bg').removeClass('bg-purple-600').addClass('bg-gray-200');
            $('#artesano-switch-toggle').removeClass('translate-x-5');
        }
    });

    // Toggle mostrar/ocultar contraseña del certificado P12
    $('#toggle-p12-password').on('click', function() {
        const passwordInput = $('#p12_password');
        const eyeIcon = $('#eye-icon-p12');

        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            eyeIcon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>');
        } else {
            passwordInput.attr('type', 'password');
            eyeIcon.html('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>');
        }
    });

    // Validar archivo .p12 y extraer información
    let selectedP12File = null;

    $('#p12_file').on('change', function() {
        const file = this.files[0];
        if (file) {
            const fileName = file.name;
            const fileExt = fileName.split('.').pop().toLowerCase();

            if (fileExt !== 'p12') {
                showNotification('Solo se permiten archivos .p12', 'error');
                $(this).val('');
                $('#p12-expiry-container').addClass('hidden');
                selectedP12File = null;
                return;
            }

            // Guardar referencia al archivo
            selectedP12File = file;

            // Mostrar nombre del archivo seleccionado
            $('#p12-file-current').text(fileName);

            // Intentar extraer fecha de caducidad del certificado
            extractP12ExpiryDate(file);
        } else {
            $('#p12-expiry-container').addClass('hidden');
            selectedP12File = null;
        }
    });

    // Re-verificar certificado cuando se ingrese/cambie la contraseña
    $('#p12_password').on('blur', function() {
        if (selectedP12File && $(this).val()) {
            extractP12ExpiryDate(selectedP12File);
        }
    });

    // Enviar formulario
    $('#form-empresa').on('submit', function(e) {
        e.preventDefault();
        guardarEmpresa();
    });
});

/*=============================================
CARGAR INFORMACIÓN DE LA EMPRESA
=============================================*/
function cargarEmpresa() {
    $.ajax({
        url: 'ajax/empresa.ajax.php',
        method: 'POST',
        data: {
            accion: 'obtener_empresa',
            csrf_token: $('input[name="csrf_token"]').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.data) {
                const empresa = response.data;

                // Llenar campos del formulario
                $('#idempresa_tenant').val(empresa.idempresa_tenant);
                $('#ruc').val(empresa.ruc);
                $('#ruc-display').text(empresa.ruc || '-');
                $('#razon_social').val(empresa.razon_social);
                $('#nombre_comercial').val(empresa.nombre_comercial);
                $('#direccion_matriz').val(empresa.direccion_matriz);
                $('#actividad_economica').val(empresa.actividad_economica);
                $('#tipo_contibuyente').val(empresa.tipo_contibuyente);
                $('#tipo-contibuyente-display').text(empresa.tipo_contibuyente === 'Persona juridica' ? 'Persona Jurídica' : 'Persona Natural');
                $('#regimen').val(empresa.regimen);
                $('#ambiente_sri').val(empresa.ambiente_sri);
                $('#telefono').val(empresa.telefono);
                $('#email').val(empresa.email);

                // Configuración de correo (solo lectura)
                $('#correo_envio_factura').val(empresa.correo_envio_factura);
                $('#correo-envio-display').text(empresa.correo_envio_factura || 'No configurado');

                $('#servidor_smtp_correo_envio_factura').val(empresa.servidor_smtp_correo_envio_factura);
                $('#servidor-smtp-display').text(empresa.servidor_smtp_correo_envio_factura || 'No configurado');

                $('#puerto_correo_envio_factura').val(empresa.puerto_correo_envio_factura);
                $('#puerto-smtp-display').text(empresa.puerto_correo_envio_factura || 'No configurado');

                $('#password_correo_envio_factura').val(empresa.password_correo_envio_factura);
                // La contraseña siempre muestra ••••••••

                // Firma electrónica
                $('#p12_path').val(empresa.p12_path);
                if (empresa.p12_path) {
                    // Extraer solo el nombre del archivo del path
                    const fileName = empresa.p12_path.split('/').pop().split('\\').pop();
                    $('#p12-file-current').text(fileName);
                } else {
                    $('#p12-file-current').text('No configurado');
                }

                $('#p12_password').val(empresa.p12_password);

                // Mostrar fecha de caducidad del certificado si existe
                if (empresa.p12_expiration_date) {
                    displaySavedP12ExpiryDate(empresa.p12_expiration_date);
                }

                $('#numero_calificacion_artesanal').val(empresa.numero_calificacion_artesanal);

                // Establecer checkboxes y campos ocultos
                // Campos ocultos: no disponibles en versión inicial (configurables desde panel admin)
                $('#contabilidad').val(empresa.contabilidad || 0);
                $('#agente_retencion').val(empresa.agente_retencion || 0);
                $('#contribuyente_especial').val(empresa.contribuyente_especial || 0);

                // Solo campo Artesano es visible y editable
                $('#artesano').prop('checked', empresa.artesano == 1);

                // Mostrar campo de calificación artesanal y actualizar switch si es artesano
                if (empresa.artesano == 1) {
                    $('#campo-calificacion-artesanal').removeClass('hidden');
                    $('#artesano-switch-bg').addClass('bg-purple-600').removeClass('bg-gray-200');
                    $('#artesano-switch-toggle').addClass('translate-x-5');
                } else {
                    $('#artesano-switch-bg').removeClass('bg-purple-600').addClass('bg-gray-200');
                    $('#artesano-switch-toggle').removeClass('translate-x-5');
                }

            } else {
                showNotification(response.message || 'Error al cargar información de la empresa', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar empresa:', error);
            showNotification('Error al cargar información de la empresa', 'error');
        }
    });
}

/*=============================================
GUARDAR INFORMACIÓN DE LA EMPRESA
=============================================*/
function guardarEmpresa() {
    const formData = new FormData($('#form-empresa')[0]);
    formData.append('accion', 'actualizar_empresa');

    // Deshabilitar botón mientras se procesa
    const btnGuardar = $('#btn-guardar-empresa');
    const btnTextoOriginal = btnGuardar.html();
    btnGuardar.prop('disabled', true).html(`
        <svg class="animate-spin h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Guardando...
    `);

    $.ajax({
        url: 'ajax/empresa.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            btnGuardar.prop('disabled', false).html(btnTextoOriginal);

            if (response.status === 'success') {
                showNotification(response.message, 'success');
                // Recargar datos para reflejar cambios guardados
                setTimeout(function() {
                    cargarEmpresa();
                }, 500);
            } else {
                showNotification(response.message || 'Error al guardar información', 'error');
            }
        },
        error: function(xhr, status, error) {
            btnGuardar.prop('disabled', false).html(btnTextoOriginal);
            console.error('Error al guardar empresa:', error);
            showNotification('Error al guardar información de la empresa', 'error');
        }
    });
}

/*=============================================
MOSTRAR FECHA DE CADUCIDAD GUARDADA EN BD
=============================================*/
function displaySavedP12ExpiryDate(fechaExpiracion) {
    // Verificar si es fecha de ambiente de pruebas
    if (fechaExpiracion === '2001-01-01') {
        $('#p12-expiry-container').removeClass('hidden');
        $('#p12-expiry-date').html('<span class="text-yellow-600 dark:text-yellow-400">⚠️ Fecha en ambiente de pruebas</span>');
        $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-red-300 dark:border-red-700 border-green-300 dark:border-green-700')
                                 .addClass('border-yellow-300 dark:border-yellow-700 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/10 dark:to-amber-900/10');
        return;
    }

    // Calcular días restantes
    const fecha = new Date(fechaExpiracion);
    const hoy = new Date();
    const diasRestantes = Math.floor((fecha - hoy) / (1000 * 60 * 60 * 24));

    // Formatear fecha
    const formattedDate = fecha.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    // Mostrar fecha con indicador de estado
    $('#p12-expiry-container').removeClass('hidden');

    if (diasRestantes < 0) {
        // Certificado expirado
        $('#p12-expiry-date').html(`<span class="text-red-600 dark:text-red-400">⚠️ Expirado - ${formattedDate}</span>`);
        $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-yellow-300 dark:border-yellow-700 border-green-300 dark:border-green-700')
                                 .addClass('border-red-300 dark:border-red-700 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/10 dark:to-pink-900/10');
    } else if (diasRestantes <= 30) {
        // Próximo a expirar (30 días o menos)
        $('#p12-expiry-date').html(`<span class="text-orange-600 dark:text-orange-400">⚠️ ${formattedDate} (${diasRestantes} días restantes)</span>`);
        $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-yellow-300 dark:border-yellow-700 border-green-300 dark:border-green-700')
                                 .addClass('border-orange-300 dark:border-orange-700 bg-gradient-to-r from-orange-50 to-yellow-50 dark:from-orange-900/10 dark:to-yellow-900/10');
    } else {
        // Certificado válido
        $('#p12-expiry-date').html(`<span class="text-green-600 dark:text-green-400">✓ ${formattedDate} (${diasRestantes} días restantes)</span>`);
        $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-yellow-300 dark:border-yellow-700 border-red-300 dark:border-red-700')
                                 .addClass('border-green-300 dark:border-green-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10');
    }
}

/*=============================================
EXTRAER FECHA DE CADUCIDAD DEL CERTIFICADO P12
=============================================*/
function extractP12ExpiryDate(file) {
    // Verificar si hay contraseña ingresada
    const password = $('#p12_password').val();

    if (!password) {
        // Mostrar mensaje indicando que se necesita la contraseña
        $('#p12-expiry-container').removeClass('hidden');
        $('#p12-expiry-date').html('<span class="text-yellow-600 dark:text-yellow-400">⚠️ Ingrese la contraseña para verificar la fecha de caducidad</span>');
        $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-red-300 dark:border-red-700 border-green-300 dark:border-green-700')
                                 .addClass('border-yellow-300 dark:border-yellow-700 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/10 dark:to-amber-900/10');
        return;
    }

    // Mostrar loader
    $('#p12-expiry-container').removeClass('hidden');
    $('#p12-expiry-date').html('<span class="text-blue-600 dark:text-blue-400">🔄 Verificando certificado...</span>');

    // Crear FormData para enviar archivo y contraseña
    const formData = new FormData();
    formData.append('p12_file', file);
    formData.append('password', password);
    formData.append('accion', 'extraer_fecha_certificado');
    formData.append('csrf_token', $('input[name="csrf_token"]').val());

    // Enviar al backend
    $.ajax({
        url: 'ajax/empresa.ajax.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.data) {
                const data = response.data;
                const diasRestantes = data.dias_restantes;

                // Formatear fecha
                const fecha = new Date(data.fecha_expiracion);
                const formattedDate = fecha.toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                // Mostrar fecha con indicador de estado
                if (data.expirado) {
                    $('#p12-expiry-date').html(`<span class="text-red-600 dark:text-red-400">⚠️ Expirado - ${formattedDate}</span>`);
                    $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-yellow-300 dark:border-yellow-700 border-green-300 dark:border-green-700')
                                             .addClass('border-red-300 dark:border-red-700 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/10 dark:to-pink-900/10');
                } else if (data.proximo_a_expirar) {
                    $('#p12-expiry-date').html(`<span class="text-orange-600 dark:text-orange-400">⚠️ ${formattedDate} (${diasRestantes} días restantes)</span>`);
                    $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-yellow-300 dark:border-yellow-700 border-green-300 dark:border-green-700')
                                             .addClass('border-orange-300 dark:border-orange-700 bg-gradient-to-r from-orange-50 to-yellow-50 dark:from-orange-900/10 dark:to-yellow-900/10');
                } else {
                    $('#p12-expiry-date').html(`<span class="text-green-600 dark:text-green-400">✓ ${formattedDate} (${diasRestantes} días restantes)</span>`);
                    $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-yellow-300 dark:border-yellow-700 border-red-300 dark:border-red-700')
                                             .addClass('border-green-300 dark:border-green-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10');
                }
            } else {
                $('#p12-expiry-date').html(`<span class="text-red-600 dark:text-red-400">❌ ${response.message || 'Error al verificar certificado'}</span>`);
                $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-yellow-300 dark:border-yellow-700 border-green-300 dark:border-green-700')
                                         .addClass('border-red-300 dark:border-red-700 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/10 dark:to-pink-900/10');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al extraer fecha del certificado:', error);
            $('#p12-expiry-date').html('<span class="text-red-600 dark:text-red-400">❌ Error de conexión al servidor</span>');
            $('#p12-expiry-container').removeClass('border-blue-200 dark:border-blue-700 border-yellow-300 dark:border-yellow-700 border-green-300 dark:border-green-700')
                                     .addClass('border-red-300 dark:border-red-700');
        }
    });
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
