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
    
    // Sistema de gestión de usuarios con cards
    const UsuariosManager = {
        data: [],
        filteredData: [],
        filters: {
            buscar: '',
            rol: '',
            estado: '1' // Inicializar con estado activo
        },

        init: function() {
            // Solo inicializar si existe el contenedor
            if ($('#usuarios-grid').length) {
                this.bindEvents();
                this.cargarUsuarios();
            }
        },

        bindEvents: function() {
            const self = this;
            
            // Event handlers para filtros
            $('#buscar-usuario').on('input', function() {
                self.filters.buscar = $(this).val().toLowerCase();
                self.filtrarYMostrar();
            });

            $('#filtro-rol').on('change', function() {
                self.filters.rol = $(this).val();
                self.filtrarYMostrar();
            });

            $('#filtro-estado').on('change', function() {
                self.filters.estado = $(this).val();
                self.filtrarYMostrar();
            });

            // Botón limpiar filtros
            $(document).on('click', '#limpiar-filtros', function() {
                self.limpiarFiltros();
            });
        },

        cargarUsuarios: function() {
            const self = this;
            
            $.ajax({
                url: 'ajax/usuarios.ajax.php',
                type: 'POST',
                data: { accion: 'obtener_usuarios' },
                dataType: 'json',
                beforeSend: function() {
                    self.mostrarLoading();
                },
                success: function(respuesta) {
                    if (respuesta.status === 'success') {
                        self.data = respuesta.data || [];
                        self.filtrarYMostrar();
                    } else {
                        self.mostrarError('Error al cargar usuarios: ' + respuesta.message);
                    }
                },
                error: function() {
                    self.mostrarError('Error de conexión con el servidor');
                }
            });
        },

        filtrarYMostrar: function() {
            const self = this;
            
            this.filteredData = this.data.filter(usuario => {
                // Filtro de búsqueda
                const matchesBuscar = !this.filters.buscar || 
                    (usuario.nombre && usuario.nombre.toLowerCase().includes(this.filters.buscar)) ||
                    (usuario.email && usuario.email.toLowerCase().includes(this.filters.buscar)) ||
                    (usuario.cargo && usuario.cargo.toLowerCase().includes(this.filters.buscar));
                
                // Filtro de rol
                const matchesRol = !this.filters.rol || 
                    (usuario.rol && usuario.rol === this.filters.rol);
                
                // Filtro de estado
                const matchesEstado = this.filters.estado === '' || 
                    (usuario.estado && usuario.estado.toString() === this.filters.estado);
                
                return matchesBuscar && matchesRol && matchesEstado;
            });

            this.mostrarUsuarios();
        },

        mostrarUsuarios: function() {
            const container = $('#usuarios-grid');
            container.empty();

            // Ocultar loading y empty states
            this.ocultarEstados();

            if (this.filteredData.length === 0) {
                this.mostrarEmpty();
                return;
            }

            // Mostrar grid
            container.removeClass('hidden').addClass('grid');

            // Crear cards para cada usuario
            this.filteredData.forEach(usuario => {
                console.log('UsuariosManager: Datos de usuario:', usuario);
                const card = this.crearCard(usuario);
                container.append(card);
            });
        },

        crearCard: function(usuario) {
            const statusClass = usuario.estado == 1 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
            const statusText = usuario.estado == 1 ? 'Activo' : 'Inactivo';
            const statusIcon = usuario.estado == 1 ? '✅' : '❌';

            const roleIcons = {
                'Administrador': '👑',
                'Vendedor': '💼', 
                'Visualizador': '👁️'
            };
            const roleIcon = roleIcons[usuario.rol] || '👤';

            const roleClass = {
                'Administrador': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                'Vendedor': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'Visualizador': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
            };

            // Manejar imagen del usuario
            let avatarContent = '';
            if (usuario.thumbnail && usuario.thumbnail.trim() !== '' && usuario.thumbnail !== 'null') {
                // La ruta en BD es: views/img/usuarios/tenant_X/archivo.jpg
                // Desde pos/views/modules/usuarios.php la ruta correcta es directa: views/img/usuarios/tenant_X/archivo.jpg
                const imagePath = usuario.thumbnail;
                
                console.log('Usuario:', usuario.nombre, 'Thumbnail original:', usuario.thumbnail, 'Ruta usada:', imagePath);
                
                // Si hay imagen, mostrarla con mejor manejo de errores
                avatarContent = `
                    <div class="w-16 h-16 relative">
                        <img class="w-16 h-16 rounded-full object-cover border-2 border-white dark:border-neutral-700 shadow-sm" 
                             src="${imagePath}" 
                             alt="${usuario.nombre}"
                             onload="console.log('Imagen cargada exitosamente:', '${imagePath}')"
                             onerror="console.error('Error cargando imagen:', '${imagePath}'); this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-neutral-700 dark:to-neutral-600 flex items-center justify-center text-gray-400 dark:text-neutral-500 border-2 border-white dark:border-neutral-700 shadow-sm" style="display: none;">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>`;
            } else {
                // Si no hay imagen, mostrar avatar por defecto
                avatarContent = `<div class="w-16 h-16 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-neutral-700 dark:to-neutral-600 flex items-center justify-center text-gray-400 dark:text-neutral-500 border-2 border-white dark:border-neutral-700 shadow-sm">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                    </svg>
                                </div>`;
            }

            return $(`
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 dark:bg-neutral-800 dark:border-neutral-700 hover:-translate-y-1">
                    <!-- Header -->
                    <div class="p-6 pb-4 flex items-start gap-4">
                        ${avatarContent}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 truncate">${usuario.nombre || 'N/A'}</h3>
                            <p class="text-sm text-gray-600 dark:text-neutral-400 truncate">${usuario.cargo || 'N/A'}</p>
                        </div>
                    </div>
                    
                    <!-- Body -->
                    <div class="px-6 pb-4 space-y-3">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Email:</span>
                            <span class="text-gray-900 dark:text-white truncate">${usuario.email || 'N/A'}</span>
                        </div>
                        
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Teléfono:</span>
                            <span class="text-gray-900 dark:text-white truncate">${usuario.telefono || 'N/A'}</span>
                        </div>
                        
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Rol:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${roleClass[usuario.rol] || roleClass['Visualizador']}">
                                ${roleIcon} ${usuario.rol || 'N/A'}
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Estado:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                ${statusIcon} ${statusText}
                            </span>
                        </div>
                        
                        ${usuario.created_at ? `
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-500 dark:text-neutral-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h6a2 2 0 012 2v4m-4 6h.01M15 11v6m0 0v.01M15 17.99h3M12 11h.01M9 11v6m0 0v.01M9 17.99h3m-6-6.99h12a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8a2 2 0 012-2z"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-neutral-400 font-medium">Creado:</span>
                            <span class="text-gray-900 dark:text-white">${new Date(usuario.created_at).toLocaleDateString('es-EC')}</span>
                        </div>
                        ` : ''}
                    </div>
                    
                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-800/50">
                        <div class="flex gap-2 justify-end">
                            <button class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:text-blue-400 dark:bg-blue-900/20 dark:hover:bg-blue-900/30 transition-colors duration-200 btnEditarUsuario" data-id="${usuario.idusuario}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar
                            </button>
                            <button class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 dark:text-red-400 dark:bg-red-900/20 dark:hover:bg-red-900/30 transition-colors duration-200 btnEliminarUsuario" data-id="${usuario.idusuario}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            `);
        },

        mostrarLoading: function() {
            $('#usuarios-loading').removeClass('hidden');
            $('#usuarios-grid').addClass('hidden');
            $('#usuarios-empty').addClass('hidden');
        },

        mostrarEmpty: function() {
            $('#usuarios-loading').addClass('hidden');
            $('#usuarios-grid').addClass('hidden');
            $('#usuarios-empty').removeClass('hidden');
        },

        ocultarEstados: function() {
            $('#usuarios-loading').addClass('hidden');
            $('#usuarios-empty').addClass('hidden');
        },

        mostrarError: function(mensaje) {
            const container = $('#usuarios-grid');
            container.removeClass('hidden').addClass('grid');
            this.ocultarEstados();
            
            container.html(`
                <div class="col-span-full bg-red-50 border border-red-200 rounded-lg p-6 text-center dark:bg-red-900/20 dark:border-red-800">
                    <svg class="mx-auto h-8 w-8 text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-red-800 dark:text-red-400 mb-2">Error al cargar usuarios</h3>
                    <p class="text-red-600 dark:text-red-300">${mensaje}</p>
                </div>
            `);
        },

        limpiarFiltros: function() {
            // Resetear valores de filtros
            this.filters.buscar = '';
            this.filters.rol = '';
            this.filters.estado = '1'; // Mantener activos por defecto
            
            // Resetear elementos del DOM
            $('#buscar-usuario').val('');
            $('#filtro-rol').val('');
            $('#filtro-estado').val('1');
            
            // Aplicar filtros
            this.filtrarYMostrar();
        },

        recargar: function() {
            console.log('UsuariosManager: Recargando usuarios...');
            this.cargarUsuarios();
        },

        resaltarNuevoUsuario: function(email) {
            console.log('UsuariosManager: Buscando y resaltando usuario con email:', email);
            
            setTimeout(() => {
                // Buscar la card del usuario por email
                const cards = document.querySelectorAll('#usuarios-grid .bg-white');
                cards.forEach(card => {
                    const cardText = card.textContent || card.innerText;
                    if (cardText.includes(email)) {
                        console.log('UsuariosManager: Usuario encontrado, aplicando resaltado');
                        
                        // Agregar clase de resaltado
                        card.classList.add('ring-2', 'ring-green-500', 'ring-opacity-50');
                        
                        // Scroll suave hacia la card
                        card.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                        
                        // Remover resaltado después de 3 segundos
                        setTimeout(() => {
                            card.classList.remove('ring-2', 'ring-green-500', 'ring-opacity-50');
                        }, 3000);
                        
                        return;
                    }
                });
            }, 500); // Dar tiempo para que se carguen las cards
        }
    };

    // Inicializar el sistema de usuarios
    UsuariosManager.init();
    
    // Hacer UsuariosManager accesible globalmente para recarga
    window.UsuariosManager = UsuariosManager;

    // Función de test para verificar rutas de imágenes (temporal)
    window.testImagePath = function() {
        // Usar la ruta correcta sin ../
        const testPath = "views/img/usuarios/tenant_2/tenant_2_usuario_1757806599_68c600072469d.jpg";
        const img = new Image();
        img.onload = function() {
            console.log('✅ Test imagen: Ruta accesible:', testPath);
        };
        img.onerror = function() {
            console.error('❌ Test imagen: Ruta no accesible:', testPath);
        };
        img.src = testPath;
    };

    // Sistema de manejo de formulario agregar usuario
    const FormAgregarUsuario = {
        init: function() {
            console.log('FormAgregarUsuario: Inicializando...');
            this.setupDropzone();
            this.setupPasswordToggle();
            this.loadSucursales();
            this.bindEvents();
            console.log('FormAgregarUsuario: Inicialización completa');
        },

        setupDropzone: function() {
            console.log('FormAgregarUsuario: Configurando dropzone...');
            const dropzone = $('#image-dropzone');
            const fileInput = $('#usuario-imagen');
            const preview = $('#image-preview');
            const content = $('#dropzone-content');
            const removeBtn = $('#remove-image');

            console.log('FormAgregarUsuario: Elementos encontrados:', {
                dropzone: dropzone.length,
                fileInput: fileInput.length,
                preview: preview.length,
                content: content.length,
                removeBtn: removeBtn.length
            });

            // Click en dropzone abre selector de archivos
            dropzone.on('click', function() {
                console.log('FormAgregarUsuario: Click en dropzone');
                fileInput.click();
            });

            // Drag & Drop
            dropzone.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('border-blue-400 bg-blue-50');
            });

            dropzone.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('border-blue-400 bg-blue-50');
            });

            dropzone.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('border-blue-400 bg-blue-50');
                
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    FormAgregarUsuario.handleFile(files[0]);
                }
            });

            // Cambio en input file
            fileInput.on('change', function() {
                if (this.files.length > 0) {
                    FormAgregarUsuario.handleFile(this.files[0]);
                }
            });

            // Remover imagen
            removeBtn.on('click', function(e) {
                e.stopPropagation();
                FormAgregarUsuario.removeImage();
            });
        },

        handleFile: function(file) {
            // Validar tipo de archivo
            if (!file.type.match('image.*')) {
                this.showError('Por favor selecciona un archivo de imagen válido');
                return;
            }

            // Validar tamaño (2MB máximo)
            if (file.size > 2 * 1024 * 1024) {
                this.showError('La imagen debe ser menor a 2MB');
                return;
            }

            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result).removeClass('hidden');
                $('#dropzone-content').addClass('hidden');
                $('#remove-image').removeClass('hidden');
            };
            reader.readAsDataURL(file);

            // Actualizar input file con el archivo seleccionado
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.getElementById('usuario-imagen').files = dataTransfer.files;
        },

        removeImage: function() {
            $('#image-preview').addClass('hidden').attr('src', '');
            $('#dropzone-content').removeClass('hidden');
            $('#remove-image').addClass('hidden');
            $('#usuario-imagen').val('');
        },

        setupPasswordToggle: function() {
            $('#toggle-password').on('click', function() {
                const passwordInput = $('#password-usuario');
                const eyeClosed = $('#eye-closed');
                const eyeOpen = $('#eye-open');
                
                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    eyeClosed.addClass('hidden');
                    eyeOpen.removeClass('hidden');
                } else {
                    passwordInput.attr('type', 'password');
                    eyeClosed.removeClass('hidden');
                    eyeOpen.addClass('hidden');
                }
            });
        },

        loadSucursales: function() {
            console.log('FormAgregarUsuario: Cargando sucursales...');
            $.ajax({
                url: 'ajax/usuarios.ajax.php',
                type: 'POST',
                data: { accion: 'obtener_sucursales' },
                dataType: 'json',
                beforeSend: function() {
                    $('#sucursal-usuario').html('<option value="">Cargando sucursales...</option>');
                },
                success: function(response) {
                    console.log('FormAgregarUsuario: Respuesta sucursales:', response);
                    const select = $('#sucursal-usuario');
                    select.empty();
                    
                    if (response.status === 'success' && response.data) {
                        select.append('<option value="">Seleccionar sucursal</option>');
                        response.data.forEach(function(sucursal) {
                            select.append(`<option value="${sucursal.idsucursal}">${sucursal.sri_nombre}</option>`);
                        });
                    } else {
                        select.append('<option value="">No hay sucursales disponibles</option>');
                        console.log('FormAgregarUsuario: No se encontraron sucursales o error en response');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormAgregarUsuario: Error al cargar sucursales:', error);
                    $('#sucursal-usuario').html('<option value="">Error al cargar sucursales</option>');
                }
            });
        },

        bindEvents: function() {
            console.log('FormAgregarUsuario: Configurando eventos...');
            
            // Validación en tiempo real del email con debounce
            let emailTimeout;
            $('#email-usuario').on('input', function() {
                const email = $(this).val().trim();
                console.log('FormAgregarUsuario: Input event email:', email);
                clearTimeout(emailTimeout);
                
                if (email.length > 0) {
                    emailTimeout = setTimeout(() => {
                        console.log('FormAgregarUsuario: Timeout email validation para:', email);
                        FormAgregarUsuario.validateEmail.call(this);
                    }, 800); // Espera 800ms después de dejar de escribir
                } else {
                    FormAgregarUsuario.hideFieldError($(this));
                    FormAgregarUsuario.hideEmailStatus();
                }
            });

            // También validar al salir del campo
            $('#email-usuario').on('blur', function() {
                console.log('FormAgregarUsuario: Blur event email');
                FormAgregarUsuario.validateEmail.call(this);
            });
            
            // Validación de contraseña
            $('#password-usuario').on('input', this.validatePassword);
            
            // Solo números en teléfono
            $('#telefono-usuario').on('input', function() {
                this.value = this.value.replace(/\D/g, '');
            });
            
            console.log('FormAgregarUsuario: Eventos configurados exitosamente');
        },

        validateEmail: function() {
            const email = $(this).val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const field = $(this);
            
            console.log('FormAgregarUsuario: Validando email:', email);
            
            if (email && !emailRegex.test(email)) {
                console.log('FormAgregarUsuario: Email inválido formato');
                FormAgregarUsuario.showFieldError(field, 'Email no válido');
            } else if (email && emailRegex.test(email)) {
                console.log('FormAgregarUsuario: Email válido, verificando duplicados...');
                // Verificar si el email ya existe
                FormAgregarUsuario.checkEmailExists(email, field);
            } else {
                console.log('FormAgregarUsuario: Campo email vacío');
                FormAgregarUsuario.hideFieldError(field);
                FormAgregarUsuario.hideEmailStatus();
            }
        },

        checkEmailExists: function(email, field) {
            console.log('FormAgregarUsuario: Verificando email duplicado para:', email);
            
            // Limpiar estados anteriores
            this.hideEmailStatus();
            
            // Mostrar indicador de verificación con spinner
            const loadingHtml = `
                <div id="email-status" class="mt-2 flex items-center gap-2 p-2 bg-blue-50 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                    <span class="text-xs text-blue-700 dark:text-blue-400">Verificando disponibilidad...</span>
                </div>
            `;
            field.after(loadingHtml);

            $.ajax({
                url: 'ajax/usuarios.ajax.php',
                type: 'POST',
                data: { 
                    accion: 'verificar_email',
                    email: email 
                },
                dataType: 'json',
                beforeSend: function() {
                    console.log('FormAgregarUsuario: Enviando petición de verificación...');
                },
                success: function(response) {
                    console.log('FormAgregarUsuario: Respuesta verificación email:', response);
                    FormAgregarUsuario.hideEmailStatus();
                    
                    if (response.status === 'exists') {
                        FormAgregarUsuario.showFieldError(field, 'Este email ya está registrado');
                        FormAgregarUsuario.showEmailError('❌ Email no disponible - Ya existe un usuario con este correo');
                        // Marcar como inválido para bloquear envío
                        field.attr('data-email-valid', 'false');
                    } else if (response.status === 'available') {
                        FormAgregarUsuario.hideFieldError(field);
                        FormAgregarUsuario.showEmailSuccess('✅ Email disponible - Puedes usar este correo');
                        // Marcar como válido
                        field.attr('data-email-valid', 'true');
                    } else {
                        FormAgregarUsuario.hideFieldError(field);
                        field.removeAttr('data-email-valid');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormAgregarUsuario: Error AJAX verificación email:', {xhr, status, error});
                    console.error('FormAgregarUsuario: Response text:', xhr.responseText);
                    FormAgregarUsuario.hideEmailStatus();
                    FormAgregarUsuario.showEmailError('⚠️ Error al verificar email - Intenta nuevamente');
                    field.removeAttr('data-email-valid');
                }
            });
        },

        validatePassword: function() {
            const password = $(this).val();
            const field = $(this);
            
            if (!password) {
                FormAgregarUsuario.hideFieldError(field);
                FormAgregarUsuario.hidePasswordStrength();
                return true;
            }

            const requirements = {
                minLength: password.length >= 8,
                hasUpper: /[A-Z]/.test(password),
                hasLower: /[a-z]/.test(password),
                hasNumber: /\d/.test(password),
                hasSpecial: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
            };

            const missing = [];
            if (!requirements.minLength) missing.push('mínimo 8 caracteres');
            if (!requirements.hasUpper) missing.push('al menos una mayúscula');
            if (!requirements.hasLower) missing.push('al menos una minúscula');
            if (!requirements.hasNumber) missing.push('al menos un número');
            if (!requirements.hasSpecial) missing.push('al menos un caracter especial (!@#$%^&*...)');

            // Mostrar indicador de fortaleza
            FormAgregarUsuario.showPasswordStrength(requirements);

            if (missing.length > 0) {
                FormAgregarUsuario.showFieldError(field, 'Falta: ' + missing.join(', '));
                return false;
            } else {
                FormAgregarUsuario.hideFieldError(field);
                FormAgregarUsuario.showFieldSuccess(field, 'Contraseña segura');
                return true;
            }
        },

        showFieldError: function(field, message) {
            field.addClass('border-red-300 focus:border-red-500 focus:ring-red-500');
            field.removeClass('border-gray-300 focus:border-blue-500 focus:ring-blue-500');
            
            // Agregar mensaje de error si no existe
            const errorId = field.attr('id') + '-error';
            if (!$('#' + errorId).length) {
                field.after(`<p id="${errorId}" class="mt-1 text-xs text-red-600">${message}</p>`);
            }
        },

        hideFieldError: function(field) {
            field.removeClass('border-red-300 focus:border-red-500 focus:ring-red-500');
            field.addClass('border-gray-300 focus:border-blue-500 focus:ring-blue-500');
            
            // Remover mensaje de error
            const errorId = field.attr('id') + '-error';
            $('#' + errorId).remove();
        },

        showFieldSuccess: function(field, message) {
            field.removeClass('border-red-300 focus:border-red-500 focus:ring-red-500 border-gray-300 focus:border-blue-500 focus:ring-blue-500');
            field.addClass('border-green-300 focus:border-green-500 focus:ring-green-500');
            
            // Remover mensajes anteriores
            const fieldId = field.attr('id');
            $('#' + fieldId + '-error').remove();
            $('#' + fieldId + '-success').remove();
            
            // Agregar mensaje de éxito si no existe
            if (message) {
                field.after(`<p id="${fieldId}-success" class="mt-1 text-xs text-green-600">${message}</p>`);
            }
        },

        showPasswordStrength: function(requirements) {
            const strengthId = 'password-strength';
            $('#' + strengthId).remove();

            const indicators = [
                { key: 'minLength', label: '8+ caracteres', icon: requirements.minLength ? '✓' : '✗' },
                { key: 'hasUpper', label: 'Mayúscula (A-Z)', icon: requirements.hasUpper ? '✓' : '✗' },
                { key: 'hasLower', label: 'Minúscula (a-z)', icon: requirements.hasLower ? '✓' : '✗' },
                { key: 'hasNumber', label: 'Número (0-9)', icon: requirements.hasNumber ? '✓' : '✗' },
                { key: 'hasSpecial', label: 'Especial (!@#$)', icon: requirements.hasSpecial ? '✓' : '✗' }
            ];

            const strengthHtml = `
                <div id="${strengthId}" class="mt-2 p-3 bg-gray-50 dark:bg-neutral-800 rounded-lg border">
                    <p class="text-xs font-medium text-gray-700 dark:text-neutral-300 mb-2">Requisitos de contraseña:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-xs">
                        ${indicators.map(req => `
                            <div class="flex items-center gap-1">
                                <span class="${requirements[req.key] ? 'text-green-600' : 'text-red-500'}">${req.icon}</span>
                                <span class="${requirements[req.key] ? 'text-green-700 dark:text-green-400' : 'text-gray-600 dark:text-neutral-400'}">${req.label}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;

            $('#password-usuario').after(strengthHtml);
        },

        hidePasswordStrength: function() {
            $('#password-strength').remove();
        },

        showEmailSuccess: function(message) {
            this.hideEmailStatus();
            const successHtml = `
                <div id="email-status" class="mt-2 flex items-center gap-2 p-2 bg-green-50 rounded-lg border border-green-200 dark:bg-green-900/20 dark:border-green-800">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-xs text-green-700 dark:text-green-400">${message}</span>
                </div>
            `;
            $('#email-usuario').after(successHtml);
        },

        showEmailError: function(message) {
            this.hideEmailStatus();
            const errorHtml = `
                <div id="email-status" class="mt-2 flex items-center gap-2 p-2 bg-red-50 rounded-lg border border-red-200 dark:bg-red-900/20 dark:border-red-800">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-xs text-red-700 dark:text-red-400">${message}</span>
                </div>
            `;
            $('#email-usuario').after(errorHtml);
        },

        hideEmailStatus: function() {
            $('#email-status').remove();
        },

        showError: function(message) {
            // Crear toast de error
            const toastHtml = `
                <div class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                    <div class="flex p-4">
                        <div class="shrink-0">
                            <svg class="shrink-0 size-4 text-red-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 1 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <p class="text-sm text-gray-700 dark:text-neutral-400">${message}</p>
                        </div>
                    </div>
                </div>
            `;
            showToast(toastHtml);
        },

        validateForm: function() {
            let isValid = true;
            const requiredFields = [
                '#nombre-usuario',
                '#cargo-usuario', 
                '#email-usuario',
                '#telefono-usuario',
                '#rol-usuario',
                '#sucursal-usuario',
                '#password-usuario'
            ];

            // Limpiar errores previos
            $('.text-red-600').remove();
            $('.border-red-300').removeClass('border-red-300 focus:border-red-500 focus:ring-red-500')
                               .addClass('border-gray-300 focus:border-blue-500 focus:ring-blue-500');

            // Validar campos requeridos
            requiredFields.forEach(selector => {
                const field = $(selector);
                const value = field.val().trim();
                
                if (!value) {
                    this.showFieldError(field, 'Este campo es requerido');
                    isValid = false;
                }
            });

            // Validar email
            const email = $('#email-usuario').val().trim();
            const emailField = $('#email-usuario');
            if (email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    this.showFieldError(emailField, 'Email no válido');
                    isValid = false;
                } else {
                    // Verificar si el email está marcado como válido/inválido
                    const emailValidStatus = emailField.attr('data-email-valid');
                    if (emailValidStatus === 'false') {
                        this.showFieldError(emailField, 'Este email ya está registrado');
                        this.showEmailError('❌ No puedes usar este email - Ya existe en el sistema');
                        isValid = false;
                    } else if (emailValidStatus !== 'true') {
                        // Si no se ha verificado el email, forzar verificación
                        this.showFieldError(emailField, 'Verificando disponibilidad del email...');
                        this.checkEmailExists(email, emailField);
                        isValid = false;
                    }
                }
            }

            // Validar contraseña
            const password = $('#password-usuario').val();
            if (password) {
                const minLength = password.length >= 8;
                const hasUpper = /[A-Z]/.test(password);
                const hasLower = /[a-z]/.test(password);
                const hasNumber = /\d/.test(password);
                const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
                
                if (!minLength || !hasUpper || !hasLower || !hasNumber || !hasSpecial) {
                    const missing = [];
                    if (!minLength) missing.push('8+ caracteres');
                    if (!hasUpper) missing.push('mayúscula');
                    if (!hasLower) missing.push('minúscula');
                    if (!hasNumber) missing.push('número');
                    if (!hasSpecial) missing.push('carácter especial');
                    
                    this.showFieldError($('#password-usuario'), 'Falta: ' + missing.join(', '));
                    isValid = false;
                }
            }

            // Validar teléfono
            const telefono = $('#telefono-usuario').val().trim();
            if (telefono && (telefono.length < 10 || telefono.length > 13)) {
                this.showFieldError($('#telefono-usuario'), 'Teléfono debe tener entre 10 y 13 dígitos');
                isValid = false;
            }

            return isValid;
        },

        submitForm: function() {
            const self = this;
            console.log('FormAgregarUsuario: Iniciando envío del formulario');
            
            // Validar formulario
            if (!this.validateForm()) {
                this.showError('Por favor corrige los errores en el formulario');
                return;
            }

            // Preparar datos del formulario
            const formData = new FormData();
            
            // Campos del formulario
            formData.append('accion', 'crear_usuario');
            formData.append('nombre', $('#nombre-usuario').val().trim());
            formData.append('cargo', $('#cargo-usuario').val().trim());
            formData.append('direccion', $('#direccion-usuario').val().trim() || 'Quito');
            formData.append('telefono', $('#telefono-usuario').val().trim());
            formData.append('email', $('#email-usuario').val().trim());
            formData.append('password', $('#password-usuario').val());
            formData.append('rol', $('#rol-usuario').val());
            formData.append('sucursal_id', $('#sucursal-usuario').val());

            // Log de datos del formulario
            console.log('FormAgregarUsuario: Datos del formulario:', {
                nombre: $('#nombre-usuario').val().trim(),
                cargo: $('#cargo-usuario').val().trim(),
                direccion: $('#direccion-usuario').val().trim() || 'Quito',
                telefono: $('#telefono-usuario').val().trim(),
                email: $('#email-usuario').val().trim(),
                rol: $('#rol-usuario').val(),
                sucursal_id: $('#sucursal-usuario').val()
            });

            // Imagen si existe
            const fileInput = document.getElementById('usuario-imagen');
            if (fileInput.files.length > 0) {
                formData.append('imagen', fileInput.files[0]);
                console.log('FormAgregarUsuario: Imagen adjunta:', fileInput.files[0].name, 'Tamaño:', fileInput.files[0].size);
            } else {
                console.log('FormAgregarUsuario: Sin imagen adjunta');
            }

            // Deshabilitar botón durante el envío
            const submitButton = $('.bg-gradient-to-r.from-blue-600');
            const originalText = submitButton.html();
            submitButton.prop('disabled', true).html(`
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Creando...</span>
            `);

            // Enviar datos
            console.log('FormAgregarUsuario: Enviando datos al servidor...');
            $.ajax({
                url: 'ajax/usuarios.ajax.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('FormAgregarUsuario: Respuesta del servidor:', response);
                    if (response.status === 'success') {
                        // Toast de éxito
                        const successToast = `
                            <div class="max-w-xs bg-white border border-gray-200 rounded-xl shadow-lg dark:bg-neutral-800 dark:border-neutral-700" role="alert">
                                <div class="flex p-4">
                                    <div class="shrink-0">
                                        <svg class="shrink-0 size-4 text-teal-500 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                        </svg>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-sm text-gray-700 dark:text-neutral-400">Usuario creado exitosamente</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        showToast(successToast);
                        
                        // Cerrar modal
                        const modal = document.getElementById('modal-agregar-usuario');
                        if (modal && window.HSOverlay) {
                            window.HSOverlay.close(modal);
                        }
                        
                        // Recargar lista de usuarios con delay más largo para asegurar que BD esté actualizada
                        console.log('FormAgregarUsuario: Usuario creado exitosamente - Recargando lista...');
                        
                        // Mostrar indicador de recarga
                        const originalGrid = document.getElementById('usuarios-grid');
                        if (originalGrid) {
                            originalGrid.style.opacity = '0.6';
                            const loadingDiv = document.createElement('div');
                            loadingDiv.id = 'usuarios-reloading';
                            loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-20 flex items-center justify-center z-50';
                            loadingDiv.innerHTML = `
                                <div class="bg-white dark:bg-neutral-800 rounded-lg p-4 flex items-center gap-3 shadow-lg">
                                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-neutral-300">Actualizando lista de usuarios...</span>
                                </div>
                            `;
                            document.body.appendChild(loadingDiv);
                        }
                        
                        setTimeout(() => {
                            console.log('FormAgregarUsuario: Ejecutando recarga de usuarios...');
                            if (window.UsuariosManager) {
                                const emailCreado = $('#email-usuario').val().trim();
                                window.UsuariosManager.recargar();
                                
                                // Remover indicador de recarga y resaltar nuevo usuario
                                setTimeout(() => {
                                    const loadingDiv = document.getElementById('usuarios-reloading');
                                    if (loadingDiv) {
                                        loadingDiv.remove();
                                    }
                                    if (originalGrid) {
                                        originalGrid.style.opacity = '1';
                                    }
                                    
                                    // Resaltar el usuario recién creado
                                    if (emailCreado) {
                                        window.UsuariosManager.resaltarNuevoUsuario(emailCreado);
                                    }
                                }, 800);
                            } else {
                                console.error('FormAgregarUsuario: window.UsuariosManager no está disponible');
                            }
                        }, 1500);
                        
                    } else {
                        console.error('FormAgregarUsuario: Error del servidor:', response.message);
                        self.showError(response.message || 'Error al crear el usuario');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('FormAgregarUsuario: Error en AJAX:', {xhr, status, error});
                    console.error('FormAgregarUsuario: Response text:', xhr.responseText);
                    self.showError('Error de conexión con el servidor');
                },
                complete: function() {
                    // Restaurar botón
                    submitButton.prop('disabled', false).html(originalText);
                }
            });
        },

        resetForm: function() {
            // Reset del formulario
            $('#formAgregarUsuario')[0].reset();
            
            // Reset de la imagen
            this.removeImage();
            
            // Limpiar todos los mensajes de error y éxito
            $('.text-red-600, .text-green-600').remove();
            $('#password-strength').remove();
            $('#email-checking').remove();
            this.hideEmailStatus();
            
            // Reset de estilos de campos
            $('.border-red-300, .border-green-300').removeClass('border-red-300 focus:border-red-500 focus:ring-red-500 border-green-300 focus:border-green-500 focus:ring-green-500')
                                                   .addClass('border-gray-300 focus:border-blue-500 focus:ring-blue-500');
            
            // Reset del estado de validación de email
            $('#email-usuario').removeAttr('data-email-valid');
            
            // Reset del toggle de contraseña
            $('#password-usuario').attr('type', 'password');
            $('#eye-closed').removeClass('hidden');
            $('#eye-open').addClass('hidden');
            
            // Recargar sucursales
            this.loadSucursales();
        }
    };

    // Inicializar FormAgregarUsuario
    FormAgregarUsuario.init();

    // Event handler para el botón "Crear Usuario"
    $(document).on('click', '.bg-gradient-to-r.from-blue-600', function(e) {
        // Solo procesar si está dentro del modal agregar usuario
        if ($(this).closest('#modal-agregar-usuario').length) {
            e.preventDefault();
            FormAgregarUsuario.submitForm();
        }
    });

    // Reset del formulario cuando se cierre el modal
    $(document).on('hidden.hs.overlay', '#modal-agregar-usuario', function() {
        FormAgregarUsuario.resetForm();
    });

    // Inicializar FormAgregarUsuario cuando se abra el modal
    $(document).on('shown.hs.overlay', '#modal-agregar-usuario', function() {
        FormAgregarUsuario.init();
    });

    // Event handlers para los modales
    // Botón Editar Usuario en las cards
    $(document).on('click', '.btnEditarUsuario', function() {
        const userId = $(this).data('id');
        console.log('Abriendo modal editar para usuario ID:', userId);
        
        // Actualizar el ID en el modal
        $('#usuario-id-display').text(userId);
        
        // Abrir modal usando Preline UI
        const modal = document.getElementById('modal-editar-usuario');
        if (modal && window.HSOverlay) {
            window.HSOverlay.open(modal);
        }
    });
    
    // Exponer funci�n logout globalmente
    window.logout = logout;
    
    // Verificar sesi�n si no estamos en la p�gina de login
    if (!window.location.href.includes('login')) {
        verificarSesion();
    }
});