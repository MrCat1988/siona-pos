<?php

require_once "../controllers/empresa.controller.php";
require_once "../models/empresa.model.php";

class EmpresaAjax {

    public function ajaxObtenerEmpresa() {
        EmpresaController::obtenerEmpresa();
    }

    public function ajaxActualizarEmpresa() {
        EmpresaController::actualizarEmpresa();
    }

    public function ajaxExtraerFechaCertificado() {
        EmpresaController::extraerFechaCertificado();
    }
}

// Manejar las peticiones AJAX
if (isset($_POST['accion'])) {
    $empresa = new EmpresaAjax();

    switch ($_POST['accion']) {
        case 'obtener_empresa':
            $empresa->ajaxObtenerEmpresa();
            break;

        case 'actualizar_empresa':
            $empresa->ajaxActualizarEmpresa();
            break;

        case 'extraer_fecha_certificado':
            $empresa->ajaxExtraerFechaCertificado();
            break;

        default:
            echo json_encode(array("status" => "error", "message" => "Acción no válida"));
            break;
    }
}
?>
