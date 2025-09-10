<?php

class SecurityModel {
    
    public static function checkRateLimit($ip, $email = null) {
        try {
            $conexion = Connection::connect();
            
            // Limpiar intentos antiguos (más de 1 hora)
            $stmt = $conexion->prepare("DELETE FROM login_attempts WHERE last_attempt < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
            $stmt->execute();
            
            // Verificar intentos actuales
            $stmt = $conexion->prepare("
                SELECT attempts, blocked_until FROM login_attempts 
                WHERE ip_address = :ip AND (email = :email OR email IS NULL)
                AND last_attempt > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
            ");
            $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Verificar si está bloqueado
                if ($result['blocked_until'] && $result['blocked_until'] > date('Y-m-d H:i:s')) {
                    return [
                        'allowed' => false,
                        'blocked_until' => $result['blocked_until'],
                        'attempts' => $result['attempts']
                    ];
                }
                
                // Verificar límite de intentos (5 en 15 minutos)
                if ($result['attempts'] >= 5) {
                    // Bloquear por 15 minutos
                    $blocked_until = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                    self::updateAttempts($ip, $email, $result['attempts'] + 1, $blocked_until);
                    
                    return [
                        'allowed' => false,
                        'blocked_until' => $blocked_until,
                        'attempts' => $result['attempts'] + 1
                    ];
                }
            }
            
            return ['allowed' => true, 'attempts' => $result ? $result['attempts'] : 0];
            
        } catch (Exception $e) {
            error_log("Error en checkRateLimit: " . $e->getMessage());
            return ['allowed' => true, 'attempts' => 0]; // En caso de error, permitir acceso
        }
    }
    
    public static function recordLoginAttempt($ip, $email, $success = false) {
        try {
            $conexion = Connection::connect();
            
            if ($success) {
                // Login exitoso: limpiar intentos fallidos
                $stmt = $conexion->prepare("DELETE FROM login_attempts WHERE ip_address = :ip AND email = :email");
                $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                // Login fallido: incrementar intentos
                $stmt = $conexion->prepare("
                    INSERT INTO login_attempts (ip_address, email, attempts, last_attempt) 
                    VALUES (:ip, :email, 1, NOW())
                    ON DUPLICATE KEY UPDATE 
                    attempts = attempts + 1, 
                    last_attempt = NOW()
                ");
                $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
            }
            
        } catch (Exception $e) {
            error_log("Error en recordLoginAttempt: " . $e->getMessage());
        }
    }
    
    private static function updateAttempts($ip, $email, $attempts, $blocked_until = null) {
        try {
            $conexion = Connection::connect();
            
            $stmt = $conexion->prepare("
                UPDATE login_attempts 
                SET attempts = :attempts, blocked_until = :blocked_until, last_attempt = NOW()
                WHERE ip_address = :ip AND email = :email
            ");
            $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':attempts', $attempts, PDO::PARAM_INT);
            $stmt->bindParam(':blocked_until', $blocked_until, PDO::PARAM_STR);
            $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error en updateAttempts: " . $e->getMessage());
        }
    }
    
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function regenerateSession() {
        session_regenerate_id(true);
    }
}