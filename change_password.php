<?php
require 'includes/auth.php';
require 'config/database.php';

$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $actual = $_POST['actual'] ?? '';
    $nueva = $_POST['nueva'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    // Obtener la contraseña actual de la base de datos
    $stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($hash_actual);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($actual, $hash_actual)) {
        $error = 'La contraseña actual es incorrecta.';
    } elseif (strlen($nueva) < 6) {
        $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
    } elseif ($nueva !== $confirmar) {
        $error = 'La nueva contraseña y la confirmación no coinciden.';
    } else {
        $nuevo_hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->bind_param('si', $nuevo_hash, $user_id);
        $stmt->execute();
        $stmt->close();
        $mensaje = '¡Contraseña cambiada exitosamente!';
    }
}
include 'includes/header.php';
?>
<h2>Cambiar contraseña</h2>
<?php if ($mensaje): ?>
    <div style="background:#e6ffed;color:#256029;padding:12px 18px;border-radius:6px;margin-bottom:18px;font-weight:bold;">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div style="background:#ffeaea;color:#b00;padding:12px 18px;border-radius:6px;margin-bottom:18px;font-weight:bold;">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
<form method="post" style="max-width:400px;margin:0 auto;">
    <label>Contraseña actual:
        <div style="position:relative;">
            <input type="password" name="actual" id="actual" required style="padding-right:32px;">
            <button type="button" onclick="togglePassword('actual')" style="position:absolute;right:4px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1em;">👁️</button>
        </div>
    </label>
    <label>Nueva contraseña:
        <div style="position:relative;">
            <input type="password" name="nueva" id="nueva" required style="padding-right:32px;">
            <button type="button" onclick="togglePassword('nueva')" style="position:absolute;right:4px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1em;">👁️</button>
        </div>
    </label>
    <label>Confirmar nueva contraseña:
        <div style="position:relative;">
            <input type="password" name="confirmar" id="confirmar" required style="padding-right:32px;">
            <button type="button" onclick="togglePassword('confirmar')" style="position:absolute;right:4px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:1.1em;">👁️</button>
        </div>
    </label>
    <input type="submit" value="Cambiar contraseña">
</form>
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}
</script>
<?php include 'includes/footer.php'; ?> 