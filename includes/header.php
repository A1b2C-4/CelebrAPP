<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CelebrAPP</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fb; margin: 0; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; padding: 32px 40px 40px 40px; border-radius: 14px; box-shadow: 0 4px 24px #0002; }
        h1, h2 { color: #2d3a5a; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; background: #fafbff; }
        th, td { padding: 10px 12px; border-bottom: 1px solid #e3e7ef; text-align: left; }
        th { background: #e9eafd; color: #2d3a5a; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        a.button, input[type=submit], button { background: #4f6bed; color: #fff; padding: 8px 18px; border: none; border-radius: 5px; text-decoration: none; cursor: pointer; transition: background 0.2s; font-size: 1em; }
        a.button:hover, input[type=submit]:hover, button:hover { background: #364fc7; }
        .actions a { margin-right: 8px; }
        form { margin-top: 18px; }
        label { display: block; margin-top: 14px; color: #2d3a5a; font-weight: 500; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #bfc9e0; border-radius: 4px; font-size: 1em; }
        .footer { text-align: center; color: #888; margin-top: 40px; font-size: 1em; }
        /* Menú principal */
        .main-menu {
            display: flex;
            gap: 18px;
            background: #4cc736ff;
            padding: 0 0 0 0;
            border-radius: 8px 8px 0 0;
            margin-bottom: 18px;
        }
        .main-menu a {
            color: #fff;
            text-decoration: none;
            padding: 16px 28px;
            display: block;
            font-weight: 500;
            transition: background 0.2s;
        }
        
        .main-menu a:hover, .main-menu a.active {
            background: #4cc736ff;
        }
        hr { border: none; border-top: 1px solid #e3e7ef; margin: 18px 0; }
    </style>
</head>
<body>
<div class="container">
    <h1>CelebrAPP 🎉</h1>
    <nav class="main-menu">
        <a href="view_birthdays.php" class="<?= basename($_SERVER['PHP_SELF'])=='view_birthdays.php'?'active':'' ?>">Ver cumpleaños</a>
        <a href="add_birthday.php" class="<?= basename($_SERVER['PHP_SELF'])=='add_birthday.php'?'active':'' ?>">Agregar cumpleaños</a>
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">Inicio</a>
       
        <?php
         if (isset($_SESSION['username'])): ?>
            <span style="margin-left:auto; color:#fff; padding:16px 18px;">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php" style="background:#e53e3e; margin-left:8px;">Cerrar sesión</a>
        <?php endif; ?>
    </nav>
    <hr> 