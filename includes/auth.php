<?php
// Verificación de autenticación
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

/* ==========================================
   SISTEMA DE ROLES - FUNCIONES DE CONTROL
   ==========================================
   Implementado para controlar acceso según rol:
   - ADMIN: Acceso completo (editar, eliminar, registrar usuarios)
   - USER: Solo lectura (ver cumpleaños, agregar nuevos)
*/

// Función para verificar si el usuario actual es administrador
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Función para verificar si el usuario actual es usuario normal
function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

// Función para requerir permisos de administrador
// Si no es admin, redirige al index
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}
?> 