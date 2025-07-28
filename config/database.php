<?php
// Configuración de la conexión a la base de datos
$host = 'localhost';
$db   = 'celebraapp';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception('Error de conexión: ' . $conn->connect_error);
    }
    
    // Configurar charset
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    die('Error de base de datos: ' . $e->getMessage());
}
?> 