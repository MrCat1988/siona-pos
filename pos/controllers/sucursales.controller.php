<?php

// Verificar desde donde se está llamando este archivo
if (file_exists("../models/sucursales.model.php")) {
    require_once "../models/sucursales.model.php";
    require_once "../models/connection.php";
} else {
    require_once "models/sucursales.model.php";
    require_once "models/connection.php";
}

class SucursalesController {

    // Verificar sesión activa
    public static function verificarSesion() {
        session_start();
        if (isset($_SESSION['login_status']) && $_SESSION['login_status'] == true) {
            echo json_encode(array("status" => "active", "usuario" => $_SESSION['usuario_nombre']));
        } else {
            echo json_encode(array("status" => "inactive"));
        }
    }

    // Obtener sucursales con paginación
    public static function obtenerSucursales() {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['tenant_id'])) {
                echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
                return;
            }

            // Obtener parámetros de paginación y filtros
            $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 6;
            $estado = isset($_POST['estado']) && $_POST['estado'] !== '' ? (int)$_POST['estado'] : null;
            $incluir_eliminadas = isset($_POST['incluir_eliminadas']) && $_POST['incluir_eliminadas'] === 'true';

            // Validar parámetros
            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 50) $limit = 6; // Máximo 50 por página

            $resultado = SucursalesModel::obtenerSucursales($_SESSION['tenant_id'], $page, $limit, $estado, $incluir_eliminadas);

            if ($resultado === false || !isset($resultado['sucursales'])) {
                echo json_encode(array("status" => "error", "message" => "Error al consultar base de datos"));
                return;
            }
            if (empty($resultado['sucursales'])) {
                echo json_encode(array(
                    "status" => "success",
                    "data" => array(
                        'sucursales' => [],
                        'total' => 0,
                        'page' => $page,
                        'limit' => $limit,
                        'total_pages' => 0,
                        'has_previous' => false,
                        'has_next' => false
                    )
                ));
            } else {
                echo json_encode(array("status" => "success", "data" => $resultado));
            }

        } catch (Exception $e) {
            error_log("Error en obtenerSucursales controller: " . $e->getMessage());
            echo json_encode(array("status" => "error", "message" => "Error interno del servidor: " . $e->getMessage()));
        }
    }

    // Obtener una sucursal específica
    public static function obtenerSucursal() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (isset($_POST['idsucursal'])) {
            $idsucursal = $_POST['idsucursal'];
            $sucursal = SucursalesModel::obtenerSucursal($idsucursal, $_SESSION['tenant_id']);

            if ($sucursal) {
                echo json_encode(array("status" => "success", "data" => $sucursal));
            } else {
                echo json_encode(array("status" => "error", "message" => "Sucursal no encontrada"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "ID de sucursal requerido"));
        }
    }

    // Crear nueva sucursal
    public static function crearSucursal() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (!empty($_POST)) {
            // Validar CSRF token si está disponible (opcional)
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }

            // Validar datos requeridos
            $errores = [];
            if (empty($_POST['sri_codigo'])) $errores[] = "Código SRI";
            if (empty($_POST['sri_nombre'])) $errores[] = "Nombre de sucursal";
            if (empty($_POST['sri_direccion'])) $errores[] = "Dirección";

            if (!empty($errores)) {
                $mensaje = "Los siguientes campos son requeridos: " . implode(", ", $errores);
                echo json_encode(array("status" => "error", "message" => $mensaje));
                return;
            }

            // Verificar que no exista otra sucursal con el mismo código SRI en el tenant
            $codigoExiste = SucursalesModel::verificarCodigoSriExiste($_POST['sri_codigo'], $_SESSION['tenant_id']);
            if ($codigoExiste) {
                echo json_encode(array("status" => "error", "message" => "Ya existe una sucursal con este código SRI"));
                return;
            }

            // Verificar que no exista otra sucursal con el mismo nombre en el tenant
            $nombreExiste = SucursalesModel::verificarNombreExiste($_POST['sri_nombre'], $_SESSION['tenant_id']);
            if ($nombreExiste) {
                echo json_encode(array("status" => "error", "message" => "Ya existe una sucursal con este nombre"));
                return;
            }

            $datos = array(
                "sri_codigo" => $_POST['sri_codigo'],
                "sri_nombre" => $_POST['sri_nombre'],
                "sri_direccion" => $_POST['sri_direccion'],
                "estado" => $_POST['estado'] ?? 1,
                "tenant_id" => $_SESSION['tenant_id']
            );

            $respuesta = SucursalesModel::crearSucursal($datos);

            if ($respuesta) {
                echo json_encode(array("status" => "success", "message" => "Sucursal creada exitosamente"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al crear sucursal"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "No se recibieron datos"));
        }
    }

    // Editar sucursal
    public static function editarSucursal() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if ($_POST) {
            // Validar CSRF token si está disponible
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }

            // Validar datos requeridos
            if (empty($_POST['idsucursal']) || empty($_POST['sri_codigo']) || empty($_POST['sri_nombre']) || empty($_POST['sri_direccion'])) {
                echo json_encode(array("status" => "error", "message" => "ID, código SRI, nombre y dirección son requeridos"));
                return;
            }

            $idsucursal = $_POST['idsucursal'];

            // Verificar que no exista otra sucursal con el mismo código SRI en el tenant (excluyendo la actual)
            $codigoExiste = SucursalesModel::verificarCodigoSriExiste($_POST['sri_codigo'], $_SESSION['tenant_id'], $idsucursal);
            if ($codigoExiste) {
                echo json_encode(array("status" => "error", "message" => "Ya existe otra sucursal con este código SRI"));
                return;
            }

            // Verificar que no exista otra sucursal con el mismo nombre en el tenant (excluyendo la actual)
            $nombreExiste = SucursalesModel::verificarNombreExiste($_POST['sri_nombre'], $_SESSION['tenant_id'], $idsucursal);
            if ($nombreExiste) {
                echo json_encode(array("status" => "error", "message" => "Ya existe otra sucursal con este nombre"));
                return;
            }

            $datos = array(
                "idsucursal" => $idsucursal,
                "sri_codigo" => $_POST['sri_codigo'],
                "sri_nombre" => $_POST['sri_nombre'],
                "sri_direccion" => $_POST['sri_direccion'],
                "estado" => $_POST['estado'] ?? 1
            );

            $respuesta = SucursalesModel::editarSucursal($datos, $_SESSION['tenant_id']);

            if ($respuesta) {
                echo json_encode(array("status" => "success", "message" => "Sucursal actualizada exitosamente"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al actualizar sucursal"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "No se recibieron datos"));
        }
    }

    // Eliminar sucursal
    public static function eliminarSucursal() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (isset($_POST['idsucursal'])) {
            $idsucursal = $_POST['idsucursal'];

            // Validar CSRF token si está disponible
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }

            // Verificar si el usuario actual está asignado a esta sucursal
            if (isset($_SESSION['usuario_sucursal']) && $_SESSION['usuario_sucursal'] == $idsucursal) {
                echo json_encode(array("status" => "error", "message" => "No puedes eliminar la sucursal a la que estás asignado"));
                return;
            }

            // Verificar si la sucursal tiene dependencias (usuarios asociados)
            $dependencias = SucursalesModel::verificarDependenciasSucursal($idsucursal, $_SESSION['tenant_id']);

            // Proceder con la eliminación (soft delete)
            $respuesta = SucursalesModel::eliminarSucursal($idsucursal, $_SESSION['tenant_id']);

            if ($respuesta) {
                // Mensaje dependiendo de si tiene dependencias o no
                if ($dependencias['tiene_dependencias']) {
                    $mensaje = "Sucursal desactivada exitosamente. La sucursal tenía " . $dependencias['usuarios'] . " usuario(s) asociado(s), por lo que se cambió su estado a inactiva para fines de auditoría.";
                } else {
                    $mensaje = "Sucursal eliminada exitosamente.";
                }

                echo json_encode(array(
                    "status" => "success",
                    "message" => $mensaje,
                    "tipo_eliminacion" => $dependencias['tiene_dependencias'] ? 'desactivacion' : 'eliminacion',
                    "dependencias" => $dependencias
                ));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al eliminar sucursal"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "ID de sucursal requerido"));
        }
    }

    // Verificar disponibilidad de código SRI
    public static function verificarCodigoSri() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (isset($_POST['sri_codigo'])) {
            $sri_codigo = $_POST['sri_codigo'];
            $excluir_id = isset($_POST['excluir_id']) ? $_POST['excluir_id'] : null;

            // Validar formato del código SRI (3 dígitos)
            if (!preg_match('/^[0-9]{3}$/', $sri_codigo)) {
                echo json_encode(array("status" => "error", "message" => "Código SRI debe tener 3 dígitos"));
                return;
            }

            $disponible = !SucursalesModel::verificarCodigoSriExiste($sri_codigo, $_SESSION['tenant_id'], $excluir_id);

            echo json_encode(array(
                "status" => "success",
                "disponible" => $disponible,
                "codigo" => $sri_codigo
            ));
        } else {
            echo json_encode(array("status" => "error", "message" => "Código SRI requerido"));
        }
    }
}

?>