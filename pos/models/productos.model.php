<?php

require_once "connection.php";

class ModeloProductos {

    /*=============================================
    OBTENER ÚLTIMO CÓDIGO DE PRODUCTO
    =============================================*/
    static public function mdlObtenerUltimoCodigo($tenantId) {

        try {
            $stmt = Connection::connect()->prepare("
                SELECT p.codigo
                FROM producto p
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE c.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
                AND p.codigo REGEXP '^P[0-9]{7}$'
                ORDER BY CAST(SUBSTRING(p.codigo, 2) AS UNSIGNED) DESC
                LIMIT 1
            ");

            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_COLUMN);
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlObtenerUltimoCodigo: " . $e->getMessage());
            return null;
        }
    }

    /*=============================================
    VERIFICAR SI CÓDIGO EXISTE
    =============================================*/
    static public function mdlVerificarCodigoExiste($codigo, $tenantId) {

        try {
            $stmt = Connection::connect()->prepare("
                SELECT COUNT(*)
                FROM producto p
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE p.codigo = :codigo
                AND c.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
            ");

            $stmt->bindParam(":codigo", $codigo, PDO::PARAM_STR);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
            $stmt->execute();

            $count = $stmt->fetch(PDO::FETCH_COLUMN);
            $stmt->closeCursor();

            return $count > 0;

        } catch (Exception $e) {
            error_log("Error en mdlVerificarCodigoExiste: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    VERIFICAR SI CÓDIGO AUXILIAR EXISTE
    =============================================*/
    static public function mdlVerificarCodigoAuxiliarExiste($codigoAuxiliar, $tenantId, $productoId = null) {

        try {
            $sql = "
                SELECT COUNT(*)
                FROM producto p
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE p.codigo_auxiliar = :codigo_auxiliar
                AND c.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
            ";

            $params = array(
                ":codigo_auxiliar" => $codigoAuxiliar,
                ":tenant_id" => $tenantId
            );

            // Si se está editando un producto, excluirlo de la búsqueda
            if ($productoId !== null) {
                $sql .= " AND p.idproducto != :producto_id";
                $params[":producto_id"] = $productoId;
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
            error_log("Error en mdlVerificarCodigoAuxiliarExiste: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    CREAR PRODUCTO
    =============================================*/
    static public function mdlCrearProducto($datos) {

        try {
            $stmt = Connection::connect()->prepare("
                INSERT INTO producto (
                    codigo,
                    codigo_auxiliar,
                    descripcion,
                    precio_de_venta,
                    precio_de_compra,
                    tiene_descuento,
                    descuento_por_cantidad,
                    precio_con_descuento,
                    estado,
                    maneja_stock,
                    stock_actual,
                    stock_minimo,
                    stock_maximo,
                    unidad_medida,
                    peso,
                    imagen,
                    tipo_producto,
                    codigo_iva,
                    porcentaje_iva,
                    graba_ice,
                    codigo_ice,
                    porcentaje_ice,
                    es_material_construccion,
                    codigo_material_construccion,
                    created_at,
                    categoria_idcategoria
                ) VALUES (
                    :codigo,
                    :codigo_auxiliar,
                    :descripcion,
                    :precio_de_venta,
                    :precio_de_compra,
                    :tiene_descuento,
                    :descuento_por_cantidad,
                    :precio_con_descuento,
                    :estado,
                    :maneja_stock,
                    :stock_actual,
                    :stock_minimo,
                    :stock_maximo,
                    :unidad_medida,
                    :peso,
                    :imagen,
                    :tipo_producto,
                    :codigo_iva,
                    :porcentaje_iva,
                    :graba_ice,
                    :codigo_ice,
                    :porcentaje_ice,
                    :es_material_construccion,
                    :codigo_material_construccion,
                    :created_at,
                    :categoria_idcategoria
                )
            ");

            $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
            $stmt->bindParam(":codigo_auxiliar", $datos["codigo_auxiliar"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":precio_de_venta", $datos["precio_de_venta"], PDO::PARAM_STR);
            $stmt->bindParam(":precio_de_compra", $datos["precio_de_compra"], PDO::PARAM_STR);
            $stmt->bindParam(":tiene_descuento", $datos["tiene_descuento"], PDO::PARAM_INT);
            $stmt->bindParam(":descuento_por_cantidad", $datos["descuento_por_cantidad"], PDO::PARAM_INT);
            $stmt->bindParam(":precio_con_descuento", $datos["precio_con_descuento"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":maneja_stock", $datos["maneja_stock"], PDO::PARAM_INT);
            $stmt->bindParam(":stock_actual", $datos["stock_actual"], PDO::PARAM_INT);
            $stmt->bindParam(":stock_minimo", $datos["stock_minimo"], PDO::PARAM_INT);
            $stmt->bindParam(":stock_maximo", $datos["stock_maximo"], PDO::PARAM_INT);
            $stmt->bindParam(":unidad_medida", $datos["unidad_medida"], PDO::PARAM_STR);
            $stmt->bindParam(":peso", $datos["peso"], PDO::PARAM_STR);
            $stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo_producto", $datos["tipo_producto"], PDO::PARAM_STR);
            $stmt->bindParam(":codigo_iva", $datos["codigo_iva"], PDO::PARAM_STR);
            $stmt->bindParam(":porcentaje_iva", $datos["porcentaje_iva"], PDO::PARAM_STR);
            $stmt->bindParam(":graba_ice", $datos["graba_ice"], PDO::PARAM_INT);
            $stmt->bindParam(":codigo_ice", $datos["codigo_ice"], PDO::PARAM_STR);
            $stmt->bindParam(":porcentaje_ice", $datos["porcentaje_ice"], PDO::PARAM_STR);
            $stmt->bindParam(":es_material_construccion", $datos["es_material_construccion"], PDO::PARAM_INT);
            $stmt->bindParam(":codigo_material_construccion", $datos["codigo_material_construccion"], PDO::PARAM_STR);
            $stmt->bindParam(":created_at", $datos["created_at"], PDO::PARAM_STR);
            $stmt->bindParam(":categoria_idcategoria", $datos["categoria_idcategoria"], PDO::PARAM_INT);

            if ($stmt->execute()) {
                $stmt->closeCursor();
                return Connection::connect()->lastInsertId();
            } else {
                $stmt->closeCursor();
                return false;
            }

        } catch (Exception $e) {
            error_log("Error en mdlCrearProducto: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    OBTENER PRODUCTOS
    =============================================*/
    static public function mdlObtenerProductos($tenantId, $filtros = array()) {

        try {
            $sql = "
                SELECT p.*, c.nombre as categoria_nombre
                FROM producto p
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE c.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
            ";

            $params = array(":tenant_id" => $tenantId);

            // Aplicar filtros
            if (!empty($filtros["categoria"])) {
                $sql .= " AND p.categoria_idcategoria = :categoria";
                $params[":categoria"] = $filtros["categoria"];
            }

            if (isset($filtros["estado"]) && $filtros["estado"] !== "") {
                $sql .= " AND p.estado = :estado";
                $params[":estado"] = $filtros["estado"];
            }

            if (!empty($filtros["busqueda"])) {
                $sql .= " AND (p.codigo LIKE :busqueda OR p.descripcion LIKE :busqueda OR p.codigo_auxiliar LIKE :busqueda)";
                $params[":busqueda"] = "%" . $filtros["busqueda"] . "%";
            }

            $sql .= " ORDER BY p.created_at DESC";

            $stmt = Connection::connect()->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlObtenerProductos: " . $e->getMessage());
            return array();
        }
    }

    /*=============================================
    OBTENER PRODUCTO POR ID
    =============================================*/
    static public function mdlObtenerProductoPorId($idProducto, $tenantId) {

        try {
            $stmt = Connection::connect()->prepare("
                SELECT p.*, c.nombre as categoria_nombre
                FROM producto p
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE p.idproducto = :id
                AND c.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
            ");

            $stmt->bindParam(":id", $idProducto, PDO::PARAM_INT);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlObtenerProductoPorId: " . $e->getMessage());
            return false;
        }
    }

    /*=============================================
    ACTUALIZAR PRODUCTO
    =============================================*/
    static public function mdlActualizarProducto($idProducto, $datos, $tenantId) {

        try {
            // Construir la consulta dinámicamente según los campos a actualizar
            $campos = array();
            $params = array(":id" => $idProducto, ":tenant_id" => $tenantId);

            foreach ($datos as $campo => $valor) {
                $campos[] = "$campo = :$campo";
                $params[":$campo"] = $valor;
            }

            // Verificar que el producto pertenece al tenant antes de actualizar
            $checkStmt = Connection::connect()->prepare("
                SELECT p.idproducto
                FROM producto p
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                WHERE p.idproducto = :id
                AND c.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
            ");
            $checkStmt->bindParam(":id", $idProducto, PDO::PARAM_INT);
            $checkStmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->rowCount() == 0) {
                throw new Exception("Producto no encontrado o no pertenece al tenant");
            }
            $checkStmt->closeCursor();

            // Si tenemos nueva categoria, verificar que pertenece al tenant
            if (isset($datos['categoria_idcategoria'])) {
                $catStmt = Connection::connect()->prepare("
                    SELECT idcategoria
                    FROM categoria
                    WHERE idcategoria = :cat_id
                    AND tenant_id = :tenant_id
                    AND deleted_at IS NULL
                ");
                $catStmt->bindParam(":cat_id", $datos['categoria_idcategoria'], PDO::PARAM_INT);
                $catStmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
                $catStmt->execute();

                if ($catStmt->rowCount() == 0) {
                    throw new Exception("Categoría no encontrada o no pertenece al tenant");
                }
                $catStmt->closeCursor();
            }

            $sql = "
                UPDATE producto p
                SET " . implode(", ", $campos) . "
                WHERE p.idproducto = :id
                AND p.deleted_at IS NULL
            ";

            $stmt = Connection::connect()->prepare($sql);

            // Bind solo los parámetros del UPDATE, no el tenant_id
            foreach ($params as $key => $value) {
                if ($key !== ':tenant_id') {
                    $stmt->bindValue($key, $value);
                }
            }

            $resultado = $stmt->execute();
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlActualizarProducto: " . $e->getMessage());
            error_log("SQL generado: " . $sql);
            error_log("Parámetros: " . print_r($params, true));
            return false;
        }
    }

    /*=============================================
    ELIMINAR PRODUCTO (SOFT DELETE)
    =============================================*/
    static public function mdlEliminarProducto($idProducto, $tenantId) {

        try {
            $stmt = Connection::connect()->prepare("
                UPDATE producto p
                INNER JOIN categoria c ON p.categoria_idcategoria = c.idcategoria
                SET p.deleted_at = NOW()
                WHERE p.idproducto = :id
                AND c.tenant_id = :tenant_id
                AND p.deleted_at IS NULL
            ");

            $stmt->bindParam(":id", $idProducto, PDO::PARAM_INT);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

            $resultado = $stmt->execute();
            $stmt->closeCursor();

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en mdlEliminarProducto: " . $e->getMessage());
            return false;
        }
    }
}

?>