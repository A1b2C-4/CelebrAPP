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
         .alert-notificacion {
        background: #e6ffed;
        color: #207227;
        border: 1.5px solid #b7e4c7;
        border-radius: 6px;
        padding: 15px 20px;
        margin-bottom: 20px;
        font-weight: bold;
        font-size: 1.1em;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-notificacion::before {
        content: "🎉";
        font-size: 1.5em;
    }
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
            gap: 14px;
            background: transparent;
            padding: 0;
            border-radius: 8px 8px 0 0;
            margin-bottom: 18px;
            align-items: center;
        }
        .main-menu a.menu-btn {
            background: #43b649;
            color: #fff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 1em;
            box-shadow: 0 2px 8px #0001;
            border: none;
            transition: background 0.18s, box-shadow 0.18s;
            display: block;
        }
        .main-menu a.menu-btn:hover, .main-menu a.menu-btn.active {
            background: #2e8b36;
            color: #fff;
            box-shadow: 0 4px 16px #0002;
        }
        .main-menu a.logout-btn {
            background: #e53e3e;
            color: #fff;
            margin-left: 12px;
            border-radius: 6px;
            padding: 12px 24px;
            font-weight: 500;
            font-size: 1em;
            border: none;
            transition: background 0.18s, box-shadow 0.18s;
            box-shadow: 0 2px 8px #0001;
            text-decoration: none;
            display: block;
        }
        .main-menu a.logout-btn:hover {
            background: #b91c1c;
            color: #fff;
            box-shadow: 0 4px 16px #0002;
        }
        .main-menu .user-info {
            margin-left: auto;
            color: #2d3a5a;
            font-weight: 500;
            padding: 0 10px;
            display: flex;
            align-items: center;
        }
        hr { border: none; border-top: 1px solid #e3e7ef; margin: 18px 0; }
    </style>
</head>
<body>
<div class="container">
    <h1>CelebrAPP 🎉</h1>
    <nav class="main-menu">
        <a href="index.php" class="menu-btn <?= basename($_SERVER['PHP_SELF'])=='index.php'?'active':'' ?>">Inicio</a>
        <a href="view_birthdays.php" class="menu-btn <?= basename($_SERVER['PHP_SELF'])=='view_birthdays.php'?'active':'' ?>">Ver cumpleaños</a>
        <a href="add_birthday.php" class="menu-btn <?= basename($_SERVER['PHP_SELF'])=='add_birthday.php'?'active':'' ?>">Agregar cumpleaños</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="logout.php" class="logout-btn">Cerrar sesión</a>
        <?php endif; ?>
    </nav>
    <hr>