<?php

class ControladorProductoSucursal {

    /*=============================================
    OBTENER PRODUCTOS POR SUCURSAL
    =============================================*/
    static public function ctrObtenerProductosSucursal($tenantId, $filtros = array()) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida", "productos_sucursal" => array());
        }

        try{
            // DEBUG TEMPORAL - Ver filtros procesados
            error_log("CONTROLADOR filtros procesados: " . json_encode($filtros));

            $productosSucursal = ModeloProductoSucursal::mdlObtenerProductosSucursal($tenantId, $filtros);
            $total = ModeloProductoSucursal::mdlContarProductosSucursal($tenantId, $filtros);

            // DEBUG TEMPORAL - Ver resultados
            error_log("CONTROLADOR resultados: productos=" . count($productosSucursal) . ", total=" . $total);

            $response = array(
                "success" => true,
                "productos_sucursal" => $productosSucursal,
                "total" => $total
            );

            // Agregar información de paginación si está presente
            if (isset($filtros["limite"]) && isset($filtros["offset"])) {
                $response["paginacion"] = array(
                    "total" => $total,
                    "limite" => intval($filtros["limite"]),
                    "offset" => intval($filtros["offset"]),
                    "pagina_actual" => floor(intval($filtros["offset"]) / intval($filtros["limite"])) + 1,
                    "total_paginas" => ceil($total / intval($filtros["limite"]))
                );
            }

            return $response;

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error al obtener productos por sucursal: " . $e->getMessage(),
                "productos_sucursal" => array()
            );
        }
    }

    /*=============================================
    OBTENER PRODUCTO-SUCURSAL POR ID
    =============================================*/
    static public function ctrObtenerProductoSucursalPorId($idProductoSucursal, $tenantId) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida");
        }

        try {
            $productoSucursal = ModeloProductoSucursal::mdlObtenerProductoSucursalPorId($idProductoSucursal, $tenantId);

            if ($productoSucursal) {
                return array(
                    "success" => true,
                    "producto_sucursal" => $productoSucursal
                );
            } else {
                return array(
                    "success" => false,
                    "message" => "Producto-sucursal no encontrado"
                );
            }

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error al obtener producto-sucursal: " . $e->getMessage()
            );
        }
    }

    /*=============================================
    CREAR PRODUCTO-SUCURSAL
    =============================================*/
    static public function ctrCrearProductoSucursal($datos) {
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
            if (empty($datos["productos_idproducto"]) || empty($datos["sucursal_idsucursal"])) {
                return array(
                    "success" => false,
                    "message" => "El producto y la sucursal son obligatorios"
                );
            }

            // Verificar que el producto no esté ya asignado a esa sucursal
            $existe = ModeloProductoSucursal::mdlVerificarProductoEnSucursal(
                $datos["productos_idproducto"],
                $datos["sucursal_idsucursal"],
                $_SESSION["tenant_id"]
            );

            if ($existe) {
                return array(
                    "success" => false,
                    "message" => "Este producto ya está asignado a la sucursal seleccionada"
                );
            }

            // Validar campos numéricos
            $datos["precio_sucursal"] = floatval($datos["precio_sucursal"] ?? 0);
            $datos["stock_sucursal"] = intval($datos["stock_sucursal"] ?? 0);
            $datos["stock_minimo_sucursal"] = intval($datos["stock_minimo_sucursal"] ?? 0);
            $datos["stock_maximo_sucursal"] = intval($datos["stock_maximo_sucursal"] ?? 0);
            $datos["estado"] = intval($datos["estado"] ?? 1);

            // Validaciones de negocio
            if ($datos["precio_sucursal"] < 0) {
                return array(
                    "success" => false,
                    "message" => "El precio no puede ser negativo"
                );
            }

            if ($datos["stock_sucursal"] < 0) {
                return array(
                    "success" => false,
                    "message" => "El stock no puede ser negativo"
                );
            }

            if ($datos["stock_minimo_sucursal"] > $datos["stock_maximo_sucursal"] && $datos["stock_maximo_sucursal"] > 0) {
                return array(
                    "success" => false,
                    "message" => "El stock mínimo no puede ser mayor al stock máximo"
                );
            }

            // Agregar timestamp
            $datos["created_at"] = date('Y-m-d H:i:s');

            $resultado = ModeloProductoSucursal::mdlCrearProductoSucursal($datos);

            if ($resultado !== false) {
                return array(
                    "success" => true,
                    "message" => "Producto asignado a sucursal exitosamente",
                    "id" => $resultado
                );
            } else {
                return array(
                    "success" => false,
                    "message" => "Error al asignar el producto a la sucursal"
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
    ACTUALIZAR PRODUCTO-SUCURSAL
    =============================================*/
    static public function ctrActualizarProductoSucursal($idProductoSucursal, $datos, $tenantId) {
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
            // Verificar que el registro existe
            $productoSucursalExiste = ModeloProductoSucursal::mdlObtenerProductoSucursalPorId($idProductoSucursal, $tenantId);

            if (!$productoSucursalExiste) {
                return array(
                    "success" => false,
                    "message" => "Producto-sucursal no encontrado"
                );
            }

            // Si se están cambiando producto o sucursal, verificar que no exista la combinación
            if (isset($datos["productos_idproducto"]) || isset($datos["sucursal_idsucursal"])) {
                $nuevoProductoId = $datos["productos_idproducto"] ?? $productoSucursalExiste["productos_idproducto"];
                $nuevaSucursalId = $datos["sucursal_idsucursal"] ?? $productoSucursalExiste["sucursal_idsucursal"];

                $existe = ModeloProductoSucursal::mdlVerificarProductoEnSucursal(
                    $nuevoProductoId,
                    $nuevaSucursalId,
                    $tenantId,
                    $idProductoSucursal
                );

                if ($existe) {
                    return array(
                        "success" => false,
                        "message" => "Ya existe una asignación para este producto en la sucursal seleccionada"
                    );
                }
            }

            // Validar campos numéricos si se están actualizando
            if (isset($datos["precio_sucursal"])) {
                $datos["precio_sucursal"] = floatval($datos["precio_sucursal"]);
                if ($datos["precio_sucursal"] < 0) {
                    return array(
                        "success" => false,
                        "message" => "El precio no puede ser negativo"
                    );
                }
            }

            if (isset($datos["stock_sucursal"])) {
                $datos["stock_sucursal"] = intval($datos["stock_sucursal"]);
                if ($datos["stock_sucursal"] < 0) {
                    return array(
                        "success" => false,
                        "message" => "El stock no puede ser negativo"
                    );
                }
            }

            if (isset($datos["stock_minimo_sucursal"])) {
                $datos["stock_minimo_sucursal"] = intval($datos["stock_minimo_sucursal"]);
            }

            if (isset($datos["stock_maximo_sucursal"])) {
                $datos["stock_maximo_sucursal"] = intval($datos["stock_maximo_sucursal"]);
            }

            // Validar relación stock mínimo/máximo
            $stockMin = $datos["stock_minimo_sucursal"] ?? $productoSucursalExiste["stock_minimo_sucursal"];
            $stockMax = $datos["stock_maximo_sucursal"] ?? $productoSucursalExiste["stock_maximo_sucursal"];

            if ($stockMin > $stockMax && $stockMax > 0) {
                return array(
                    "success" => false,
                    "message" => "El stock mínimo no puede ser mayor al stock máximo"
                );
            }

            // Agregar timestamp de actualización
            $datos["updated_at"] = date('Y-m-d H:i:s');

            $resultado = ModeloProductoSucursal::mdlActualizarProductoSucursal($idProductoSucursal, $datos, $tenantId);

            if ($resultado) {
                return array(
                    "success" => true,
                    "message" => "Producto-sucursal actualizado exitosamente"
                );
            } else {
                return array(
                    "success" => false,
                    "message" => "Error al actualizar el producto-sucursal"
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
    ELIMINAR PRODUCTO-SUCURSAL
    =============================================*/
    static public function ctrEliminarProductoSucursal($idProductoSucursal, $tenantId) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida");
        }

        // Validar CSRF token si está disponible
        if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                return array("success" => false, "message" => "Token CSRF inválido");
            }
        }

        try {
            $resultado = ModeloProductoSucursal::mdlEliminarProductoSucursal($idProductoSucursal, $tenantId);

            if ($resultado) {
                return array(
                    "success" => true,
                    "message" => "Asignación eliminada exitosamente"
                );
            } else {
                return array(
                    "success" => false,
                    "message" => "Error al eliminar la asignación"
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
    OBTENER PRODUCTOS DISPONIBLES
    =============================================*/
    static public function ctrObtenerProductosDisponibles($sucursalId, $tenantId, $searchTerm = '') {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida", "productos" => array());
        }

        try {
            $productos = ModeloProductoSucursal::mdlObtenerProductosDisponibles($sucursalId, $tenantId, $searchTerm);

            return array(
                "success" => true,
                "productos" => $productos
            );

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error al obtener productos disponibles: " . $e->getMessage(),
                "productos" => array()
            );
        }
    }

    /*=============================================
    OBTENER SUCURSALES DISPONIBLES
    =============================================*/
    static public function ctrObtenerSucursalesDisponibles($tenantId) {
        // Validar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            return array("success" => false, "message" => "Sesión no válida", "sucursales" => array());
        }

        try {
            $sucursales = ModeloProductoSucursal::mdlObtenerSucursalesDisponibles($tenantId);

            return array(
                "success" => true,
                "sucursales" => $sucursales
            );

        } catch (Exception $e) {
            return array(
                "success" => false,
                "message" => "Error al obtener sucursales disponibles: " . $e->getMessage(),
                "sucursales" => array()
            );
        }
    }
}

?>