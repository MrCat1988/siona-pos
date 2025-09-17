<?php

// Iniciar sesión y validaciones básicas
session_start();

// Validar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array("success" => false, "message" => "Método no permitido"));
    exit;
}

// Validar que exista una sesión activa
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== true) {
    echo json_encode(array("success" => false, "message" => "Sesión no válida"));
    exit;
}

require_once "../controllers/productos.controller.php";
require_once "../models/productos.model.php";

// Validar que las clases se hayan cargado correctamente
if (!class_exists('ControladorProductos')) {
    echo json_encode(array("success" => false, "message" => "Error: Controlador no encontrado"));
    exit;
}

if (!class_exists('ModeloProductos')) {
    echo json_encode(array("success" => false, "message" => "Error: Modelo no encontrado"));
    exit;
}

class AjaxProductos {

    /*=============================================
    OBTENER SIGUIENTE CÓDIGO SECUENCIAL
    =============================================*/
    public function ajaxObtenerSiguienteCodigo() {

        try {
            // Validar que se reciba tenant_id
            if (!isset($_POST["tenant_id"]) || empty($_POST["tenant_id"])) {
                echo json_encode(array("success" => false, "message" => "Tenant ID es requerido"));
                return;
            }

            $tenantId = intval($_POST["tenant_id"]);

            $respuesta = ControladorProductos::ctrObtenerSiguienteCodigo($tenantId);

            echo json_encode($respuesta);

        } catch (Exception $e) {
            echo json_encode(array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }

    /*=============================================
    VERIFICAR SI CÓDIGO EXISTE
    =============================================*/
    public function ajaxVerificarCodigo() {

        try {
            // Validar parámetros requeridos
            if (!isset($_POST["codigo"]) || empty($_POST["codigo"])) {
                echo json_encode(array("success" => false, "message" => "Código es requerido"));
                return;
            }

            if (!isset($_POST["tenant_id"]) || empty($_POST["tenant_id"])) {
                echo json_encode(array("success" => false, "message" => "Tenant ID es requerido"));
                return;
            }

            $codigo = $_POST["codigo"];
            $tenantId = intval($_POST["tenant_id"]);

            $respuesta = ControladorProductos::ctrVerificarCodigoExiste($codigo, $tenantId);

            echo json_encode($respuesta);

        } catch (Exception $e) {
            echo json_encode(array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }

    /*=============================================
    CREAR PRODUCTO
    =============================================*/
    public function ajaxCrearProducto() {

        $datos = array(
            "codigo" => $_POST["codigo"],
            "codigo_auxiliar" => $_POST["codigo_auxiliar"],
            "descripcion" => $_POST["descripcion"],
            "precio_de_venta" => $_POST["precio_de_venta"],
            "precio_de_compra" => $_POST["precio_de_compra"],
            "tiene_descuento" => isset($_POST["tiene_descuento"]) ? 1 : 0,
            "descuento_por_cantidad" => $_POST["descuento_por_cantidad"] ?? 0,
            "precio_con_descuento" => $_POST["precio_con_descuento"] ?? 0,
            "estado" => 1,
            "maneja_stock" => isset($_POST["maneja_stock"]) ? 1 : 0,
            "stock_actual" => $_POST["stock_actual"] ?? 0,
            "stock_minimo" => $_POST["stock_minimo"] ?? 0,
            "stock_maximo" => $_POST["stock_maximo"] ?? 0,
            "unidad_medida" => $_POST["unidad_medida"] ?? "Unidad",
            "peso" => $_POST["peso"] ?? 0,
            "imagen" => $_POST["imagen"] ?? null,
            "tipo_producto" => $_POST["tipo_producto"],
            "graba_iva" => isset($_POST["graba_iva"]) ? 1 : 0,
            "porcentaje_iva" => $_POST["porcentaje_iva"] ?? 0,
            "graba_ice" => isset($_POST["graba_ice"]) ? 1 : 0,
            "porcentaje_ice" => $_POST["porcentaje_ice"] ?? 0,
            "es_material_construccion" => 0,
            "codigo_material_construccion" => null,
            "categoria_idcategoria" => $_POST["categoria_idcategoria"]
        );

        $respuesta = ControladorProductos::ctrCrearProducto($datos);

        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER PRODUCTOS
    =============================================*/
    public function ajaxObtenerProductos() {

        $tenantId = $_POST["tenant_id"];
        $filtros = array();

        if (isset($_POST["categoria"])) {
            $filtros["categoria"] = $_POST["categoria"];
        }

        if (isset($_POST["estado"])) {
            $filtros["estado"] = $_POST["estado"];
        }

        if (isset($_POST["busqueda"])) {
            $filtros["busqueda"] = $_POST["busqueda"];
        }

        $respuesta = ControladorProductos::ctrObtenerProductos($tenantId, $filtros);

        echo json_encode($respuesta);
    }

    /*=============================================
    OBTENER CATEGORÍAS
    =============================================*/
    public function ajaxObtenerCategorias() {

        try {
            // Validar tenant_id
            if (!isset($_POST["tenant_id"]) || empty($_POST["tenant_id"])) {
                echo json_encode(array("success" => false, "message" => "Tenant ID es requerido"));
                return;
            }

            $tenantId = intval($_POST["tenant_id"]);

            $respuesta = ControladorCategorias::ctrObtenerCategorias($tenantId);

            echo json_encode($respuesta);

        } catch (Exception $e) {
            echo json_encode(array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }
}

/*=============================================
PROCESAR REQUESTS AJAX
=============================================*/

if (isset($_POST["action"])) {

    $ajax = new AjaxProductos();

    switch ($_POST["action"]) {
        case "obtenerSiguienteCodigo":
            $ajax->ajaxObtenerSiguienteCodigo();
            break;

        case "verificarCodigo":
            $ajax->ajaxVerificarCodigo();
            break;

        case "crearProducto":
            $ajax->ajaxCrearProducto();
            break;

        case "obtenerProductos":
            $ajax->ajaxObtenerProductos();
            break;

        case "obtenerCategorias":
            $ajax->ajaxObtenerCategorias();
            break;

        default:
            echo json_encode(array("success" => false, "message" => "Acción no válida"));
            break;
    }
}

?>