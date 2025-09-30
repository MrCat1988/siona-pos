<?php

// Verificar desde donde se está llamando este archivo
if (file_exists("../controllers/punto-de-emision.controller.php")) {
    require_once "../controllers/punto-de-emision.controller.php";
} else {
    require_once "controllers/punto-de-emision.controller.php";
}

class AjaxPuntoDeEmision {

    public $idpunto_de_emision;
    public $codigo_sri;
    public $descripcion;
    public $estado;

    public function ajaxVerificarSesion() {
        PuntoDeEmisionController::verificarSesion();
    }

    public function ajaxObtenerPuntosEmision() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        PuntoDeEmisionController::obtenerPuntosEmision();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxObtenerPuntoEmision() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        PuntoDeEmisionController::obtenerPuntoEmision();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxObtenerSucursales() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        PuntoDeEmisionController::obtenerSucursales();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxCrearPuntoEmision() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        PuntoDeEmisionController::crearPuntoEmision();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxEditarPuntoEmision() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        PuntoDeEmisionController::editarPuntoEmision();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxEliminarPuntoEmision() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        PuntoDeEmisionController::eliminarPuntoEmision();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxVerificarCodigoSri() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        PuntoDeEmisionController::verificarCodigoSri();

        $output = ob_get_clean();
        echo $output;
    }
}

// Si se reciben datos via POST con accion
if (isset($_POST["accion"])) {
    error_log("AJAX punto-de-emision - Accion recibida: " . $_POST["accion"]);
    error_log("AJAX punto-de-emision - POST completo: " . print_r($_POST, true));

    $ajax = new AjaxPuntoDeEmision();

    switch ($_POST["accion"]) {
        case "verificar_sesion":
            $ajax->ajaxVerificarSesion();
            break;
        case "obtener_puntos_emision":
            $ajax->ajaxObtenerPuntosEmision();
            break;
        case "obtener_punto_emision":
            $ajax->ajaxObtenerPuntoEmision();
            break;
        case "obtener_sucursales":
            $ajax->ajaxObtenerSucursales();
            break;
        case "crear_punto_emision":
            error_log("AJAX punto-de-emision - Ejecutando crear_punto_emision");
            $ajax->ajaxCrearPuntoEmision();
            break;
        case "editar_punto_emision":
            $ajax->ajaxEditarPuntoEmision();
            break;
        case "eliminar_punto_emision":
            $ajax->ajaxEliminarPuntoEmision();
            break;
        case "verificar_codigo_sri":
            $ajax->ajaxVerificarCodigoSri();
            break;
        default:
            error_log("AJAX punto-de-emision - Accion no reconocida: " . $_POST["accion"]);
            echo json_encode(array("status" => "error", "message" => "Acción no válida"));
            break;
    }
} else {
    error_log("AJAX punto-de-emision - No se recibió parámetro 'accion'");
}
?>