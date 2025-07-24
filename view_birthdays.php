<?php
require 'includes/auth.php';
require 'config/database.php';
include 'includes/header.php';

if (isset($_SESSION['mensaje'])): ?>
    <div class="alert-notificacion">
        <?= htmlspecialchars($_SESSION['mensaje']) ?>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<?php

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
<h2>Lista de cumpleaños</h2>
<?php if (count($cumplenHoy) > 0): ?>
    <div style="background: #e6ffed; color: #256029; padding: 12px 18px; border-radius: 6px; margin-bottom: 12px; font-weight: bold;">
        Hoy cumplen años: <?= htmlspecialchars(implode(', ', $cumplenHoy)) ?>
    </div>
<?php endif; ?>
<?php if (count($proximos) > 0): ?>
    <div style="background: #fffbe6; color: #b04f00ff; padding: 12px 18px; border-radius: 6px; margin-bottom: 18px; font-weight: bold;">
        Próximos cumpleaños (siguientes 7 días): <?= htmlspecialchars(implode(', ', $proximos)) ?>
    </div>
<?php endif; ?>
<table>
    <tr>
        <th>Nombre completo</th>
        <th>Fecha de nacimiento</th>
        <th>Teléfono</th>
        <th>Email</th>
        <th>Relación</th>
        <th>Acciones</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
        <td><?= date('d/m/Y', strtotime($row['fecha_nacimiento'])) ?></td>
        <td><?= htmlspecialchars($row['telefono']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['tipo_relacion']) ?></td>
        <td class="actions">
            
            <a href="edit_birthday.php?id=<?= $row['id'] ?>" class="button">Editar</a>
    </br>
    </br>
            <a href="delete_birthday.php?id=<?= $row['id'] ?>" class="button" onclick="return confirm('¿Seguro que deseas eliminar este cumpleaños?');">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php
include 'includes/footer.php';
?> 
?> 