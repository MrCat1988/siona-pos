<?php

class ClientesController {

    /**
     * Verificar sesión activa
     */
    private static function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return false;
        }

        return true;
    }

    /**
     * Obtener lista de clientes con filtros y paginación
     */
    public static function obtenerClientes() {
        if (!self::verificarSesion()) {
            return;
        }

        try {
            $filtros = array();

            // Aplicar filtros si vienen en la petición
            if (isset($_POST['estado']) && $_POST['estado'] !== '') {
                $filtros['estado'] = (int)$_POST['estado'];
            }

            if (isset($_POST['tipo_identificacion']) && $_POST['tipo_identificacion'] !== '') {
                $filtros['tipo_identificacion'] = $_POST['tipo_identificacion'];
            }

            if (isset($_POST['busqueda']) && !empty(trim($_POST['busqueda']))) {
                $filtros['busqueda'] = trim($_POST['busqueda']);
            }

            // Paginación
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 50;
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $offset = ($page - 1) * $limit;

            $filtros['limit'] = $limit;
            $filtros['offset'] = $offset;

            // Obtener clientes
            $clientes = ClientesModel::mdlObtenerClientes($_SESSION['tenant_id'], $filtros);

            // Obtener total de registros para paginación
            $total = ClientesModel::mdlContarClientes($_SESSION['tenant_id'], $filtros);

            echo json_encode(array(
                "status" => "success",
                "data" => $clientes,
                "pagination" => array(
                    "total" => $total,
                    "page" => $page,
                    "limit" => $limit,
                    "pages" => ceil($total / $limit)
                )
            ));

        } catch (Exception $e) {
            error_log("Error en obtenerClientes: " . $e->getMessage());
            echo json_encode(array(
                "status" => "error",
                "message" => "Error al obtener los clientes"
            ));
        }
    }

    /**
     * Obtener un cliente por ID
     */
    public static function obtenerClientePorId() {
        if (!self::verificarSesion()) {
            return;
        }

        if (!isset($_POST['idcliente'])) {
            echo json_encode(array("status" => "error", "message" => "ID de cliente no especificado"));
            return;
        }

        try {
            $idcliente = (int)$_POST['idcliente'];
            $cliente = ClientesModel::mdlObtenerClientePorId($idcliente, $_SESSION['tenant_id']);

            if ($cliente) {
                echo json_encode(array(
                    "status" => "success",
                    "data" => $cliente
                ));
            } else {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Cliente no encontrado"
                ));
            }

        } catch (Exception $e) {
            error_log("Error en obtenerClientePorId: " . $e->getMessage());
            echo json_encode(array(
                "status" => "error",
                "message" => "Error al obtener el cliente"
            ));
        }
    }

    /**
     * Crear nuevo cliente
     */
    public static function crearCliente() {
        if (!self::verificarSesion()) {
            return;
        }

        // Validar CSRF token
        if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                return;
            }
        }

        if ($_POST) {
            try {
                // Validaciones
                if (empty($_POST['numero_identificacion']) || empty($_POST['nombres'])) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Número de identificación y nombres son obligatorios"
                    ));
                    return;
                }

                // Validar formato de email si se proporciona
                if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "El formato del email es inválido"
                    ));
                    return;
                }

                // Validar tipo de identificación SRI
                $tiposValidos = array('04', '05', '06', '07', '08');
                if (!empty($_POST['tipo_identificacion_sri']) && !in_array($_POST['tipo_identificacion_sri'], $tiposValidos)) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Tipo de identificación no válido"
                    ));
                    return;
                }

                // Verificar si ya existe un cliente con el mismo número de identificación
                $numeroIdentificacion = trim($_POST['numero_identificacion']);
                if (ClientesModel::mdlVerificarClienteExistente($numeroIdentificacion, $_SESSION['tenant_id'])) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Ya existe un cliente con este número de identificación"
                    ));
                    return;
                }

                // Preparar datos
                $datos = array(
                    "tipo_identificacion_sri" => $_POST['tipo_identificacion_sri'] ?? '04',
                    "numero_identificacion" => $numeroIdentificacion,
                    "nombres" => trim($_POST['nombres']),
                    "apellidos" => trim($_POST['apellidos'] ?? ''),
                    "email" => trim($_POST['email'] ?? ''),
                    "telefono" => trim($_POST['telefono'] ?? ''),
                    "direccion" => trim($_POST['direccion'] ?? ''),
                    "estado" => isset($_POST['estado']) ? (int)$_POST['estado'] : 1
                );

                // Crear cliente
                $idcliente = ClientesModel::mdlCrearCliente($datos, $_SESSION['tenant_id']);

                if ($idcliente) {
                    echo json_encode(array(
                        "status" => "success",
                        "message" => "Cliente creado exitosamente",
                        "idcliente" => $idcliente
                    ));
                } else {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Error al crear el cliente"
                    ));
                }

            } catch (Exception $e) {
                error_log("Error en crearCliente: " . $e->getMessage());
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Error interno al crear el cliente"
                ));
            }
        }
    }

    /**
     * Actualizar cliente existente
     */
    public static function actualizarCliente() {
        if (!self::verificarSesion()) {
            return;
        }

        // Validar CSRF token
        if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                return;
            }
        }

        if ($_POST) {
            try {
                // Validaciones
                if (empty($_POST['idcliente']) || empty($_POST['numero_identificacion']) || empty($_POST['nombres'])) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "ID, número de identificación y nombres son obligatorios"
                    ));
                    return;
                }

                $idcliente = (int)$_POST['idcliente'];

                // Verificar que el cliente pertenece al tenant
                $clienteActual = ClientesModel::mdlObtenerClientePorId($idcliente, $_SESSION['tenant_id']);
                if (!$clienteActual) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Cliente no encontrado"
                    ));
                    return;
                }

                // Proteger al consumidor final de ser editado
                if ($clienteActual['tipo_identificacion_sri'] == '07') {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "El cliente Consumidor Final no puede ser editado"
                    ));
                    return;
                }

                // Validar formato de email si se proporciona
                if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "El formato del email es inválido"
                    ));
                    return;
                }

                // Validar tipo de identificación SRI
                $tiposValidos = array('04', '05', '06', '07', '08');
                if (!empty($_POST['tipo_identificacion_sri']) && !in_array($_POST['tipo_identificacion_sri'], $tiposValidos)) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Tipo de identificación no válido"
                    ));
                    return;
                }

                // Verificar duplicados (excluyendo el registro actual)
                $numeroIdentificacion = trim($_POST['numero_identificacion']);
                if (ClientesModel::mdlVerificarClienteExistente($numeroIdentificacion, $_SESSION['tenant_id'], $idcliente)) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Ya existe otro cliente con este número de identificación"
                    ));
                    return;
                }

                // Preparar datos
                $datos = array(
                    "tipo_identificacion_sri" => $_POST['tipo_identificacion_sri'] ?? '04',
                    "numero_identificacion" => $numeroIdentificacion,
                    "nombres" => trim($_POST['nombres']),
                    "apellidos" => trim($_POST['apellidos'] ?? ''),
                    "email" => trim($_POST['email'] ?? ''),
                    "telefono" => trim($_POST['telefono'] ?? ''),
                    "direccion" => trim($_POST['direccion'] ?? ''),
                    "estado" => isset($_POST['estado']) ? (int)$_POST['estado'] : 1
                );

                // Actualizar cliente
                $respuesta = ClientesModel::mdlActualizarCliente($datos, $idcliente, $_SESSION['tenant_id']);

                if ($respuesta) {
                    echo json_encode(array(
                        "status" => "success",
                        "message" => "Cliente actualizado exitosamente"
                    ));
                } else {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Error al actualizar el cliente"
                    ));
                }

            } catch (Exception $e) {
                error_log("Error en actualizarCliente: " . $e->getMessage());
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Error interno al actualizar el cliente"
                ));
            }
        }
    }

    /**
     * Eliminar cliente (soft delete)
     */
    public static function eliminarCliente() {
        if (!self::verificarSesion()) {
            return;
        }

        // Validar CSRF token
        if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                return;
            }
        }

        if (!isset($_POST['idcliente'])) {
            echo json_encode(array("status" => "error", "message" => "ID de cliente no especificado"));
            return;
        }

        try {
            $idcliente = (int)$_POST['idcliente'];

            // Verificar que el cliente pertenece al tenant
            $cliente = ClientesModel::mdlObtenerClientePorId($idcliente, $_SESSION['tenant_id']);
            if (!$cliente) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Cliente no encontrado"
                ));
                return;
            }

            // Proteger al consumidor final de ser eliminado
            if ($cliente['tipo_identificacion_sri'] == '07') {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "El cliente Consumidor Final no puede ser eliminado"
                ));
                return;
            }

            // Eliminar cliente
            $respuesta = ClientesModel::mdlEliminarCliente($idcliente, $_SESSION['tenant_id']);

            if ($respuesta) {
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Cliente eliminado exitosamente"
                ));
            } else {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Error al eliminar el cliente"
                ));
            }

        } catch (Exception $e) {
            error_log("Error en eliminarCliente: " . $e->getMessage());
            echo json_encode(array(
                "status" => "error",
                "message" => "Error interno al eliminar el cliente"
            ));
        }
    }

    /**
     * Verificar si existe un cliente con el mismo número de identificación
     */
    public static function verificarDuplicado() {
        if (!self::verificarSesion()) {
            return;
        }

        if (!isset($_POST['numero_identificacion'])) {
            echo json_encode(array("status" => "error", "message" => "Número de identificación no especificado"));
            return;
        }

        try {
            $numeroIdentificacion = trim($_POST['numero_identificacion']);
            $idclienteExcluir = isset($_POST['idcliente']) ? (int)$_POST['idcliente'] : null;

            // Verificar si existe
            $existe = ClientesModel::mdlVerificarClienteExistente(
                $numeroIdentificacion,
                $_SESSION['tenant_id'],
                $idclienteExcluir
            );

            echo json_encode(array(
                "status" => "success",
                "existe" => $existe
            ));

        } catch (Exception $e) {
            error_log("Error en verificarDuplicado: " . $e->getMessage());
            echo json_encode(array(
                "status" => "error",
                "message" => "Error al verificar duplicados"
            ));
        }
    }
}
?>
