-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS gimnasio_app;
USE gimnasio_app;

-- A TENER EN CUENTA : TIPOS DE CUOTA Y CUANTOS 

-- Tabla: Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL,
    apellido1 VARCHAR(20) NOT NULL,
    apellido2 VARCHAR(20) NOT NULL,
    dni varchar(9) not null,
	password varchar(255) not null,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono int not null,
    fecha_nacimiento DATE not null,
    genero ENUM('M', 'F') not null,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP not null
);

-- Tabla: Membresías
CREATE TABLE membresias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    duracion_dias INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL
);

-- Tabla: Membresías de Usuarios
CREATE TABLE usuarios_membresias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    membresia_id INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (membresia_id) REFERENCES membresias(id) ON DELETE CASCADE
);

-- Tabla: Entrenadores
CREATE TABLE entrenadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    especialidad VARCHAR(100),
    email VARCHAR(100),
    telefono VARCHAR(20)
);

-- Tabla: Clases
CREATE TABLE clases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    horario TIME NOT NULL,
    dias_semana VARCHAR(50), -- Ej: "Lunes, Miércoles, Viernes"
    entrenador_id INT,
    FOREIGN KEY (entrenador_id) REFERENCES entrenadores(id) ON DELETE SET NULL
);

-- Tabla: Registro de Asistencia
CREATE TABLE asistencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    clase_id INT NOT NULL,
    fecha DATE NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (clase_id) REFERENCES clases(id) ON DELETE CASCADE
);

-- Tabla: Staff del sistema (opcional)
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'recepcion', 'entrenador') NOT NULL
);

use gimnasio_app;

DELIMITER //

CREATE PROCEDURE Registro_Usuario(
    IN _nombre VARCHAR(20),
    IN _apellido1 VARCHAR(20),
    IN _apellido2 VARCHAR(20),
    IN _dni VARCHAR(9),
    IN _password VARCHAR(255),
    IN _email VARCHAR(100),
    IN _telefono INT,
    IN _fecha_nacimiento DATE,
    IN _genero CHAR(1),
    OUT _resultado INT
)
BEGIN
    DECLARE edad INT;

    -- Código de resultado:
    -- 0 = Éxito
    -- 1 = Faltan campos obligatorios
    -- 2 = Edad menor a 16
    -- 3 = Email inválido (sin @)

    -- 1. Validar campos vacíos o nulos
    IF _nombre IS NULL OR _nombre = '' OR
       _apellido1 IS NULL OR _apellido1 = '' OR
       _apellido2 IS NULL OR _apellido2 = '' OR
       _dni IS NULL OR _dni = '' OR
       _password IS NULL OR _password = '' OR
       _email IS NULL OR _email = '' OR
       _telefono IS NULL OR
       _fecha_nacimiento IS NULL OR
       _genero IS NULL OR _genero = ''
    THEN
        SET _resultado = -1; -- Campos vacíos

    END IF;

    -- 2. Validar edad mínima (16 años)
    SET edad = TIMESTAMPDIFF(YEAR, _fecha_nacimiento, CURDATE());
    IF edad < 16 THEN
        SET _resultado = -2; -- Edad insuficiente

    END IF;

    -- 3. Validar formato básico de email
    IF LOCATE('@', _email) = 0 THEN
        SET _resultado = -3; -- Email inválido

    END IF;

    -- 4. Si todo está bien, insertar el registro
    INSERT INTO usuarios (
        nombre, apellido1, apellido2, dni, password,
        email, telefono, fecha_nacimiento, genero
    )
    VALUES (
        _nombre, _apellido1, _apellido2, _dni, _password,
        _email, _telefono, _fecha_nacimiento, _genero
    );

    SET _resultado = 0; -- Éxito
END;
//
DELIMITER ;


create view vista_Usuarios as 
select usr.nombre as Nombre,
        concat(usr.apellido1,' ',usr.apellido2)as Apellidos,
        usr.dni as DNI,
		usr.email AS Email,
        usr.telefono as Telefono,
        usr.fecha_registro as Fecha_Registro,
        user_member.fecha_inicio as Fecha_Inicio,
        user_member.fecha_fin as Fecha_Fin,
        membre.nombre as Tipo_Membresia 
        
from usuarios usr
left join usuarios_membresias user_member on (usr.id = user_member.id)
left join membresias membre on(user_member.id = membre.id);

insert into staff (username,password,rol)
values
("root","$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO","admin");

INSERT INTO usuarios (nombre, apellido1,apellido2,dni,password, email, telefono, fecha_nacimiento, genero)
VALUES 
('Carlos', 'Gonzalez','Gonzalez',"12345678A","$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO", 'carlos@gym.com', '1234567890', '1990-05-12', 'Masculino'),
('Ana', 'Martinez','Gonzalez',"12345678B","$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO", 'ana@gym.com', '1231231234', '1985-09-23', 'Femenino'),
('Luis', 'Fernandez','Gonzalez',"12345678C", "$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO",'luis@gym.com', '3213214321', '1993-01-30', 'Masculino'),
('Maria', 'Lopez','Gonzalez',"12345678D", "$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO",'maria@gym.com', '5551234567', '1997-11-11', 'Femenino'),
('Pedro', 'Ramirez','Gonzalez',"12345678F", "$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO",'pedro@gym.com', '4443332221', '2000-07-07', 'Masculino');


INSERT INTO membresias (nombre, descripcion, duracion_dias, precio)
VALUES 
('Mensual', 'Acceso completo por un mes', 30, 30.00),
('Trimestral', 'Acceso completo por tres meses', 90, 80.00),
('Anual', 'Acceso completo por un año', 365, 250.00),
('Fin de Semana', 'Solo fines de semana', 30, 15.00),
('Clases Grupales', 'Solo acceso a clases grupales', 30, 20.00);


INSERT INTO usuarios_membresias (usuario_id, membresia_id, fecha_inicio, fecha_fin)
VALUES
(1, 1, '2025-05-01', '2025-05-31'),
(2, 2, '2025-04-15', '2025-07-14'),
(3, 3, '2025-01-01', '2025-12-31'),
(4, 4, '2025-05-05', '2025-06-04'),
(5, 5, '2025-05-10', '2025-06-09');




