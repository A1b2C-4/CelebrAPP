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
    $stmt->bind_result($password_actual);
    $stmt->fetch();
    $stmt->close();

    if ($actual !== $password_actual) {
        $error = 'La contraseña actual es incorrecta.';
    } elseif (strlen($nueva) < 6) {
        $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
    } elseif ($nueva !== $confirmar) {
        $error = 'La nueva contraseña y la confirmación no coinciden.';
    } else {
        // Guardar la nueva contraseña en texto plano
        $stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
        $stmt->bind_param('si', $nueva, $user_id);
        
        if ($stmt->execute()) {
            $mensaje = '✅ ¡Contraseña cambiada exitosamente!';
        } else {
            $error = '❌ Error al cambiar la contraseña.';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<h1>🔐 Cambiar Contraseña</h1>

<?php if ($mensaje): ?>
    <div class="alert-notificacion">
        <?= htmlspecialchars($mensaje) ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert-notificacion alert-error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="post" style="max-width: 500px; margin: 0 auto;">
    <div class="form-group">
        <label for="actual">🔑 Contraseña Actual *</label>
        <div style="position: relative;">
            <input type="password" name="actual" id="actual" required style="padding-right: 50px;">
            <button type="button" onclick="togglePassword('actual')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2em; color: #667eea;">👁️</button>
        </div>
    </div>
    
    <div class="form-group">
        <label for="nueva">🆕 Nueva Contraseña *</label>
        <div style="position: relative;">
            <input type="password" name="nueva" id="nueva" required style="padding-right: 50px;" minlength="6">
            <button type="button" onclick="togglePassword('nueva')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2em; color: #667eea;">👁️</button>
        </div>
        <small style="color: #718096; font-size: 0.9em;">Mínimo 6 caracteres</small>
    </div>
    
    <div class="form-group">
        <label for="confirmar">✅ Confirmar Nueva Contraseña *</label>
        <div style="position: relative;">
            <input type="password" name="confirmar" id="confirmar" required style="padding-right: 50px;">
            <button type="button" onclick="togglePassword('confirmar')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.2em; color: #667eea;">👁️</button>
        </div>
    </div>
    
    <div style="margin-top: 30px; text-align: center;">
        <button type="submit" class="button" style="background: linear-gradient(45deg, #48bb78, #38a169);">💾 Cambiar Contraseña</button>
        <a href="view_birthdays.php" class="button" style="background: linear-gradient(45deg, #718096, #4a5568);">↩️ Volver</a>
    </div>
</form>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const button = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        button.textContent = '🙈';
    } else {
        input.type = 'password';
        button.textContent = '👁️';
    }
}

// Validación en tiempo real
document.getElementById('confirmar').addEventListener('input', function() {
    const nueva = document.getElementById('nueva').value;
    const confirmar = this.value;
    
    if (confirmar && nueva !== confirmar) {
        this.style.borderColor = '#e53e3e';
        this.style.boxShadow = '0 0 0 3px rgba(229, 62, 62, 0.1)';
    } else {
        this.style.borderColor = '#e2e8f0';
        this.style.boxShadow = 'none';
    }
});
</script>

<?php include 'includes/footer.php'; ?> 