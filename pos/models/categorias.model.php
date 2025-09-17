<?php

require_once "connection.php";

class CategoriasModel {

    // Obtener categorías con paginación
    public static function obtenerCategorias($tenant_id, $page = 1, $limit = 6, $estado = null, $incluir_eliminadas = false) {
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

            // Consulta para obtener categorías con paginación
            $stmt = $conexion->prepare("
                SELECT idcategoria, nombre, descripcion, estado, created_at, deleted_at
                FROM categoria
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
            $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener total de categorías para calcular páginas (con el mismo filtro)
            $stmtCount = $conexion->prepare("
                SELECT COUNT(*) as total
                FROM categoria
                WHERE $whereClause
            ");

            $stmtCount->bindParam(':tenant_id', $params[':tenant_id'], PDO::PARAM_INT);
            if (isset($params[':estado'])) {
                $stmtCount->bindParam(':estado', $params[':estado'], PDO::PARAM_INT);
            }

            $stmtCount->execute();
            $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

            // Calcular información de paginación
            $total_pages = ceil($total / $limit);
            $has_previous = $page > 1;
            $has_next = $page < $total_pages;

            return [
                'categorias' => $categorias,
                'total' => (int)$total,
                'page' => (int)$page,
                'limit' => (int)$limit,
                'total_pages' => (int)$total_pages,
                'has_previous' => $has_previous,
                'has_next' => $has_next
            ];

        } catch (Exception $e) {
            error_log("Error en obtenerCategorias: " . $e->getMessage());
            return false;
        }
    }

    // Obtener una categoría específica
    public static function obtenerCategoria($idcategoria, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                SELECT idcategoria, nombre, descripcion, estado, created_at
                FROM categoria
                WHERE idcategoria = :idcategoria AND tenant_id = :tenant_id AND deleted_at IS NULL
            ");

            $stmt->bindParam(':idcategoria', $idcategoria, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error en obtenerCategoria: " . $e->getMessage());
            return false;
        }
    }

    // Verificar si existe una categoría con el mismo nombre
    public static function verificarNombreExiste($nombre, $tenant_id, $excluir_id = null) {
        try {
            $conexion = Connection::connect();

            if ($excluir_id) {
                $stmt = $conexion->prepare("
                    SELECT COUNT(*) as count
                    FROM categoria
                    WHERE LOWER(nombre) = LOWER(:nombre) AND tenant_id = :tenant_id
                    AND idcategoria != :excluir_id AND deleted_at IS NULL
                ");
                $stmt->bindParam(':excluir_id', $excluir_id, PDO::PARAM_INT);
            } else {
                $stmt = $conexion->prepare("
                    SELECT COUNT(*) as count
                    FROM categoria
                    WHERE LOWER(nombre) = LOWER(:nombre) AND tenant_id = :tenant_id AND deleted_at IS NULL
                ");
            }

            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;

        } catch (Exception $e) {
            error_log("Error en verificarNombreExiste: " . $e->getMessage());
            return true; // En caso de error, asumir que existe para evitar duplicados
        }
    }

    // Verificar dependencias de la categoría (productos asociados)
    public static function verificarDependenciasCategoria($idcategoria, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                SELECT COUNT(*) as count
                FROM producto p
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE c.idcategoria = :idcategoria AND c.tenant_id = :tenant_id AND p.deleted_at IS NULL
            ");

            $stmt->bindParam(':idcategoria', $idcategoria, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'tiene_dependencias' => $result['count'] > 0,
                'productos' => $result['count']
            ];

        } catch (Exception $e) {
            error_log("Error en verificarDependenciasCategoria: " . $e->getMessage());
            return ['tiene_dependencias' => true, 'productos' => 0]; // En caso de error, asumir que tiene dependencias
        }
    }

    // Crear nueva categoría
    public static function crearCategoria($datos) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                INSERT INTO categoria (nombre, descripcion, estado, tenant_id, created_at)
                VALUES (:nombre, :descripcion, :estado, :tenant_id, NOW())
            ");

            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $datos['tenant_id'], PDO::PARAM_INT);

            $resultado = $stmt->execute();

            if ($resultado) {
                return true;
            } else {
                return false;
            }

        } catch (Exception $e) {
            error_log("Error en crearCategoria: " . $e->getMessage());
            return false;
        }
    }

    // Editar categoría
    public static function editarCategoria($datos, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                UPDATE categoria
                SET nombre = :nombre,
                    descripcion = :descripcion,
                    estado = :estado,
                    updated_at = NOW()
                WHERE idcategoria = :idcategoria AND tenant_id = :tenant_id
            ");

            $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_INT);
            $stmt->bindParam(':idcategoria', $datos['idcategoria'], PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en editarCategoria: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar categoría (soft delete)
    public static function eliminarCategoria($idcategoria, $tenant_id) {
        try {
            $conexion = Connection::connect();
            $stmt = $conexion->prepare("
                UPDATE categoria
                SET deleted_at = NOW()
                WHERE idcategoria = :idcategoria AND tenant_id = :tenant_id
            ");

            $stmt->bindParam(':idcategoria', $idcategoria, PDO::PARAM_INT);
            $stmt->bindParam(':tenant_id', $tenant_id, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en eliminarCategoria: " . $e->getMessage());
            return false;
        }
    }
}

?>