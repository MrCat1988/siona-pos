<?php

// Verificar desde donde se está llamando este archivo
if (file_exists("../models/punto-de-emision.model.php")) {
    require_once "../models/punto-de-emision.model.php";
    require_once "../models/sucursales.model.php";
    require_once "../models/connection.php";
} else {
    require_once "models/punto-de-emision.model.php";
    require_once "models/sucursales.model.php";
    require_once "models/connection.php";
}

class PuntoDeEmisionController {

    // Verificar sesión activa
    public static function verificarSesion() {
        session_start();
        if (isset($_SESSION['login_status']) && $_SESSION['login_status'] == true) {
            echo json_encode(array("status" => "active", "usuario" => $_SESSION['usuario_nombre']));
        } else {
            echo json_encode(array("status" => "inactive"));
        }
    }

    // Obtener todos los puntos de emisión
    public static function obtenerPuntosEmision() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['tenant_id'])) {
                echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
                return;
            }

            $incluir_eliminados = isset($_POST['incluir_eliminados']) && $_POST['incluir_eliminados'] === 'true';

            $puntosEmision = PuntoDeEmisionModel::mdlCargarPuntosEmision(
                "punto_de_emision",
                $incluir_eliminados,
                $_SESSION['tenant_id']
            );

            if ($puntosEmision === false) {
                echo json_encode(array("status" => "error", "message" => "Error al consultar base de datos"));
                return;
            }

            echo json_encode(array("status" => "success", "data" => $puntosEmision));

        } catch (Exception $e) {
            error_log("Error en obtenerPuntosEmision controller: " . $e->getMessage());
            echo json_encode(array("status" => "error", "message" => "Error interno del servidor: " . $e->getMessage()));
        }
    }

    // Obtener un punto de emisión específico
    public static function obtenerPuntoEmision() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (isset($_POST['idpunto_de_emision'])) {
            $idpunto_de_emision = $_POST['idpunto_de_emision'];
            $puntoEmision = PuntoDeEmisionModel::mdlObtenerPuntoEmision(
                "punto_de_emision",
                "idpunto_de_emision",
                $idpunto_de_emision,
                $_SESSION['tenant_id']
            );

            if ($puntoEmision) {
                echo json_encode(array("status" => "success", "data" => $puntoEmision));
            } else {
                echo json_encode(array("status" => "error", "message" => "Punto de emisión no encontrado"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "ID de punto de emisión requerido"));
        }
    }

    // Obtener sucursales activas para el selector
    public static function obtenerSucursales() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        try {
            $sucursales = SucursalesModel::obtenerSucursales($_SESSION['tenant_id'], 1, 100, 1, false);

            if ($sucursales && isset($sucursales['sucursales'])) {
                echo json_encode(array("status" => "success", "data" => $sucursales['sucursales']));
            } else {
                echo json_encode(array("status" => "error", "message" => "No se encontraron sucursales"));
            }
        } catch (Exception $e) {
            error_log("Error en obtenerSucursales: " . $e->getMessage());
            echo json_encode(array("status" => "error", "message" => "Error al obtener sucursales"));
        }
    }

    // Crear nuevo punto de emisión
    public static function crearPuntoEmision() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        error_log("crearPuntoEmision - Iniciando...");
        error_log("crearPuntoEmision - POST data: " . print_r($_POST, true));

        if (!isset($_SESSION['tenant_id'])) {
            error_log("crearPuntoEmision - Error: Sesión no válida");
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        error_log("crearPuntoEmision - tenant_id: " . $_SESSION['tenant_id']);

        if (!empty($_POST)) {
            // Validar CSRF token si está disponible
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    error_log("crearPuntoEmision - Error: Token CSRF inválido");
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }

            // Validar datos requeridos
            $errores = [];
            if (empty($_POST['codigo_sri'])) $errores[] = "Código SRI";
            if (empty($_POST['descripcion'])) $errores[] = "Descripción";
            if (empty($_POST['sucursal_idsucursal'])) $errores[] = "Sucursal";

            if (!empty($errores)) {
                $mensaje = "Los siguientes campos son requeridos: " . implode(", ", $errores);
                error_log("crearPuntoEmision - Error: " . $mensaje);
                echo json_encode(array("status" => "error", "message" => $mensaje));
                return;
            }

            // Validar formato del código SRI (3 dígitos)
            if (!preg_match('/^[0-9]{3}$/', $_POST['codigo_sri'])) {
                error_log("crearPuntoEmision - Error: Formato de código SRI inválido");
                echo json_encode(array("status" => "error", "message" => "El código SRI debe tener 3 dígitos numéricos"));
                return;
            }

            // Verificar que la sucursal pertenezca al tenant
            error_log("crearPuntoEmision - Verificando sucursal: " . $_POST['sucursal_idsucursal']);
            $sucursal = SucursalesModel::obtenerSucursal($_POST['sucursal_idsucursal'], $_SESSION['tenant_id']);
            if (!$sucursal) {
                error_log("crearPuntoEmision - Error: Sucursal no válida");
                echo json_encode(array("status" => "error", "message" => "Sucursal no válida"));
                return;
            }

            // Verificar que no exista un punto de emisión con el mismo código en la sucursal
            error_log("crearPuntoEmision - Verificando código existente...");
            $codigoExiste = PuntoDeEmisionModel::mdlVerificarCodigoExistente(
                "punto_de_emision",
                $_POST['codigo_sri'],
                $_POST['sucursal_idsucursal'],
                null,
                $_SESSION['tenant_id']
            );

            if ($codigoExiste) {
                error_log("crearPuntoEmision - Error: Código ya existe");
                echo json_encode(array("status" => "error", "message" => "Ya existe un punto de emisión con este código en la sucursal seleccionada"));
                return;
            }

            $datos = array(
                "codigo_sri" => $_POST['codigo_sri'],
                "descripcion" => $_POST['descripcion'],
                "secuencial_factura" => $_POST['secuencial_factura'] ?? 1,
                "secuencial_nota_credito" => $_POST['secuencial_nota_credito'] ?? 1,
                "secuencial_nota_debito" => $_POST['secuencial_nota_debito'] ?? 1,
                "secuencial_guia_remision" => $_POST['secuencial_guia_remision'] ?? 1,
                "secuencial_retencion" => $_POST['secuencial_retencion'] ?? 1,
                "estado" => $_POST['estado'] ?? 1,
                "sucursal_idsucursal" => $_POST['sucursal_idsucursal']
            );

            error_log("crearPuntoEmision - Datos a insertar: " . print_r($datos, true));

            try {
                $respuesta = PuntoDeEmisionModel::mdlCrearPuntoEmision("punto_de_emision", $datos);
                error_log("crearPuntoEmision - Respuesta del model: " . print_r($respuesta, true));

                if ($respuesta) {
                    error_log("crearPuntoEmision - Éxito. ID: " . $respuesta);
                    echo json_encode(array("status" => "success", "message" => "Punto de emisión creado exitosamente", "id" => $respuesta));
                } else {
                    error_log("crearPuntoEmision - Error: El model retornó false");
                    echo json_encode(array("status" => "error", "message" => "Error al crear punto de emisión"));
                }
            } catch (Exception $e) {
                error_log("crearPuntoEmision - Excepción: " . $e->getMessage());
                echo json_encode(array("status" => "error", "message" => "Error al crear punto de emisión: " . $e->getMessage()));
            }
        } else {
            error_log("crearPuntoEmision - Error: No se recibieron datos POST");
            echo json_encode(array("status" => "error", "message" => "No se recibieron datos"));
        }
    }

    // Editar punto de emisión
    public static function editarPuntoEmision() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if ($_POST) {
            // Validar CSRF token si está disponible
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }

            // Validar datos requeridos
            if (empty($_POST['idpunto_de_emision']) || empty($_POST['codigo_sri']) ||
                empty($_POST['descripcion']) || empty($_POST['sucursal_idsucursal'])) {
                echo json_encode(array("status" => "error", "message" => "ID, código SRI, descripción y sucursal son requeridos"));
                return;
            }

            $idpunto_de_emision = $_POST['idpunto_de_emision'];

            // Validar formato del código SRI
            if (!preg_match('/^[0-9]{3}$/', $_POST['codigo_sri'])) {
                echo json_encode(array("status" => "error", "message" => "El código SRI debe tener 3 dígitos numéricos"));
                return;
            }

            // Verificar que el punto de emisión pertenezca al tenant
            $puntoEmisionActual = PuntoDeEmisionModel::mdlObtenerPuntoEmision(
                "punto_de_emision",
                "idpunto_de_emision",
                $idpunto_de_emision,
                $_SESSION['tenant_id']
            );

            if (!$puntoEmisionActual) {
                echo json_encode(array("status" => "error", "message" => "Punto de emisión no válido"));
                return;
            }

            // Verificar que la sucursal pertenezca al tenant
            $sucursal = SucursalesModel::obtenerSucursal($_POST['sucursal_idsucursal'], $_SESSION['tenant_id']);
            if (!$sucursal) {
                echo json_encode(array("status" => "error", "message" => "Sucursal no válida"));
                return;
            }

            // Verificar que no exista otro punto de emisión con el mismo código en la sucursal
            $codigoExiste = PuntoDeEmisionModel::mdlVerificarCodigoExistente(
                "punto_de_emision",
                $_POST['codigo_sri'],
                $_POST['sucursal_idsucursal'],
                $idpunto_de_emision,
                $_SESSION['tenant_id']
            );

            if ($codigoExiste) {
                echo json_encode(array("status" => "error", "message" => "Ya existe otro punto de emisión con este código en la sucursal seleccionada"));
                return;
            }

            $datos = array(
                "idpunto_de_emision" => $idpunto_de_emision,
                "codigo_sri" => $_POST['codigo_sri'],
                "descripcion" => $_POST['descripcion'],
                "secuencial_factura" => $_POST['secuencial_factura'] ?? 1,
                "secuencial_nota_credito" => $_POST['secuencial_nota_credito'] ?? 1,
                "secuencial_nota_debito" => $_POST['secuencial_nota_debito'] ?? 1,
                "secuencial_guia_remision" => $_POST['secuencial_guia_remision'] ?? 1,
                "secuencial_retencion" => $_POST['secuencial_retencion'] ?? 1,
                "estado" => $_POST['estado'] ?? 1,
                "sucursal_idsucursal" => $_POST['sucursal_idsucursal']
            );

            $respuesta = PuntoDeEmisionModel::mdlActualizarPuntoEmision("punto_de_emision", $datos);

            if ($respuesta) {
                echo json_encode(array("status" => "success", "message" => "Punto de emisión actualizado exitosamente"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al actualizar punto de emisión"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "No se recibieron datos"));
        }
    }

    // Eliminar punto de emisión
    public static function eliminarPuntoEmision() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (isset($_POST['idpunto_de_emision'])) {
            $idpunto_de_emision = $_POST['idpunto_de_emision'];

            // Validar CSRF token si está disponible
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }

            // Verificar que el punto de emisión pertenezca al tenant
            $puntoEmision = PuntoDeEmisionModel::mdlObtenerPuntoEmision(
                "punto_de_emision",
                "idpunto_de_emision",
                $idpunto_de_emision,
                $_SESSION['tenant_id']
            );

            if (!$puntoEmision) {
                echo json_encode(array("status" => "error", "message" => "Punto de emisión no válido"));
                return;
            }

            // Verificar si tiene facturas asociadas
            $tieneFacturas = PuntoDeEmisionModel::mdlVerificarFacturasAsociadas($idpunto_de_emision);

            if ($tieneFacturas) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "No se puede eliminar el punto de emisión porque tiene facturas asociadas. Se recomienda desactivarlo en su lugar."
                ));
                return;
            }

            // Proceder con la eliminación (soft delete)
            $respuesta = PuntoDeEmisionModel::mdlEliminarPuntoEmision("punto_de_emision", $idpunto_de_emision);

            if ($respuesta) {
                echo json_encode(array("status" => "success", "message" => "Punto de emisión eliminado exitosamente"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al eliminar punto de emisión"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "ID de punto de emisión requerido"));
        }
    }

    // Verificar disponibilidad de código SRI en una sucursal
    public static function verificarCodigoSri() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (isset($_POST['codigo_sri']) && isset($_POST['sucursal_id'])) {
            $codigo_sri = $_POST['codigo_sri'];
            $sucursal_id = $_POST['sucursal_id'];
            $excluir_id = isset($_POST['excluir_id']) ? $_POST['excluir_id'] : null;

            // Validar formato del código SRI (3 dígitos)
            if (!preg_match('/^[0-9]{3}$/', $codigo_sri)) {
                echo json_encode(array("status" => "error", "message" => "Código SRI debe tener 3 dígitos"));
                return;
            }

            $existe = PuntoDeEmisionModel::mdlVerificarCodigoExistente(
                "punto_de_emision",
                $codigo_sri,
                $sucursal_id,
                $excluir_id,
                $_SESSION['tenant_id']
            );

            echo json_encode(array(
                "status" => "success",
                "disponible" => !$existe,
                "codigo" => $codigo_sri
            ));
        } else {
            echo json_encode(array("status" => "error", "message" => "Código SRI y sucursal requeridos"));
        }
    }
}

?>