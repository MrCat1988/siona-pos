<?php

// Verificar desde donde se está llamando este archivo
if (file_exists("../controllers/sucursales.controller.php")) {
    require_once "../controllers/sucursales.controller.php";
} else {
    require_once "controllers/sucursales.controller.php";
}

class AjaxSucursales {

    public $idsucursal;
    public $nombre;
    public $direccion;
    public $telefono;
    public $estado;

    public function ajaxVerificarSesion() {
        SucursalesController::verificarSesion();
    }

    public function ajaxObtenerSucursales() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        SucursalesController::obtenerSucursales();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxObtenerSucursal() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        SucursalesController::obtenerSucursal();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxCrearSucursal() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        SucursalesController::crearSucursal();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxEditarSucursal() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        SucursalesController::editarSucursal();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxEliminarSucursal() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        SucursalesController::eliminarSucursal();

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
        SucursalesController::verificarCodigoSri();

        $output = ob_get_clean();
        echo $output;
    }
}

// Si se reciben datos via POST con accion
if (isset($_POST["accion"])) {
    $ajax = new AjaxSucursales();

    switch ($_POST["accion"]) {
        case "verificar_sesion":
            $ajax->ajaxVerificarSesion();
            break;
        case "obtener_sucursales":
            $ajax->ajaxObtenerSucursales();
            break;
        case "obtener_sucursal":
            $ajax->ajaxObtenerSucursal();
            break;
        case "crear_sucursal":
            $ajax->ajaxCrearSucursal();
            break;
        case "editar_sucursal":
            $ajax->ajaxEditarSucursal();
            break;
        case "eliminar_sucursal":
            $ajax->ajaxEliminarSucursal();
            break;
        case "verificar_codigo_sri":
            $ajax->ajaxVerificarCodigoSri();
            break;
    }
}

// Limpiar cualquier output al final
if (ob_get_level()) {
    ob_end_clean();
}
?>