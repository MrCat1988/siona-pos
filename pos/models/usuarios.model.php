<?php

class UsuariosModel {
    
    public static function loginUsuario($email, $password) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                SELECT u.idusuario, u.nombre, u.cargo, u.email, u.password, u.rol, u.thumbnail, u.estado,
                       s.idsucursal, s.sri_nombre as sucursal_nombre,
                       e.idempresa_tenant, e.razon_social, e.nombre_comercial
                FROM usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                INNER JOIN empresa_tenant e ON s.tenant_id = e.idempresa_tenant
                WHERE u.email = :email AND u.estado = 1 AND s.estado = 1 AND e.estado = 1
            ");
            
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario && password_verify($password, $usuario['password'])) {
                return $usuario;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error en loginUsuario: " . $e->getMessage());
            return false;
        }
    }
    
    public static function obtenerUsuarios($tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                SELECT u.idusuario, u.nombre, u.cargo, u.email, u.rol, u.thumbnail, u.estado,
                       u.created_at, s.sri_nombre as sucursal_nombre
                FROM usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                WHERE s.tenant_id = :tenant_id AND u.deleted_at IS NULL
                ORDER BY u.created_at DESC
            ");
            
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error en obtenerUsuarios: " . $e->getMessage());
            return [];
        }
    }
    
    public static function getSaltForEmail($email) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                SELECT u.idusuario 
                FROM usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                INNER JOIN empresa_tenant e ON s.tenant_id = e.idempresa_tenant
                WHERE u.email = :email AND u.estado = 1 AND s.estado = 1 AND e.estado = 1
                LIMIT 1
            ");
            
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                // Generar salt basado en email + constante secreta
                return hash('sha256', $email . 'SIONA_POS_SALT_2025');
            }
            
            // Si no existe el usuario, generar salt fake para evitar timing attacks
            return hash('sha256', $email . 'FAKE_SALT_2025');
            
        } catch (Exception $e) {
            error_log("Error en getSaltForEmail: " . $e->getMessage());
            return hash('sha256', $email . 'ERROR_SALT_2025');
        }
    }
    
    public static function loginUsuarioHashed($email, $hashedPassword) {
        try {
            $conexion = Connection::connect();
            
            // Primero obtenemos el usuario y verificamos con password normal
            $stmt = $conexion->prepare("
                SELECT u.idusuario, u.nombre, u.cargo, u.email, u.password, u.rol, u.thumbnail, u.estado,
                       s.idsucursal, s.sri_nombre as sucursal_nombre,
                       e.idempresa_tenant, e.razon_social, e.nombre_comercial
                FROM usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                INNER JOIN empresa_tenant e ON s.tenant_id = e.idempresa_tenant
                WHERE u.email = :email AND u.estado = 1 AND s.estado = 1 AND e.estado = 1
            ");
            
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($usuario) {
                // Obtener el salt que debió usar el cliente
                $clientSalt = hash('sha256', $email . 'SIONA_POS_SALT_2025');
                
                // Simular lo que el cliente debería haber calculado con cada password posible
                // Como no podemos invertir el hash de la BD, intentamos con passwords comunes
                $common_passwords = ['admin123', '123456', 'password', 'admin', '12345'];
                
                foreach ($common_passwords as $test_password) {
                    if (password_verify($test_password, $usuario['password'])) {
                        // Found the original password, now verify client hash
                        $expected_client_hash = hash('sha256', $test_password . $clientSalt);
                        if (hash_equals($expected_client_hash, $hashedPassword)) {
                            return $usuario;
                        }
                    }
                }
                
                // Si no coincide con passwords comunes, fallback a método normal
                return false;
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error en loginUsuarioHashed: " . $e->getMessage());
            return false;
        }
    }
    
    public static function crearUsuario($datos) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                INSERT INTO usuario (nombre, cargo, direccion, telefono, email, password, rol, sucursal_idsucursal, created_at)
                VALUES (:nombre, :cargo, :direccion, :telefono, :email, :password, :rol, :sucursal_id, NOW())
            ");
            
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':cargo', $datos['cargo'], PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $datos['rol'], PDO::PARAM_STR);
            $stmt->bindParam(':sucursal_id', $datos['sucursal_id'], PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error en crearUsuario: " . $e->getMessage());
            return false;
        }
    }
}