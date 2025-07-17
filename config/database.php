<?php
// config/database.php
// Configuración de la conexión a la base de datos

$host = 'localhost';
$db   = 'celebraapp'; //base de datos 
$user = 'root';      // usuario 
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}
?> 