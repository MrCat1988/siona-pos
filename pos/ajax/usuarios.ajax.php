<?php

session_start();
require_once "../models/connection.php";
require_once "../models/usuarios.model.php";
require_once "../models/security.model.php";
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
    
    public function ajaxGetSalt() {
        if (isset($_POST['email'])) {
            $salt = UsuariosModel::getSaltForEmail($_POST['email']);
            echo json_encode(array("status" => "success", "salt" => $salt));
        } else {
            echo json_encode(array("status" => "error", "message" => "Email requerido"));
        }
    }
}

if (isset($_POST["email"]) && isset($_POST["password"])) {
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
        case "get_salt":
            $ajax->ajaxGetSalt();
            break;
    }
}