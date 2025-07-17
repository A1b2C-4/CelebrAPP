--Tabla de usuarios para login
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Usuario administrador por defecto (usuario: admin, contraseña: admin123)
INSERT INTO users (username, password) VALUES ('admin', '$2y$10$w6Qw6Qw6Qw6Qw6Qw6Qw6QeQw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6Qw6');
-- El hash corresponde a la contraseña 'admin123' generada con password_hash('admin123', PASSWORD_DEFAULT)

CREATE TABLE IF NOT EXISTS birthdays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    tipo_relacion ENUM('Amigo','Familiar','Compañero','Otro') NOT NULL
); 