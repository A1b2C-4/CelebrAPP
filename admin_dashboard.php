<?php

require 'includes/auth.php';

// Verificar que sea administrador, si es usuario normal redirigir a su dashboard
if (!isAdmin()) {
    header('Location: user_dashboard.php');
    exit;
}

require 'config/database.php';
include 'includes/header.php';

// Mostrar mensajes de notificación
if (isset($_SESSION['mensaje'])): ?>
    <div class="alert-notificacion">
        <?= htmlspecialchars($_SESSION['mensaje']) ?>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php
// Configurar zona horaria
date_default_timezone_set('America/Guayaquil');

// Configurar idioma español
setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain.1252');

// Cumpleaños de hoy
$hoy = date('m-d');
$sqlHoy = "SELECT nombre_completo FROM birthdays WHERE DATE_FORMAT(fecha_nacimiento, '%m-%d') = ?";
$stmtHoy = $conn->prepare($sqlHoy);
$stmtHoy->bind_param('s', $hoy);
$stmtHoy->execute();
$resHoy = $stmtHoy->get_result();
$cumplenHoy = [];
while ($row = $resHoy->fetch_assoc()) {
    $cumplenHoy[] = $row['nombre_completo'];
}
$stmtHoy->close();

// Cumpleaños próximos (siguientes 7 días)
$proximos = [];
$hoyDate = new DateTime();
$finRango = (clone $hoyDate)->modify('+7 days');
$sqlTodos = "SELECT nombre_completo, fecha_nacimiento FROM birthdays";
$resTodos = $conn->query($sqlTodos);
while ($row = $resTodos->fetch_assoc()) {
    $cumple = DateTime::createFromFormat('Y-m-d', $row['fecha_nacimiento']);
    $cumple->setDate($hoyDate->format('Y'), $cumple->format('m'), $cumple->format('d'));
    if ($cumple < $hoyDate) {
        $cumple->modify('+1 year');
    }
    $diff = $hoyDate->diff($cumple)->days;
    if ($diff > 0 && $diff <= 7) {
        $proximos[] = $row['nombre_completo'] . ' (' . $cumple->format('d/m') . ')';
    }
}

// Obtener estadísticas del sistema
$totalCumpleanos = $conn->query("SELECT COUNT(*) as total FROM birthdays")->fetch_assoc()['total'];
$totalUsuarios = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalAdmins = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];

// Obtener todos los cumpleaños
$sql = "SELECT * FROM birthdays ORDER BY fecha_nacimiento ASC";
$result = $conn->query($sql);
?>

<!-- DASHBOARD DE ADMINISTRADOR - INTERFAZ COMPLETA CON PALETA SUAVE -->
<div style="background: linear-gradient(45deg, #ced3d8ff, #bbc3caff); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
    <h1 style="margin: 0; font-size: 2.5em;">👋 Bienvenido</h1>
    <p style="margin: 10px 0 0 0; font-size: 1.2em; opacity: 0.9;">
        </span>
    </p>
</div>

<!-- Botones de estadísticas con paleta suave -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px;">
    <!-- Botón Cumpleaños - Verde Menta Suave -->
    <button onclick="toggleInfo('birthdays')" style="
        background: linear-gradient(135deg, #A8E6CF 0%, #7FCDCD 100%); 
        color: #2C5530; 
        padding: 20px; 
        border-radius: 15px; 
        text-align: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(168, 230, 207, 0.3);
        font-family: 'Segoe UI', sans-serif;
    " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(168, 230, 207, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(168, 230, 207, 0.3)'">
        <div style="font-size: 2.5em; margin-bottom: 10px;">🎂</div>
        <h3 style="margin: 0; font-size: 2em; font-weight: 600;"><?= $totalCumpleanos ?></h3>
        <p style="margin: 5px 0 0 0; opacity: 0.8; font-weight: 500;">Cumpleaños Registrados</p>
        <p style="margin: 8px 0 0 0; font-size: 0.85em; opacity: 0.7;">👆 Clic para ver detalles</p>
    </button>

    <!-- Botón Usuarios - Azul Claro Suave -->
    <button onclick="toggleInfo('users')" style="
        background: linear-gradient(135deg, #A8D8EA 0%, #7FB3D3 100%); 
        color: #1A365D; 
        padding: 20px; 
        border-radius: 15px; 
        text-align: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(168, 216, 234, 0.3);
        font-family: 'Segoe UI', sans-serif;
    " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(168, 216, 234, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(168, 216, 234, 0.3)'">
        <div style="font-size: 2.5em; margin-bottom: 10px;">👥</div>
        <h3 style="margin: 0; font-size: 2em; font-weight: 600;"><?= $totalUsuarios ?></h3>
        <p style="margin: 5px 0 0 0; opacity: 0.8; font-weight: 500;">Usuarios del Sistema</p>
        <p style="margin: 8px 0 0 0; font-size: 0.85em; opacity: 0.7;">👆 Clic para ver detalles</p>
    </button>

    <!-- Botón Administradores - Lavanda Suave -->
    <button onclick="toggleInfo('admins')" style="
        background: linear-gradient(135deg, #D1C4E9 0%, #B39DDB 100%); 
        color: #4A148C; 
        padding: 20px; 
        border-radius: 15px; 
        text-align: center;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(209, 196, 233, 0.3);
        font-family: 'Segoe UI', sans-serif;
    " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(209, 196, 233, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(209, 196, 233, 0.3)'">
        <div style="font-size: 2.5em; margin-bottom: 10px;">🔐</div>
        <h3 style="margin: 0; font-size: 2em; font-weight: 600;"><?= $totalAdmins ?></h3>
        <p style="margin: 5px 0 0 0; opacity: 0.8; font-weight: 500;">Administradores</p>
        <p style="margin: 8px 0 0 0; font-size: 0.85em; opacity: 0.7;">👆 Clic para ver detalles</p>
    </button>
</div>

<!-- Paneles de información con colores suaves -->

<!-- Panel de Cumpleaños - Verde Menta -->
<div id="birthdays-info" style="display: none; background: linear-gradient(135deg, #F0F9F0 0%, #E8F5E8 100%); border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 8px 32px rgba(127, 205, 205, 0.15); border-left: 5px solid #7FCDCD;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #2C5530; font-size: 1.4em; font-weight: 600;">🎂 Cumpleaños</h3>
        <button onclick="toggleInfo('birthdays')" style="background: #E8F5E8; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; color: #2C5530; font-weight: 500; transition: all 0.2s ease;" onmouseover="this.style.background='#D4F4D4'" onmouseout="this.style.background='#E8F5E8'">✖️ Cerrar</button>
    </div>
    <?php
    // Función para traducir meses al español
    function traducirMes($mesIngles) {
        $meses = [
            'January' => 'Enero',
            'February' => 'Febrero', 
            'March' => 'Marzo',
            'April' => 'Abril',
            'May' => 'Mayo',
            'June' => 'Junio',
            'July' => 'Julio',
            'August' => 'Agosto',
            'September' => 'Septiembre',
            'October' => 'Octubre',
            'November' => 'Noviembre',
            'December' => 'Diciembre'
        ];
        return $meses[$mesIngles] ?? $mesIngles;
    }
    
    // Reset query pointers for reuse
    $cumpleanosPorMes = $conn->query("
        SELECT MONTHNAME(fecha_nacimiento) as mes, COUNT(*) as total 
        FROM birthdays 
        GROUP BY MONTH(fecha_nacimiento) 
        ORDER BY MONTH(fecha_nacimiento)
    ");
    
    $cumpleanosPorRelacion = $conn->query("
        SELECT tipo_relacion, COUNT(*) as total 
        FROM birthdays 
        GROUP BY tipo_relacion 
        ORDER BY total DESC
    ");
    ?>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(127, 205, 205, 0.1);">
            <h4 style="color: #2C5530; margin-bottom: 15px; font-size: 1.1em; font-weight: 600;">📅 Mes</h4>
            <?php while($row = $cumpleanosPorMes->fetch_assoc()): ?>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #E8F5E8; color: #2C5530;">
                    <span style="font-weight: 500;"><?= traducirMes($row['mes']) ?></span>
                    <strong style="color: #7FCDCD; font-size: 1.1em;"><?= $row['total'] ?></strong>
                </div>
            <?php endwhile; ?>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(127, 205, 205, 0.1);">
            <h4 style="color: #2C5530; margin-bottom: 15px; font-size: 1.1em; font-weight: 600;">👥 Relación</h4>
            <?php while($row = $cumpleanosPorRelacion->fetch_assoc()): ?>
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #E8F5E8; color: #2C5530;">
                    <span style="font-weight: 500;"><?= $row['tipo_relacion'] ?></span>
                    <strong style="color: #7FCDCD; font-size: 1.1em;"><?= $row['total'] ?></strong>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Panel de Usuarios - Azul Claro -->
<div id="users-info" style="display: none; background: linear-gradient(135deg, #F0F8FF 0%, #E6F3FF 100%); border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 8px 32px rgba(127, 179, 211, 0.15); border-left: 5px solid #7FB3D3;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #1A365D; font-size: 1.4em; font-weight: 600;">👥 Gestión de Usuarios</h3>
        <button onclick="toggleInfo('users')" style="background: #E6F3FF; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; color: #1A365D; font-weight: 500; transition: all 0.2s ease;" onmouseover="this.style.background='#D1E9FF'" onmouseout="this.style.background='#E6F3FF'">✖️ Cerrar</button>
    </div>
    <?php
    // Reset query pointer
    $usuariosDetalle = $conn->query("SELECT id, username, role, created_at FROM users ORDER BY role DESC, created_at DESC");
    ?>
    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(127, 179, 211, 0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: linear-gradient(135deg, #A8D8EA 0%, #7FB3D3 100%);">
                    <th style="padding: 15px; text-align: left; color: #1A365D; font-weight: 600;">ID</th>
                    <th style="padding: 15px; text-align: left; color: #1A365D; font-weight: 600;">Usuario</th>
                    <th style="padding: 15px; text-align: left; color: #1A365D; font-weight: 600;">Rol</th>
                    <th style="padding: 15px; text-align: left; color: #1A365D; font-weight: 600;">Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $usuariosDetalle->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid #E6F3FF;" onmouseover="this.style.background='#F0F8FF'" onmouseout="this.style.background='white'">
                    <td style="padding: 12px; color: #1A365D;"><?= $user['id'] ?></td>
                    <td style="padding: 12px; font-weight: 600; color: #1A365D;"><?= htmlspecialchars($user['username']) ?></td>
                    <td style="padding: 12px;">
                        <?php if($user['role'] === 'admin'): ?>
                            <span style="background: linear-gradient(135deg, #D1C4E9, #B39DDB); color: #4A148C; padding: 6px 12px; border-radius: 12px; font-size: 0.85em; font-weight: 600;">🔹 ADMIN</span>
                        <?php else: ?>
                            <span style="background: linear-gradient(135deg, #A8D8EA, #7FB3D3); color: #1A365D; padding: 6px 12px; border-radius: 12px; font-size: 0.85em; font-weight: 600;">🔸 USER</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 12px; color: #718096; font-size: 0.9em;"><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Panel de Administradores  -->
<div id="admins-info" style="display: none; background: linear-gradient(135deg, #FAF5FF 0%, #F3E8FF 100%); border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 8px 32px rgba(179, 157, 219, 0.15); border-left: 5px solid #B39DDB;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin: 0; color: #4A148C; font-size: 1.4em; font-weight: 600;">🔐 Administradores del Sistema</h3>
        <button onclick="toggleInfo('admins')" style="background: #F3E8FF; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer; color: #4A148C; font-weight: 500; transition: all 0.2s ease;" onmouseover="this.style.background='#E9D5FF'" onmouseout="this.style.background='#F3E8FF'">✖️ Cerrar</button>
    </div>
    <?php
    // Reset query pointer
    $administradores = $conn->query("SELECT id, username, created_at FROM users WHERE role = 'admin' ORDER BY created_at DESC");
    ?>
    <div style="background: linear-gradient(135deg, #FFF8E1 0%, #FFF3C4 100%); padding: 18px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #FFB74D;">
        <p style="margin: 0; color: #E65100; font-weight: 500;"><strong>⚠️ Información Sensible:</strong></p>
    </div>
    <div style="display: grid; gap: 15px;">
        <?php while($admin = $administradores->fetch_assoc()): ?>
        <div style="background: white; padding: 20px; border-radius: 12px; border-left: 4px solid #B39DDB; box-shadow: 0 4px 15px rgba(179, 157, 219, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #4A148C; font-size: 1.2em;">🔹 <?= htmlspecialchars($admin['username']) ?></strong>
                    <p style="margin: 8px 0 0 0; color: #718096; font-size: 0.95em;">
                        Registrado: <?= date('d/m/Y H:i', strtotime($admin['created_at'])) ?>
                    </p>
                </div>
                <div style="text-align: right;">
                    <span style="background: linear-gradient(135deg, #D1C4E9, #B39DDB); color: #4A148C; padding: 8px 16px; border-radius: 12px; font-size: 0.9em; font-weight: 600;">
                        ADMIN
                    </span>
                </div>
            </div>
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #F3E8FF;">
                <small style="color: #718096; line-height: 1.4;">
                    <strong>Permisos:</strong> Control total del sistema, gestión de usuarios, operaciones CRUD completas, acceso a información sensible
                </small>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
/* 
   JAVASCRIPT PARA BOTONES INTERACTIVOS
   Funciones para mostrar/ocultar paneles de información
*/

function toggleInfo(section) {
    // Obtener el panel específico
    const panel = document.getElementById(section + '-info');
    
    // Ocultar todos los otros paneles
    const allPanels = ['birthdays-info', 'users-info', 'admins-info'];
    allPanels.forEach(panelId => {
        if (panelId !== section + '-info') {
            document.getElementById(panelId).style.display = 'none';
        }
    });
    
    // Toggle del panel seleccionado
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
        
        // Scroll suave hacia el panel
        panel.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
        
        // Efecto visual de aparición
        panel.style.opacity = '0';
        panel.style.transform = 'translateY(-20px)';
        
        setTimeout(() => {
            panel.style.transition = 'all 0.3s ease';
            panel.style.opacity = '1';
            panel.style.transform = 'translateY(0)';
        }, 10);
        
    } else {
        // Efecto de desaparición
        panel.style.transition = 'all 0.3s ease';
        panel.style.opacity = '0';
        panel.style.transform = 'translateY(-20px)';
        
        setTimeout(() => {
            panel.style.display = 'none';
        }, 300);
    }
}

// Cerrar paneles al hacer clic fuera de ellos
document.addEventListener('click', function(event) {
    const isButton = event.target.closest('button[onclick*="toggleInfo"]');
    const isPanel = event.target.closest('[id$="-info"]');
    
    if (!isButton && !isPanel) {
        const allPanels = ['birthdays-info', 'users-info', 'admins-info'];
        allPanels.forEach(panelId => {
            const panel = document.getElementById(panelId);
            if (panel.style.display === 'block') {
                panel.style.transition = 'all 0.3s ease';
                panel.style.opacity = '0';
                panel.style.transform = 'translateY(-20px)';
                
                setTimeout(() => {
                    panel.style.display = 'none';
                }, 300);
            }
        });
    }
});
</script>

<!-- Notificaciones de cumpleaños -->
<?php if (count($cumplenHoy) > 0): ?>
    <div style="background: linear-gradient(45deg, #48bb78, #38a169); color: white; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
        🎉 <strong>¡Hoy cumplen años:</strong> <?= htmlspecialchars(implode(', ', $cumplenHoy)) ?>!
    </div>
<?php endif; ?>

<?php if (count($proximos) > 0): ?>
    <div style="background: linear-gradient(45deg, #ed8936, #dd6b20); color: white; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
        ⏰ <strong>Próximos cumpleaños (siguientes 7 días):</strong> <?= htmlspecialchars(implode(', ', $proximos)) ?>
    </div>
<?php endif; ?>

<!-- Panel de acciones administrativas -->
<div style="background: linear-gradient(135deg, #FAFAFA 0%, #F5F5F5 100%); padding: 25px; border-radius: 15px; margin-bottom: 25px; border-left: 5px solid #9B59B6; box-shadow: 0 8px 32px rgba(155, 89, 182, 0.1);">
    <h3 style="margin: 0 0 20px 0; color: #4A148C; font-size: 1.4em; font-weight: 600;">🛠️ Control Administrativo</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px;">
        <a href="add_birthday.php" style="
            background: linear-gradient(135deg, #A8E6CF 0%, #7FCDCD 100%);
            color: #2C5530;
            padding: 18px;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(168, 230, 207, 0.3);
            font-family: 'Segoe UI', sans-serif;
        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(168, 230, 207, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(168, 230, 207, 0.3)'">
            <div style="font-size: 1.8em; margin-bottom: 8px;">➕</div>
            Agregar Cumpleaños
        </a>
        <a href="register_user.php" style="
            background: linear-gradient(135deg, #A8D8EA 0%, #7FB3D3 100%);
            color: #1A365D;
            padding: 18px;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(168, 216, 234, 0.3);
            font-family: 'Segoe UI', sans-serif;
        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(168, 216, 234, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(168, 216, 234, 0.3)'">
            <div style="font-size: 1.8em; margin-bottom: 8px;">👥</div>
            Registrar Usuario
        </a>
        <a href="change_password.php" style="
            background: linear-gradient(135deg, #D1C4E9 0%, #B39DDB 100%);
            color: #4A148C;
            padding: 18px;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(209, 196, 233, 0.3);
            font-family: 'Segoe UI', sans-serif;
        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(209, 196, 233, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(209, 196, 233, 0.3)'">
            <div style="font-size: 1.8em; margin-bottom: 8px;">🔑</div>
            Cambiar Contraseña
        </a>
    </div>
</div>

<!-- Tabla de cumpleaños - CONTROL COMPLETO  -->
<?php if ($result->num_rows > 0): ?>
    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 32px rgba(155, 89, 182, 0.1); border: 1px solid #F0F0F0;">
        <div style="background: linear-gradient(135deg, #6B73FF 0%, #9B59B6 100%); color: white; padding: 20px;">
            <h3 style="margin: 0; font-size: 1.4em; font-weight: 600;">📋 Gestiónnde Cumpleaños</h3>
            <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 0.95em;">Editar, eliminar y gestionar registros</p>
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: linear-gradient(135deg, #FAFAFA 0%, #F0F0F0 100%);">
                    <th style="padding: 18px; text-align: left; color: #4A148C; font-weight: 600; font-size: 0.95em;">👤 Nombre Completo</th>
                    <th style="padding: 18px; text-align: left; color: #4A148C; font-weight: 600; font-size: 0.95em;">📅 Fecha de Nacimiento</th>
                    <th style="padding: 18px; text-align: left; color: #4A148C; font-weight: 600; font-size: 0.95em;">📞 Teléfono</th>
                    <th style="padding: 18px; text-align: left; color: #4A148C; font-weight: 600; font-size: 0.95em;">📧 Email</th>
                    <th style="padding: 18px; text-align: left; color: #4A148C; font-weight: 600; font-size: 0.95em;">👥 Relación</th>
                    <th style="padding: 18px; text-align: center; color: #4A148C; font-weight: 600; font-size: 0.95em;">⚙️ Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid #F5F5F5;" onmouseover="this.style.backgroundColor='#F8F9FA'" onmouseout="this.style.backgroundColor='white'">
                    <td style="padding: 16px; font-weight: 600; color: #2D3748; font-size: 0.95em;">
                        <?= htmlspecialchars($row['nombre_completo']) ?>
                    </td>
                    <td style="padding: 16px; color: #4A5568; font-size: 0.9em;">
                        <?= date('d/m/Y', strtotime($row['fecha_nacimiento'])) ?>
                    </td>
                    <td style="padding: 16px; color: #4A5568; font-size: 0.9em;">
                        <?= htmlspecialchars($row['telefono'] ?: '-') ?>
                    </td>
                    <td style="padding: 16px; color: #4A5568; font-size: 0.9em;">
                        <?= htmlspecialchars($row['email'] ?: '-') ?>
                    </td>
                    <td style="padding: 16px;">
                        <span style="
                            background: <?= $row['tipo_relacion'] === 'Familiar' ? 'linear-gradient(135deg, #A8E6CF, #7FCDCD)' : 
                                          ($row['tipo_relacion'] === 'Amigo' ? 'linear-gradient(135deg, #A8D8EA, #7FB3D3)' : 
                                          ($row['tipo_relacion'] === 'Compañero' ? 'linear-gradient(135deg, #D1C4E9, #B39DDB)' : 'linear-gradient(135deg, #E2E8F0, #CBD5E0)')) ?>;
                            color: <?= $row['tipo_relacion'] === 'Familiar' ? '#2C5530' : 
                                     ($row['tipo_relacion'] === 'Amigo' ? '#1A365D' : 
                                     ($row['tipo_relacion'] === 'Compañero' ? '#4A148C' : '#4A5568')) ?>;
                            padding: 8px 16px;
                            border-radius: 20px;
                            font-size: 0.85em;
                            font-weight: 600;
                            display: inline-block;
                        ">
                            <?= htmlspecialchars($row['tipo_relacion']) ?>
                        </span>
                    </td>
                    <td style="padding: 16px; text-align: center;">
                        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <a href="edit_birthday.php?id=<?= $row['id'] ?>" style="
                                background: linear-gradient(135deg, #A8D8EA 0%, #7FB3D3 100%);
                                color: #1A365D;
                                padding: 10px 18px;
                                border-radius: 8px;
                                text-decoration: none;
                                font-size: 0.85em;
                                font-weight: 600;
                                transition: all 0.3s ease;
                                box-shadow: 0 4px 12px rgba(168, 216, 234, 0.3);
                            " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(168, 216, 234, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(168, 216, 234, 0.3)'">
                                ✏️ Editar
                            </a>
                            <a href="delete_birthday.php?id=<?= $row['id'] ?>" style="
                                background: linear-gradient(135deg, #FFB4B4 0%, #FF9999 100%);
                                color: #8B0000;
                                padding: 10px 18px;
                                border-radius: 8px;
                                text-decoration: none;
                                font-size: 0.85em;
                                font-weight: 600;
                                transition: all 0.3s ease;
                                box-shadow: 0 4px 12px rgba(255, 180, 180, 0.3);
                            " onclick="return confirm('¿Estás seguro de eliminar este cumpleaños?')" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(255, 180, 180, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(255, 180, 180, 0.3)'">
                                🗑️ Eliminar
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div style="text-align: center; padding: 60px; background: linear-gradient(135deg, #F8F9FA 0%, #F0F0F0 100%); border-radius: 15px; color: #718096; box-shadow: 0 8px 32px rgba(155, 89, 182, 0.1);">
        <div style="font-size: 4em; margin-bottom: 20px; opacity: 0.6;">🎂</div>
        <h3 style="margin-bottom: 15px; color: #4A148C; font-weight: 600;">No hay cumpleaños registrados</h3>
        <p style="margin-bottom: 25px; color: #718096; line-height: 1.5;">Comienza agregando el primer cumpleaños al sistema para empezar a gestionar las fechas importantes.</p>
        <a href="add_birthday.php" style="
            background: linear-gradient(135deg, #A8E6CF 0%, #7FCDCD 100%);
            color: #2C5530;
            padding: 18px 35px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1em;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(168, 230, 207, 0.3);
            display: inline-block;
        " onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(168, 230, 207, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 6px 20px rgba(168, 230, 207, 0.3)'">
            ➕ Agregar Primer Cumpleaños
        </a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?> 