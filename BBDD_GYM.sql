DROP DATABASE IF EXISTS gimnasio_app;
CREATE DATABASE IF NOT EXISTS gimnasio_app;
USE gimnasio_app;

-- Eliminar vista y procedimiento si existen
DROP VIEW IF EXISTS vista_Usuarios;
DROP PROCEDURE IF EXISTS Registro_Usuario;

-- Eliminar tablas en orden correcto por claves foráneas
DROP TABLE IF EXISTS Tr_Observaciones;
DROP TABLE IF EXISTS Tr_Usuario_Actividades;
DROP TABLE IF EXISTS Tr_Pagos;
DROP TABLE IF EXISTS Tr_Usuarios;
DROP TABLE IF EXISTS Tr_Staff;
DROP TABLE IF EXISTS Tm_Tipo_Incidencias;
DROP TABLE IF EXISTS Tm_Horarios;
DROP TABLE IF EXISTS Tm_Actividades;

create table Tm_Actividades(id_actividad smallint auto_increment primary key,
							nombre varchar(30) not null
);

create table Tm_Horarios(id_horario smallint auto_increment primary key ,
						hora time not null );
                        
create table Tm_Tipo_Incidencias(id_tipo_incidencia smallint auto_increment primary key,
								nombre varchar(20)not null);
-- Tabla: Usuarios
CREATE TABLE Tr_Usuarios (
    id_usuario smallint AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL,
    apellido1 VARCHAR(20) NOT NULL,
    apellido2 VARCHAR(20) NOT NULL,
    dni varchar(9) not null,
	password varchar(255) not null,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono int not null,
    fecha_nacimiento DATE not null,
    genero ENUM('M', 'F') not null,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP not null,
    activo boolean default 1 not null
);

create table Tr_Pagos(id_pago smallint auto_increment primary key,
					fecha_pago timestamp not null,
                    fecha_fin_pago datetime not null,
                    cantidad int not null,
                    id_usuario smallint not null,
                    foreign key (id_usuario) references Tr_Usuarios(id_usuario)
                    );

create table Tr_Usuario_Actividades(id_usuario_actividad smallint auto_increment primary key ,
								id_actividad smallint not null,
                                id_horario smallint not null,
                                id_usuario smallint not null,
                                foreign key (id_actividad) references Tm_Actividades(id_actividad),
                                foreign key (id_horario) references Tm_Horarios(id_horario),
                                foreign key (id_usuario) references Tr_Usuarios(id_usuario)
								);





-- Tabla: Staff del sistema (opcional)
CREATE TABLE Tr_Staff (
    id_staff smallint AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol enum("admin","monitor")
);


create table Tr_Observaciones(id_observacion smallint auto_increment primary key,
							titulo varchar(20) not null,
                            descripcion TEXT not null,
                            id_tipo_incidencia smallint not null,
                            fecha_incidencia datetime not null,
							id_usuario smallint not null,
                            id_staff smallint not null,
                            foreign key (id_tipo_incidencia) references Tm_Tipo_Incidencias(id_tipo_incidencia),
                            foreign key (id_usuario) references Tr_Usuarios(id_usuario),
                            foreign key (id_staff) references Tr_Staff(id_staff)
							);





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
    INSERT INTO Tr_Usuarios (
        nombre, apellido1, apellido2, dni, password,
        email, telefono, fecha_nacimiento, genero
    )
    VALUES (
        _nombre, _apellido1, _apellido2, _dni, _password,
        _email, _telefono, _fecha_nacimiento, _genero
    );

    SET _resultado = 0; -- Éxito
END;

DELIMITER ;

create view vista_Usuarios as 
select usr.nombre as Nombre,
        concat(usr.apellido1,' ',usr.apellido2)as Apellidos,
        usr.dni as DNI,
		usr.email AS Email,
        usr.telefono as Telefono,
         date_format(usr.fecha_registro,"%e %c %Y") as Fecha_Registro,
        date_format(pag.fecha_pago,"%e %c %Y") as Pago,
		 date_format(pag.fecha_fin_pago,"%e %c %Y") AS Fin_Pago
        
        
from Tr_usuarios usr
left join tr_pagos pag on (usr.id_usuario = id_pago);




insert into Tr_staff (username,password,rol)
values
("root","$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO","admin");

INSERT INTO Tr_Usuarios (nombre, apellido1,apellido2,dni,password, email, telefono, fecha_nacimiento, genero)
VALUES 
('Carlos', 'Gonzalez','Gonzalez',"12345678A","$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO", 'carlos@gym.com', '123456789', '1990-05-12', 'M'),
('Ana', 'Martinez','Gonzalez',"12345678B","$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO", 'ana@gym.com', '123123123', '1985-09-23', 'F'),
('Luis', 'Fernandez','Gonzalez',"12345678C", "$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO",'luis@gym.com', '321321432', '1993-01-30', 'M'),
('Maria', 'Lopez','Gonzalez',"12345678D", "$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO",'maria@gym.com', '555123456', '1997-11-11', 'F'),
('Pedro', 'Ramirez','Gonzalez',"12345678F", "$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO",'pedro@gym.com', '444333222', '2000-07-07', 'M');


-- Pagos ficticios para usuarios existentes
INSERT INTO Tr_Pagos (fecha_pago, fecha_fin_pago, cantidad, id_usuario)
VALUES 
  (NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 50, 1),
  (NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 50, 2),
  (NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 50, 3),
  (NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 50, 4),
  (NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 50, 5);












