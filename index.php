<?php
session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: view_birthdays.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a CelebrAPP</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fb; margin: 0; }
        .container { max-width: 600px; margin: 60px auto; background: #fff; padding: 40px 36px 36px 36px; border-radius: 16px; box-shadow: 0 4px 24px #0002; text-align: center; }
        h1 { color: #4f6bed; font-size: 2.2em; margin-bottom: 0.2em; }
        p { color: #2d3a5a; font-size: 1.2em; margin-bottom: 1.5em; }
        .main-img { width: 180px; margin-bottom: 24px; border-radius: 12px; box-shadow: 0 2px 8px #0001; }
        .main-buttons { margin-top: 32px; display: flex; justify-content: center; gap: 24px; }
        .main-buttons a { background: #4f6bed; color: #fff; padding: 16px 32px; border-radius: 8px; text-decoration: none; font-size: 1.1em; font-weight: 500; transition: background 0.2s; }
        .main-buttons a:hover { background: #364fc7; }
        .footer { text-align: center; color: #888; margin-top: 40px; font-size: 1em; }
    </style>
</head>
<body>
    <div class="container">
        <img src="minion.jpg" alt="" class="main-img">
        <h1>¡Bienvenido a CelebrAPP!</h1>
        <p>Gestiona y recuerda los cumpleaños de tus amigos, familiares y compañeros de forma sencilla y visual.<br>¡Nunca olvides una fecha especial!</p>
        <div class="main-buttons">
            <a href="view_birthdays.php">Ver cumpleaños</a>
            <a href="add_birthday.php">Agregar cumpleaños</a>
        </div>
    </div>
    <div class="footer">
        CelebrAPP &copy; 2025
    </div>
</body>
</html> 