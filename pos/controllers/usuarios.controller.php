<?php

class UsuariosController {
    
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
        session_start();
        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }
        
        $usuarios = UsuariosModel::obtenerUsuarios($_SESSION['tenant_id']);
        echo json_encode(array("status" => "success", "data" => $usuarios));
    }
    
    public static function crearUsuario() {
        session_start();
        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }
        
        if ($_POST) {
            $datos = array(
                "nombre" => $_POST['nombre'],
                "cargo" => $_POST['cargo'],
                "direccion" => $_POST['direccion'] ?? 'Quito',
                "telefono" => $_POST['telefono'],
                "email" => $_POST['email'],
                "password" => $_POST['password'],
                "rol" => $_POST['rol'],
                "sucursal_id" => $_POST['sucursal_id']
            );
            
            $respuesta = UsuariosModel::crearUsuario($datos);
            
            if ($respuesta) {
                echo json_encode(array("status" => "success", "message" => "Usuario creado exitosamente"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al crear usuario"));
            }
        }
    }
}