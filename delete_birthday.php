<?php
require 'includes/auth.php';
require 'config/database.php';

$id = $_GET['id'] ?? null;

if ($id) {
    // Obtener el nombre antes de eliminar para el mensaje
    $stmt = $conn->prepare("SELECT nombre_completo FROM birthdays WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $birthday = $result->fetch_assoc();
    $stmt->close();
    
    if ($birthday) {
        // Eliminar el cumpleaños
        $stmt = $conn->prepare("DELETE FROM birthdays WHERE id = ?");
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "🗑️ Cumpleaños de " . $birthday['nombre_completo'] . " eliminado exitosamente.";
        } else {
            $_SESSION['mensaje'] = "❌ Error al eliminar el cumpleaños.";
        }
        $stmt->close();
    } else {
        $_SESSION['mensaje'] = "❌ No se encontró el cumpleaños a eliminar.";
    }
} else {
    $_SESSION['mensaje'] = "❌ ID de cumpleaños no proporcionado.";
}

header('Location: view_birthdays.php');
exit;
?> 