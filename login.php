<?php
session_start();
require_once 'config/database.php';

// Si ya está logueado, redirigir al index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username && $password) {
        // SISTEMA DE ROLES: Consulta modificada para incluir el campo 'role'
        // Permite identificar si el usuario es 'admin' o 'user'
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Comparar la contraseña directamente (texto plano)
        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // SISTEMA DE ROLES: Guardar rol en sesión para control de acceso
            // Este valor se usa en las funciones isAdmin() e isUser()
            $_SESSION['role'] = $user['role']; // NUEVO: Guardar rol en sesión
            
            /* REDIRECCIÓN AUTOMÁTICA SEGÚN ROL
               ================================
               - ADMIN: Redirige a admin_dashboard.php (control completo)
               - USER: Redirige a user_dashboard.php (solo lectura)
            */
            if ($user['role'] === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: user_dashboard.php');
            }
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
        $stmt->close();
    } else {
        $error = 'Por favor, completa todos los campos';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CelebrAPP</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .logo {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        h2 {
            color: #2d3748;
            margin-bottom: 30px;
            font-size: 1.8em;
            font-weight: 600;
        }
        
        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
            font-size: 0.95em;
        }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        
        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .error {
            color: white;
            text-align: center;
            margin-bottom: 25px;
            padding: 15px;
            background: linear-gradient(45deg, #e53e3e, #c53030);
            border-radius: 10px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
        }
        
        .demo-info {
            background: linear-gradient(45deg, #48bb78, #38a169);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 0.9em;
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
        }
        
        .demo-info strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🎉</div>
        <h2>CelebrAPP</h2>

        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html> 