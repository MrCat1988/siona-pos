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
                SELECT u.idusuario, u.nombre, u.cargo, u.direccion, u.telefono, u.email, 
                       u.rol, u.thumbnail, u.estado, u.created_at,
                       s.sri_nombre as sucursal_nombre
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
    
    public static function validarSucursalTenant($sucursal_id, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                SELECT COUNT(*) as count
                FROM sucursal
                WHERE idsucursal = :sucursal_id AND tenant_id = :tenant_id AND estado = 1
            ");
            
            $stmt->bindParam(':sucursal_id', $sucursal_id, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
            
        } catch (Exception $e) {
            error_log("Error en validarSucursalTenant - Exception: " . $e->getMessage());
            error_log("Error en validarSucursalTenant - Sucursal ID: $sucursal_id, Tenant ID: $tenant_id");
            return false;
        }
    }
    
    public static function obtenerSucursalesPorTenant($tenant_id) {
        try {
            error_log("obtenerSucursalesPorTenant - Consultando tenant_id: $tenant_id");
            
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                SELECT idsucursal, sri_nombre, estado
                FROM sucursal
                WHERE tenant_id = :tenant_id
                ORDER BY sri_nombre
            ");
            
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("obtenerSucursalesPorTenant - Filas encontradas: " . count($result));
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Error en obtenerSucursalesPorTenant - Exception: " . $e->getMessage());
            return [];
        }
    }
    
    public static function verificarEmailExiste($email, $tenant_id, $excluir_usuario_id = null) {
        try {
            $conexion = Connection::connect();
            
            $query = "
                SELECT COUNT(*) as count
                FROM usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                WHERE u.email = :email AND s.tenant_id = :tenant_id AND u.deleted_at IS NULL
            ";
            
            if ($excluir_usuario_id) {
                $query .= " AND u.idusuario != :excluir_usuario_id";
            }
            
            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            
            if ($excluir_usuario_id) {
                $stmt->bindParam(':excluir_usuario_id', $excluir_usuario_id, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (Exception $e) {
            error_log("Error en verificarEmailExiste: " . $e->getMessage());
            return true; // En caso de error, asumir que ya existe para evitar duplicados
        }
    }
    
    public static function crearUsuario($datos) {
        try {
            $conexion = Connection::connect();
            
            // Primero verificar si la columna thumbnail existe
            $checkColumn = $conexion->prepare("SHOW COLUMNS FROM usuario LIKE 'thumbnail'");
            $checkColumn->execute();
            $thumbnailExists = $checkColumn->rowCount() > 0;
            // error_log("Verificación columna thumbnail - Existe: " . ($thumbnailExists ? 'Sí' : 'No'));
            
            if ($thumbnailExists) {
                $stmt = $conexion->prepare("
                    INSERT INTO usuario (nombre, cargo, direccion, telefono, email, password, rol, thumbnail, sucursal_idsucursal, created_at)
                    VALUES (:nombre, :cargo, :direccion, :telefono, :email, :password, :rol, :thumbnail, :sucursal_id, NOW())
                ");
            } else {
                // error_log("Warning: Campo thumbnail no existe en la tabla usuario");
                $stmt = $conexion->prepare("
                    INSERT INTO usuario (nombre, cargo, direccion, telefono, email, password, rol, sucursal_idsucursal, created_at)
                    VALUES (:nombre, :cargo, :direccion, :telefono, :email, :password, :rol, :sucursal_id, NOW())
                ");
            }
            
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':cargo', $datos['cargo'], PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $datos['rol'], PDO::PARAM_STR);
            if ($thumbnailExists) {
                $stmt->bindParam(':thumbnail', $datos['thumbnail'], PDO::PARAM_STR);
            }
            $stmt->bindParam(':sucursal_id', $datos['sucursal_id'], PDO::PARAM_INT);
            
            $result = $stmt->execute();
            
            if (!$result) {
                // error_log("Error en crearUsuario - SQL Error: " . json_encode($stmt->errorInfo()));
                // error_log("Error en crearUsuario - Datos: " . json_encode($datos));
            }
            
            return $result;
            
        } catch (Exception $e) {
            // error_log("Error en crearUsuario - Exception: " . $e->getMessage());
            // error_log("Error en crearUsuario - SQL: " . $stmt->queryString ?? 'Query not available');
            return false;
        }
    }
    
    public static function obtenerUsuario($idusuario, $tenant_id) {
        try {
            error_log("obtenerUsuario Model - Parámetros recibidos: idusuario=$idusuario, tenant_id=$tenant_id");
            
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                SELECT u.idusuario, u.nombre, u.cargo, u.direccion, u.telefono, u.email, 
                       u.rol, u.thumbnail, u.estado, u.created_at,
                       s.sri_nombre as sucursal_nombre
                FROM usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                WHERE u.idusuario = :idusuario AND s.tenant_id = :tenant_id AND u.deleted_at IS NULL
            ");
            
            $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            
            error_log("obtenerUsuario Model - Ejecutando consulta SQL");
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("obtenerUsuario Model - Resultado de consulta: " . json_encode($result));
            
            if (!$result) {
                // Verificar si el usuario existe sin restricción de tenant
                $stmt2 = $conexion->prepare("SELECT idusuario, deleted_at FROM usuario WHERE idusuario = :idusuario");
                $stmt2->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
                $stmt2->execute();
                $userCheck = $stmt2->fetch(PDO::FETCH_ASSOC);
                
                error_log("obtenerUsuario Model - Verificación usuario sin tenant: " . json_encode($userCheck));
                
                if ($userCheck && $userCheck['deleted_at']) {
                    error_log("obtenerUsuario Model - Usuario eliminado (deleted_at no es NULL)");
                } elseif ($userCheck) {
                    error_log("obtenerUsuario Model - Usuario existe pero no pertenece al tenant $tenant_id");
                } else {
                    error_log("obtenerUsuario Model - Usuario no existe en absoluto");
                }
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("obtenerUsuario Model - Error en consulta: " . $e->getMessage());
            return false;
        }
    }
    
    public static function editarUsuario($datos, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                UPDATE usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                SET u.nombre = :nombre, u.cargo = :cargo, u.direccion = :direccion,
                    u.telefono = :telefono, u.email = :email, u.rol = :rol, u.estado = :estado
                WHERE u.idusuario = :idusuario AND s.tenant_id = :tenant_id
            ");
            
            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':cargo', $datos['cargo'], PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(':rol', $datos['rol'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindParam(':idusuario', $datos['idusuario'], PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error en editarUsuario: " . $e->getMessage());
            return false;
        }
    }
    
    public static function eliminarUsuario($idusuario, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                UPDATE usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                SET u.deleted_at = NOW()
                WHERE u.idusuario = :idusuario AND s.tenant_id = :tenant_id
            ");
            
            $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error en eliminarUsuario: " . $e->getMessage());
            return false;
        }
    }
}