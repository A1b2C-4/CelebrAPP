<?php
session_start();
require 'config/database.php';

$nombre = $fecha = $telefono = $email = $tipo = '';
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_completo']);
    $fecha = $_POST['fecha_nacimiento'];
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $tipo = $_POST['tipo_relacion'];

    if ($nombre === '') $errores[] = 'El nombre es obligatorio.';
    if ($fecha === '') $errores[] = 'La fecha de nacimiento es obligatoria.';
    if ($tipo === '') $errores[] = 'El tipo de relación es obligatorio.';

    if (empty($errores)) {
        // Forzar ambas fechas a medianoche
        $hoy = new DateTime('today');
        $anio_actual = $hoy->format('Y');
        $mes_dia_cumple = date('m-d', strtotime($fecha));
        $cumple_this_year = DateTime::createFromFormat('Y-m-d', $anio_actual . '-' . $mes_dia_cumple);
        $cumple_this_year->setTime(0, 0, 0);

        if ($cumple_this_year < $hoy) {
            $cumple_this_year->modify('+1 year');
        }

        $dias_restantes = (int)$hoy->diff($cumple_this_year)->format('%a');

        if ($dias_restantes <= 7) {
            if ($dias_restantes == 0) {
                $_SESSION['mensaje'] = "¡Hoy es el cumpleaños de $nombre!";
            } else {
                $_SESSION['mensaje'] = "¡El cumpleaños de $nombre es en $dias_restantes día(s)!";
            }
        }

        $stmt = $conn->prepare("INSERT INTO birthdays (nombre_completo, fecha_nacimiento, telefono, email, tipo_relacion) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $nombre, $fecha, $telefono, $email, $tipo);
        $stmt->execute();
        $stmt->close();
        header('Location: add_birthday.php');
        exit;
    }
}

include 'includes/header.php';
date_default_timezone_set('America/Guayaquil');
?>
<h2>Agregar cumpleaños</h2>
<?php if (isset($_SESSION['🎉mensaje'])): ?>
    <div class="alert-notificacion">
        <?= htmlspecialchars($_SESSION['mensaje']) ?>
    </div>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>
<?php if ($errores): ?>
    <ul style="color: #b00;">
        <?php foreach ($errores as $e) echo "<li>$e</li>"; ?>
    </ul>
<?php endif; ?>
<form method="post">
    <label>Nombre completo:
        <input type="text" name="nombre_completo" value="<?= htmlspecialchars($nombre) ?>" required>
    </label>
    <label>Fecha de nacimiento:
        <input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($fecha) ?>" required>
    </label>
    <label>Teléfono:
        <input type="text" name="telefono" value="<?= htmlspecialchars($telefono) ?>">
    </label>
    <label>Email:
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>">
    </label>
    <label>Tipo de relación:
        <select name="tipo_relacion" required>
            <option value="">Selecciona...</option>
            <option value="Amigo" <?= $tipo==='Amigo'?'selected':'' ?>>Amigo</option>
            <option value="Familiar" <?= $tipo==='Familiar'?'selected':'' ?>>Familiar</option>
            <option value="Compañero" <?= $tipo==='Compañero'?'selected':'' ?>>Compañero</option>
            <option value="Otro" <?= $tipo==='Otro'?'selected':'' ?>>Otro</option>
        </select>
    </label>
    <br>
    <input type="submit" value="Agregar cumpleaños">
</form>
<?php include 'includes/footer.php'; ?>