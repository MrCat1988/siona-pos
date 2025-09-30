<?php
require_once "connection.php";

class PuntoDeEmisionModel {

    /**
     * Obtener todos los puntos de emisión con información de sucursal
     */
    static public function mdlCargarPuntosEmision($tabla, $incluirEliminados = false, $tenantId = null) {
        $whereClause = "WHERE pe.sucursal_idsucursal = s.idsucursal AND s.tenant_id = :tenant_id";

        if (!$incluirEliminados) {
            $whereClause .= " AND pe.deleted_at IS NULL";
        }

        $sql = "SELECT
                    pe.idpunto_de_emision,
                    pe.codigo_sri,
                    pe.descripcion,
                    pe.secuencial_factura,
                    pe.secuencial_nota_credito,
                    pe.secuencial_nota_debito,
                    pe.secuencial_guia_remision,
                    pe.secuencial_retencion,
                    pe.estado,
                    pe.created_at,
                    pe.updated_at,
                    pe.deleted_at,
                    pe.sucursal_idsucursal,
                    s.sri_codigo as sucursal_codigo,
                    s.sri_nombre as sucursal_nombre
                FROM $tabla pe, sucursal s
                $whereClause
                ORDER BY pe.created_at DESC";

        $stmt = Connection::connect()->prepare($sql);
        $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un punto de emisión específico
     */
    static public function mdlObtenerPuntoEmision($tabla, $campo, $valor, $tenantId = null) {
        $sql = "SELECT
                    pe.idpunto_de_emision,
                    pe.codigo_sri,
                    pe.descripcion,
                    pe.secuencial_factura,
                    pe.secuencial_nota_credito,
                    pe.secuencial_nota_debito,
                    pe.secuencial_guia_remision,
                    pe.secuencial_retencion,
                    pe.estado,
                    pe.created_at,
                    pe.updated_at,
                    pe.deleted_at,
                    pe.sucursal_idsucursal,
                    s.sri_codigo as sucursal_codigo,
                    s.sri_nombre as sucursal_nombre
                FROM $tabla pe
                INNER JOIN sucursal s ON pe.sucursal_idsucursal = s.idsucursal
                WHERE pe.$campo = :valor AND s.tenant_id = :tenant_id";

        $stmt = Connection::connect()->prepare($sql);
        $stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
        $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si existe un punto de emisión con el mismo código en la sucursal
     */
    static public function mdlVerificarCodigoExistente($tabla, $codigoSri, $sucursalId, $puntoEmisionId = null, $tenantId = null) {
        $whereClause = "WHERE pe.codigo_sri = :codigo_sri AND pe.sucursal_idsucursal = :sucursal_id AND s.tenant_id = :tenant_id AND pe.deleted_at IS NULL";

        if ($puntoEmisionId) {
            $whereClause .= " AND pe.idpunto_de_emision != :punto_emision_id";
        }

        $sql = "SELECT COUNT(*) as total
                FROM $tabla pe
                INNER JOIN sucursal s ON pe.sucursal_idsucursal = s.idsucursal
                $whereClause";

        $stmt = Connection::connect()->prepare($sql);
        $stmt->bindParam(":codigo_sri", $codigoSri, PDO::PARAM_STR);
        $stmt->bindParam(":sucursal_id", $sucursalId, PDO::PARAM_INT);
        $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

        if ($puntoEmisionId) {
            $stmt->bindParam(":punto_emision_id", $puntoEmisionId, PDO::PARAM_INT);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }

    /**
     * Crear un nuevo punto de emisión
     */
    static public function mdlCrearPuntoEmision($tabla, $datos) {
        $sql = "INSERT INTO $tabla (
                    codigo_sri,
                    descripcion,
                    secuencial_factura,
                    secuencial_nota_credito,
                    secuencial_nota_debito,
                    secuencial_guia_remision,
                    secuencial_retencion,
                    estado,
                    created_at,
                    sucursal_idsucursal
                ) VALUES (
                    :codigo_sri,
                    :descripcion,
                    :secuencial_factura,
                    :secuencial_nota_credito,
                    :secuencial_nota_debito,
                    :secuencial_guia_remision,
                    :secuencial_retencion,
                    :estado,
                    NOW(),
                    :sucursal_idsucursal
                )";

        $conn = Connection::connect();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(":codigo_sri", $datos["codigo_sri"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":secuencial_factura", $datos["secuencial_factura"], PDO::PARAM_INT);
        $stmt->bindParam(":secuencial_nota_credito", $datos["secuencial_nota_credito"], PDO::PARAM_INT);
        $stmt->bindParam(":secuencial_nota_debito", $datos["secuencial_nota_debito"], PDO::PARAM_INT);
        $stmt->bindParam(":secuencial_guia_remision", $datos["secuencial_guia_remision"], PDO::PARAM_INT);
        $stmt->bindParam(":secuencial_retencion", $datos["secuencial_retencion"], PDO::PARAM_INT);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
        $stmt->bindParam(":sucursal_idsucursal", $datos["sucursal_idsucursal"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $lastId = $conn->lastInsertId();
            error_log("mdlCrearPuntoEmision - INSERT exitoso. lastInsertId: " . $lastId);
            error_log("mdlCrearPuntoEmision - rowCount: " . $stmt->rowCount());

            // Si lastInsertId retorna 0, intentar obtener el ID de otra forma
            if ($lastId == 0 || $lastId === '0') {
                $lastId = $conn->query("SELECT LAST_INSERT_ID()")->fetchColumn();
                error_log("mdlCrearPuntoEmision - Usando SELECT LAST_INSERT_ID(): " . $lastId);
            }

            return $lastId ? $lastId : true; // Retornar al menos true si se insertó
        } else {
            $errorInfo = $stmt->errorInfo();
            error_log("mdlCrearPuntoEmision - Error SQL: " . print_r($errorInfo, true));
            return false;
        }
    }

    /**
     * Actualizar un punto de emisión
     */
    static public function mdlActualizarPuntoEmision($tabla, $datos) {
        $sql = "UPDATE $tabla SET
                    codigo_sri = :codigo_sri,
                    descripcion = :descripcion,
                    secuencial_factura = :secuencial_factura,
                    secuencial_nota_credito = :secuencial_nota_credito,
                    secuencial_nota_debito = :secuencial_nota_debito,
                    secuencial_guia_remision = :secuencial_guia_remision,
                    secuencial_retencion = :secuencial_retencion,
                    estado = :estado,
                    updated_at = NOW(),
                    sucursal_idsucursal = :sucursal_idsucursal
                WHERE idpunto_de_emision = :idpunto_de_emision";

        $stmt = Connection::connect()->prepare($sql);

        $stmt->bindParam(":codigo_sri", $datos["codigo_sri"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":secuencial_factura", $datos["secuencial_factura"], PDO::PARAM_INT);
        $stmt->bindParam(":secuencial_nota_credito", $datos["secuencial_nota_credito"], PDO::PARAM_INT);
        $stmt->bindParam(":secuencial_nota_debito", $datos["secuencial_nota_debito"], PDO::PARAM_INT);
        $stmt->bindParam(":secuencial_guia_remision", $datos["secuencial_guia_remision"], PDO::PARAM_INT);
        $stmt->bindParam(":secuencial_retencion", $datos["secuencial_retencion"], PDO::PARAM_INT);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
        $stmt->bindParam(":sucursal_idsucursal", $datos["sucursal_idsucursal"], PDO::PARAM_INT);
        $stmt->bindParam(":idpunto_de_emision", $datos["idpunto_de_emision"], PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Eliminar un punto de emisión (soft delete)
     */
    static public function mdlEliminarPuntoEmision($tabla, $id) {
        $sql = "UPDATE $tabla SET deleted_at = NOW() WHERE idpunto_de_emision = :id";

        $stmt = Connection::connect()->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Verificar si un punto de emisión tiene facturas asociadas
     */
    static public function mdlVerificarFacturasAsociadas($puntoEmisionId) {
        $sql = "SELECT COUNT(*) as total FROM factura WHERE puntos_de_emision_idpunto_de_emision = :punto_emision_id";

        $stmt = Connection::connect()->prepare($sql);
        $stmt->bindParam(":punto_emision_id", $puntoEmisionId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    }

    /**
     * Obtener siguiente secuencial para un tipo de documento
     */
    static public function mdlObtenerSiguienteSecuencial($tabla, $puntoEmisionId, $tipoDocumento) {
        $campo = "secuencial_" . $tipoDocumento;

        $sql = "SELECT $campo as secuencial FROM $tabla WHERE idpunto_de_emision = :punto_emision_id";

        $stmt = Connection::connect()->prepare($sql);
        $stmt->bindParam(":punto_emision_id", $puntoEmisionId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['secuencial'] : 1;
    }

    /**
     * Actualizar secuencial de un tipo de documento
     */
    static public function mdlActualizarSecuencial($tabla, $puntoEmisionId, $tipoDocumento, $nuevoSecuencial) {
        $campo = "secuencial_" . $tipoDocumento;

        $sql = "UPDATE $tabla SET $campo = :nuevo_secuencial, updated_at = NOW()
                WHERE idpunto_de_emision = :punto_emision_id";

        $stmt = Connection::connect()->prepare($sql);
        $stmt->bindParam(":nuevo_secuencial", $nuevoSecuencial, PDO::PARAM_INT);
        $stmt->bindParam(":punto_emision_id", $puntoEmisionId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>