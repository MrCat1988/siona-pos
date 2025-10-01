<?php

class UsuariosController {

    /**
     * Función auxiliar para eliminar archivo de imagen del sistema
     * @param string $rutaImagen Ruta relativa de la imagen (ej: "views/img/usuarios/tenant_1/imagen.jpg")
     * @param string $contexto Contexto para logs (ej: "EditarUsuario", "EliminarUsuario")
     * @return bool True si se eliminó exitosamente o no existía, False si hubo error
     */
    private static function eliminarImagenUsuario($rutaImagen, $contexto = "") {
        if (empty($rutaImagen)) {
            return true; // No hay imagen que eliminar
        }

        $rutaCompleta = __DIR__ . "/../" . $rutaImagen;

        if (file_exists($rutaCompleta)) {
            if (unlink($rutaCompleta)) {
                error_log("$contexto - Imagen eliminada exitosamente: " . $rutaCompleta);
                return true;
            } else {
                error_log("$contexto - ERROR: No se pudo eliminar imagen: " . $rutaCompleta);
                return false;
            }
        } else {
            error_log("$contexto - Imagen no existe en el sistema de archivos: " . $rutaCompleta);
            return true; // Considerar éxito si el archivo no existe
        }
    }

    public static function login() {
        if ($_POST) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $ip = $_SERVER['REMOTE_ADDR'];
            
            // 1. Validar CSRF token
            if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                echo json_encode(array("status" => "error", "message" => "Token de seguridad inválido"));
                return;
            }
            
            // 2. Verificar rate limiting (solo si SecurityModel existe)
            if (class_exists('SecurityModel')) {
                $rateCheck = SecurityModel::checkRateLimit($ip, $email);
                if (!$rateCheck['allowed']) {
                    $minutes = ceil((strtotime($rateCheck['blocked_until']) - time()) / 60);
                    echo json_encode(array(
                        "status" => "error", 
                        "message" => "Demasiados intentos fallidos. Inténtalo en $minutes minutos."
                    ));
                    return;
                }
            }
            
            // Detectar si es hash desde cliente o password normal
            if (isset($_POST['is_hashed']) && $_POST['is_hashed'] === 'true') {
                // Es un hash del cliente
                $respuesta = UsuariosModel::loginUsuarioHashed($email, $password);
            } else {
                // Es password normal (fallback)
                $respuesta = UsuariosModel::loginUsuario($email, $password);
            }
            
            if ($respuesta) {
                // 3. Login exitoso: regenerar session_id y limpiar rate limiting
                session_regenerate_id(true);
                if (class_exists('SecurityModel')) {
                    SecurityModel::recordLoginAttempt($ip, $email, true);
                }
                $_SESSION['usuario_id'] = $respuesta['idusuario'];
                $_SESSION['usuario_nombre'] = $respuesta['nombre'];
                $_SESSION['usuario_email'] = $respuesta['email'];
                $_SESSION['usuario_rol'] = $respuesta['rol'];
                $_SESSION['usuario_cargo'] = $respuesta['cargo'];
                $_SESSION['usuario_thumbnail'] = $respuesta['thumbnail'];
                $_SESSION['sucursal_id'] = $respuesta['idsucursal'];
                $_SESSION['sucursal_nombre'] = $respuesta['sucursal_nombre'];
                $_SESSION['tenant_id'] = $respuesta['idempresa_tenant'];
                $_SESSION['razon_social'] = $respuesta['razon_social'];
                $_SESSION['nombre_comercial'] = $respuesta['nombre_comercial'];
                $_SESSION['login_status'] = true;
                $_SESSION['login_time'] = time();
                $_SESSION['user_ip'] = $ip;
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                
                echo json_encode(array("status" => "success", "message" => "Login exitoso"));
            } else {
                // 4. Login fallido: registrar intento
                if (class_exists('SecurityModel')) {
                    SecurityModel::recordLoginAttempt($ip, $email, false);
                }
                echo json_encode(array("status" => "error", "message" => "Credenciales incorrectas"));
            }
        }
    }
    
    public static function logout() {
        session_start();
        session_destroy();
        echo json_encode(array("status" => "success", "message" => "Sesión cerrada"));
    }
    
    public static function verificarSesion() {
        session_start();
        if (isset($_SESSION['login_status']) && $_SESSION['login_status'] == true) {
            echo json_encode(array("status" => "active", "usuario" => $_SESSION['usuario_nombre']));
        } else {
            echo json_encode(array("status" => "inactive"));
        }
    }
    
    public static function obtenerUsuarios() {
        try {
            if (!isset($_SESSION['tenant_id'])) {
                echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
                return;
            }

            // Obtener parámetros de paginación y filtros
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 6;
            $estado = isset($_POST['estado']) && $_POST['estado'] !== '' ? (int)$_POST['estado'] : null;
            $incluir_eliminados = isset($_POST['incluir_eliminados']) && $_POST['incluir_eliminados'] === 'true';

            // Validar parámetros
            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 50) $limit = 6; // Máximo 50 por página

            $resultado = UsuariosModel::obtenerUsuarios($_SESSION['tenant_id'], $page, $limit, $estado, $incluir_eliminados);

            if ($resultado === false || !isset($resultado['usuarios'])) {
                echo json_encode(array("status" => "error", "message" => "Error al consultar base de datos"));
                return;
            }

            echo json_encode(array("status" => "success", "data" => $resultado));

        } catch (Exception $e) {
            error_log("Error en obtenerUsuarios controller: " . $e->getMessage());
            echo json_encode(array("status" => "error", "message" => "Error interno del servidor"));
        }
    }
    
    public static function crearUsuario() {
        // Iniciar output buffering para evitar problemas con JSON
        ob_start();
        
        session_start();
        if (!isset($_SESSION['tenant_id'])) {
            ob_clean();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            ob_end_flush();
            exit();
        }
        
        // Debug información recibida (comentado para evitar problemas de output)
        if ($_POST) {
            // Validar CSRF token si está disponible
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }
            
            // Manejar subida de imagen con estructura multitenant
            $thumbnail = null;
            
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                // Crear estructura de directorios por tenant (ruta relativa desde el AJAX)
                $uploadDir = "../views/img/usuarios/tenant_{$_SESSION['tenant_id']}/";
                $absoluteUploadDir = __DIR__ . "/../views/img/usuarios/tenant_{$_SESSION['tenant_id']}/";
                error_log("Directorio de upload relativo: " . $uploadDir);
                error_log("Directorio de upload absoluto: " . $absoluteUploadDir);
                
                if (!file_exists($absoluteUploadDir)) {
                    $created = mkdir($absoluteUploadDir, 0755, true);
                    error_log("Directorio creado: " . ($created ? 'Sí' : 'No'));
                    if (!$created) {
                        error_log("ERROR: No se pudo crear el directorio de imágenes");
                        echo json_encode(array("status" => "error", "message" => "Error al crear directorio de imágenes"));
                        return;
                    }
                }
                
                $fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $maxFileSize = 2 * 1024 * 1024; // 2MB
                error_log("Extensión del archivo: " . $fileExtension);
                error_log("Tamaño del archivo: " . $_FILES['imagen']['size'] . " bytes");
                
                if (in_array($fileExtension, $allowedExtensions) && $_FILES['imagen']['size'] <= $maxFileSize) {
                    // Generar nombre único con prefijo de tenant
                    $filename = "tenant_{$_SESSION['tenant_id']}_usuario_" . time() . '_' . uniqid() . '.' . $fileExtension;
                    $targetPath = $absoluteUploadDir . $filename;
                    error_log("Ruta objetivo: " . $targetPath);
                    
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
                        // Guardar ruta relativa para mostrar desde las vistas (sin "../")
                        $thumbnail = "views/img/usuarios/tenant_{$_SESSION['tenant_id']}/" . $filename;
                        error_log("Imagen guardada exitosamente: " . $filename);
                        error_log("Ruta thumbnail guardada: " . $thumbnail);
                        error_log("Target path usado: " . $targetPath);
                    } else {
                        error_log("Error al mover archivo de imagen");
                        echo json_encode(array("status" => "error", "message" => "Error al guardar la imagen"));
                        return;
                    }
                } else {
                    $errors = [];
                    if (!in_array($fileExtension, $allowedExtensions)) {
                        $errors[] = "Tipo de archivo no permitido. Solo: " . implode(', ', $allowedExtensions);
                        error_log("Extensión no permitida: " . $fileExtension);
                    }
                    if ($_FILES['imagen']['size'] > $maxFileSize) {
                        $errors[] = "Archivo demasiado grande. Máximo 2MB";
                        error_log("Archivo demasiado grande: " . $_FILES['imagen']['size'] . " bytes (máximo: $maxFileSize bytes)");
                    }
                    echo json_encode(array("status" => "error", "message" => implode('. ', $errors)));
                    return;
                }
            } else {
                if (isset($_FILES['imagen'])) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP',
                        UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario',
                        UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                        UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                        UPLOAD_ERR_NO_TMP_DIR => 'Falta directorio temporal',
                        UPLOAD_ERR_CANT_WRITE => 'Error al escribir archivo en disco',
                        UPLOAD_ERR_EXTENSION => 'Subida detenida por extensión PHP'
                    ];
                    
                    $errorCode = $_FILES['imagen']['error'];
                    $errorMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : 'Error desconocido';
                    error_log("Error en archivo de imagen: Código $errorCode - $errorMessage");
                } else {
                    error_log("No se recibió archivo de imagen en la petición");
                }
            }
            
            // Validar que el email no esté duplicado
            $emailExiste = UsuariosModel::verificarEmailExiste($_POST['email'], $_SESSION['tenant_id']);
            if ($emailExiste) {
                echo json_encode(array("status" => "error", "message" => "Este email ya está registrado en el sistema"));
                return;
            }

            // Validar que la sucursal pertenezca al tenant del usuario actual
            $validacionSucursal = UsuariosModel::validarSucursalTenant($_POST['sucursal_id'], $_SESSION['tenant_id']);
            error_log("Validación sucursal - Sucursal ID: " . $_POST['sucursal_id'] . ", Tenant ID: " . $_SESSION['tenant_id'] . ", Válida: " . ($validacionSucursal ? 'Sí' : 'No'));

            if (!$validacionSucursal) {
                // Mostrar sucursales disponibles para debugging
                $sucursalesDisponibles = UsuariosModel::obtenerSucursalesPorTenant($_SESSION['tenant_id']);
                error_log("Sucursales disponibles para tenant " . $_SESSION['tenant_id'] . ": " . json_encode($sucursalesDisponibles));
                echo json_encode(array("status" => "error", "message" => "Sucursal no válida para este tenant. Revisar logs."));
                return;
            }
            
            // Manejar contraseña hasheada o texto plano
            $password = $_POST['password'];
            $is_hashed = isset($_POST['is_hashed']) && $_POST['is_hashed'] === 'true';

            if ($is_hashed) {
                // Si viene hasheada del cliente, necesitamos guardarla de forma compatible con loginUsuarioHashed
                error_log("crearUsuario - Recibida contraseña hasheada del cliente");
                // Para contraseñas hasheadas, usar un método de almacenamiento compatible
                $password_to_store = $password; // El hash del cliente se guardará directamente
            } else {
                // Si viene en texto plano, usar el método normal
                error_log("crearUsuario - Recibida contraseña en texto plano");
                $password_to_store = $password;
            }

            $datos = array(
                "nombre" => $_POST['nombre'],
                "cargo" => $_POST['cargo'],
                "direccion" => $_POST['direccion'] ?? '',
                "telefono" => $_POST['telefono'],
                "email" => $_POST['email'],
                "password" => $password_to_store,
                "rol" => $_POST['rol'],
                "sucursal_id" => $_POST['sucursal_id'],
                "thumbnail" => $thumbnail,
                "tenant_id" => $_SESSION['tenant_id'],
                "is_hashed" => $is_hashed
            );
            
            // Verificar antes de intentar crear
            // error_log("Intentando crear usuario con datos: " . json_encode($datos));
            
            // Verificar email duplicado antes de crear
            if (UsuariosModel::verificarEmailExiste($_POST['email'], $_SESSION['tenant_id'])) {
                // error_log("Intento de crear usuario con email duplicado: " . $_POST['email']);
                ob_clean();
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array("status" => "error", "message" => "Ya existe un usuario con este email"), JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit();
            }
            
            $respuesta = UsuariosModel::crearUsuario($datos);
            
            if ($respuesta) {
                // error_log("Usuario creado exitosamente - Enviando respuesta de éxito");
                // Limpiar cualquier output previo
                ob_clean();
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array("status" => "success", "message" => "Usuario creado exitosamente"), JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit();
            } else {
                // Log detallado del error
                // error_log("FALLO al crear usuario - Datos enviados: " . json_encode($datos));
                // error_log("FALLO al crear usuario - Session tenant_id: " . $_SESSION['tenant_id']);
                ob_clean();
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(array("status" => "error", "message" => "Error al crear usuario. Verificar logs del servidor."), JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit();
            }
        }
    }
    
    public static function obtenerUsuario() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        error_log("obtenerUsuario - Iniciando función");
        error_log("obtenerUsuario - Session tenant_id: " . ($_SESSION['tenant_id'] ?? 'NO_SET'));
        error_log("obtenerUsuario - POST data: " . json_encode($_POST));
        
        if (!isset($_SESSION['tenant_id'])) {
            error_log("obtenerUsuario - Error: Sesión no válida");
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }
        
        if (isset($_POST['idusuario'])) {
            $idusuario = $_POST['idusuario'];
            error_log("obtenerUsuario - Buscando usuario ID: $idusuario para tenant: " . $_SESSION['tenant_id']);
            
            $usuario = UsuariosModel::obtenerUsuario($idusuario, $_SESSION['tenant_id']);
            error_log("obtenerUsuario - Resultado del modelo: " . json_encode($usuario));
            
            if ($usuario) {
                error_log("obtenerUsuario - Usuario encontrado, enviando respuesta exitosa");
                echo json_encode(array("status" => "success", "data" => $usuario));
            } else {
                error_log("obtenerUsuario - Usuario no encontrado en la base de datos");
                echo json_encode(array("status" => "error", "message" => "Usuario no encontrado"));
            }
        } else {
            error_log("obtenerUsuario - Error: No se recibió idusuario en POST");
            echo json_encode(array("status" => "error", "message" => "ID de usuario requerido"));
        }
    }
    
    public static function editarUsuario() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if ($_POST) {
            // Validar CSRF token
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }
            // Verificar que el usuario no se desactive a sí mismo
            if ($_POST['idusuario'] == $_SESSION['usuario_id'] && $_POST['estado'] == '0') {
                echo json_encode(array("status" => "error", "message" => "No puedes desactivar tu propio usuario"));
                return;
            }

            // Verificar email duplicado antes de editar (excluyendo el usuario actual)
            if (UsuariosModel::verificarEmailExiste($_POST['email'], $_SESSION['tenant_id'], $_POST['idusuario'])) {
                echo json_encode(array("status" => "error", "message" => "Ya existe otro usuario con este email"));
                return;
            }

            // Manejar subida de imagen con estructura multitenant (solo si se envía nueva imagen)
            $thumbnail = null;
            $imagenAnterior = null;
            error_log("EditarUsuario - Procesando imagen - FILES: " . json_encode($_FILES));

            // Si se va a subir nueva imagen, obtener la imagen anterior para eliminarla después
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                // Obtener la imagen actual del usuario antes de la actualización
                $usuarioActual = UsuariosModel::obtenerUsuario($_POST['idusuario'], $_SESSION['tenant_id']);
                if ($usuarioActual && !empty($usuarioActual['thumbnail'])) {
                    $imagenAnterior = $usuarioActual['thumbnail'];
                    error_log("EditarUsuario - Imagen anterior encontrada para eliminar: " . $imagenAnterior);
                }
                // Crear estructura de directorios por tenant (ruta relativa desde el AJAX)
                $uploadDir = "../views/img/usuarios/tenant_{$_SESSION['tenant_id']}/";
                $absoluteUploadDir = __DIR__ . "/../views/img/usuarios/tenant_{$_SESSION['tenant_id']}/";
                error_log("EditarUsuario - Directorio de upload relativo: " . $uploadDir);
                error_log("EditarUsuario - Directorio de upload absoluto: " . $absoluteUploadDir);

                if (!file_exists($absoluteUploadDir)) {
                    $created = mkdir($absoluteUploadDir, 0755, true);
                    error_log("EditarUsuario - Directorio creado: " . ($created ? 'Sí' : 'No'));
                    if (!$created) {
                        echo json_encode(array("status" => "error", "message" => "Error al crear directorio de imágenes"));
                        return;
                    }
                }

                $fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                $maxFileSize = 2 * 1024 * 1024; // 2MB
                error_log("EditarUsuario - Extensión del archivo: " . $fileExtension);
                error_log("EditarUsuario - Tamaño del archivo: " . $_FILES['imagen']['size'] . " bytes");

                if (in_array($fileExtension, $allowedExtensions) && $_FILES['imagen']['size'] <= $maxFileSize) {
                    // Generar nombre único con prefijo de tenant
                    $filename = "tenant_{$_SESSION['tenant_id']}_usuario_" . time() . '_' . uniqid() . '.' . $fileExtension;
                    $targetPath = $absoluteUploadDir . $filename;
                    error_log("EditarUsuario - Ruta objetivo: " . $targetPath);

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
                        // Guardar ruta relativa para mostrar desde las vistas (sin "../")
                        $thumbnail = "views/img/usuarios/tenant_{$_SESSION['tenant_id']}/" . $filename;
                        error_log("EditarUsuario - Imagen guardada exitosamente: " . $filename);
                        error_log("EditarUsuario - Ruta thumbnail guardada: " . $thumbnail);

                        // Eliminar imagen anterior si existe y es diferente
                        if ($imagenAnterior && $imagenAnterior !== $thumbnail) {
                            self::eliminarImagenUsuario($imagenAnterior, "EditarUsuario");
                        }
                    } else {
                        error_log("EditarUsuario - Error al mover archivo de imagen");
                        echo json_encode(array("status" => "error", "message" => "Error al guardar la imagen"));
                        return;
                    }
                } else {
                    $errors = [];
                    if (!in_array($fileExtension, $allowedExtensions)) {
                        $errors[] = "Tipo de archivo no permitido. Solo: " . implode(', ', $allowedExtensions);
                        error_log("EditarUsuario - Extensión no permitida: " . $fileExtension);
                    }
                    if ($_FILES['imagen']['size'] > $maxFileSize) {
                        $errors[] = "Archivo demasiado grande. Máximo 2MB";
                        error_log("EditarUsuario - Archivo demasiado grande: " . $_FILES['imagen']['size'] . " bytes (máximo: $maxFileSize bytes)");
                    }
                    echo json_encode(array("status" => "error", "message" => implode('. ', $errors)));
                    return;
                }
            } else {
                if (isset($_FILES['imagen'])) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP',
                        UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo permitido por el formulario',
                        UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                        UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                        UPLOAD_ERR_NO_TMP_DIR => 'Falta directorio temporal',
                        UPLOAD_ERR_CANT_WRITE => 'Error al escribir archivo en disco',
                        UPLOAD_ERR_EXTENSION => 'Subida detenida por extensión PHP'
                    ];

                    $errorCode = $_FILES['imagen']['error'];
                    if ($errorCode !== UPLOAD_ERR_NO_FILE) {
                        $errorMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : 'Error desconocido';
                        error_log("EditarUsuario - Error en archivo de imagen: Código $errorCode - $errorMessage");
                    }
                } else {
                    error_log("EditarUsuario - No se recibió archivo de imagen en la petición");
                }
            }

            $datos = array(
                "idusuario" => $_POST['idusuario'],
                "nombre" => $_POST['nombre'],
                "cargo" => $_POST['cargo'],
                "direccion" => $_POST['direccion'] ?? '',
                "telefono" => $_POST['telefono'],
                "email" => $_POST['email'],
                "rol" => $_POST['rol'],
                "estado" => $_POST['estado'],
                "thumbnail" => $thumbnail // Solo se actualizará si se subió nueva imagen
            );

            // Agregar contraseña solo si se proporcionó una nueva
            if (isset($_POST['password']) && !empty(trim($_POST['password']))) {
                $datos['password'] = $_POST['password']; // Pasar texto plano, el modelo se encarga del hash
                error_log("EditarUsuario - Nueva contraseña proporcionada, será actualizada");
            } else {
                error_log("EditarUsuario - Sin nueva contraseña, manteniendo la actual");
            }
            
            $respuesta = UsuariosModel::editarUsuario($datos, $_SESSION['tenant_id']);
            
            if ($respuesta) {
                echo json_encode(array("status" => "success", "message" => "Usuario actualizado exitosamente"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al actualizar usuario"));
            }
        }
    }
    
    public static function eliminarUsuario() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (isset($_POST['idusuario'])) {
            // Validar CSRF token
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }

            $idusuario = $_POST['idusuario'];

            // No permitir eliminar al propio usuario
            if ($idusuario == $_SESSION['usuario_id']) {
                echo json_encode(array("status" => "error", "message" => "No puedes eliminar tu propio usuario"));
                return;
            }

            // Verificar si el usuario tiene dependencias (facturas asociadas)
            $dependencias = UsuariosModel::verificarDependenciasUsuario($idusuario, $_SESSION['tenant_id']);

            // Obtener datos del usuario antes de eliminarlo para limpiar su imagen
            $usuarioAEliminar = UsuariosModel::obtenerUsuario($idusuario, $_SESSION['tenant_id']);
            $imagenAEliminar = null;
            if ($usuarioAEliminar && !empty($usuarioAEliminar['thumbnail'])) {
                $imagenAEliminar = $usuarioAEliminar['thumbnail'];
                error_log("EliminarUsuario - Imagen a eliminar: " . $imagenAEliminar);
            }

            // Proceder con la eliminación (soft delete)
            $respuesta = UsuariosModel::eliminarUsuario($idusuario, $_SESSION['tenant_id']);

            if ($respuesta) {
                // Eliminar imagen del usuario si existe
                if ($imagenAEliminar) {
                    self::eliminarImagenUsuario($imagenAEliminar, "EliminarUsuario");
                }

                // Mensaje dependiendo de si tiene dependencias o no
                if ($dependencias['tiene_dependencias']) {
                    $mensaje = "Usuario desactivado exitosamente. El usuario tenía " . $dependencias['facturas'] . " factura(s) asociada(s), por lo que se cambió su estado a inactivo para fines de auditoría.";
                } else {
                    $mensaje = "Usuario eliminado exitosamente.";
                }

                echo json_encode(array(
                    "status" => "success",
                    "message" => $mensaje,
                    "tipo_eliminacion" => $dependencias['tiene_dependencias'] ? 'desactivacion' : 'eliminacion',
                    "dependencias" => $dependencias
                ));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al eliminar usuario"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "ID de usuario requerido"));
        }
    }
    
    public static function obtenerSucursales() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("obtenerSucursales - Session tenant_id: " . ($_SESSION['tenant_id'] ?? 'NO_SET'));
        
        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }
        
        $sucursales = UsuariosModel::obtenerSucursalesPorTenant($_SESSION['tenant_id']);
        error_log("obtenerSucursales - Sucursales encontradas: " . json_encode($sucursales));
        echo json_encode(array("status" => "success", "data" => $sucursales));
    }
    
    public static function verificarEmailDisponible() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }
        
        if (!isset($_POST['email'])) {
            echo json_encode(array("status" => "error", "message" => "Email requerido"));
            return;
        }
        
        $email = $_POST['email'];
        $excluir_usuario_id = isset($_POST['excluir_usuario_id']) ? $_POST['excluir_usuario_id'] : null;
        
        error_log("verificarEmailDisponible - Email: $email, Tenant: " . $_SESSION['tenant_id']);
        
        // Validación básica de formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            error_log("verificarEmailDisponible - Email inválido: $email");
            echo json_encode(array("status" => "error", "message" => "Formato de email inválido"));
            return;
        }
        
        $existe = UsuariosModel::verificarEmailExiste($email, $_SESSION['tenant_id'], $excluir_usuario_id);
        error_log("verificarEmailDisponible - Email existe: " . ($existe ? 'SÍ' : 'NO'));
        
        if ($existe) {
            echo json_encode(array("status" => "exists", "message" => "Este email ya está en uso"));
        } else {
            echo json_encode(array("status" => "available", "message" => "Email disponible"));
        }
    }
}