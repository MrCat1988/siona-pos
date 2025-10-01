<?php

class EmpresaController {

    /**
     * Verificar sesión activa
     */
    private static function verificarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['tenant_id'])) {
            echo json_encode(array("status" => "error", "message" => "Sesión no válida"));
            return false;
        }

        return true;
    }

    /**
     * Obtener información de la empresa del tenant actual
     */
    public static function obtenerEmpresa() {
        if (!self::verificarSesion()) {
            return;
        }

        try {
            $empresa = EmpresaModel::mdlObtenerEmpresa($_SESSION['tenant_id']);

            if ($empresa) {
                echo json_encode(array(
                    "status" => "success",
                    "data" => $empresa
                ));
            } else {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "No se encontró información de la empresa"
                ));
            }

        } catch (Exception $e) {
            error_log("Error en obtenerEmpresa: " . $e->getMessage());
            echo json_encode(array(
                "status" => "error",
                "message" => "Error al obtener información de la empresa"
            ));
        }
    }

    /**
     * Actualizar información de la empresa
     */
    public static function actualizarEmpresa() {
        if (!self::verificarSesion()) {
            return;
        }

        // Validar CSRF token
        if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
            if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                echo json_encode(array("status" => "error", "message" => "Token CSRF inválido"));
                return;
            }
        }

        if ($_POST) {
            try {
                // Validaciones básicas
                if (empty($_POST['ruc']) || empty($_POST['razon_social'])) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "RUC y Razón Social son obligatorios"
                    ));
                    return;
                }

                // Validar formato RUC (13 dígitos)
                if (!preg_match('/^\d{13}$/', $_POST['ruc'])) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "El RUC debe tener exactamente 13 dígitos"
                    ));
                    return;
                }

                // Validar email si se proporciona
                if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "El formato del email es inválido"
                    ));
                    return;
                }

                // Validar correo de envío si se proporciona
                if (!empty($_POST['correo_envio_factura']) && !filter_var($_POST['correo_envio_factura'], FILTER_VALIDATE_EMAIL)) {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "El formato del correo de envío es inválido"
                    ));
                    return;
                }

                // Obtener información actual de la empresa para manejar archivo P12
                $empresaActual = EmpresaModel::mdlObtenerEmpresa($_SESSION['tenant_id']);
                $p12PathAnterior = $empresaActual['p12_path'] ?? '';
                $ruc = trim($_POST['ruc']);

                // Manejar subida de archivo P12
                $p12Path = $p12PathAnterior; // Mantener el anterior por defecto
                $p12ExpirationDate = $empresaActual['p12_expiration_date'] ?? null;

                if (isset($_FILES['p12_file']) && $_FILES['p12_file']['error'] === UPLOAD_ERR_OK) {
                    // Validar que sea un archivo .p12
                    $fileName = $_FILES['p12_file']['name'];
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if ($fileExt !== 'p12') {
                        echo json_encode(array(
                            "status" => "error",
                            "message" => "Solo se permiten archivos .p12"
                        ));
                        return;
                    }

                    // Crear directorio para el RUC si no existe
                    $uploadDir = "../p12_signs/" . $ruc . "/";
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    // Generar nombre único para el archivo
                    $nuevoNombreArchivo = "cert_" . $ruc . "_" . time() . ".p12";
                    $rutaCompleta = $uploadDir . $nuevoNombreArchivo;

                    // Mover archivo subido
                    if (move_uploaded_file($_FILES['p12_file']['tmp_name'], $rutaCompleta)) {
                        // Eliminar archivo anterior si existe
                        if (!empty($p12PathAnterior) && file_exists($p12PathAnterior)) {
                            unlink($p12PathAnterior);
                        }

                        $p12Path = $rutaCompleta;

                        // Intentar extraer fecha de caducidad del certificado
                        try {
                            $p12Content = file_get_contents($rutaCompleta);
                            $password = trim($_POST['p12_password'] ?? '');
                            $certs = array();

                            if (!empty($password) && openssl_pkcs12_read($p12Content, $certs, $password)) {
                                if (isset($certs['cert'])) {
                                    $certInfo = openssl_x509_parse($certs['cert']);
                                    if ($certInfo && isset($certInfo['validTo_time_t'])) {
                                        $p12ExpirationDate = date('Y-m-d', $certInfo['validTo_time_t']);
                                    }
                                }
                            } else {
                                // Si no se puede abrir (ambiente de pruebas sin SSL), guardar fecha predeterminada
                                $p12ExpirationDate = '2001-01-01';
                            }
                        } catch (Exception $e) {
                            // Error al leer certificado, guardar fecha predeterminada
                            $p12ExpirationDate = '2001-01-01';
                            error_log("Error al extraer fecha del certificado: " . $e->getMessage());
                        }
                    } else {
                        echo json_encode(array(
                            "status" => "error",
                            "message" => "Error al guardar el archivo P12"
                        ));
                        return;
                    }
                }

                // Preparar datos para actualización
                $datos = array(
                    "ruc" => $ruc,
                    "nombre_comercial" => trim($_POST['nombre_comercial'] ?? ''),
                    "razon_social" => trim($_POST['razon_social']),
                    "direccion_matriz" => trim($_POST['direccion_matriz'] ?? ''),
                    "actividad_economica" => trim($_POST['actividad_economica'] ?? ''),
                    "tipo_contibuyente" => $_POST['tipo_contibuyente'] ?? 'Persona natural',
                    "regimen" => $_POST['regimen'] ?? 'Regimen general',
                    "contabilidad" => isset($_POST['contabilidad']) ? (int)$_POST['contabilidad'] : 0,
                    "agente_retencion" => isset($_POST['agente_retencion']) ? (int)$_POST['agente_retencion'] : 0,
                    "contribuyente_especial" => isset($_POST['contribuyente_especial']) ? (int)$_POST['contribuyente_especial'] : 0,
                    "artesano" => isset($_POST['artesano']) ? 1 : 0,
                    "numero_calificacion_artesanal" => trim($_POST['numero_calificacion_artesanal'] ?? ''),
                    "telefono" => trim($_POST['telefono'] ?? ''),
                    "email" => trim($_POST['email'] ?? ''),
                    "correo_envio_factura" => trim($_POST['correo_envio_factura'] ?? ''),
                    "ambiente_sri" => $_POST['ambiente_sri'] ?? 'Pruebas',
                    "password_correo_envio_factura" => trim($_POST['password_correo_envio_factura'] ?? ''),
                    "puerto_correo_envio_factura" => trim($_POST['puerto_correo_envio_factura'] ?? ''),
                    "servidor_smtp_correo_envio_factura" => trim($_POST['servidor_smtp_correo_envio_factura'] ?? ''),
                    "p12_path" => $p12Path,
                    "p12_password" => trim($_POST['p12_password'] ?? ''),
                    "p12_expiration_date" => $p12ExpirationDate
                );

                // Actualizar empresa
                $respuesta = EmpresaModel::mdlActualizarEmpresa($datos, $_SESSION['tenant_id']);

                if ($respuesta) {
                    // Actualizar también en la sesión para reflejar cambios inmediatos
                    $_SESSION['razon_social'] = $datos['razon_social'];
                    $_SESSION['nombre_comercial'] = $datos['nombre_comercial'];

                    echo json_encode(array(
                        "status" => "success",
                        "message" => "Información de la empresa actualizada exitosamente"
                    ));
                } else {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "Error al actualizar la información de la empresa"
                    ));
                }

            } catch (Exception $e) {
                error_log("Error en actualizarEmpresa: " . $e->getMessage());
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Error interno al actualizar la empresa"
                ));
            }
        } else {
            echo json_encode(array(
                "status" => "error",
                "message" => "No se recibieron datos"
            ));
        }
    }

    /**
     * Extraer fecha de caducidad del certificado P12
     */
    public static function extraerFechaCertificado() {
        if (!self::verificarSesion()) {
            return;
        }

        try {
            // Verificar si se subió un archivo
            if (!isset($_FILES['p12_file']) || $_FILES['p12_file']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "No se recibió el archivo o hubo un error en la carga"
                ));
                return;
            }

            // Validar contraseña
            if (!isset($_POST['password']) || empty($_POST['password'])) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Se requiere la contraseña del certificado"
                ));
                return;
            }

            $archivo = $_FILES['p12_file']['tmp_name'];
            $password = $_POST['password'];

            // Leer el contenido del archivo P12
            $p12_content = file_get_contents($archivo);

            if ($p12_content === false) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "No se pudo leer el archivo P12"
                ));
                return;
            }

            // Intentar abrir el certificado P12
            $certs = array();
            if (!openssl_pkcs12_read($p12_content, $certs, $password)) {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "No se pudo abrir el certificado. Verifique la contraseña"
                ));
                return;
            }

            // Obtener información del certificado
            if (isset($certs['cert'])) {
                $cert_info = openssl_x509_parse($certs['cert']);

                if ($cert_info && isset($cert_info['validTo_time_t'])) {
                    $fecha_expiracion = $cert_info['validTo_time_t'];
                    $fecha_inicio = $cert_info['validFrom_time_t'];

                    // Calcular días restantes
                    $dias_restantes = floor(($fecha_expiracion - time()) / 86400);

                    // Formatear fechas
                    $fecha_expiracion_formateada = date('Y-m-d', $fecha_expiracion);
                    $fecha_inicio_formateada = date('Y-m-d', $fecha_inicio);

                    // Obtener información adicional del certificado
                    $subject = isset($cert_info['subject']) ? $cert_info['subject'] : array();
                    $issuer = isset($cert_info['issuer']) ? $cert_info['issuer'] : array();

                    echo json_encode(array(
                        "status" => "success",
                        "data" => array(
                            "fecha_expiracion" => $fecha_expiracion_formateada,
                            "fecha_expiracion_timestamp" => $fecha_expiracion,
                            "fecha_inicio" => $fecha_inicio_formateada,
                            "dias_restantes" => $dias_restantes,
                            "expirado" => $dias_restantes < 0,
                            "proximo_a_expirar" => $dias_restantes <= 30 && $dias_restantes >= 0,
                            "subject" => $subject,
                            "issuer" => $issuer
                        )
                    ));
                } else {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "No se pudo obtener la información del certificado"
                    ));
                }
            } else {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "Certificado no válido"
                ));
            }

        } catch (Exception $e) {
            error_log("Error en extraerFechaCertificado: " . $e->getMessage());
            echo json_encode(array(
                "status" => "error",
                "message" => "Error al procesar el certificado: " . $e->getMessage()
            ));
        }
    }
}
?>
