<?php

// Limpiar cualquier output previo
if (ob_get_level()) {
    ob_end_clean();
}

// Establecer headers JSON
header('Content-Type: application/json; charset=utf-8');

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
require_once "../controllers/categorias.controller.php";
require_once "../models/categorias.model.php";

// Validar que las clases se hayan cargado correctamente
if (!class_exists('ControladorProductos')) {
    echo json_encode(array("success" => false, "message" => "Error: Controlador no encontrado"));
    exit;
}

if (!class_exists('ModeloProductos')) {
    echo json_encode(array("success" => false, "message" => "Error: Modelo no encontrado"));
    exit;
}

if (!class_exists('ControladorCategorias')) {
    echo json_encode(array("success" => false, "message" => "Error: Controlador de categorías no encontrado"));
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
    VERIFICAR SI CÓDIGO AUXILIAR EXISTE
    =============================================*/
    public function ajaxVerificarCodigoAuxiliar() {

        try {
            // Validar parámetros requeridos
            if (!isset($_POST["codigo_auxiliar"]) || empty($_POST["codigo_auxiliar"])) {
                echo json_encode(array("success" => false, "message" => "Código auxiliar es requerido"));
                return;
            }

            if (!isset($_POST["tenant_id"]) || empty($_POST["tenant_id"])) {
                echo json_encode(array("success" => false, "message" => "Tenant ID es requerido"));
                return;
            }

            $codigoAuxiliar = $_POST["codigo_auxiliar"];
            $tenantId = intval($_POST["tenant_id"]);
            $productoId = isset($_POST["producto_id"]) ? intval($_POST["producto_id"]) : null;

            $respuesta = ControladorProductos::ctrVerificarCodigoAuxiliarExiste($codigoAuxiliar, $tenantId, $productoId);

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

        try {
            // Manejar upload de imagen
            $nombreImagen = null;
            if (isset($_FILES["producto_imagen"]) && $_FILES["producto_imagen"]["error"] === UPLOAD_ERR_OK) {
                $nombreImagen = $this->procesarImagenProducto($_FILES["producto_imagen"], $_SESSION["tenant_id"]);
                if (!$nombreImagen) {
                    echo json_encode(array("success" => false, "message" => "Error al procesar la imagen"));
                    return;
                }
            }


            $datos = array(
                "codigo" => $_POST["codigo"],
                "codigo_auxiliar" => $_POST["codigo_auxiliar"] ?? null,
                "descripcion" => $_POST["descripcion"],
                "precio_de_venta" => $_POST["precio_de_venta"],
                "precio_de_compra" => $_POST["precio_de_compra"] ?? 0,
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
                "imagen" => $nombreImagen,
                "tipo_producto" => $_POST["tipo_producto"],
                "codigo_iva" => $_POST["codigo_iva"],
                "porcentaje_iva" => $_POST["porcentaje_iva"],
                "graba_ice" => isset($_POST["graba_ice"]) ? 1 : 0,
                "codigo_ice" => $_POST["codigo_ice"] ?? null,
                "porcentaje_ice" => $_POST["porcentaje_ice"] ?? 0,
                "es_material_construccion" => isset($_POST["es_material_construccion"]) ? 1 : 0,
                "codigo_material_construccion" => $_POST["codigo_material_construccion"] ?? null,
                "categoria_idcategoria" => $_POST["categoria_idcategoria"]
            );

            $respuesta = ControladorProductos::ctrCrearProducto($datos);
            echo json_encode($respuesta);

        } catch (Exception $e) {
            echo json_encode(array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }

    /*=============================================
    ELIMINAR IMAGEN ANTERIOR DEL SERVIDOR
    =============================================*/
    private function eliminarImagenAnterior($rutaImagen) {
        try {
            // Construir ruta completa desde la raíz del proyecto
            $rutaCompleta = "../" . $rutaImagen;

            // Verificar que el archivo existe y eliminarlo
            if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
                unlink($rutaCompleta);
                error_log("Imagen anterior eliminada: " . $rutaCompleta);
            }
        } catch (Exception $e) {
            // Solo log del error, no interrumpir el flujo principal
            error_log("Error al eliminar imagen anterior: " . $e->getMessage());
        }
    }

    /*=============================================
    PROCESAR IMAGEN DEL PRODUCTO (MULTITENANT)
    =============================================*/
    private function procesarImagenProducto($archivo, $tenantId) {

        try {
            // Validar archivo
            $tiposPermitidos = array('image/jpeg', 'image/png', 'image/webp', 'image/gif');
            if (!in_array($archivo["type"], $tiposPermitidos)) {
                return false;
            }

            // Validar tamaño (máximo 2MB)
            if ($archivo["size"] > 2 * 1024 * 1024) {
                return false;
            }

            // Crear directorio multitenant si no existe
            $directorioTenant = "../uploads/productos/tenant_" . $tenantId . "/";
            if (!is_dir($directorioTenant)) {
                mkdir($directorioTenant, 0755, true);
            }

            // Generar nombre único para la imagen
            $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
            $nombreArchivo = "producto_" . time() . "_" . rand(1000, 9999) . "." . $extension;
            $rutaCompleta = $directorioTenant . $nombreArchivo;

            // Mover archivo
            if (move_uploaded_file($archivo["tmp_name"], $rutaCompleta)) {
                // Retornar ruta relativa para la base de datos
                return "uploads/productos/tenant_" . $tenantId . "/" . $nombreArchivo;
            }

            return false;

        } catch (Exception $e) {
            error_log("Error al procesar imagen: " . $e->getMessage());
            return false;
        }
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

    /*=============================================
    OBTENER PRODUCTO POR ID
    =============================================*/
    public function ajaxObtenerProducto() {

        try {
            // Validar parámetros requeridos
            if (!isset($_POST["id"]) || empty($_POST["id"])) {
                echo json_encode(array("success" => false, "message" => "ID del producto es requerido"));
                return;
            }

            if (!isset($_POST["tenant_id"]) || empty($_POST["tenant_id"])) {
                echo json_encode(array("success" => false, "message" => "Tenant ID es requerido"));
                return;
            }

            $idProducto = intval($_POST["id"]);
            $tenantId = intval($_POST["tenant_id"]);

            $respuesta = ControladorProductos::ctrObtenerProductoPorId($idProducto, $tenantId);

            echo json_encode($respuesta);

        } catch (Exception $e) {
            echo json_encode(array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }

    /*=============================================
    ACTUALIZAR PRODUCTO
    =============================================*/
    public function ajaxActualizarProducto() {

        try {
            // Validar parámetros requeridos
            if (!isset($_POST["producto_id"]) || empty($_POST["producto_id"])) {
                echo json_encode(array("success" => false, "message" => "ID del producto es requerido"));
                return;
            }

            if (!isset($_SESSION["tenant_id"]) || empty($_SESSION["tenant_id"])) {
                echo json_encode(array("success" => false, "message" => "Tenant ID es requerido"));
                return;
            }

            $idProducto = intval($_POST["producto_id"]);
            $tenantId = $_SESSION["tenant_id"];

            // Manejar upload de imagen si se proporciona
            $nombreImagen = null;
            $actualizarImagen = false;
            $imagenAnterior = null;

            if (isset($_FILES["producto_imagen"]) && $_FILES["producto_imagen"]["error"] === UPLOAD_ERR_OK) {
                // Obtener imagen anterior para eliminarla después
                $productoActual = ControladorProductos::ctrObtenerProductoPorId($idProducto, $tenantId);
                if ($productoActual && $productoActual["success"] && !empty($productoActual["data"]["imagen"])) {
                    $imagenAnterior = $productoActual["data"]["imagen"];
                }

                $nombreImagen = $this->procesarImagenProducto($_FILES["producto_imagen"], $tenantId);
                if (!$nombreImagen) {
                    echo json_encode(array("success" => false, "message" => "Error al procesar la imagen"));
                    return;
                }
                $actualizarImagen = true;
            }

            // Preparar datos para actualización
            $datos = array(
                "codigo_auxiliar" => $_POST["codigo_auxiliar"] ?? null,
                "descripcion" => $_POST["descripcion"],
                "precio_de_venta" => $_POST["precio_de_venta"],
                "precio_de_compra" => $_POST["precio_de_compra"] ?? 0,
                "tiene_descuento" => isset($_POST["tiene_descuento"]) ? 1 : 0,
                "descuento_por_cantidad" => $_POST["descuento_por_cantidad"] ?? 0,
                "precio_con_descuento" => $_POST["precio_con_descuento"] ?? 0,
                "maneja_stock" => isset($_POST["maneja_stock"]) ? 1 : 0,
                "stock_actual" => $_POST["stock_actual"] ?? 0,
                "stock_minimo" => $_POST["stock_minimo"] ?? 0,
                "stock_maximo" => $_POST["stock_maximo"] ?? 0,
                "unidad_medida" => $_POST["unidad_medida"] ?? "Unidad",
                "peso" => $_POST["peso"] ?? 0,
                "tipo_producto" => $_POST["tipo_producto"],
                "codigo_iva" => $_POST["codigo_iva"],
                "porcentaje_iva" => $_POST["porcentaje_iva"],
                "graba_ice" => isset($_POST["graba_ice"]) ? 1 : 0,
                "codigo_ice" => $_POST["codigo_ice"] ?? null,
                "porcentaje_ice" => $_POST["porcentaje_ice"] ?? 0,
                "es_material_construccion" => isset($_POST["es_material_construccion"]) ? 1 : 0,
                "codigo_material_construccion" => $_POST["codigo_material_construccion"] ?? null,
                "categoria_idcategoria" => $_POST["categoria_idcategoria"],
                "estado" => $_POST["estado"] ?? 1,
                "updated_at" => date('Y-m-d H:i:s')
            );

            // Agregar imagen solo si se subió una nueva
            if ($actualizarImagen) {
                $datos["imagen"] = $nombreImagen;
            }

            $respuesta = ControladorProductos::ctrActualizarProducto($idProducto, $datos, $tenantId);

            // Si la actualización fue exitosa y se subió nueva imagen, eliminar la anterior
            if ($respuesta["success"] && $actualizarImagen && $imagenAnterior) {
                $this->eliminarImagenAnterior($imagenAnterior);
            }

            echo json_encode($respuesta);

        } catch (Exception $e) {
            error_log("Error en ajaxActualizarProducto: " . $e->getMessage());
            error_log("POST data: " . print_r($_POST, true));
            echo json_encode(array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage(),
                "debug" => $e->getFile() . ":" . $e->getLine()
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

        case "verificarCodigoAuxiliar":
            $ajax->ajaxVerificarCodigoAuxiliar();
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

        case "obtenerProducto":
            $ajax->ajaxObtenerProducto();
            break;

        case "actualizarProducto":
            $ajax->ajaxActualizarProducto();
            break;

        default:
            echo json_encode(array("success" => false, "message" => "Acción no válida"));
            break;
    }
}
?>