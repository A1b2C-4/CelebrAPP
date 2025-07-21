<?php
session_start();
require_once 'config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - CelebrAPP</title>
    <style>
        body { background: #f4f6fb; font-family: 'Segoe UI', Arial, sans-serif; }
        .login-header { text-align: center; margin-top: 60px; margin-bottom: 18px; }
        .login-header img { width: 64px; height: 64px; border-radius: 12px; box-shadow: 0 2px 8px #0001; }
        .login-header h1 { color: #4f6bed; font-size: 2em; margin: 12px 0 0 0; letter-spacing: 1px; }
        .login-box { max-width: 400px; margin: 0 auto 80px auto; background: #fff; padding: 32px 28px 28px 28px; border-radius: 12px; box-shadow: 0 4px 24px #0002; }
        h2 { color: #2d3a5a; text-align: center; }
        label { display: block; margin-top: 18px; color: #2d3a5a; font-weight: 500; }
        input[type=text], input[type=password] { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #bfc9e0; border-radius: 4px; font-size: 1em; }
        input[type=submit] { background: #4f6bed; color: #fff; padding: 10px 0; border: none; border-radius: 5px; width: 100%; margin-top: 24px; font-size: 1.1em; cursor: pointer; transition: background 0.2s; }
        input[type=submit]:hover { background: #364fc7; }
        .error { color: #b00; margin-top: 18px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-header">
        <img src="inicio.jpg" alt="">
        <h1>CelebrAPP</h1>
    </div>
    <div class="login-box">
        <h2>

        Iniciar sesión
    </h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <label>Usuario:
                <input type="text" name="username" required autofocus>
            </label>
            <label>Contraseña:
                <input type="password" name="password" required>
            </label>
            <input type="submit" value="Entrar">
        </form>
    </div>
</body>
</html> 