<?php
/* =====================================================
   PÁGINA DE REDIRECCIÓN AUTOMÁTICA SEGÚN ROL
   =====================================================
   Este archivo redirige automáticamente a los usuarios
   según su rol después del login:
   
   - ADMIN: admin_dashboard.php (control completo)
   - USER: user_dashboard.php (solo lectura)
   
   Si no hay sesión activa, redirige al login
*/

session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si hay sesión activa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Incluir funciones de autenticación para verificar roles
require_once 'includes/auth.php';

/* REDIRECCIÓN AUTOMÁTICA SEGÚN ROL
   ================================
   Detecta el rol del usuario y redirige al dashboard apropiado
*/
if (isAdmin()) {
    // Administrador: redirigir al panel de control completo
    header('Location: admin_dashboard.php');
} else {
    // Usuario normal: redirigir al panel de solo lectura
    header('Location: user_dashboard.php');
}
exit;
?> 