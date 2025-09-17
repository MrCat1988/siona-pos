<?php

// Verificar desde donde se está llamando este archivo
if (file_exists("../controllers/categorias.controller.php")) {
    require_once "../controllers/categorias.controller.php";
} else {
    require_once "controllers/categorias.controller.php";
}

class AjaxCategorias {

    public $idcategoria;
    public $nombre;
    public $descripcion;
    public $estado;

    public function ajaxVerificarSesion() {
        CategoriasController::verificarSesion();
    }

    public function ajaxObtenerCategorias() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        CategoriasController::obtenerCategorias();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxObtenerCategoria() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        CategoriasController::obtenerCategoria();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxCrearCategoria() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        CategoriasController::crearCategoria();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxEditarCategoria() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        CategoriasController::editarCategoria();

        $output = ob_get_clean();
        echo $output;
    }

    public function ajaxEliminarCategoria() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        header('Content-Type: application/json; charset=utf-8');
        CategoriasController::eliminarCategoria();

        $output = ob_get_clean();
        echo $output;
    }
}

// Si se reciben datos via POST con accion
if (isset($_POST["accion"])) {
    $ajax = new AjaxCategorias();

    switch ($_POST["accion"]) {
        case "verificar_sesion":
            $ajax->ajaxVerificarSesion();
            break;
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
    }
}

// Limpiar cualquier output al final
if (ob_get_level()) {
    ob_end_clean();
}
?>