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

<!-- SISTEMA DE ROLES - PANEL DE CONTROL
     ===================================
     Muestra diferentes elementos según el rol del usuario:
     - ADMIN: Ve indicador rojo, botón de registrar usuarios
     - USER: Ve indicador azul, solo botones básicos
-->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
    <div>
        <h2 style="margin: 0; color: #2d3748;">📝 Lista de Cumpleaños</h2>
        <p style="margin: 5px 0 0 0; color: #718096;">
            <!-- Indicador visual del rol actual -->
            <?php if (isAdmin()): ?>
                <span style="color: #e53e3e; font-weight: bold;">🔴 ADMIN</span> - Tienes permisos completos
            <?php else: ?>
                <span style="color: #4299e1; font-weight: bold;">🔵 USUARIO</span> - Solo lectura
            <?php endif; ?>
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <!-- Botón disponible para todos los usuarios -->
        <a href="add_birthday.php" class="button" style="background: linear-gradient(45deg, #48bb78, #38a169);">➕ Agregar Cumpleaños</a>
        
        <!-- CONTROL DE ACCESO: Botón solo visible para administradores -->
        <?php if (isAdmin()): ?>
            <a href="register_user.php" class="button" style="background: linear-gradient(45deg, #667eea, #764ba2);">👥 Registrar Usuario</a>
        <?php endif; ?>
    </div>
</div>

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

// Obtener todo los cumpleaños
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
        <td>
            <!-- SISTEMA DE ROLES - CONTROL DE BOTONES DE ACCIÓN
                 ===============================================
                 Los botones Editar y Eliminar solo aparecen para ADMIN
                 Los usuarios normales ven mensaje "Solo lectura"
            -->
            <?php if (isAdmin()): ?>
                <!-- Botones disponibles solo para administradores -->
                <a href="edit_birthday.php?id=<?= $row['id'] ?>" class="button">Editar</a>
                <br><br>
                <a href="delete_birthday.php?id=<?= $row['id'] ?>" class="button" onclick="return confirm('¿Seguro que deseas eliminar este cumpleaños?');" style="background: linear-gradient(45deg, #e53e3e, #c53030);">Eliminar</a>
            <?php else: ?>
                <!-- Mensaje para usuarios sin permisos de edición -->
                <span style="color: #718096; font-style: italic;">👀 Solo lectura</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php
include 'includes/footer.php';
?> 
?> 