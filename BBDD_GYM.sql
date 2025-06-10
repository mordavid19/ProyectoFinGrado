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
                                
create table Tm_Dias(id_dia smallint primary key  auto_increment,
					nombre varchar(20) not null);
                    
create table Tm_Grupos_Musculares(id_grupo_muscular smallint primary key auto_increment,
									nombre varchar(30) not null);
                                    
                                    
create table Tr_Ejercicios(id_ejercicio smallint primary key auto_increment,
							nombre varchar(40)not null,
                            id_grupo_muscular smallint not null,
                            foreign key (id_grupo_muscular) references Tm_Grupos_Musculares(id_grupo_muscular));
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

create table Tr_Rutinas(id_rutina smallint primary key auto_increment,
						id_usuario smallint not null,
                        id_dia smallint not null,
                        foreign key (id_usuario)references Tr_Usuarios(id_usuario),
                        foreign key (id_dia) references Tm_DIas(id_dia)
);



CREATE TABLE Tr_Detalle_Rutina (
    id_detalle smallint AUTO_INCREMENT PRIMARY KEY,
    id_rutina smallint not null,
    id_ejercicio smallint not null,
    series INT not null,
    repeticiones INT not null,
    peso DECIMAL(5,2) not null,
    FOREIGN KEY (id_rutina) REFERENCES Tr_Rutinas(id_rutina),
    FOREIGN KEY (id_ejercicio) REFERENCES Tr_Ejercicios(id_ejercicio)
);

create table Tr_Pesos(id_peso smallint primary key auto_increment,
					peso decimal(5,2) not null,
                    fecha_peso timestamp not null);


create table Tr_Usuarios_Pesos(id_usuario_peso smallint primary key auto_increment,
								id_peso smallint not null,
                                id_usuario smallint not null,
                                foreign key (id_peso)references Tr_Pesos(id_peso),
                                foreign key (id_usuario)references Tr_Usuarios (id_usuario));
                                
                                
                                
                                
                                

create table Tr_Pagos(id_pago smallint auto_increment primary key,
					fecha_pago timestamp ,
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
    rol enum("admin","monitor") not null
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

CREATE PROCEDURE Renovar_Pagos(
    IN _id_usuario INT,
    IN _cantidadPago INT

)
BEGIN
    DECLARE _fecha_fin DATETIME;
    
    -- Calcular la fecha de finalización según la cantidad de pago
    IF _cantidadPago = 30 THEN
        SET _fecha_fin = DATE_ADD(NOW(), INTERVAL 1 MONTH);
    ELSEIF _cantidadPago = 90 THEN
        SET _fecha_fin = DATE_ADD(NOW(), INTERVAL 3 MONTH);
    ELSEIF _cantidadPago = 150 THEN
        SET _fecha_fin = DATE_ADD(NOW(), INTERVAL 1 YEAR);
    ELSE
        -- Por defecto, usar proporcional: cada 30 unidades = 1 mes
        SET _fecha_fin = DATE_ADD(NOW(), INTERVAL FLOOR(_cantidadPago / 30) MONTH);
    END IF;

    -- Insertar el pago
    INSERT INTO Tr_Pagos (
        fecha_pago,
        fecha_fin_pago,
        cantidad,
        id_usuario
    ) VALUES (
        NOW(),
        _fecha_fin,
        _cantidadPago,
        _id_usuario
    );
END;
//

DELIMITER ;


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
    IN _cantidadPago int,
    OUT _resultado INT
)
BEGIN
    DECLARE edad INT;
    DECLARE _id_usuario INT;
    BEGIN
        SET _resultado = -4; -- Error inesperado
    END;

    main: BEGIN

        -- Validar campos obligatorios
        IF _nombre IS NULL OR _nombre = '' OR
           _apellido1 IS NULL OR _apellido1 = '' OR
           _apellido2 IS NULL OR _apellido2 = '' OR
           _dni IS NULL OR _dni = '' OR
           _password IS NULL OR _password = '' OR
           _email IS NULL OR _email = '' OR
           _telefono IS NULL OR
           _fecha_nacimiento IS NULL OR
           _genero IS NULL OR _genero = '' THEN
            SET _resultado = -1;
            LEAVE main;
        END IF;

        -- Validar edad mínima
        SET edad = TIMESTAMPDIFF(YEAR, _fecha_nacimiento, CURDATE());
        IF edad < 16 THEN
            SET _resultado = -2;
            LEAVE main;
        END IF;

        -- Validar email básico
        IF LOCATE('@', _email) = 0 THEN
            SET _resultado = -3;
            LEAVE main;
        END IF;

        -- Insertar usuario
        INSERT INTO Tr_Usuarios (
            nombre, apellido1, apellido2, dni, password,
            email, telefono, fecha_nacimiento, genero
        )
        VALUES (
            _nombre, _apellido1, _apellido2, _dni, _password,
            _email, _telefono, _fecha_nacimiento, _genero
        );
        
         SET _id_usuario = LAST_INSERT_ID();

        CALL Renovar_Pagos(_id_usuario, _cantidadPago);
  
        SET _resultado = 0; -- Éxito

    END main;

END;
//

DELIMITER ;



DELIMITER //

CREATE PROCEDURE Registrar_Admin(
    IN _nombreUsuario VARCHAR(50),
    IN _password VARCHAR(255),
    IN _rol VARCHAR(15),
    OUT _resultado INT
)
BEGIN
    DECLARE _existe INT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SET _resultado = -4; -- Error inesperado
    END;

    -- Validar campos obligatorios
    IF _nombreUsuario IS NULL OR _nombreUsuario = '' OR
       _password IS NULL OR _password = '' OR
       _rol IS NULL OR _rol = '' THEN
        SET _resultado = -1; -- Campos requeridos faltantes

    ELSEIF _rol NOT IN ('admin', 'monitor') THEN
        SET _resultado = -2; -- Rol inválido

    ELSE
        -- Verificar si el nombre de usuario ya existe
        SELECT COUNT(*) INTO _existe
        FROM Tr_Staff
        WHERE username = _nombreUsuario;

        IF _existe > 0 THEN
            SET _resultado = -3; -- Usuario duplicado
        ELSE
            -- Insertar nuevo registro en Tr_Staff
            INSERT INTO Tr_Staff (
                username, password, rol
            ) VALUES (
                _nombreUsuario, _password, _rol
            );

            SET _resultado = 0; -- Éxito
        END IF;
    END IF;

END;
//

DELIMITER ;

DELIMITER //

CREATE PROCEDURE Editar_Usuario(
    IN _dni VARCHAR(9),
    IN _email VARCHAR(100),
    IN _password VARCHAR(255),
    IN _telefono INT,
    OUT _resultado INT
)
BEGIN
    DECLARE v_password_actual VARCHAR(255);
    DECLARE v_count INT;

    SET _resultado = 0; -- Éxito por defecto

    -- Validar campos obligatorios
    IF _email IS NULL OR _email = '' THEN
        SET _resultado = -1; -- Email vacío
    ELSEIF _telefono IS NULL THEN
        SET _resultado = -3; -- Teléfono vacío
    ELSEIF LOCATE('@', _email) = 0 THEN
        SET _resultado = -4; -- Email inválido
    ELSE
        -- Verificar existencia del usuario
        SELECT COUNT(*) INTO v_count FROM Tr_Usuarios WHERE dni = _dni;
        IF v_count = 0 THEN
            SET _resultado = -5; -- Usuario no encontrado
        ELSE
            -- Si se envió una contraseña, validar si es diferente
            IF _password IS NOT NULL AND _password != '' THEN
                SELECT password INTO v_password_actual FROM Tr_Usuarios WHERE dni = _dni;
                IF v_password_actual = _password THEN
                    SET _resultado = -6; -- Nueva contraseña igual a la actual
                ELSE
                    -- Actualizar todo (correo, teléfono y contraseña)
                    UPDATE Tr_Usuarios
                    SET email = _email,
                        password = _password,
                        telefono = _telefono
                    WHERE dni = _dni;
                END IF;
            ELSE
                -- Actualizar solo correo y teléfono
                UPDATE Tr_Usuarios
                SET email = _email,
                    telefono = _telefono
                WHERE dni = _dni;
            END IF;
        END IF;
    END IF;

END;
//

DELIMITER ;

DELIMITER //

CREATE PROCEDURE Introducir_Pesos (
    IN _dni VARCHAR(9),
    IN _peso DECIMAL(5,2),
    OUT _resultado INT
)
BEGIN
    DECLARE iduser SMALLINT;

    -- Validación de parámetros
    IF _dni IS NULL OR LENGTH(TRIM(_dni)) = 0 OR _peso IS NULL OR _peso <= 0 THEN
        SET _resultado = -1; -- Parámetros inválidos
    ELSE
        -- Buscar ID del usuario por DNI
        SELECT id_usuario INTO iduser
        FROM Tr_usuarios
        WHERE dni = _dni
        LIMIT 1;

        -- Verificar si se encontró el usuario
        IF iduser IS NULL or iduser = "" THEN
            SET _resultado = -2; -- Usuario no encontrado
        ELSE
            -- Insertar en tr_pesos
            INSERT INTO tr_pesos (peso, fecha_peso)
            VALUES (_peso, NOW());

            -- Asociar al usuario
            INSERT INTO tr_usuarios_pesos (id_usuario, id_peso)
            VALUES (iduser, LAST_INSERT_ID());

            SET _resultado = 0; -- Éxito
        END IF;
    END IF;
END;
//

DELIMITER ;





CREATE VIEW vista_Usuarios AS
SELECT 
    usu.nombre AS Nombre,
    usu.apellido1 as Primer_Apellido,
    usu.apellido2 as Segundo_Apellido,
    usu.dni as DNI,
    usu.password AS Contrasenna,
    usu.email as Correo,
    usu.telefono as Telefono,
	TIMESTAMPDIFF(YEAR, usu.fecha_nacimiento, CURDATE()) AS edad,
    usu.genero as Genero,
    usu.fecha_registro as Fecha_Registro,
    pag.cantidad as Cantidad
FROM Tr_Usuarios usu
inner join Tr_Pagos pag on (usu.id_usuario = pag.id_usuario);


create view vista_Usuarios_Admin as 
select usr.nombre as Nombre,
        concat(usr.apellido1,' ',usr.apellido2)as Apellidos,
        usr.dni as DNI,
		usr.email AS Email,
        usr.telefono as Telefono,
        usr.activo as Activo,
        date_format(usr.fecha_registro,"%e %c %Y") as Fecha_Registro,
        date_format(pag.fecha_pago,"%e %c %Y") as Pago,
		date_format(pag.fecha_fin_pago,"%e %c %Y") AS Fin_Pago
from Tr_Usuarios as usr 
left join Tr_Pagos as pag on(usr.id_usuario = pag.id_usuario);

CREATE  VIEW vista_Rutinas AS
SELECT 
    u.id_usuario,
    CONCAT(u.nombre, ' ', u.apellido1, ' ', u.apellido2) AS nombre_completo,
    d.nombre AS dia,
    e.nombre AS ejercicio,
    gm.nombre AS grupo_muscular,
    dr.series,
    dr.repeticiones,
    dr.peso
FROM Tr_Usuarios u
JOIN Tr_Rutinas r ON u.id_usuario = r.id_usuario
JOIN Tm_Dias d ON r.id_dia = d.id_dia
JOIN Tr_Detalle_Rutina dr ON r.id_rutina = dr.id_rutina
JOIN Tr_Ejercicios e ON dr.id_ejercicio = e.id_ejercicio
JOIN Tm_Grupos_Musculares gm ON e.id_grupo_muscular = gm.id_grupo_muscular;


Create view vista_observaciones as

	select usu.email as Correo_Usuario,
			obs.id_observacion as ID,
            obs.titulo AS Titulo,
            obs.descripcion as Descripcion,
            obs.fecha_incidencia as Fecha_Incidencia,
            tipinc.nombre as Tipo
	from Tr_usuarios usu
	inner join Tr_observaciones obs on (usu.id_usuario = obs.id_usuario)
	inner join Tr_Staff staff on(obs.id_staff = staff.id_staff)
	inner join Tm_tipo_incidencias tipinc on(obs.id_tipo_incidencia=tipinc.id_tipo_incidencia);



CREATE VIEW vista_pesos AS 
SELECT 
    usu.dni AS DNI,
    pes.peso AS Peso,
    DATE_FORMAT(pes.fecha_peso, "%e %c %Y") AS Fecha_Pesaje,
    
    -- Media por mes
    AVG(pes.peso) OVER (
        PARTITION BY usu.id_usuario, YEAR(pes.fecha_peso), MONTH(pes.fecha_peso)
    ) AS Promedio_Mensual,

    -- Media por año
    AVG(pes.peso) OVER (
        PARTITION BY usu.id_usuario, YEAR(pes.fecha_peso)
    ) AS Promedio_Anual

FROM Tr_usuarios AS usu
INNER JOIN tr_usuarios_pesos usupeso ON usu.id_usuario = usupeso.id_usuario
INNER JOIN tr_pesos pes ON usupeso.id_peso = pes.id_peso;





DELIMITER //

CREATE EVENT IF NOT EXISTS RenovarPagosPrueba
ON SCHEDULE EVERY 1 MINUTE
DO
BEGIN
    -- 1. Renovar pagos vencidos de usuarios activos
    UPDATE Tr_Pagos p
    JOIN Tr_Usuarios u ON p.id_usuario = u.id_usuario
    SET
        p.fecha_fin_pago = DATE_ADD(NOW(), INTERVAL TIMESTAMPDIFF(DAY, p.fecha_pago, p.fecha_fin_pago) DAY),
        p.fecha_pago = NOW()
    WHERE
        p.fecha_fin_pago <= NOW()
        AND u.activo = 1;

    -- 2. Anular solo fecha_pago de usuarios inactivos con pagos vencidos
    UPDATE Tr_Pagos p
    JOIN Tr_Usuarios u ON p.id_usuario = u.id_usuario
    SET
        p.fecha_pago = NULL
    WHERE
        p.fecha_fin_pago <= NOW()
        AND u.activo = 0;
END;
//

DELIMITER ;








insert into Tr_staff (username,password,rol)
values
("root","$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO","admin");

INSERT INTO Tr_Usuarios (nombre, apellido1,apellido2,dni,password, email, telefono, fecha_nacimiento, genero)
VALUES 
('Carlos', 'Gonzalez','Gonzalez',"12345678A","$2y$10$Vr6g8kDYXMWqPPLeV6iWj.M6IywpPsXmAXrfhnhhwyiSwhbv3qs8G", 'carlos@gym.com', '123456789', '1990-05-12', 'M'),
('Ana', 'Martinez','Gonzalez',"12345678B","$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO", 'ana@gym.com', '123123123', '1985-09-23', 'F'),
('Luis', 'Fernandez','Gonzalez',"12345678C", "$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO",'luis@gym.com', '321321432', '1993-01-30', 'M'),
('Maria', 'Lopez','Gonzalez',"12345678D", "$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO",'maria@gym.com', '555123456', '1997-11-11', 'F'),
('Pedro', 'Ramirez','Gonzalez',"12345678F", "$2y$10$nipFq.xgp4PRevbs238uDeied8hsr6/3YdgDio5382xDquoAsSDdO",'pedro@gym.com', '444333222', '2000-07-07', 'M'),
("David","Belmonte","Moreno","71822694L","$2y$10$Vr6g8kDYXMWqPPLeV6iWj.M6IywpPsXmAXrfhnhhwyiSwhbv3qs8G","david@gmail.com","634512787","2005-10-19","M")
;


-- Pagos ficticios para usuarios existentes
INSERT INTO Tr_Pagos (fecha_pago, fecha_fin_pago, cantidad, id_usuario)
VALUES 
  (NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 30, 1),
  (NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 30, 2),
  (NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 30, 3),
  (NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 50, 4),
  (NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH), 50, 5),
  (NOW(),date_add(now(), INTERVAL 3 MONTH),30 ,6);


INSERT INTO Tm_Actividades (nombre) VALUES
('Spinning'), ('CrossFit'), ('Yoga'), ('Zumba'), ('Boxeo');

INSERT INTO Tm_Horarios (hora) VALUES
('07:00:00'), ('09:00:00'), ('12:00:00'), ('17:00:00'), ('19:00:00');

INSERT INTO Tm_Tipo_Incidencias (nombre) VALUES
('Queja'), ('Averia'), ('Recomendacion'), ('Otro');

INSERT INTO Tm_Dias (nombre) VALUES
('Lunes'), ('Martes'), ('Miércoles'), ('Jueves'), ('Viernes'), ('Sábado'), ('Domingo');

INSERT INTO Tm_Grupos_Musculares (nombre) VALUES
('Pecho'), ('Espalda'), ('Piernas'), ('Bíceps'), ('Tríceps'), ('Hombros'), ('Core');

INSERT INTO Tr_Ejercicios (nombre, id_grupo_muscular) VALUES
('Press banca plano', 1),
('Remo con barra', 2),
('Sentadilla libre', 3),
('Curl bíceps con mancuerna', 4),
('Fondos en paralelas', 5),
('Press militar', 6),
('Crunch abdominal', 7);


INSERT INTO Tr_Rutinas (id_usuario, id_dia) VALUES
(1, 1),
(2, 2);

INSERT INTO Tr_Detalle_Rutina (id_rutina, id_ejercicio, series, repeticiones, peso) VALUES
(1, 1, 4, 10, 80.00),
(1, 5, 3, 12, 50.00),
(2, 2, 4, 10, 60.00),
(2, 4, 3, 15, 10.00);

INSERT INTO Tr_Pesos (peso, fecha_peso) VALUES
(78.5, NOW()),
(64.2, NOW());

INSERT INTO Tr_Usuarios_Pesos (id_peso, id_usuario) VALUES
(1, 1),
(2, 2);



INSERT INTO Tr_Usuario_Actividades (id_actividad, id_horario, id_usuario) VALUES
(1, 1, 1),
(3, 2, 2);


INSERT INTO Tr_Observaciones (titulo, descripcion, id_tipo_incidencia, fecha_incidencia, id_usuario, id_staff) VALUES
('Falta clase', 'El usuario no asistió a su clase del lunes.', 2, NOW(), 1, 1),
('Dolor lumbar', 'El usuario reportó molestia después del remo.', 1, NOW(), 2, 1);









