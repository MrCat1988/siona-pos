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
            
            if ($usuario) {
                error_log("loginUsuario - Usuario encontrado: " . $usuario['email']);
                error_log("loginUsuario - Password almacenado inicia con CLIENT_HASH: " . (strpos($usuario['password'], 'CLIENT_HASH:') === 0 ? 'SÍ' : 'NO'));

                // Verificar si es una contraseña hasheada del cliente
                if (strpos($usuario['password'], 'CLIENT_HASH:') === 0) {
                    // Contraseña almacenada como hash del cliente, comparar directamente
                    $stored_hash = substr($usuario['password'], 12); // Remover prefijo "CLIENT_HASH:"

                    // Generar hash del password ingresado con el mismo salt
                    $clientSalt = hash('sha256', $usuario['email'] . 'SIONA_POS_SALT_2025');
                    $expected_hash = hash('sha256', $password . $clientSalt);

                    error_log("loginUsuario - Stored hash: " . $stored_hash);
                    error_log("loginUsuario - Expected hash: " . $expected_hash);
                    error_log("loginUsuario - Hashes match: " . (hash_equals($stored_hash, $expected_hash) ? 'SÍ' : 'NO'));

                    if (hash_equals($stored_hash, $expected_hash)) {
                        return $usuario;
                    }
                } else {
                    // Contraseña almacenada con password_hash, verificar normalmente
                    error_log("loginUsuario - Verificando con password_verify");
                    if (password_verify($password, $usuario['password'])) {
                        error_log("loginUsuario - password_verify exitoso");
                        return $usuario;
                    } else {
                        error_log("loginUsuario - password_verify falló");
                    }
                }
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Error en loginUsuario: " . $e->getMessage());
            return false;
        }
    }
    
    public static function obtenerUsuarios($tenant_id, $page = 1, $limit = 6, $estado = null, $incluir_eliminados = false) {
        try {
            // Debug logging para verificar parámetros recibidos
            error_log("UsuariosModel::obtenerUsuarios - tenant_id recibido: $tenant_id");
            error_log("UsuariosModel::obtenerUsuarios - parámetros: page=$page, limit=$limit, estado=$estado, incluir_eliminados=" . ($incluir_eliminados ? 'true' : 'false'));

            $conexion = Connection::connect();

            // Calcular offset para paginación
            $offset = ($page - 1) * $limit;

            // Construir WHERE clause dinámicamente
            if ($incluir_eliminados) {
                $whereClause = "s.tenant_id = :tenant_id"; // No filtrar por deleted_at
            } else {
                $whereClause = "s.tenant_id = :tenant_id AND u.deleted_at IS NULL";
            }

            $params = [':tenant_id' => $tenant_id];

            if ($estado !== null && $estado !== '') {
                $whereClause .= " AND u.estado = :estado";
                $params[':estado'] = $estado;
            }

            // Consulta para obtener usuarios con paginación
            $stmt = $conexion->prepare("
                SELECT u.idusuario, u.nombre, u.cargo, u.direccion, u.telefono, u.email,
                       u.rol, u.thumbnail, u.estado, u.created_at, u.deleted_at,
                       s.sri_nombre as sucursal_nombre
                FROM usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                WHERE $whereClause
                ORDER BY u.created_at ASC
                LIMIT :limit OFFSET :offset
            ");

            // Binding directo para evitar problemas de referencia
            $stmt->bindParam(':tenant_id', $params[':tenant_id'], PDO::PARAM_INT);
            error_log("UsuariosModel::obtenerUsuarios - binding :tenant_id = " . $params[':tenant_id']);

            if (isset($params[':estado'])) {
                $stmt->bindParam(':estado', $params[':estado'], PDO::PARAM_INT);
                error_log("UsuariosModel::obtenerUsuarios - binding :estado = " . $params[':estado']);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            error_log("UsuariosModel::obtenerUsuarios - WHERE clause: $whereClause");
            error_log("UsuariosModel::obtenerUsuarios - Ejecutando consulta...");

            $stmt->execute();

            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug: verificar usuarios devueltos
            error_log("UsuariosModel::obtenerUsuarios - Usuarios encontrados: " . count($usuarios));
            foreach ($usuarios as $index => $usuario) {
                error_log("UsuariosModel::obtenerUsuarios - Usuario $index: ID={$usuario['idusuario']}, Nombre={$usuario['nombre']}, Sucursal={$usuario['sucursal_nombre']}");
            }

            // Obtener total de usuarios para calcular páginas (con el mismo filtro)
            $stmtCount = $conexion->prepare("
                SELECT COUNT(*) as total
                FROM usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                WHERE $whereClause
            ");

            // Binding directo para COUNT también
            $stmtCount->bindParam(':tenant_id', $params[':tenant_id'], PDO::PARAM_INT);
            if (isset($params[':estado'])) {
                $stmtCount->bindParam(':estado', $params[':estado'], PDO::PARAM_INT);
            }
            $stmtCount->execute();
            $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'usuarios' => $usuarios,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit),
                'has_previous' => $page > 1,
                'has_next' => $page < ceil($total / $limit)
            ];

        } catch (Exception $e) {
            error_log("Error en obtenerUsuarios: " . $e->getMessage());
            return [
                'usuarios' => [],
                'total' => 0,
                'page' => 1,
                'limit' => $limit,
                'total_pages' => 0,
                'has_previous' => false,
                'has_next' => false
            ];
        }
    }
    
    public static function getSaltForEmail($email) {
        try {
            // Siempre generar el mismo salt para el mismo email
            // Esto garantiza consistencia entre creación de usuario y login
            return hash('sha256', $email . 'SIONA_POS_SALT_2025');

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
                error_log("loginUsuarioHashed - Usuario encontrado: " . $usuario['email']);
                error_log("loginUsuarioHashed - Password almacenado inicia con CLIENT_HASH: " . (strpos($usuario['password'], 'CLIENT_HASH:') === 0 ? 'SÍ' : 'NO'));

                // Verificar si es una contraseña hasheada del cliente
                if (strpos($usuario['password'], 'CLIENT_HASH:') === 0) {
                    // Contraseña almacenada como hash del cliente, comparar directamente
                    $stored_hash = substr($usuario['password'], 12); // Remover prefijo "CLIENT_HASH:"

                    error_log("loginUsuarioHashed - Stored hash: " . $stored_hash);
                    error_log("loginUsuarioHashed - Received hash: " . $hashedPassword);
                    error_log("loginUsuarioHashed - Hashes match: " . (hash_equals($stored_hash, $hashedPassword) ? 'SÍ' : 'NO'));

                    if (hash_equals($stored_hash, $hashedPassword)) {
                        return $usuario;
                    }
                } else {
                    // Contraseña almacenada con password_hash, intentar con passwords comunes
                    $clientSalt = hash('sha256', $email . 'SIONA_POS_SALT_2025');
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
                }

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

            // Manejar contraseñas hasheadas y no hasheadas
            if (isset($datos['is_hashed']) && $datos['is_hashed']) {
                // Si viene hasheada del cliente, almacenar de forma especial
                // Usamos un prefijo para identificar contraseñas hasheadas del cliente
                $password_hash = 'CLIENT_HASH:' . $datos['password'];
                error_log("crearUsuario - Almacenando contraseña hasheada del cliente");
            } else {
                // Si viene en texto plano, usar password_hash normal
                $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
                error_log("crearUsuario - Hasheando contraseña con password_hash");
            }

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
                       s.idsucursal, s.sri_nombre as sucursal_nombre
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

            // Construir SQL dinámicamente dependiendo de qué campos se actualizan
            $setParts = [
                "u.nombre = :nombre",
                "u.cargo = :cargo",
                "u.direccion = :direccion",
                "u.telefono = :telefono",
                "u.email = :email",
                "u.rol = :rol",
                "u.estado = :estado"
            ];

            // Agregar thumbnail si se proporciona
            if (isset($datos['thumbnail']) && $datos['thumbnail'] !== null) {
                $setParts[] = "u.thumbnail = :thumbnail";
            }

            // Agregar password si se proporciona
            if (isset($datos['password']) && $datos['password'] !== null) {
                $setParts[] = "u.password = :password";
            }

            $sql = "
                UPDATE usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                SET " . implode(", ", $setParts) . "
                WHERE u.idusuario = :idusuario AND s.tenant_id = :tenant_id
            ";

            $stmt = $conexion->prepare($sql);

            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':cargo', $datos['cargo'], PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $datos['direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
            $stmt->bindParam(':email', $datos['email'], PDO::PARAM_STR);
            $stmt->bindParam(':rol', $datos['rol'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindParam(':idusuario', $datos['idusuario'], PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            // Bind de thumbnail si se está actualizando
            if (isset($datos['thumbnail']) && $datos['thumbnail'] !== null) {
                $stmt->bindParam(':thumbnail', $datos['thumbnail'], PDO::PARAM_STR);
                error_log("editarUsuario Model - Actualizando con nueva imagen: " . $datos['thumbnail']);
            }

            // Bind de password si se está actualizando
            if (isset($datos['password']) && $datos['password'] !== null) {
                $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
                error_log("editarUsuario Model - Actualizando con nueva contraseña (hasheada)");
            }

            if (!isset($datos['thumbnail']) && !isset($datos['password'])) {
                error_log("editarUsuario Model - Actualizando solo datos básicos (sin imagen ni contraseña)");
            }
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error en editarUsuario: " . $e->getMessage());
            return false;
        }
    }
    
    public static function verificarDependenciasUsuario($idusuario, $tenant_id) {
        try {
            $conexion = Connection::connect();

            // Verificar si el usuario tiene facturas
            $stmt = $conexion->prepare("
                SELECT COUNT(*) as count
                FROM factura f
                INNER JOIN usuario u ON f.usuario_idusuario = u.idusuario
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                WHERE u.idusuario = :idusuario AND s.tenant_id = :tenant_id
            ");

            $stmt->bindParam(':idusuario', $idusuario, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'tiene_dependencias' => $result['count'] > 0,
                'facturas' => $result['count']
            ];

        } catch (Exception $e) {
            error_log("Error en verificarDependenciasUsuario: " . $e->getMessage());
            return ['tiene_dependencias' => true, 'facturas' => 0]; // En caso de error, asumir que tiene dependencias
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