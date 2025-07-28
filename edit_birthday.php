<?php
require 'includes/auth.php';
require 'config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: view_birthdays.php');
    exit;
}

// Obtener datos del cumpleaños
$stmt = $conn->prepare("SELECT * FROM birthdays WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$birthday = $result->fetch_assoc();
$stmt->close();

if (!$birthday) {
    $_SESSION['mensaje'] = "❌ No se encontró el cumpleaños.";
    header('Location: view_birthdays.php');
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

    // Validaciones
    if (empty($nombre)) $errores[] = 'El nombre es obligatorio.';
    if (empty($fecha)) $errores[] = 'La fecha de nacimiento es obligatoria.';
    if (empty($tipo)) $errores[] = 'El tipo de relación es obligatorio.';
    
    // Validar email si se proporciona
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El formato del email no es válido.';
    }

    if (empty($errores)) {
        $stmt = $conn->prepare("UPDATE birthdays SET nombre_completo=?, fecha_nacimiento=?, telefono=?, email=?, tipo_relacion=? WHERE id=?");
        $stmt->bind_param('sssssi', $nombre, $fecha, $telefono, $email, $tipo, $id);
        
        if ($stmt->execute()) {
            $_SESSION['mensaje'] = "✅ Cumpleaños de $nombre actualizado exitosamente.";
            header('Location: view_birthdays.php');
            exit;
        } else {
            $errores[] = 'Error al actualizar en la base de datos.';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<h1>✏️ Editar Cumpleaños</h1>

<?php if (!empty($errores)): ?>
    <div class="alert-notificacion alert-error">
        <strong>Errores encontrados:</strong>
        <ul style="margin: 10px 0 0 20px;">
            <?php foreach ($errores as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <label for="nombre_completo">👤 Nombre Completo *</label>
            <input type="text" id="nombre_completo" name="nombre_completo" value="<?= htmlspecialchars($nombre) ?>" required placeholder="Ej: Juan Pérez">
        </div>
        
        <div>
            <label for="fecha_nacimiento">📅 Fecha de Nacimiento *</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($fecha) ?>" required>
        </div>
        
        <div>
            <label for="telefono">📞 Teléfono</label>
            <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($telefono) ?>" placeholder="Ej: 0991234567">
        </div>
        
        <div>
            <label for="email">📧 Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Ej: juan@email.com">
        </div>
        
        <div style="grid-column: 1 / -1;">
            <label for="tipo_relacion">👥 Tipo de Relación *</label>
            <select id="tipo_relacion" name="tipo_relacion" required>
                <option value="">Selecciona una opción...</option>
                <option value="Familiar" <?= $tipo === 'Familiar' ? 'selected' : '' ?>>👨‍👩‍👧‍👦 Familiar</option>
                <option value="Amigo" <?= $tipo === 'Amigo' ? 'selected' : '' ?>>👥 Amigo</option>
                <option value="Compañero" <?= $tipo === 'Compañero' ? 'selected' : '' ?>>💼 Compañero</option>
                <option value="Otro" <?= $tipo === 'Otro' ? 'selected' : '' ?>>🤝 Otro</option>
            </select>
        </div>
    </div>
    
    <div style="margin-top: 30px; text-align: center;">
        <button type="submit" class="button" style="background: linear-gradient(45deg, #4299e1, #3182ce);">💾 Guardar Cambios</button>
        <a href="view_birthdays.php" class="button" style="background: linear-gradient(45deg, #718096, #4a5568);">↩️ Cancelar</a>
    </div>
</form>

<script>
// Validación en tiempo real para email
document.getElementById('email').addEventListener('blur', function() {
    const email = this.value;
    if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        this.style.borderColor = '#e53e3e';
        this.style.boxShadow = '0 0 0 3px rgba(229, 62, 62, 0.1)';
        this.setCustomValidity('Por favor, ingresa un email válido');
    } else {
        this.style.borderColor = '#e2e8f0';
        this.style.boxShadow = 'none';
        this.setCustomValidity('');
    }
});
</script>

<?php include 'includes/footer.php'; ?> 