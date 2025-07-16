<?php
require 'config/database.php';
include 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: view_birthdays.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM birthdays WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$birthday = $result->fetch_assoc();
$stmt->close();

if (!$birthday) {
    echo '<p>No se encontró el cumpleaños.</p>';
    include 'includes/footer.php';
    exit;
}

$nombre = $birthday['nombre_completo'];
$fecha = $birthday['fecha_nacimiento'];
$telefono = $birthday['telefono'];
$email = $birthday['email'];
$tipo = $birthday['tipo_relacion'];
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
        $stmt = $conn->prepare("UPDATE birthdays SET nombre_completo=?, fecha_nacimiento=?, telefono=?, email=?, tipo_relacion=? WHERE id=?");
        $stmt->bind_param('sssssi', $nombre, $fecha, $telefono, $email, $tipo, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: view_birthdays.php');
        exit;
    }
}
?>
<h2>Editar cumpleaños</h2>
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
    <input type="submit" value="Guardar cambios">
</form>
<?php include 'includes/footer.php'; ?> 