<?php
/* Solo leectura para usuarios normales
*/

require 'includes/auth.php';

// Verificar que sea usuario normal, si es admin redirigir a su dashboard
if (isAdmin()) {
    header('Location: admin_dashboard.php');
    exit;
}

require 'config/database.php';
include 'includes/header.php';

// Configurar zona horaria
date_default_timezone_set('America/Guayaquil');

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

// Obtener todos los cumpleaños
$sql = "SELECT * FROM birthdays ORDER BY fecha_nacimiento ASC";
$result = $conn->query($sql);
?>

<!-- DASHBOARD DE USUARIO - INTERFAZ DE SOLO LECTURA -->
<div style="background: linear-gradient(45deg, #ced3d8ff, #bbc3caff); color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
    <h1 style="margin: 0; font-size: 2.5em;">👋 Bienvenido</h1>
    <p style="margin: 10px 0 0 0; font-size: 1.2em; opacity: 0.9;">
        </span>
    </p>
</div>

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

<!-- Panel de información para usuario -->
<div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4299e1;">
    <h3 style="margin: 0 0 10px 0; color: #2d3748;">📋 Tu Vista de Usuario</h3>
    <p style="margin: 0; color: #718096;">
        Puedes <strong>ver todos los cumpleaños</strong> registrados, pero no puedes editarlos o eliminarlos. 
        Solo los administradores tienen permisos para modificar la información.
    </p>
</div>

<!-- Tabla de cumpleaños - SOLO LECTURA -->
<?php if ($result->num_rows > 0): ?>
    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: linear-gradient(45deg, #4299e1, #3182ce); color: white;">
                    <th style="padding: 15px; text-align: left;">👤 Nombre Completo</th>
                    <th style="padding: 15px; text-align: left;">📅 Fecha de Nacimiento</th>
                    <th style="padding: 15px; text-align: left;">📞 Teléfono</th>
                    <th style="padding: 15px; text-align: left;">📧 Email</th>
                    <th style="padding: 15px; text-align: left;">👥 Relación</th>
                    <th style="padding: 15px; text-align: center;">👀 Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr style="border-bottom: 1px solid #e2e8f0;" onmouseover="this.style.backgroundColor='#f7fafc'" onmouseout="this.style.backgroundColor='white'">
                    <td style="padding: 15px; font-weight: bold; color: #2d3748;">
                        <?= htmlspecialchars($row['nombre_completo']) ?>
                    </td>
                    <td style="padding: 15px; color: #4a5568;">
                        <?= date('d/m/Y', strtotime($row['fecha_nacimiento'])) ?>
                    </td>
                    <td style="padding: 15px; color: #4a5568;">
                        <?= htmlspecialchars($row['telefono'] ?: '-') ?>
                    </td>
                    <td style="padding: 15px; color: #4a5568;">
                        <?= htmlspecialchars($row['email'] ?: '-') ?>
                    </td>
                    <td style="padding: 15px;">
                        <span style="
                            background: <?= $row['tipo_relacion'] === 'Familiar' ? '#48bb78' : 
                                          ($row['tipo_relacion'] === 'Amigo' ? '#4299e1' : 
                                          ($row['tipo_relacion'] === 'Compañero' ? '#ed8936' : '#a0aec0')) ?>;
                            color: white;
                            padding: 6px 12px;
                            border-radius: 15px;
                            font-size: 0.85em;
                            font-weight: 500;
                        ">
                            <?= htmlspecialchars($row['tipo_relacion']) ?>
                        </span>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <span style="color: #718096; font-style: italic; background: #f7fafc; padding: 6px 12px; border-radius: 15px; font-size: 0.85em;">
                            👀 Solo Lectura
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div style="text-align: center; padding: 60px; background: #f8f9fa; border-radius: 15px; color: #718096;">
        <div style="font-size: 4em; margin-bottom: 20px;">📋</div>
        <h3 style="margin-bottom: 10px; color: #2d3748;">No hay cumpleaños registrados</h3>
        <p>Aún no hay cumpleaños en el sistema. Los administradores pueden agregar nuevos registros.</p>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?> 