<?php

require_once "../controllers/clientes.controller.php";
require_once "../models/clientes.model.php";

class ClientesAjax {

    public function ajaxObtenerClientes() {
        ClientesController::obtenerClientes();
    }

    public function ajaxObtenerClientePorId() {
        ClientesController::obtenerClientePorId();
    }

    public function ajaxCrearCliente() {
        ClientesController::crearCliente();
    }

    public function ajaxActualizarCliente() {
        ClientesController::actualizarCliente();
    }

    public function ajaxEliminarCliente() {
        ClientesController::eliminarCliente();
    }

    public function ajaxVerificarDuplicado() {
        ClientesController::verificarDuplicado();
    }
}

// Manejar las peticiones AJAX
if (isset($_POST['accion'])) {
    $clientes = new ClientesAjax();

    switch ($_POST['accion']) {
        case 'obtener_clientes':
            $clientes->ajaxObtenerClientes();
            break;

        case 'obtener_cliente':
            $clientes->ajaxObtenerClientePorId();
            break;

        case 'crear_cliente':
            $clientes->ajaxCrearCliente();
            break;

        case 'actualizar_cliente':
            $clientes->ajaxActualizarCliente();
            break;

        case 'eliminar_cliente':
            $clientes->ajaxEliminarCliente();
            break;

        case 'verificar_duplicado':
            $clientes->ajaxVerificarDuplicado();
            break;

        default:
            echo json_encode(array("status" => "error", "message" => "Acción no válida"));
            break;
    }
}
?>
