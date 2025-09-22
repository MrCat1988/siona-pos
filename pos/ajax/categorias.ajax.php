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
    echo json_encode(array("status" => "error", "message" => "Método no permitido"));
    exit;
}

// Validar que exista una sesión activa
if (!isset($_SESSION["login_status"]) || $_SESSION["login_status"] !== true) {
    echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
    exit;
}

require_once "../controllers/categorias.controller.php";
require_once "../models/categorias.model.php";

// Validar que las clases se hayan cargado correctamente
if (!class_exists('ControladorCategorias')) {
    echo json_encode(array("status" => "error", "message" => "Error: Controlador no encontrado"));
    exit;
}

if (!class_exists('ModeloCategorias')) {
    echo json_encode(array("status" => "error", "message" => "Error: Modelo no encontrado"));
    exit;
}

class AjaxCategorias {

    /*=============================================
    OBTENER CATEGORÍAS
    =============================================*/
    public function ajaxObtenerCategorias() {
        try {
            // Validar tenant_id
            if (!isset($_SESSION["tenant_id"]) || empty($_SESSION["tenant_id"])) {
                echo json_encode(array("status" => "error", "message" => "Tenant ID es requerido"));
                return;
            }

            // Obtener parámetros de paginación y filtros
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 6;
            $estado = isset($_POST['estado']) && $_POST['estado'] !== '' ? (int)$_POST['estado'] : null;
            $incluir_eliminadas = isset($_POST['incluir_eliminadas']) && $_POST['incluir_eliminadas'] === 'true';

            // Validar parámetros
            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 50) $limit = 6;

            $tenantId = $_SESSION["tenant_id"];
            $resultado = ModeloCategorias::obtenerCategorias($tenantId, $page, $limit, $estado, $incluir_eliminadas);

            if ($resultado === false || !isset($resultado['categorias'])) {
                echo json_encode(array("status" => "error", "message" => "Error al consultar base de datos"));
                return;
            }

            echo json_encode(array(
                "status" => "success",
                "data" => $resultado
            ));

        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }

    /*=============================================
    OBTENER CATEGORÍA POR ID
    =============================================*/
    public function ajaxObtenerCategoria() {
        try {
            // Validar parámetros requeridos
            if (!isset($_POST["idcategoria"]) || empty($_POST["idcategoria"])) {
                echo json_encode(array("status" => "error", "message" => "ID de categoría es requerido"));
                return;
            }

            if (!isset($_SESSION["tenant_id"]) || empty($_SESSION["tenant_id"])) {
                echo json_encode(array("status" => "error", "message" => "Tenant ID es requerido"));
                return;
            }

            $idcategoria = intval($_POST["idcategoria"]);
            $tenantId = $_SESSION["tenant_id"];

            $categoria = ModeloCategorias::obtenerCategoriaPorId($idcategoria, $tenantId);

            if ($categoria) {
                echo json_encode(array(
                    "status" => "success",
                    "data" => $categoria
                ));
            } else {
                echo json_encode(array("status" => "error", "message" => "Categoría no encontrada"));
            }

        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }

    /*=============================================
    CREAR CATEGORÍA
    =============================================*/
    public function ajaxCrearCategoria() {
        try {
            // Validar parámetros requeridos
            if (!isset($_POST["nombre"]) || empty($_POST["nombre"])) {
                echo json_encode(array("status" => "error", "message" => "Nombre es requerido"));
                return;
            }

            if (!isset($_SESSION["tenant_id"]) || empty($_SESSION["tenant_id"])) {
                echo json_encode(array("status" => "error", "message" => "Tenant ID es requerido"));
                return;
            }

            $datos = array(
                "nombre" => trim($_POST["nombre"]),
                "descripcion" => isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : null,
                "estado" => isset($_POST["estado"]) ? intval($_POST["estado"]) : 1,
                "tenant_id" => $_SESSION["tenant_id"],
                "created_at" => date('Y-m-d H:i:s')
            );

            $resultado = ModeloCategorias::crearCategoria($datos);

            if ($resultado) {
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Categoría creada exitosamente",
                    "id" => $resultado
                ));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al crear categoría"));
            }

        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }

    /*=============================================
    EDITAR CATEGORÍA
    =============================================*/
    public function ajaxEditarCategoria() {
        try {
            // Validar parámetros requeridos
            if (!isset($_POST["idcategoria"]) || empty($_POST["idcategoria"])) {
                echo json_encode(array("status" => "error", "message" => "ID de categoría es requerido"));
                return;
            }

            if (!isset($_POST["nombre"]) || empty($_POST["nombre"])) {
                echo json_encode(array("status" => "error", "message" => "Nombre es requerido"));
                return;
            }

            if (!isset($_SESSION["tenant_id"]) || empty($_SESSION["tenant_id"])) {
                echo json_encode(array("status" => "error", "message" => "Tenant ID es requerido"));
                return;
            }

            $idcategoria = intval($_POST["idcategoria"]);
            $tenantId = $_SESSION["tenant_id"];

            $datos = array(
                "nombre" => trim($_POST["nombre"]),
                "descripcion" => isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : null,
                "estado" => isset($_POST["estado"]) ? intval($_POST["estado"]) : 1,
                "updated_at" => date('Y-m-d H:i:s')
            );

            $resultado = ModeloCategorias::editarCategoria($idcategoria, $datos, $tenantId);

            if ($resultado) {
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Categoría actualizada exitosamente"
                ));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al actualizar categoría"));
            }

        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }

    /*=============================================
    ELIMINAR CATEGORÍA
    =============================================*/
    public function ajaxEliminarCategoria() {
        try {
            // Validar parámetros requeridos
            if (!isset($_POST["idcategoria"]) || empty($_POST["idcategoria"])) {
                echo json_encode(array("status" => "error", "message" => "ID de categoría es requerido"));
                return;
            }

            if (!isset($_SESSION["tenant_id"]) || empty($_SESSION["tenant_id"])) {
                echo json_encode(array("status" => "error", "message" => "Tenant ID es requerido"));
                return;
            }

            $idcategoria = intval($_POST["idcategoria"]);
            $tenantId = $_SESSION["tenant_id"];

            $resultado = ModeloCategorias::eliminarCategoria($idcategoria, $tenantId);

            if ($resultado) {
                echo json_encode(array(
                    "status" => "success",
                    "message" => "Categoría eliminada exitosamente"
                ));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al eliminar categoría"));
            }

        } catch (Exception $e) {
            echo json_encode(array(
                "status" => "error",
                "message" => "Error interno: " . $e->getMessage()
            ));
        }
    }
}

/*=============================================
PROCESAR REQUESTS AJAX
=============================================*/

if (isset($_POST["accion"])) {

    $ajax = new AjaxCategorias();

    switch ($_POST["accion"]) {
        case "obtener_categorias":
            $ajax->ajaxObtenerCategorias();
            break;

        case "obtener_categoria":
            $ajax->ajaxObtenerCategoria();
            break;

        case "crear_categoria":
            $ajax->ajaxCrearCategoria();
            break;

        case "editar_categoria":
            $ajax->ajaxEditarCategoria();
            break;

        case "eliminar_categoria":
            $ajax->ajaxEliminarCategoria();
            break;

        default:
            echo json_encode(array("status" => "error", "message" => "Acción no válida"));
            break;
    }
}
?>