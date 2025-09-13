<?php

// Suprimir notices y warnings para respuestas JSON limpias
error_reporting(E_ERROR | E_PARSE);

// Iniciar output buffering para respuestas JSON limpias
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../models/connection.php";
require_once "../models/usuarios.model.php";
if (file_exists("../models/security.model.php")) {
    require_once "../models/security.model.php";
}
require_once "../controllers/usuarios.controller.php";

class AjaxUsuarios {
    
    public $email;
    public $password;
    public $accion;
    
    public function ajaxLogin() {
        UsuariosController::login();
    }
    
    public function ajaxLogout() {
        UsuariosController::logout();
    }
    
    public function ajaxVerificarSesion() {
        UsuariosController::verificarSesion();
    }
    
    public function ajaxObtenerUsuarios() {
        UsuariosController::obtenerUsuarios();
    }
    
    public function ajaxCrearUsuario() {
        UsuariosController::crearUsuario();
    }
    
    public function ajaxObtenerUsuario() {
        // Limpiar cualquier output previo
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        header('Content-Type: application/json; charset=utf-8');
        UsuariosController::obtenerUsuario();
        
        $output = ob_get_clean();
        echo $output;
    }
    
    public function ajaxEditarUsuario() {
        UsuariosController::editarUsuario();
    }
    
    public function ajaxEliminarUsuario() {
        UsuariosController::eliminarUsuario();
    }
    
    public function ajaxGetSalt() {
        if (isset($_POST['email'])) {
            $salt = UsuariosModel::getSaltForEmail($_POST['email']);
            echo json_encode(array("status" => "success", "salt" => $salt));
        } else {
            echo json_encode(array("status" => "error", "message" => "Email requerido"));
        }
    }
    
    public function ajaxObtenerSucursales() {
        UsuariosController::obtenerSucursales();
    }
    
    public function ajaxVerificarEmail() {
        UsuariosController::verificarEmailDisponible();
    }
}

if (isset($_POST["email"]) && isset($_POST["password"]) && !isset($_POST["accion"])) {
    $login = new AjaxUsuarios();
    $login->email = $_POST["email"];
    $login->password = $_POST["password"];
    $login->ajaxLogin();
}

if (isset($_POST["accion"])) {
    $ajax = new AjaxUsuarios();
    $ajax->accion = $_POST["accion"];
    
    switch ($_POST["accion"]) {
        case "logout":
            $ajax->ajaxLogout();
            break;
        case "verificar_sesion":
            $ajax->ajaxVerificarSesion();
            break;
        case "obtener_usuarios":
            $ajax->ajaxObtenerUsuarios();
            break;
        case "crear_usuario":
            $ajax->ajaxCrearUsuario();
            break;
        case "obtener_usuario":
            $ajax->ajaxObtenerUsuario();
            break;
        case "editar_usuario":
            $ajax->ajaxEditarUsuario();
            break;
        case "eliminar_usuario":
            $ajax->ajaxEliminarUsuario();
            break;
        case "get_salt":
            $ajax->ajaxGetSalt();
            break;
        case "obtener_sucursales":
            $ajax->ajaxObtenerSucursales();
            break;
        case "verificar_email":
            $ajax->ajaxVerificarEmail();
            break;
    }
}