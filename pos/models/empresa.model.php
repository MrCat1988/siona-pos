<?php

require_once "connection.php";

class EmpresaModel {

    /**
     * Obtener información de la empresa del tenant actual
     */
    static public function mdlObtenerEmpresa($tenantId) {
        try {
            $sql = "SELECT idempresa_tenant, ruc, nombre_comercial, razon_social, direccion_matriz,
                           actividad_economica, tipo_contibuyente, regimen, contabilidad, agente_retencion,
                           contribuyente_especial, artesano, numero_calificacion_artesanal, telefono, email,
                           correo_envio_factura, ambiente_sri, password_correo_envio_factura,
                           puerto_correo_envio_factura, servidor_smtp_correo_envio_factura,
                           p12_path, p12_password, p12_expiration_date,
                           estado, created_at, updated_at
                    FROM empresa_tenant
                    WHERE idempresa_tenant = :tenant_id
                    AND deleted_at IS NULL";

            $stmt = Connection::connect()->prepare($sql);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error en mdlObtenerEmpresa: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar información de la empresa
     */
    static public function mdlActualizarEmpresa($datos, $tenantId) {
        try {
            $sql = "UPDATE empresa_tenant SET
                        ruc = :ruc,
                        nombre_comercial = :nombre_comercial,
                        razon_social = :razon_social,
                        direccion_matriz = :direccion_matriz,
                        actividad_economica = :actividad_economica,
                        tipo_contibuyente = :tipo_contibuyente,
                        regimen = :regimen,
                        contabilidad = :contabilidad,
                        agente_retencion = :agente_retencion,
                        contribuyente_especial = :contribuyente_especial,
                        artesano = :artesano,
                        numero_calificacion_artesanal = :numero_calificacion_artesanal,
                        telefono = :telefono,
                        email = :email,
                        correo_envio_factura = :correo_envio_factura,
                        ambiente_sri = :ambiente_sri,
                        password_correo_envio_factura = :password_correo_envio_factura,
                        puerto_correo_envio_factura = :puerto_correo_envio_factura,
                        servidor_smtp_correo_envio_factura = :servidor_smtp_correo_envio_factura,
                        p12_path = :p12_path,
                        p12_password = :p12_password,
                        p12_expiration_date = :p12_expiration_date,
                        updated_at = NOW()
                    WHERE idempresa_tenant = :tenant_id
                    AND deleted_at IS NULL";

            $stmt = Connection::connect()->prepare($sql);

            $stmt->bindParam(":ruc", $datos["ruc"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_comercial", $datos["nombre_comercial"], PDO::PARAM_STR);
            $stmt->bindParam(":razon_social", $datos["razon_social"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion_matriz", $datos["direccion_matriz"], PDO::PARAM_STR);
            $stmt->bindParam(":actividad_economica", $datos["actividad_economica"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo_contibuyente", $datos["tipo_contibuyente"], PDO::PARAM_STR);
            $stmt->bindParam(":regimen", $datos["regimen"], PDO::PARAM_STR);
            $stmt->bindParam(":contabilidad", $datos["contabilidad"], PDO::PARAM_INT);
            $stmt->bindParam(":agente_retencion", $datos["agente_retencion"], PDO::PARAM_INT);
            $stmt->bindParam(":contribuyente_especial", $datos["contribuyente_especial"], PDO::PARAM_INT);
            $stmt->bindParam(":artesano", $datos["artesano"], PDO::PARAM_INT);
            $stmt->bindParam(":numero_calificacion_artesanal", $datos["numero_calificacion_artesanal"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
            $stmt->bindParam(":correo_envio_factura", $datos["correo_envio_factura"], PDO::PARAM_STR);
            $stmt->bindParam(":ambiente_sri", $datos["ambiente_sri"], PDO::PARAM_STR);
            $stmt->bindParam(":password_correo_envio_factura", $datos["password_correo_envio_factura"], PDO::PARAM_STR);
            $stmt->bindParam(":puerto_correo_envio_factura", $datos["puerto_correo_envio_factura"], PDO::PARAM_STR);
            $stmt->bindParam(":servidor_smtp_correo_envio_factura", $datos["servidor_smtp_correo_envio_factura"], PDO::PARAM_STR);
            $stmt->bindParam(":p12_path", $datos["p12_path"], PDO::PARAM_STR);
            $stmt->bindParam(":p12_password", $datos["p12_password"], PDO::PARAM_STR);
            $stmt->bindParam(":p12_expiration_date", $datos["p12_expiration_date"], PDO::PARAM_STR);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en mdlActualizarEmpresa: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el RUC existe (excluyendo la empresa actual)
     */
    static public function mdlVerificarRucExiste($ruc, $tenantIdExcluir = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM empresa_tenant
                    WHERE ruc = :ruc
                    AND deleted_at IS NULL";

            if ($tenantIdExcluir !== null) {
                $sql .= " AND idempresa_tenant != :tenant_id_excluir";
            }

            $stmt = Connection::connect()->prepare($sql);
            $stmt->bindParam(":ruc", $ruc, PDO::PARAM_STR);

            if ($tenantIdExcluir !== null) {
                $stmt->bindParam(":tenant_id_excluir", $tenantIdExcluir, PDO::PARAM_INT);
            }

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return $resultado['total'] > 0;

        } catch (Exception $e) {
            error_log("Error en mdlVerificarRucExiste: " . $e->getMessage());
            return false;
        }
    }
}
?>
