<?php

require_once "connection.php";

class SucursalesModel {

    // Obtener sucursales con paginación
    public static function obtenerSucursales($tenant_id, $page = 1, $limit = 6, $estado = null, $incluir_eliminadas = false) {
        try {
            $conexion = Connection::connect();

            // Calcular offset para paginación
            $offset = ($page - 1) * $limit;

            // Construir WHERE clause dinámicamente
            if ($incluir_eliminadas) {
                $whereClause = "tenant_id = :tenant_id"; // No filtrar por deleted_at
            } else {
                $whereClause = "tenant_id = :tenant_id AND deleted_at IS NULL";
            }

            $params = [':tenant_id' => $tenant_id];

            if ($estado !== null && $estado !== '') {
                $whereClause .= " AND estado = :estado";
                $params[':estado'] = $estado;
            }

            // Consulta para obtener sucursales con paginación
            $stmt = $conexion->prepare("
                SELECT idsucursal, sri_codigo, sri_nombre, sri_direccion, estado, created_at, deleted_at
                FROM sucursal
                WHERE $whereClause
                ORDER BY created_at ASC
                LIMIT :limit OFFSET :offset
            ");

            // Binding directo para evitar problemas de referencia
            $stmt->bindParam(':tenant_id', $params[':tenant_id'], PDO::PARAM_INT);

            if (isset($params[':estado'])) {
                $stmt->bindParam(':estado', $params[':estado'], PDO::PARAM_INT);
            }

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            $sucursales = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener total de sucursales para calcular páginas (con el mismo filtro)
            $stmtCount = $conexion->prepare("
                SELECT COUNT(*) as total
                FROM sucursal
                WHERE $whereClause
            ");

            // Binding directo para COUNT también
            $stmtCount->bindParam(':tenant_id', $params[':tenant_id'], PDO::PARAM_INT);
            if (isset($params[':estado'])) {
                $stmtCount->bindParam(':estado', $params[':estado'], PDO::PARAM_INT);
            }
            $stmtCount->execute();
            $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

            // Calcular información de paginación
            $totalPages = ceil($total / $limit);
            $hasPrevious = $page > 1;
            $hasNext = $page < $totalPages;

            return [
                'sucursales' => $sucursales,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => $totalPages,
                'has_previous' => $hasPrevious,
                'has_next' => $hasNext
            ];

        } catch (Exception $e) {
            error_log("Error en obtenerSucursales: " . $e->getMessage());
            return [
                'sucursales' => [],
                'total' => 0,
                'page' => 1,
                'limit' => $limit,
                'total_pages' => 0,
                'has_previous' => false,
                'has_next' => false
            ];
        }
    }

    // Obtener una sucursal específica
    public static function obtenerSucursal($idsucursal, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                SELECT idsucursal, sri_codigo, sri_nombre, sri_direccion, estado, created_at, deleted_at
                FROM sucursal
                WHERE idsucursal = :idsucursal AND tenant_id = :tenant_id
            ");

            $stmt->bindParam(':idsucursal', $idsucursal, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error en obtenerSucursal: " . $e->getMessage());
            return false;
        }
    }

    // Verificar si existe una sucursal con el mismo nombre
    public static function verificarNombreExiste($nombre, $tenant_id, $excluir_sucursal_id = null) {
        try {
            $conexion = Connection::connect();

            $query = "
                SELECT COUNT(*) as count
                FROM sucursal
                WHERE sri_nombre = :nombre AND tenant_id = :tenant_id AND deleted_at IS NULL
            ";

            if ($excluir_sucursal_id) {
                $query .= " AND idsucursal != :excluir_sucursal_id";
            }

            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            if ($excluir_sucursal_id) {
                $stmt->bindParam(':excluir_sucursal_id', $excluir_sucursal_id, PDO::PARAM_INT);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;

        } catch (Exception $e) {
            error_log("Error en verificarNombreExiste: " . $e->getMessage());
            return true; // En caso de error, asumir que ya existe para evitar duplicados
        }
    }

    // Verificar dependencias de una sucursal
    public static function verificarDependenciasSucursal($idsucursal, $tenant_id) {
        try {
            $conexion = Connection::connect();

            // Verificar si la sucursal tiene usuarios asociados
            $stmt = $conexion->prepare("
                SELECT COUNT(*) as count
                FROM usuario u
                INNER JOIN sucursal s ON u.sucursal_idsucursal = s.idsucursal
                WHERE s.idsucursal = :idsucursal AND s.tenant_id = :tenant_id
            ");

            $stmt->bindParam(':idsucursal', $idsucursal, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'tiene_dependencias' => $result['count'] > 0,
                'usuarios' => $result['count']
            ];

        } catch (Exception $e) {
            error_log("Error en verificarDependenciasSucursal: " . $e->getMessage());
            return ['tiene_dependencias' => true, 'usuarios' => 0]; // En caso de error, asumir que tiene dependencias
        }
    }

    // Crear nueva sucursal
    public static function crearSucursal($datos) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                INSERT INTO sucursal (sri_codigo, sri_nombre, sri_direccion, estado, tenant_id, created_at)
                VALUES (:sri_codigo, :sri_nombre, :sri_direccion, :estado, :tenant_id, NOW())
            ");

            $stmt->bindParam(':sri_codigo', $datos['sri_codigo'], PDO::PARAM_STR);
            $stmt->bindParam(':sri_nombre', $datos['sri_nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':sri_direccion', $datos['sri_direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $datos['tenant_id'], PDO::PARAM_INT);

            $resultado = $stmt->execute();

            if ($resultado) {
                return $resultado;
            } else {
                return false;
            }

        } catch (Exception $e) {
            error_log("Error en crearSucursal: " . $e->getMessage());
            return false;
        }
    }

    // Editar sucursal
    public static function editarSucursal($datos, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                UPDATE sucursal
                SET sri_codigo = :sri_codigo,
                    sri_nombre = :sri_nombre,
                    sri_direccion = :sri_direccion,
                    estado = :estado,
                    updated_at = NOW()
                WHERE idsucursal = :idsucursal AND tenant_id = :tenant_id
            ");

            $stmt->bindParam(':sri_codigo', $datos['sri_codigo'], PDO::PARAM_STR);
            $stmt->bindParam(':sri_nombre', $datos['sri_nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':sri_direccion', $datos['sri_direccion'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindParam(':idsucursal', $datos['idsucursal'], PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en editarSucursal: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar sucursal (soft delete)
    public static function eliminarSucursal($idsucursal, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                UPDATE sucursal
                SET deleted_at = NOW()
                WHERE idsucursal = :idsucursal AND tenant_id = :tenant_id
            ");

            $stmt->bindParam(':idsucursal', $idsucursal, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en eliminarSucursal: " . $e->getMessage());
            return false;
        }
    }

    // Verificar si existe una sucursal con el mismo código SRI
    public static function verificarCodigoSriExiste($sri_codigo, $tenant_id, $excluir_sucursal_id = null) {
        try {
            $conexion = Connection::connect();

            $query = "
                SELECT COUNT(*) as count
                FROM sucursal
                WHERE sri_codigo = :sri_codigo AND tenant_id = :tenant_id AND deleted_at IS NULL
            ";

            if ($excluir_sucursal_id) {
                $query .= " AND idsucursal != :excluir_sucursal_id";
            }

            $stmt = $conexion->prepare($query);
            $stmt->bindParam(':sri_codigo', $sri_codigo, PDO::PARAM_STR);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            if ($excluir_sucursal_id) {
                $stmt->bindParam(':excluir_sucursal_id', $excluir_sucursal_id, PDO::PARAM_INT);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;

        } catch (Exception $e) {
            error_log("Error en verificarCodigoSriExiste: " . $e->getMessage());
            return true; // En caso de error, asumir que ya existe para evitar duplicados
        }
    }
}

?>