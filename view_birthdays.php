<?php
require 'includes/auth.php';
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
    // Si ya pasó este año, lo ponemos para el año siguiente
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

<h1>🎂 Gestión de Cumpleaños</h1>

<?php if (count($cumplenHoy) > 0): ?>
    <div class="alert-notificacion">
        🎉 ¡Hoy cumplen años: <?= htmlspecialchars(implode(', ', $cumplenHoy)) ?>!
    </div>
<?php endif; ?>

<?php if (count($proximos) > 0): ?>
    <div class="alert-notificacion alert-warning">
        ⏰ Próximos cumpleaños (siguientes 7 días): <?= htmlspecialchars(implode(', ', $proximos)) ?>
    </div>
<?php endif; ?>

<div style="text-align: right; margin-bottom: 20px;">
    <a href="add_birthday.php" class="button">➕ Agregar Nuevo Cumpleaños</a>
</div>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>👤 Nombre Completo</th>
                <th>📅 Fecha de Nacimiento</th>
                <th>📞 Teléfono</th>
                <th>📧 Email</th>
                <th>👥 Relación</th>
                <th>⚙️ Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><strong><?= htmlspecialchars($row['nombre_completo']) ?></strong></td>
                <td><?= date('d/m/Y', strtotime($row['fecha_nacimiento'])) ?></td>
                <td><?= htmlspecialchars($row['telefono'] ?: '-') ?></td>
                <td><?= htmlspecialchars($row['email'] ?: '-') ?></td>
                <td>
                    <span style="
                        background: <?= $row['tipo_relacion'] === 'Familiar' ? '#48bb78' : 
                                      ($row['tipo_relacion'] === 'Amigo' ? '#4299e1' : 
                                      ($row['tipo_relacion'] === 'Compañero' ? '#ed8936' : '#a0aec0')) ?>;
                        color: white;
                        padding: 4px 8px;
                        border-radius: 12px;
                        font-size: 0.8em;
                        font-weight: 500;
                    ">
                        <?= htmlspecialchars($row['tipo_relacion']) ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="edit_birthday.php?id=<?= $row['id'] ?>" class="button" style="background: linear-gradient(45deg, #4299e1, #3182ce);">✏️ Editar</a>
                    <a href="delete_birthday.php?id=<?= $row['id'] ?>" class="button" style="background: linear-gradient(45deg, #e53e3e, #c53030);" onclick="return confirm('¿Estás seguro de que quieres eliminar este cumpleaños?')">🗑️ Eliminar</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div style="text-align: center; padding: 50px; color: #718096;">
        <div style="font-size: 4em; margin-bottom: 20px;">🎂</div>
        <h3>No hay cumpleaños registrados</h3>
        <p>Comienza agregando el primer cumpleaños a tu lista.</p>
        <a href="add_birthday.php" class="button" style="margin-top: 20px;">➕ Agregar Primer Cumpleaños</a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?> 