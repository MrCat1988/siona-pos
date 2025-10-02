<?php

require_once "connection.php";

class ClientesModel {

    /**
     * Obtener todos los clientes del tenant con filtros y búsqueda
     */
    static public function mdlObtenerClientes($tenantId, $filtros = array()) {
        try {
            $sql = "SELECT idcliente, tipo_identificacion_sri, numero_identificacion,
                           nombres, apellidos, email, telefono, direccion, estado,
                           created_at, updated_at
                    FROM cliente
                    WHERE tenant_id = :tenant_id
                    AND deleted_at IS NULL";

            // Solo excluir Consumidor Final si NO se está buscando específicamente
            if (!isset($filtros['tipo_identificacion']) || $filtros['tipo_identificacion'] !== '07') {
                $sql .= " AND tipo_identificacion_sri != '07'";
            }

            // Aplicar filtro de estado
            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $sql .= " AND estado = :estado";
            }

            // Aplicar filtro de tipo de identificación
            if (isset($filtros['tipo_identificacion']) && $filtros['tipo_identificacion'] !== '') {
                $sql .= " AND tipo_identificacion_sri = :tipo_identificacion";
            }

            // Aplicar búsqueda por texto (nombre, apellido, identificación, email)
            if (isset($filtros['busqueda']) && !empty($filtros['busqueda'])) {
                $sql .= " AND (nombres LIKE :busqueda
                          OR apellidos LIKE :busqueda
                          OR numero_identificacion LIKE :busqueda
                          OR email LIKE :busqueda
                          OR CONCAT(nombres, ' ', apellidos) LIKE :busqueda)";
            }

            $sql .= " ORDER BY created_at DESC";

            // Aplicar límite para paginación
            if (isset($filtros['limit']) && isset($filtros['offset'])) {
                $sql .= " LIMIT :limit OFFSET :offset";
            }

            $stmt = Connection::connect()->prepare($sql);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $stmt->bindParam(":estado", $filtros['estado'], PDO::PARAM_INT);
            }

            if (isset($filtros['tipo_identificacion']) && $filtros['tipo_identificacion'] !== '') {
                $stmt->bindParam(":tipo_identificacion", $filtros['tipo_identificacion'], PDO::PARAM_STR);
            }

            if (isset($filtros['busqueda']) && !empty($filtros['busqueda'])) {
                $busqueda = "%" . $filtros['busqueda'] . "%";
                $stmt->bindParam(":busqueda", $busqueda, PDO::PARAM_STR);
            }

            if (isset($filtros['limit']) && isset($filtros['offset'])) {
                $stmt->bindParam(":limit", $filtros['limit'], PDO::PARAM_INT);
                $stmt->bindParam(":offset", $filtros['offset'], PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error en mdlObtenerClientes: " . $e->getMessage());
            return array();
        }
    }

    /**
     * Contar total de clientes con filtros
     */
    static public function mdlContarClientes($tenantId, $filtros = array()) {
        try {
            $sql = "SELECT COUNT(*) as total
                    FROM cliente
                    WHERE tenant_id = :tenant_id
                    AND deleted_at IS NULL";

            // Solo excluir Consumidor Final si NO se está buscando específicamente
            if (!isset($filtros['tipo_identificacion']) || $filtros['tipo_identificacion'] !== '07') {
                $sql .= " AND tipo_identificacion_sri != '07'";
            }

            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $sql .= " AND estado = :estado";
            }

            if (isset($filtros['tipo_identificacion']) && $filtros['tipo_identificacion'] !== '') {
                $sql .= " AND tipo_identificacion_sri = :tipo_identificacion";
            }

            if (isset($filtros['busqueda']) && !empty($filtros['busqueda'])) {
                $sql .= " AND (nombres LIKE :busqueda
                          OR apellidos LIKE :busqueda
                          OR numero_identificacion LIKE :busqueda
                          OR email LIKE :busqueda
                          OR CONCAT(nombres, ' ', apellidos) LIKE :busqueda)";
            }

            $stmt = Connection::connect()->prepare($sql);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $stmt->bindParam(":estado", $filtros['estado'], PDO::PARAM_INT);
            }

            if (isset($filtros['tipo_identificacion']) && $filtros['tipo_identificacion'] !== '') {
                $stmt->bindParam(":tipo_identificacion", $filtros['tipo_identificacion'], PDO::PARAM_STR);
            }

            if (isset($filtros['busqueda']) && !empty($filtros['busqueda'])) {
                $busqueda = "%" . $filtros['busqueda'] . "%";
                $stmt->bindParam(":busqueda", $busqueda, PDO::PARAM_STR);
            }

            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'];

        } catch (Exception $e) {
            error_log("Error en mdlContarClientes: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener un cliente por ID
     */
    static public function mdlObtenerClientePorId($idcliente, $tenantId) {
        try {
            $sql = "SELECT idcliente, tipo_identificacion_sri, numero_identificacion,
                           nombres, apellidos, email, telefono, direccion, estado,
                           created_at, updated_at
                    FROM cliente
                    WHERE idcliente = :idcliente
                    AND tenant_id = :tenant_id
                    AND deleted_at IS NULL";

            $stmt = Connection::connect()->prepare($sql);
            $stmt->bindParam(":idcliente", $idcliente, PDO::PARAM_INT);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            error_log("Error en mdlObtenerClientePorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si ya existe un cliente con el mismo número de identificación
     */
    static public function mdlVerificarClienteExistente($numeroIdentificacion, $tenantId, $idclienteExcluir = null) {
        try {
            $sql = "SELECT idcliente
                    FROM cliente
                    WHERE numero_identificacion = :numero_identificacion
                    AND tenant_id = :tenant_id
                    AND deleted_at IS NULL";

            if ($idclienteExcluir !== null) {
                $sql .= " AND idcliente != :idcliente_excluir";
            }

            $stmt = Connection::connect()->prepare($sql);
            $stmt->bindParam(":numero_identificacion", $numeroIdentificacion, PDO::PARAM_STR);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

            if ($idclienteExcluir !== null) {
                $stmt->bindParam(":idcliente_excluir", $idclienteExcluir, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;

        } catch (Exception $e) {
            error_log("Error en mdlVerificarClienteExistente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear nuevo cliente
     */
    static public function mdlCrearCliente($datos, $tenantId) {
        try {
            $sql = "INSERT INTO cliente (
                        tipo_identificacion_sri, numero_identificacion, nombres, apellidos,
                        email, telefono, direccion, estado, tenant_id, created_at, updated_at
                    ) VALUES (
                        :tipo_identificacion_sri, :numero_identificacion, :nombres, :apellidos,
                        :email, :telefono, :direccion, :estado, :tenant_id, NOW(), NOW()
                    )";

            // IMPORTANTE: Guardar la conexión en una variable para usar la MISMA instancia
            $conn = Connection::connect();
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(":tipo_identificacion_sri", $datos["tipo_identificacion_sri"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_identificacion", $datos["numero_identificacion"], PDO::PARAM_STR);
            $stmt->bindParam(":nombres", $datos["nombres"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos", $datos["apellidos"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Usar LA MISMA conexión que preparó el statement
                return $conn->lastInsertId();
            }

            return false;

        } catch (Exception $e) {
            error_log("Error en mdlCrearCliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar cliente existente
     */
    static public function mdlActualizarCliente($datos, $idcliente, $tenantId) {
        try {
            $sql = "UPDATE cliente SET
                        tipo_identificacion_sri = :tipo_identificacion_sri,
                        numero_identificacion = :numero_identificacion,
                        nombres = :nombres,
                        apellidos = :apellidos,
                        email = :email,
                        telefono = :telefono,
                        direccion = :direccion,
                        estado = :estado,
                        updated_at = NOW()
                    WHERE idcliente = :idcliente
                    AND tenant_id = :tenant_id
                    AND deleted_at IS NULL";

            $stmt = Connection::connect()->prepare($sql);

            $stmt->bindParam(":tipo_identificacion_sri", $datos["tipo_identificacion_sri"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_identificacion", $datos["numero_identificacion"], PDO::PARAM_STR);
            $stmt->bindParam(":nombres", $datos["nombres"], PDO::PARAM_STR);
            $stmt->bindParam(":apellidos", $datos["apellidos"], PDO::PARAM_STR);
            $stmt->bindParam(":email", $datos["email"], PDO::PARAM_STR);
            $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
            $stmt->bindParam(":idcliente", $idcliente, PDO::PARAM_INT);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en mdlActualizarCliente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar cliente (soft delete)
     */
    static public function mdlEliminarCliente($idcliente, $tenantId) {
        try {
            $sql = "UPDATE cliente SET
                        deleted_at = NOW(),
                        updated_at = NOW()
                    WHERE idcliente = :idcliente
                    AND tenant_id = :tenant_id
                    AND deleted_at IS NULL";

            $stmt = Connection::connect()->prepare($sql);
            $stmt->bindParam(":idcliente", $idcliente, PDO::PARAM_INT);
            $stmt->bindParam(":tenant_id", $tenantId, PDO::PARAM_INT);

            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en mdlEliminarCliente: " . $e->getMessage());
            return false;
        }
    }
}
?>
