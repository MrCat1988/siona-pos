<?php

session_start();

require_once "../models/producto-sucursal.model.php";
require_once "../controllers/producto-sucursal.controller.php";

class AjaxProductoSucursal {

    /*=============================================
    OBTENER PRODUCTOS POR SUCURSAL
    =============================================*/
    public function obtenerProductosSucursal() {


        if (!isset($_SESSION["tenant_id"])) {
            echo json_encode(array(
                "success" => false,
                "message" => "Sesión no válida"
            ));
            return;
        }

        $filtros = array();

        // DEBUG TEMPORAL - Ver filtros recibidos
        error_log("AJAX filtros recibidos POST: " . json_encode($_POST));

        // Aplicar filtros desde el frontend
        if (!empty($_POST["sucursal"])) {
            $filtros["sucursal"] = $_POST["sucursal"];
        }

        if (!empty($_POST["categoria"])) {
            $filtros["categoria"] = $_POST["categoria"];
        }

        if (!empty($_POST["busqueda"])) {
            $filtros["busqueda"] = $_POST["busqueda"];
        }

        if (isset($_POST["estado"]) && $_POST["estado"] !== "") {
            $filtros["estado"] = $_POST["estado"];
        }

        // Parámetros de paginación
        if (isset($_POST["limite"]) && is_numeric($_POST["limite"])) {
            $filtros["limite"] = intval($_POST["limite"]);
        }

        if (isset($_POST["offset"]) && is_numeric($_POST["offset"])) {
            $filtros["offset"] = intval($_POST["offset"]);
        }

        $respuesta = ControladorProductoSucursal::ctrObtenerProductosSucursal($_SESSION["tenant_id"], $filtros);


        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER PRODUCTO-SUCURSAL POR ID
    =============================================*/
    public function obtenerProductoSucursalPorId() {

        if (!isset($_SESSION["tenant_id"]) || !isset($_POST["id"])) {
            echo json_encode(array(
                "success" => false,
                "message" => "Parámetros no válidos"
            ));
            return;
        }

        $respuesta = ControladorProductoSucursal::ctrObtenerProductoSucursalPorId($_POST["id"], $_SESSION["tenant_id"]);

        echo json_encode($respuesta);
    }

    /*=============================================
    CREAR PRODUCTO-SUCURSAL
    =============================================*/
    public function crearProductoSucursal() {

        if (!isset($_SESSION["tenant_id"])) {
            echo json_encode(array(
                "success" => false,
                "message" => "Sesión no válida"
            ));
            return;
        }

        $datos = array(
            "productos_idproducto" => $_POST["productos_idproducto"] ?? null,
            "sucursal_idsucursal" => $_POST["sucursal_idsucursal"] ?? null,
            "precio_sucursal" => $_POST["precio_sucursal"] ?? "0.00000",
            "stock_sucursal" => $_POST["stock_sucursal"] ?? "0",
            "stock_minimo_sucursal" => $_POST["stock_minimo_sucursal"] ?? "0",
            "stock_maximo_sucursal" => $_POST["stock_maximo_sucursal"] ?? "0",
            "estado" => $_POST["estado"] ?? "1"
        );

        $respuesta = ControladorProductoSucursal::ctrCrearProductoSucursal($datos);

        echo json_encode($respuesta);
    }

    /*=============================================
    ACTUALIZAR PRODUCTO-SUCURSAL
    =============================================*/
    public function actualizarProductoSucursal() {

        if (!isset($_SESSION["tenant_id"]) || !isset($_POST["idproducto_sucursal"])) {
            echo json_encode(array(
                "success" => false,
                "message" => "Parámetros no válidos"
            ));
            return;
        }

        $datos = array();

        // Solo incluir campos que se van a actualizar
        if (isset($_POST["productos_idproducto"])) {
            $datos["productos_idproducto"] = $_POST["productos_idproducto"];
        }

        if (isset($_POST["sucursal_idsucursal"])) {
            $datos["sucursal_idsucursal"] = $_POST["sucursal_idsucursal"];
        }

        if (isset($_POST["precio_sucursal"])) {
            $datos["precio_sucursal"] = $_POST["precio_sucursal"];
        }

        if (isset($_POST["stock_sucursal"])) {
            $datos["stock_sucursal"] = $_POST["stock_sucursal"];
        }

        if (isset($_POST["stock_minimo_sucursal"])) {
            $datos["stock_minimo_sucursal"] = $_POST["stock_minimo_sucursal"];
        }

        if (isset($_POST["stock_maximo_sucursal"])) {
            $datos["stock_maximo_sucursal"] = $_POST["stock_maximo_sucursal"];
        }

        if (isset($_POST["estado"])) {
            $datos["estado"] = $_POST["estado"];
        }

        $respuesta = ControladorProductoSucursal::ctrActualizarProductoSucursal(
            $_POST["idproducto_sucursal"],
            $datos,
            $_SESSION["tenant_id"]
        );

        echo json_encode($respuesta);
    }

    /*=============================================
    ELIMINAR PRODUCTO-SUCURSAL
    =============================================*/
    public function eliminarProductoSucursal() {

        if (!isset($_SESSION["tenant_id"]) || !isset($_POST["idproducto_sucursal"])) {
            echo json_encode(array(
                "success" => false,
                "message" => "Parámetros no válidos"
            ));
            return;
        }

        $respuesta = ControladorProductoSucursal::ctrEliminarProductoSucursal(
            $_POST["idproducto_sucursal"],
            $_SESSION["tenant_id"]
        );

        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER PRODUCTOS DISPONIBLES
    =============================================*/
    public function obtenerProductosDisponibles() {

        if (!isset($_SESSION["tenant_id"]) || !isset($_POST["sucursal_id"])) {
            echo json_encode(array(
                "success" => false,
                "message" => "Parámetros no válidos"
            ));
            return;
        }

        $searchTerm = isset($_POST["search"]) ? trim($_POST["search"]) : '';

        $respuesta = ControladorProductoSucursal::ctrObtenerProductosDisponibles(
            $_POST["sucursal_id"],
            $_SESSION["tenant_id"],
            $searchTerm
        );

        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER SUCURSALES DISPONIBLES
    =============================================*/
    public function obtenerSucursalesDisponibles() {

        if (!isset($_SESSION["tenant_id"])) {
            echo json_encode(array(
                "success" => false,
                "message" => "Sesión no válida"
            ));
            return;
        }

        $respuesta = ControladorProductoSucursal::ctrObtenerSucursalesDisponibles($_SESSION["tenant_id"]);

        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER CATEGORÍAS PARA FILTROS
    =============================================*/
    public function obtenerCategoriasParaFiltros() {

        if (!isset($_SESSION["tenant_id"])) {
            echo json_encode(array(
                "success" => false,
                "message" => "Sesión no válida"
            ));
            return;
        }

        // Reutilizar el modelo de categorías existente
        require_once "../models/categorias.model.php";
        require_once "../controllers/categorias.controller.php";

        $respuesta = ControladorCategorias::ctrObtenerCategorias($_SESSION["tenant_id"]);

        echo json_encode($respuesta);
    }
}

// Procesar la acción solicitada
if (isset($_POST["accion"])) {
    $ajax = new AjaxProductoSucursal();

    switch ($_POST["accion"]) {
        case "obtener_productos_sucursal":
            $ajax->obtenerProductosSucursal();
            break;

        case "obtener_producto_sucursal_por_id":
            $ajax->obtenerProductoSucursalPorId();
            break;

        case "crear_producto_sucursal":
            $ajax->crearProductoSucursal();
            break;

        case "actualizar_producto_sucursal":
            $ajax->actualizarProductoSucursal();
            break;

        case "eliminar_producto_sucursal":
            $ajax->eliminarProductoSucursal();
            break;

        case "obtener_productos_disponibles":
            $ajax->obtenerProductosDisponibles();
            break;

        case "obtener_sucursales_disponibles":
            $ajax->obtenerSucursalesDisponibles();
            break;

        case "obtener_categorias_para_filtros":
            $ajax->obtenerCategoriasParaFiltros();
            break;

        default:
            echo json_encode(array(
                "success" => false,
                "message" => "Acción no válida"
            ));
            break;
    }
}

?>