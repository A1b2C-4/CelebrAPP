<?php
/* =====================================================
   SISTEMA DE ROLES - REGISTRO DE USUARIOS
   =====================================================
   Este archivo permite SOLO a administradores crear nuevos usuarios
   
   Funcionalidades:
   - Control de acceso: requireAdmin()
   - Validación de formularios
   - Creación de usuarios con roles específicos
   - Solo admins pueden crear otros admins
   
   Seguridad:
   - Verificación de usuario único
   - Validación de contraseñas
   - Control de roles
*/

require_once 'includes/auth.php';
requireAdmin(); // CRÍTICO: Solo admins pueden acceder
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validaciones
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Todos los campos son obligatorios';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } elseif (strlen($password) < 3) {
        $error = 'La contraseña debe tener al menos 3 caracteres';
    } else {
        // Verificar si el usuario ya existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'El usuario ya existe';
        } else {
            // Crear nuevo usuario
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $username, $password, $role);
            
            if ($stmt->execute()) {
                $success = 'Usuario creado exitosamente';
                $username = ''; // Limpiar formulario
            } else {
                $error = 'Error al crear el usuario';
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario - CelebrAPP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2d3748;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
        }
        
        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin-right: 10px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #718096;
        }
        
        .error {
            color: white;
            background: #e53e3e;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success {
            color: white;
            background: #48bb78;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Registrar Nuevo Usuario</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Nombre de Usuario:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label for="role">Rol:</label>
                <select id="role" name="role" required>
                    <option value="user">Usuario</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            
            <button type="submit" class="btn">Crear Usuario</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
        
        <div class="back-link">
            <a href="index.php" style="color: #667eea;">← Volver al inicio</a>
        </div>
    </div>
</body>
</html>