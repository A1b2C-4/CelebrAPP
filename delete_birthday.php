<?php
require 'includes/auth.php';

/* SISTEMA DE ROLES - PROTECCIÓN DE ELIMINACIÓN
   =============================================
   Solo usuarios con rol 'admin' pueden eliminar cumpleaños
   Esta función es crítica y requiere permisos de administrador
*/
requireAdmin(); // Solo admins pueden eliminar cumpleaños

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
            
            // OPCIÓN A: Solo reiniciar AUTO_INCREMENT (mantiene IDs actuales)
            $conn->query("ALTER TABLE birthdays AUTO_INCREMENT = 1");
            
            // OPCIÓN B: Renumerar todos los IDs (descomenta si quieres esto)
            /*
            // Crear tabla temporal con IDs consecutivos
            $conn->query("CREATE TEMPORARY TABLE temp_birthdays AS 
                         SELECT ROW_NUMBER() OVER (ORDER BY id) as new_id, 
                                nombre_completo, fecha_nacimiento, telefono, email, tipo_relacion, created_at, updated_at
                         FROM birthdays ORDER BY id");
            
            // Limpiar tabla original
            $conn->query("DELETE FROM birthdays");
            $conn->query("ALTER TABLE birthdays AUTO_INCREMENT = 1");
            
            // Insertar datos con IDs consecutivos
            $conn->query("INSERT INTO birthdays (nombre_completo, fecha_nacimiento, telefono, email, tipo_relacion, created_at, updated_at)
                         SELECT nombre_completo, fecha_nacimiento, telefono, email, tipo_relacion, created_at, updated_at 
                         FROM temp_birthdays ORDER BY new_id");
            
            // Eliminar tabla temporal
            $conn->query("DROP TEMPORARY TABLE temp_birthdays");
            */
            
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