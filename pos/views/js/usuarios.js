// Función para hash SHA-256 lado cliente
async function hashPassword(password, salt) {
    const encoder = new TextEncoder();
    const data = encoder.encode(password + salt);
    const hashBuffer = await crypto.subtle.digest('SHA-256', data);
    const hashArray = Array.from(new Uint8Array(hashBuffer));
    return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
}

// Función para obtener salt del servidor
function getSalt(email) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'ajax/usuarios.ajax.php',
            type: 'POST',
            data: { accion: 'get_salt', email: email },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    resolve(response.salt);
                } else {
                    reject(response.message);
                }
            },
            error: function() {
                reject('Error del servidor');
            }
        });
    });
}

// Función para mostrar toasts
function showToast(toastHtml) {
    // Crear contenedor si no existe
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 space-y-3';
        document.body.appendChild(container);
    }
    
    const container = document.getElementById('toast-container');
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = toastHtml;
    const toast = tempDiv.firstElementChild;
    
    // Añadir clases de animación
    toast.style.transform = 'translateX(100%)';
    toast.style.opacity = '0';
    toast.style.transition = 'all 0.3s ease-in-out';
    
    container.appendChild(toast);
    
    // Animar entrada
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
    }, 10);
    
    // Auto-remover después de 4 segundos
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        toast.style.opacity = '0';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 4000);
}

$(document).ready(function() {
    
    // Login Form Handler
    $('#loginForm').on('submit', async function(e) {
        e.preventDefault();
        
        var email = $('#email').val();
        var password = $('#password').val();
        
        if (email === '' || password === '') {
            // Toast para campos requeridos
            HSStaticMethods.autoInit(['toast']);
            const toastHtml = `
                <div id="toast-warning" class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                    <div class="flex p-4">
                        <div class="shrink-0">
                            <svg class="shrink-0 size-4 text-yellow-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm text-gray-700 dark:text-neutral-400">
                                Por favor complete todos los campos
                            </p>
                        </div>
                    </div>
                </div>
            `;
            showToast(toastHtml);
            return;
        }
        
        var csrf_token = $('input[name="csrf_token"]').val();
        
        try {
            // Obtener salt y hacer hash del password
            $('#btnLogin').prop('disabled', true).text('Procesando...');
            const salt = await getSalt(email);
            const hashedPassword = await hashPassword(password, salt);
            
            var datos = {
                email: email,
                password: hashedPassword,
                csrf_token: csrf_token,
                is_hashed: true
            };
        } catch (error) {
            // Si falla el hash, usar password original como fallback
            var datos = {
                email: email,
                password: password,
                csrf_token: csrf_token,
                is_hashed: false
            };
        }
        
        $.ajax({
            url: 'ajax/usuarios.ajax.php',
            type: 'POST',
            data: datos,
            dataType: 'json',
            beforeSend: function() {
                $('#btnLogin').prop('disabled', true).text('Iniciando...');
            },
            success: function(respuesta) {
                if (respuesta.status === 'success') {
                    // Toast de éxito
                    const toastHtml = `
                        <div id="toast-success" class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                            <div class="flex p-4">
                                <div class="shrink-0">
                                    <svg class="shrink-0 size-4 text-teal-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                </div>
                                <div class="ms-3">
                                    <p class="text-sm text-gray-700 dark:text-neutral-400">
                                        ¡Bienvenido! Redirigiendo...
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    showToast(toastHtml);
                    setTimeout(() => {
                        window.location.href = 'content';
                    }, 1500);
                } else {
                    // Toast de error
                    const toastHtml = `
                        <div id="toast-error" class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                            <div class="flex p-4">
                                <div class="shrink-0">
                                    <svg class="shrink-0 size-4 text-red-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 1 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                    </svg>
                                </div>
                                <div class="ms-3">
                                    <p class="text-sm text-gray-700 dark:text-neutral-400">
                                        ${respuesta.message}
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    showToast(toastHtml);
                }
            },
            error: function() {
                // Toast de error de servidor
                const toastHtml = `
                    <div id="toast-server-error" class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                        <div class="flex p-4">
                            <div class="shrink-0">
                                <svg class="shrink-0 size-4 text-red-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 1 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                </svg>
                            </div>
                            <div class="ms-3">
                                <p class="text-sm text-gray-700 dark:text-neutral-400">
                                    No se pudo conectar con el servidor
                                </p>
                            </div>
                        </div>
                    </div>
                `;
                showToast(toastHtml);
            },
            complete: function() {
                $('#btnLogin').prop('disabled', false).text('Iniciar sesión');
            }
        });
    });
    
    // Logout Handler
    function logout() {
        Swal.fire({
            title: '¿Cerrar sesión?',
            text: '¿Estás seguro de que quieres cerrar la sesión?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cerrar sesión',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/usuarios.ajax.php',
                    type: 'POST',
                    data: { accion: 'logout' },
                    dataType: 'json',
                    success: function(respuesta) {
                        if (respuesta.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sesión cerrada',
                                text: 'Has cerrado sesión exitosamente',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = 'login';
                            });
                        }
                    },
                    error: function() {
                        window.location.href = 'login';
                    }
                });
            }
        });
    }
    
    // Verificar sesi�n al cargar p�gina
    function verificarSesion() {
        $.ajax({
            url: 'ajax/usuarios.ajax.php',
            type: 'POST',
            data: { accion: 'verificar_sesion' },
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta.status === 'inactive') {
                    window.location.href = 'login';
                }
            }
        });
    }
    
    // DataTable para usuarios (si existe la tabla)
    if ($('#tablaUsuarios').length) {
        cargarTablaUsuarios();
    }
    
    function cargarTablaUsuarios() {
        $.ajax({
            url: 'ajax/usuarios.ajax.php',
            type: 'POST',
            data: { accion: 'obtener_usuarios' },
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta.status === 'success') {
                    $('#tablaUsuarios').DataTable({
                        data: respuesta.data,
                        columns: [
                            { data: 'nombre' },
                            { data: 'cargo' },
                            { data: 'email' },
                            { data: 'rol' },
                            { data: 'sucursal_nombre' },
                            { 
                                data: 'estado',
                                render: function(data) {
                                    return data == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';
                                }
                            },
                            {
                                data: null,
                                render: function(data, type, row) {
                                    return '<button class="btn btn-warning btn-sm btnEditarUsuario" data-id="' + row.idusuario + '">Editar</button> ' +
                                           '<button class="btn btn-danger btn-sm btnEliminarUsuario" data-id="' + row.idusuario + '">Eliminar</button>';
                                }
                            }
                        ],
                        language: {
                            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                        },
                        responsive: true,
                        autoWidth: false
                    });
                }
            }
        });
    }
    
    // Exponer funci�n logout globalmente
    window.logout = logout;
    
    // Verificar sesi�n si no estamos en la p�gina de login
    if (!window.location.href.includes('login')) {
        verificarSesion();
    }
});