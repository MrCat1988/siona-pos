<?php

require_once "../models/connection.php";

class ControladorCategorias {

    // Verificar sesión activa
    public static function verificarSesion() {
        session_start();
        if (isset($_SESSION['login_status']) && $_SESSION['login_status'] == true) {
            echo json_encode(array("status" => "active", "usuario" => $_SESSION['usuario_nombre']));
        } else {
            echo json_encode(array("status" => "inactive"));
        }
    }

    // Obtener categorías con paginación
    public static function obtenerCategorias() {
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

            $resultado = ModeloCategorias::obtenerCategorias($_SESSION['tenant_id'], $page, $limit, $estado, $incluir_eliminadas);

            if ($resultado === false || !isset($resultado['categorias'])) {
                echo json_encode(array("status" => "error", "message" => "Error al consultar base de datos"));
                return;
            }

            if (empty($resultado['categorias'])) {
                echo json_encode(array(
                    "status" => "success",
                    "data" => array(
                        'categorias' => [],
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
            error_log("Error en obtenerCategorias controller: " . $e->getMessage());
            echo json_encode(array("status" => "error", "message" => "Error interno del servidor: " . $e->getMessage()));
        }
    }

    // Obtener una categoría específica
    public static function obtenerCategoria() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (isset($_POST['idcategoria'])) {
            $idcategoria = $_POST['idcategoria'];
            $categoria = ModeloCategorias::obtenerCategoria($idcategoria, $_SESSION['tenant_id']);

            if ($categoria) {
                echo json_encode(array("status" => "success", "data" => $categoria));
            } else {
                echo json_encode(array("status" => "error", "message" => "Categoría no encontrada"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "ID de categoría requerido"));
        }
    }

    // Crear nueva categoría
    public static function crearCategoria() {
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
            if (empty($_POST['nombre'])) $errores[] = "Nombre de categoría";

            if (!empty($errores)) {
                $mensaje = "Los siguientes campos son requeridos: " . implode(", ", $errores);
                echo json_encode(array("status" => "error", "message" => $mensaje));
                return;
            }

            // Verificar que no exista otra categoría con el mismo nombre en el tenant
            $nombreExiste = ModeloCategorias::verificarNombreExiste($_POST['nombre'], $_SESSION['tenant_id']);
            if ($nombreExiste) {
                echo json_encode(array("status" => "error", "message" => "Ya existe una categoría con este nombre"));
                return;
            }

            $datos = array(
                "nombre" => trim($_POST['nombre']),
                "descripcion" => isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null,
                "estado" => $_POST['estado'] ?? 1,
                "tenant_id" => $_SESSION['tenant_id']
            );

            $respuesta = ModeloCategorias::crearCategoria($datos);

            if ($respuesta) {
                echo json_encode(array("status" => "success", "message" => "Categoría creada exitosamente"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al crear categoría"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "No se recibieron datos"));
        }
    }

    // Editar categoría
    public static function editarCategoria() {
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
            if (empty($_POST['idcategoria']) || empty($_POST['nombre'])) {
                echo json_encode(array("status" => "error", "message" => "ID y nombre de categoría son requeridos"));
                return;
            }

            $idcategoria = $_POST['idcategoria'];

            // Verificar que no exista otra categoría con el mismo nombre en el tenant (excluyendo la actual)
            $nombreExiste = ModeloCategorias::verificarNombreExiste($_POST['nombre'], $_SESSION['tenant_id'], $idcategoria);
            if ($nombreExiste) {
                echo json_encode(array("status" => "error", "message" => "Ya existe otra categoría con este nombre"));
                return;
            }

            $datos = array(
                "idcategoria" => $idcategoria,
                "nombre" => trim($_POST['nombre']),
                "descripcion" => isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null,
                "estado" => $_POST['estado'] ?? 1
            );

            $respuesta = ModeloCategorias::editarCategoria($datos, $_SESSION['tenant_id']);

            if ($respuesta) {
                echo json_encode(array("status" => "success", "message" => "Categoría actualizada exitosamente"));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al actualizar categoría"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "No se recibieron datos"));
        }
    }

    // Eliminar categoría
    public static function eliminarCategoria() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return;
        }

        if (isset($_POST['idcategoria'])) {
            $idcategoria = $_POST['idcategoria'];

            // Validar CSRF token si está disponible
            if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
                if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                    return;
                }
            }

            // Verificar si la categoría tiene dependencias (productos asociados)
            $dependencias = ModeloCategorias::verificarDependenciasCategoria($idcategoria, $_SESSION['tenant_id']);

            // Proceder con la eliminación (soft delete)
            $respuesta = ModeloCategorias::eliminarCategoria($idcategoria, $_SESSION['tenant_id']);

            if ($respuesta) {
                // Mensaje dependiendo de si tiene dependencias o no
                if ($dependencias['tiene_dependencias']) {
                    $mensaje = "Categoría desactivada exitosamente. La categoría tenía " . $dependencias['productos'] . " producto(s) asociado(s), por lo que se cambió su estado a inactiva para fines de auditoría.";
                } else {
                    $mensaje = "Categoría eliminada exitosamente.";
                }

                echo json_encode(array(
                    "status" => "success",
                    "message" => $mensaje,
                    "tipo_eliminacion" => $dependencias['tiene_dependencias'] ? 'desactivacion' : 'eliminacion',
                    "dependencias" => $dependencias
                ));
            } else {
                echo json_encode(array("status" => "error", "message" => "Error al eliminar categoría"));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "ID de categoría requerido"));
        }
    }

    /*=============================================
    OBTENER CATEGORÍAS PARA PRODUCTOS (WRAPPER)
    =============================================*/
    static public function ctrObtenerCategorias($tenantId) {

        try {
            $categorias = ModeloCategorias::obtenerCategorias($tenantId, 1, 100, 1, false);

            if ($categorias === false || !isset($categorias['categorias'])) {
                return array(
                    "success" => false,
                    "message" => "Error al consultar base de datos",
                    "categorias" => array()
                );
            }

            return array(
                "success" => true,
                "categorias" => $categorias['categorias'],
                "total" => $categorias['total']
            );

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage(),
                "categorias" => array()
            );
        }
    }
}

?>