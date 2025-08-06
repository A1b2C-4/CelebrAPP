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
    <title></title>
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
            margin: 0; 
        }
        
        .container { 
            max-width: 1000px; 
            margin: 20px auto; 
            background: #fff; 
            padding: 30px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        h1, h2 { 
            color: #2d3748; 
            margin-bottom: 20px;
        }
        
        h1 {
            font-size: 2.5em;
            text-align: center;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            background: #f8fafc; 
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        th, td { 
            padding: 15px 12px; 
            border-bottom: 1px solid #e2e8f0; 
            text-align: left; 
        }
        
        th { 
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white; 
            font-weight: 600; 
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: 0.5px;
        }
        
        tr:hover {
            background-color: #f1f5f9;
        }
        
        tr:last-child td { 
            border-bottom: none; 
        }
        
        a.button, input[type=submit], button { 
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white; 
            padding: 12px 24px; 
            border: none; 
            border-radius: 8px; 
            text-decoration: none; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            font-size: 1em; 
            font-weight: 500;
            display: inline-block;
            margin: 5px;
        }
        
        a.button:hover, input[type=submit]:hover, button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.4);
        }
        
        .actions a { 
            margin-right: 8px; 
        }
        
        form { 
            margin-top: 20px; 
            background: #f8fafc;
            padding: 25px;
            border-radius: 15px;
        }
        
        label { 
            display: block; 
            margin-top: 15px; 
            color: #2d3748; 
            font-weight: 500; 
            font-size: 0.95em;
        }
        
        input, select { 
            width: 100%; 
            padding: 12px; 
            margin-top: 8px; 
            border: 2px solid #e2e8f0; 
            border-radius: 8px; 
            font-size: 1em; 
            transition: border-color 0.3s ease;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .footer { 
            text-align: center; 
            color: #718096; 
            margin-top: 40px; 
            font-size: 0.9em; 
        }
        
        /* Menú principal */
        .main-menu {
            display: flex;
            gap: 15px;
            background: linear-gradient(45deg, #90b0d1ff, #90b0d1ff);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .main-menu a.menu-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 1em;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .main-menu a.menu-btn:hover, .main-menu a.menu-btn.active {
            background: rgba(255,255,255,0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .main-menu a.logout-btn {
            background:  #dd6b20;
            color: white;
            margin-left: auto;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
            font-size: 1em;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .main-menu a.logout-btn:hover {
            background: #dd6b20;
            transform: translateY(-2px);
        }
        
        .main-menu .user-info {
            margin-left: auto;
            color: white;
            font-weight: 500;
            font-size: 1.1em;
        }
        
        .alert-notificacion {
            background: linear-gradient(45deg, #48bb78, #38a169);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 1.1em;
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .alert-notificacion::before {
            content: "🎉";
            font-size: 1.8em;
        }
        
        .alert-warning {
            background: linear-gradient(45deg, #ed8936, #dd6b20);
            box-shadow: 0 4px 12px rgba(237, 137, 54, 0.3);
        }
        
        .alert-warning::before {
            content: "⚠️";
        }
        
        .alert-error {
            background: linear-gradient(45deg, #e53e3e, #c53030);
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.3);
        }
        
        .alert-error::before {
            content: "❌";
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 20px;
            }
            
            .main-menu {
                flex-direction: column;
                text-align: center;
            }
            
            .main-menu .user-info {
                margin-left: 0;
                margin-top: 10px;
            }
            
            table {
                font-size: 0.9em;
            }
            
            th, td {
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- MENÚ PRINCIPAL DINÁMICO-->
            <div class="main-menu">
                <!-- Enlace al dashboard específico según rol -->
                <?php if (isAdmin()): ?>
                    <a href="admin_dashboard.php" class="menu-btn" style="background: #dd6b20;">Inicio </a>
                <?php else: ?>
                    <a href="user_dashboard.php" class="menu-btn" style="background:  #dd6b20;">Inicio</a>
                <?php endif; ?>
     

                <!-- Información del usuario con indicador de rol -->
                <div class="user-info">
                
                    <!-- INDICADOR VISUAL DE ROL -->
                    <?php if (isAdmin()): ?>
                        <span style="background: #e53e3e; padding: 4px 8px; border-radius: 12px; font-size: 0.8em; margin-left: 8px;">🔴 ADMIN</span>
                    <?php else: ?>
                        <span style="background: #4299e1; padding: 4px 8px; border-radius: 12px; font-size: 0.8em; margin-left: 8px;">🔵 USER</span>
                    <?php endif; ?>
                </div>
                <a href="logout.php" class="logout-btn"> ⏻ Salir </a>
            </div>
        <?php endif; ?>