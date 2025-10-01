<?php

class ControladorProductos {

    /*=============================================
    OBTENER SIGUIENTE CÓDIGO SECUENCIAL
    =============================================*/
    static public function ctrObtenerSiguienteCodigo($tenantId) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida");
        }

        try {
            $ultimoCodigo = ModeloProductos::mdlObtenerUltimoCodigo($tenantId);

            if ($ultimoCodigo && !empty($ultimoCodigo)) {
                // Extraer la parte numérica del último código (7 dígitos exactos)
                preg_match('/P(\d{7})/', $ultimoCodigo, $matches);

                if (isset($matches[1])) {
                    $ultimoNumero = intval($matches[1]);
                    $siguienteNumero = $ultimoNumero + 1;
                } else {
                    // Si no se encuentra patrón válido, empezar desde 1
                    $siguienteNumero = 1;
                }
            } else {
                // Si no hay productos, empezar desde 1
                $siguienteNumero = 1;
            }

            // Formatear con padding de 7 dígitos para asegurar P0000001 (8 caracteres total)
            $codigo = "P" . str_pad($siguienteNumero, 7, "0", STR_PAD_LEFT);

            return array(
                "success" => true,
                "codigo" => $codigo,
                "numero_secuencial" => $siguienteNumero
            );

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error al generar código: " . $e->getMessage()
            );
        }
    }

    /*=============================================
    VERIFICAR SI CÓDIGO EXISTE
    =============================================*/
    static public function ctrVerificarCodigoExiste($codigo, $tenantId) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida", "existe" => false);
        }

        try {
            $existe = ModeloProductos::mdlVerificarCodigoExiste($codigo, $tenantId);

            return array(
                "success" => true,
                "existe" => $existe
            );

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error al verificar código: " . $e->getMessage(),
                "existe" => false
            );
        }
    }

    /*=============================================
    VERIFICAR SI CÓDIGO AUXILIAR EXISTE
    =============================================*/
    static public function ctrVerificarCodigoAuxiliarExiste($codigoAuxiliar, $tenantId, $productoId = null) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida", "existe" => false);
        }

        try {
            $existe = ModeloProductos::mdlVerificarCodigoAuxiliarExiste($codigoAuxiliar, $tenantId, $productoId);

            return array(
                "success" => true,
                "existe" => $existe,
                "codigo_auxiliar" => $codigoAuxiliar
            );

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error al verificar código auxiliar: " . $e->getMessage(),
                "existe" => false
            );
        }
    }

    /*=============================================
    CREAR PRODUCTO
    =============================================*/
    static public function ctrCrearProducto($datos) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida");
        }

        // Validar CSRF token si está disponible
        if (isset($datos['csrf_token']) && isset($_SESSION['csrf_token'])) {
            if (!hash_equals($_SESSION['csrf_token'], $datos['csrf_token'])) {
                return array("success" => false, "message" => "Token CSRF inválido");
            }
        }

        try {

            // Validaciones básicas
            if (empty($datos["codigo"]) || empty($datos["descripcion"]) || empty($datos["categoria_idcategoria"])) {
                return array(
                    "success" => false,
                    "message" => "Los campos código, descripción y categoría son obligatorios"
                );
            }

            // Verificar que el código no exista
            $codigoExiste = ModeloProductos::mdlVerificarCodigoExiste($datos["codigo"], $_SESSION["tenant_id"]);

            if ($codigoExiste) {
                return array(
                    "success" => false,
                    "message" => "El código de producto ya existe"
                );
            }

            // Agregar timestamp de creación
            $datos["created_at"] = date('Y-m-d H:i:s');

            $resultado = ModeloProductos::mdlCrearProducto($datos);

            if ($resultado !== false) {
                return array(
                    "success" => true,
                    "message" => "Producto creado exitosamente",
                    "id" => $resultado
                );
            } else {
                return array(
                    "success" => false,
                    "message" => "Error al crear el producto"
                );
            }

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage()
            );
        }
    }

    /*=============================================
    OBTENER PRODUCTOS
    =============================================*/
    static public function ctrObtenerProductos($tenantId, $filtros = array()) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida", "productos" => array());
        }

        try {
            $productos = ModeloProductos::mdlObtenerProductos($tenantId, $filtros);

            return array(
                "success" => true,
                "productos" => $productos,
                "total" => count($productos)
            );

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error al obtener productos: " . $e->getMessage(),
                "productos" => array()
            );
        }
    }

    /*=============================================
    OBTENER PRODUCTO POR ID
    =============================================*/
    static public function ctrObtenerProductoPorId($idProducto, $tenantId) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida");
        }

        try {
            $producto = ModeloProductos::mdlObtenerProductoPorId($idProducto, $tenantId);

            if ($producto) {
                return array(
                    "success" => true,
                    "producto" => $producto
                );
            } else {
                return array(
                    "success" => false,
                    "message" => "Producto no encontrado"
                );
            }

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error al obtener producto: " . $e->getMessage()
            );
        }
    }

    /*=============================================
    ACTUALIZAR PRODUCTO
    =============================================*/
    static public function ctrActualizarProducto($idProducto, $datos, $tenantId) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida");
        }

        // Validar CSRF token si está disponible
        if (isset($datos['csrf_token']) && isset($_SESSION['csrf_token'])) {
            if (!hash_equals($_SESSION['csrf_token'], $datos['csrf_token'])) {
                return array("success" => false, "message" => "Token CSRF inválido");
            }
        }

        try {
            // Verificar que el producto existe y pertenece al tenant
            $productoExiste = ModeloProductos::mdlObtenerProductoPorId($idProducto, $tenantId);

            if (!$productoExiste) {
                return array(
                    "success" => false,
                    "message" => "Producto no encontrado"
                );
            }

            // Si se está cambiando el código, verificar que no exista
            if (isset($datos["codigo"]) && $datos["codigo"] !== $productoExiste["codigo"]) {
                $codigoExiste = ModeloProductos::mdlVerificarCodigoExiste($datos["codigo"], $tenantId);

                if ($codigoExiste) {
                    return array(
                        "success" => false,
                        "message" => "El código de producto ya existe"
                    );
                }
            }

            // Agregar timestamp de actualización
            $datos["updated_at"] = date('Y-m-d H:i:s');

            $resultado = ModeloProductos::mdlActualizarProducto($idProducto, $datos, $tenantId);

            if ($resultado) {
                return array(
                    "success" => true,
                    "message" => "Producto actualizado exitosamente"
                );
            } else {
                error_log("Error en actualización - Modelo retornó false");
                error_log("ID Producto: " . $idProducto . ", Tenant: " . $tenantId);
                error_log("Datos enviados: " . print_r($datos, true));
                return array(
                    "success" => false,
                    "message" => "Error al actualizar el producto"
                );
            }

        } catch (Exception $e) {
            error_log("Error en ctrActualizarProducto: " . $e->getMessage());
            error_log("Datos recibidos: " . print_r($datos, true));
            return array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage(),
                "debug" => $e->getFile() . ":" . $e->getLine()
            );
        }
    }

    /*=============================================
    ELIMINAR PRODUCTO (SOFT DELETE)
    =============================================*/
    static public function ctrEliminarProducto($idProducto, $tenantId) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida");
        }

        // Validar CSRF token si está disponible (cuando se llama desde AJAX con POST)
        if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                return array("success" => false, "message" => "Token CSRF inválido");
            }
        }

        try {
            $resultado = ModeloProductos::mdlEliminarProducto($idProducto, $tenantId);

            if ($resultado) {
                return array(
                    "success" => true,
                    "message" => "Producto eliminado exitosamente"
                );
            } else {
                return array(
                    "success" => false,
                    "message" => "Error al eliminar el producto"
                );
            }

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error interno: " . $e->getMessage()
            );
        }
    }
}

?>