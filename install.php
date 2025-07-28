<?php
// Archivo de instalación automática para CelebrAPP

echo "<h1>🎉 Instalación de CelebrAPP</h1>";

// Verificar si ya está instalado
if (file_exists('config/database.php')) {
    echo "<p>✅ La aplicación ya está instalada.</p>";
    echo "<p><a href='login.php'>Ir al login</a></p>";
    exit;
}

// Configuración de la base de datos
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'celebraapp';

try {
    // Conectar sin seleccionar base de datos
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        throw new Exception('Error de conexión: ' . $conn->connect_error);
    }
    
    echo "<p>✅ Conexión a MySQL exitosa</p>";
    
    // Crear base de datos
    $sql = "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        echo "<p>✅ Base de datos '$db_name' creada/verificada</p>";
    } else {
        throw new Exception('Error al crear base de datos: ' . $conn->error);
    }
    
    // Seleccionar la base de datos
    $conn->select_db($db_name);
    
    // Crear tabla users
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_username (username)
    )";
    
    if ($conn->query($sql)) {
        echo "<p>✅ Tabla 'users' creada</p>";
    } else {
        throw new Exception('Error al crear tabla users: ' . $conn->error);
    }
    
    // Crear tabla birthdays
    $sql = "CREATE TABLE IF NOT EXISTS birthdays (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre_completo VARCHAR(100) NOT NULL,
        fecha_nacimiento DATE NOT NULL,
        telefono VARCHAR(20),
        email VARCHAR(100),
        tipo_relacion ENUM('Amigo','Familiar','Compañero','Otro') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_fecha (fecha_nacimiento),
        INDEX idx_tipo (tipo_relacion),
        INDEX idx_nombre (nombre_completo)
    )";
    
    if ($conn->query($sql)) {
        echo "<p>✅ Tabla 'birthdays' creada</p>";
    } else {
        throw new Exception('Error al crear tabla birthdays: ' . $conn->error);
    }
    
    // Insertar usuario administrador
    $sql = "INSERT INTO users (username, password) VALUES ('admin1', 'r') ON DUPLICATE KEY UPDATE password = 'r'";
    if ($conn->query($sql)) {
        echo "<p>✅ Usuario administrador creado (admin1/r)</p>";
    } else {
        throw new Exception('Error al crear usuario: ' . $conn->error);
    }
    
    // Insertar datos de ejemplo
    $sql = "INSERT INTO birthdays (nombre_completo, fecha_nacimiento, telefono, email, tipo_relacion) 
            SELECT * FROM (
                SELECT 'María González' as nombre, '1990-05-15' as fecha, '0991234567' as tel, 'maria@email.com' as email, 'Familiar' as tipo
                UNION ALL
                SELECT 'Juan Pérez', '1985-08-22', '0987654321', 'juan@email.com', 'Amigo'
                UNION ALL
                SELECT 'Ana Rodríguez', '1992-12-03', '0976543210', 'ana@email.com', 'Compañero'
                UNION ALL
                SELECT 'Carlos López', '1988-03-10', '0965432109', 'carlos@email.com', 'Amigo'
                UNION ALL
                SELECT 'Laura Martínez', '1995-07-18', '0954321098', 'laura@email.com', 'Familiar'
            ) AS temp
            WHERE NOT EXISTS (SELECT 1 FROM birthdays LIMIT 1)";
    
    if ($conn->query($sql)) {
        echo "<p>✅ Datos de ejemplo insertados</p>";
    } else {
        echo "<p>⚠️ Los datos de ejemplo ya existen o hubo un error: " . $conn->error . "</p>";
    }
    
    // Crear archivo de configuración
    $config_content = '<?php
// Configuración de la conexión a la base de datos
$host = \'localhost\';
$db   = \'celebraapp\';
$user = \'root\';
$pass = \'\';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        throw new Exception(\'Error de conexión: \' . $conn->connect_error);
    }
    
    // Configurar charset
    $conn->set_charset("utf8");
    
} catch (Exception $e) {
    die(\'Error de base de datos: \' . $e->getMessage());
}
?>';
    
    // Crear directorio config si no existe
    if (!is_dir('config')) {
        mkdir('config', 0755, true);
    }
    
    if (file_put_contents('config/database.php', $config_content)) {
        echo "<p>✅ Archivo de configuración creado</p>";
    } else {
        throw new Exception('Error al crear archivo de configuración');
    }
    
    echo "<div style='background: #e6ffed; color: #256029; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h2>🎉 ¡Instalación Completada!</h2>";
    echo "<p><strong>Credenciales de acceso:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Usuario:</strong> admin1</li>";
    echo "<li><strong>Contraseña:</strong> r</li>";
    echo "</ul>";
    echo "<p><a href='login.php' style='background: #48bb78; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Ir al Login</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #ffeaea; color: #b00; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h2>❌ Error de Instalación</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><strong>Verifica que:</strong></p>";
    echo "<ul>";
    echo "<li>XAMPP esté iniciado (Apache y MySQL)</li>";
    echo "<li>Las credenciales de MySQL sean correctas</li>";
    echo "<li>Tengas permisos para crear archivos y carpetas</li>";
    echo "</ul>";
    echo "</div>";
}

$conn->close();
?> 