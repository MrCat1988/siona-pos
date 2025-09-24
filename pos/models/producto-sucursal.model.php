<?php

require_once "connection.php";

class ModeloProductoSucursal {

    /*=============================================
    OBTENER PRODUCTOS POR SUCURSAL
    =============================================*/
    static public function mdlObtenerProductosSucursal($tenantId, $filtros = array()) {

        try {
            $sql = "
                SELECT
                    ps.*,
                    p.codigo,
                    p.codigo_auxiliar,
                    p.descripcion,
                    p.imagen,
                    p.precio_de_venta as precio_base,
                    s.sri_nombre as sucursal_nombre,
                    c.nombre as categoria_nombre
                FROM producto_por_sucursal ps
                INNER JOIN producto p ON ps.productos_idproducto = p.idproducto
                INNER JOIN sucursal s ON ps.sucursal_idsucursal = s.idsucursal
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE c.tenant_id = :tenant_id
                AND s.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
                AND s.deleted_at IS NULL
            ";

            $params = array(":tenant_id" => $tenantId);

            // Aplicar filtros
            if (!empty($filtros["sucursal"])) {
                $sql .= " AND ps.sucursal_idsucursal = :sucursal";
                $params[":sucursal"] = $filtros["sucursal"];
            }

            if (!empty($filtros["categoria"])) {
                $sql .= " AND p.categoria_idcategoria = :categoria";
                $params[":categoria"] = $filtros["categoria"];
            }

            if (!empty($filtros["busqueda"])) {
                $sql .= " AND (p.codigo LIKE :busqueda OR p.descripcion LIKE :busqueda)";
                $params[":busqueda"] = "%" . $filtros["busqueda"] . "%";
            }

            // Manejar filtro de estado y elementos eliminados
            if (isset($filtros["estado"]) && $filtros["estado"] !== "") {
                if ($filtros["estado"] === "deleted") {
                    $sql .= " AND ps.deleted_at IS NOT NULL";
                } else {
                    $sql .= " AND ps.deleted_at IS NULL AND ps.estado = :estado";
                    $params[":estado"] = $filtros["estado"];
                }
            } else {
                // Por defecto, excluir elementos eliminados
                $sql .= " AND ps.deleted_at IS NULL";
            }

            $sql .= " ORDER BY s.sri_nombre, p.descripcion";

            // Agregar paginación si se especifica
            if (isset($filtros["limite"]) && isset($filtros["offset"])) {
                $limite = intval($filtros["limite"]);
                $offset = intval($filtros["offset"]);
                $sql .= " LIMIT " . $limite . " OFFSET " . $offset;
            }

            $stmt = Connection::connect()->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();


            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlObtenerProductosSucursal: " . $e->getMessage());
            return array();
        }
    }

    /*=============================================
    CONTAR TOTAL DE PRODUCTOS-SUCURSAL
    =============================================*/
    static public function mdlContarProductosSucursal($tenantId, $filtros = array()) {

        try {
            $sql = "
                SELECT COUNT(*) as total
                FROM producto_por_sucursal ps
                INNER JOIN producto p ON ps.productos_idproducto = p.idproducto
                INNER JOIN sucursal s ON ps.sucursal_idsucursal = s.idsucursal
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE c.tenant_id = :tenant_id
                AND s.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
                AND s.deleted_at IS NULL
            ";

            $params = array(":tenant_id" => $tenantId);

            // Aplicar los mismos filtros que en la consulta principal
            if (!empty($filtros["sucursal"])) {
                $sql .= " AND ps.sucursal_idsucursal = :sucursal";
                $params[":sucursal"] = $filtros["sucursal"];
            }

            if (!empty($filtros["categoria"])) {
                $sql .= " AND p.categoria_idcategoria = :categoria";
                $params[":categoria"] = $filtros["categoria"];
            }

            if (!empty($filtros["busqueda"])) {
                $sql .= " AND (p.codigo LIKE :busqueda OR p.descripcion LIKE :busqueda)";
                $params[":busqueda"] = "%" . $filtros["busqueda"] . "%";
            }

            // Manejar filtro de estado y elementos eliminados (igual que en la función principal)
            if (isset($filtros["estado"]) && $filtros["estado"] !== "") {
                if ($filtros["estado"] === "deleted") {
                    $sql .= " AND ps.deleted_at IS NOT NULL";
                } else {
                    $sql .= " AND ps.deleted_at IS NULL AND ps.estado = :estado";
                    $params[":estado"] = $filtros["estado"];
                }
            } else {
                // Por defecto, excluir elementos eliminados
                $sql .= " AND ps.deleted_at IS NULL";
            }

            $stmt = Connection::connect()->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return intval($resultado['total']);

        } catch (Exception $e) {
            error_log("Error en mdlContarProductosSucursal: " . $e->getMessage());
            return 0;
        }
    }

    /*=============================================
    OBTENER PRODUCTO-SUCURSAL POR ID
    =============================================*/
    static public function mdlObtenerProductoSucursalPorId($idProductoSucursal, $tenantId) {

        try {
            $stmt = Connection::connect()->prepare("
                SELECT
                    ps.*,
                    p.codigo,
                    p.codigo_auxiliar,
                    p.descripcion,
                    p.imagen,
                    p.precio_de_venta as precio_base,
                    s.sri_nombre as sucursal_nombre,
                    c.nombre as categoria_nombre
                FROM producto_por_sucursal ps
                INNER JOIN producto p ON ps.productos_idproducto = p.idproducto
                INNER JOIN sucursal s ON ps.sucursal_idsucursal = s.idsucursal
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE ps.idproducto_sucursal = :id
                AND c.tenant_id = :tenant_id
                AND s.tenant_id = :tenant_id
                AND ps.deleted_at IS NULL
            ");

            $stmt->bindParam(":id", $idProductoSucursal, PDO::PARAM_INT);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlObtenerProductoSucursalPorId: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    VERIFICAR SI PRODUCTO YA EXISTE EN SUCURSAL
    =============================================*/
    static public function mdlVerificarProductoEnSucursal($productoId, $sucursalId, $tenantId, $excluirId = null) {

        try {
            $sql = "
                SELECT COUNT(*)
                FROM producto_por_sucursal ps
                INNER JOIN producto p ON ps.productos_idproducto = p.idproducto
                INNER JOIN sucursal s ON ps.sucursal_idsucursal = s.idsucursal
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE ps.productos_idproducto = :producto_id
                AND ps.sucursal_idsucursal = :sucursal_id
                AND c.tenant_id = :tenant_id
                AND s.tenant_id = :tenant_id
                AND ps.deleted_at IS NULL
            ";

            $params = array(
                ":producto_id" => $productoId,
                ":sucursal_id" => $sucursalId,
                ":tenant_id" => $tenantId
            );

            // Si se está editando, excluir el registro actual
            if ($excluirId !== null) {
                $sql .= " AND ps.idproducto_sucursal != :excluir_id";
                $params[":excluir_id"] = $excluirId;
            }

            $stmt = Connection::connect()->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_COLUMN);
            $stmt->closeCursor();

            return $count > 0;

        } catch (Exception $e) {
            error_log("Error en mdlVerificarProductoEnSucursal: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    CREAR PRODUCTO-SUCURSAL
    =============================================*/
    static public function mdlCrearProductoSucursal($datos) {

        try {
            $stmt = Connection::connect()->prepare("
                INSERT INTO producto_por_sucursal (
                    precio_sucursal,
                    stock_sucursal,
                    stock_minimo_sucursal,
                    stock_maximo_sucursal,
                    estado,
                    created_at,
                    sucursal_idsucursal,
                    productos_idproducto
                ) VALUES (
                    :precio_sucursal,
                    :stock_sucursal,
                    :stock_minimo_sucursal,
                    :stock_maximo_sucursal,
                    :estado,
                    :created_at,
                    :sucursal_idsucursal,
                    :productos_idproducto
                )
            ");

            $stmt->bindParam(":precio_sucursal", $datos["precio_sucursal"], PDO::PARAM_STR);
            $stmt->bindParam(":stock_sucursal", $datos["stock_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":stock_minimo_sucursal", $datos["stock_minimo_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":stock_maximo_sucursal", $datos["stock_maximo_sucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":created_at", $datos["created_at"], PDO::PARAM_STR);
            $stmt->bindParam(":sucursal_idsucursal", $datos["sucursal_idsucursal"], PDO::PARAM_INT);
            $stmt->bindParam(":productos_idproducto", $datos["productos_idproducto"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->closeCursor();
                return Connection::connect()->lastInsertId();
            } else {
                $stmt->closeCursor();
                return false;
            }

        } catch (Exception $e) {
            error_log("Error en mdlCrearProductoSucursal: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    ACTUALIZAR PRODUCTO-SUCURSAL
    =============================================*/
    static public function mdlActualizarProductoSucursal($idProductoSucursal, $datos, $tenantId) {

        try {
            // Verificar que el registro pertenece al tenant
            $checkStmt = Connection::connect()->prepare("
                SELECT ps.idproducto_sucursal
                FROM producto_por_sucursal ps
                INNER JOIN producto p ON ps.productos_idproducto = p.idproducto
                INNER JOIN sucursal s ON ps.sucursal_idsucursal = s.idsucursal
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE ps.idproducto_sucursal = :id
                AND c.tenant_id = :tenant_id
                AND s.tenant_id = :tenant_id
                AND ps.deleted_at IS NULL
            ");
            $checkStmt->bindParam(":id", $idProductoSucursal, PDO::PARAM_INT);
            $checkStmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->rowCount() == 0) {
                throw new Exception("Registro no encontrado o no pertenece al tenant");
            }
            $checkStmt->closeCursor();

            // Construir la consulta dinámicamente
            $campos = array();
            $params = array(":id" => $idProductoSucursal);

            foreach ($datos as $campo => $valor) {
                $campos[] = "$campo = :$campo";
                $params[":$campo"] = $valor;
            }

            $sql = "
                UPDATE producto_por_sucursal
                SET " . implode(", ", $campos) . "
                WHERE idproducto_sucursal = :id
                AND deleted_at IS NULL
            ";

            $stmt = Connection::connect()->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $resultado = $stmt->execute();
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlActualizarProductoSucursal: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    ELIMINAR PRODUCTO-SUCURSAL (SOFT DELETE)
    =============================================*/
    static public function mdlEliminarProductoSucursal($idProductoSucursal, $tenantId) {

        try {
            $stmt = Connection::connect()->prepare("
                UPDATE producto_por_sucursal ps
                INNER JOIN producto p ON ps.productos_idproducto = p.idproducto
                INNER JOIN sucursal s ON ps.sucursal_idsucursal = s.idsucursal
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                SET ps.deleted_at = NOW()
                WHERE ps.idproducto_sucursal = :id
                AND c.tenant_id = :tenant_id
                AND s.tenant_id = :tenant_id
                AND ps.deleted_at IS NULL
            ");

            $stmt->bindParam(":id", $idProductoSucursal, PDO::PARAM_INT);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

            $resultado = $stmt->execute();
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlEliminarProductoSucursal: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    OBTENER PRODUCTOS DISPONIBLES (no asignados a sucursal)
    =============================================*/
    static public function mdlObtenerProductosDisponibles($sucursalId, $tenantId, $searchTerm = '') {

        try {
            $sql = "
                SELECT p.idproducto, p.codigo, p.descripcion, p.precio_de_venta
                FROM producto p
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                LEFT JOIN producto_por_sucursal ps ON p.idproducto = ps.productos_idproducto
                    AND ps.sucursal_idsucursal = :sucursal_id
                    AND ps.deleted_at IS NULL
                WHERE c.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
                AND p.estado = 1
                AND ps.idproducto_sucursal IS NULL
            ";

            $params = array(
                ":sucursal_id" => $sucursalId,
                ":tenant_id" => $tenantId
            );

            // Agregar filtro de búsqueda si se proporciona
            if (!empty($searchTerm)) {
                $sql .= " AND (p.codigo LIKE :search OR p.descripcion LIKE :search)";
                $params[":search"] = "%" . $searchTerm . "%";
            }

            $sql .= " ORDER BY p.descripcion LIMIT 50";

            $stmt = Connection::connect()->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlObtenerProductosDisponibles: " . $e->getMessage());
            return array();
        }
    }

    /*=============================================
    OBTENER SUCURSALES DISPONIBLES
    =============================================*/
    static public function mdlObtenerSucursalesDisponibles($tenantId) {

        try {
            $stmt = Connection::connect()->prepare("
                SELECT idsucursal, sri_nombre as nombre
                FROM sucursal
                WHERE tenant_id = :tenant_id
                AND deleted_at IS NULL
                AND estado = 1
                ORDER BY sri_nombre
            ");

            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlObtenerSucursalesDisponibles: " . $e->getMessage());
            return array();
        }
    }
}

?>