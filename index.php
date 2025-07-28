<?php
session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
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
            max-width: 800px; 
            margin: 60px auto; 
            background: #fff; 
            padding: 50px; 
            border-radius: 25px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center; 
        }
        
        h1 { 
            color: #2d3748; 
            font-size: 3em; 
            margin-bottom: 20px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        p { 
            color: #4a5568; 
            font-size: 1.3em; 
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .main-img { 
            width: 200px; 
            margin-bottom: 30px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }
        
        .main-img:hover {
            transform: scale(1.05);
        }
        
        .main-buttons { 
            margin-top: 40px; 
            display: flex; 
            justify-content: center; 
            gap: 30px;
            flex-wrap: wrap;
        }
        
        .main-buttons a { 
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: #fff; 
            padding: 18px 36px; 
            border-radius: 15px; 
            text-decoration: none; 
            font-size: 1.2em; 
            font-weight: 600; 
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        .main-buttons a:hover { 
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }
        
        .footer { 
            text-align: center; 
            color: #718096; 
            margin-top: 50px; 
            font-size: 1em; 
        }
        
        .welcome-message {
            background: linear-gradient(45deg, #48bb78, #38a169);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            font-size: 1.1em;
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .stat-item {
            background: #f8fafc;
            padding: 20px;
            border-radius: 15px;
            min-width: 150px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            color: #4a5568;
            font-size: 0.9em;
            margin-top: 5px;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 30px;
            }
            
            h1 {
                font-size: 2.2em;
            }
            
            .main-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .stats {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="minion.jpg" alt="CelebrAPP" class="main-img">
        <h1>¡Bienvenido a CelebrAPP!</h1>
        <p>🎉 Nunca olvides una fecha especial con nuestra aplicación de gestión de cumpleaños</p>
        
        <div class="welcome-message">
            ¡Hola <?= htmlspecialchars($_SESSION['username']) ?>! Estamos listos para ayudarte a recordar todos los cumpleaños importantes.
        </div>
        
        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">🎂</div>
                <div class="stat-label">Gestión de Cumpleaños</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">📅</div>
                <div class="stat-label">Recordatorios</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">👥</div>
                <div class="stat-label">Contactos</div>
            </div>
        </div>
        
        <div class="main-buttons">
            <a href="view_birthdays.php">📋 Ver Cumpleaños</a>
            <a href="add_birthday.php">➕ Agregar Cumpleaños</a>
        </div>
    </div>
    <div class="footer">
        CelebrAPP &copy; 2025 - Gestión de Cumpleaños
    </div>
</body>
</html> 