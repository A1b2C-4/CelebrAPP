-- CelebrAPP - Base de Datos
-- Aplicación de Gestión de Cumpleaños

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS celebraapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE celebraapp;

-- Tabla de usuarios para login
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username)
);

-- Tabla de cumpleaños
CREATE TABLE IF NOT EXISTS birthdays (
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
);

-- Limpiar datos existentes (opcional)
-- DELETE FROM users WHERE username = 'admin1';
-- DELETE FROM birthdays;

-- Usuario administrador por defecto (usuario: admin1, contraseña: r)
INSERT INTO users (username, password) VALUES ('admin1', 'r') 
ON DUPLICATE KEY UPDATE password = 'r';

-- Datos de ejemplo para cumpleaños (solo si la tabla está vacía)
INSERT INTO birthdays (nombre_completo, fecha_nacimiento, telefono, email, tipo_relacion) 
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
WHERE NOT EXISTS (SELECT 1 FROM birthdays LIMIT 1);

-- Comentarios sobre la estructura
-- La aplicación usa contraseñas en texto plano para simplicidad
-- En producción, se recomienda usar password_hash() y password_verify() 