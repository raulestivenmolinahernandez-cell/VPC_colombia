DROP DATABASE IF EXISTS gestion_nomina;
CREATE DATABASE gestion_nomina;
USE gestion_nomina;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 1;

-- ===============================
-- USUARIOS
-- ===============================

CREATE TABLE Usuario (
    ID_Usuario INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Usuario VARCHAR(50) NOT NULL,
    Contrasena VARCHAR(255) NOT NULL,
    Rol ENUM('Admin','Empleado','Supervisor') NOT NULL,
    Correo_Electronico VARCHAR(150) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================
-- EMPLEADOS (MISMO ID QUE USUARIO)
-- ===============================

CREATE TABLE Empleado (
    ID_Empleado INT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Apellido VARCHAR(100) NOT NULL,
    Documento_Identidad VARCHAR(50) UNIQUE NOT NULL,
    Cargo VARCHAR(100),
    Horario_Asignado VARCHAR(100),
    Estado ENUM('Activo','Inactivo') DEFAULT 'Activo',
    FOREIGN KEY (ID_Empleado) REFERENCES Usuario(ID_Usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================
-- REGISTRO DE ASISTENCIA
-- ===============================

CREATE TABLE Registro_Asistencia (
    ID_Registro INT AUTO_INCREMENT PRIMARY KEY,
    Fecha DATE NOT NULL,
    Hora_Entrada TIME NOT NULL,
    Hora_Salida TIME,
    Tipo_Registro ENUM('Manual') DEFAULT 'Manual',
    Observaciones VARCHAR(255),
    ID_Empleado INT NOT NULL,
    FOREIGN KEY (ID_Empleado) REFERENCES Empleado(ID_Empleado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================
-- NÓMINA
-- ===============================

CREATE TABLE Nomina (
    ID_Nomina INT AUTO_INCREMENT PRIMARY KEY,
    Periodo VARCHAR(50) NOT NULL,
    Salario_Base DECIMAL(12,2) NOT NULL DEFAULT 0,
    Horas_Trabajadas INT NOT NULL DEFAULT 0,
    Horas_Extras INT DEFAULT 0,
    Deducciones DECIMAL(12,2) DEFAULT 0.00,
    Pago_Total DECIMAL(12,2) DEFAULT 0,
    ID_Empleado INT NOT NULL,
    FOREIGN KEY (ID_Empleado) REFERENCES Empleado(ID_Empleado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================
-- DETALLE DE NÓMINA
-- ===============================

CREATE TABLE Detalle_Nomina (
    ID_Detalle INT AUTO_INCREMENT PRIMARY KEY,
    ID_Nomina INT NOT NULL,
    ID_Registro INT NOT NULL,
    Horas_Contabilizadas INT NOT NULL,
    Tipo_Hora ENUM('Normal','Extra','Festivo'),
    FOREIGN KEY (ID_Nomina) REFERENCES Nomina(ID_Nomina),
    FOREIGN KEY (ID_Registro) REFERENCES Registro_Asistencia(ID_Registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===============================
-- ROLES
-- ===============================

CREATE TABLE Rol (
    ID_Rol INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Rol ENUM('Admin','Supervisor','Empleado') UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Rol (Nombre_Rol) VALUES ('Admin'), ('Supervisor'), ('Empleado');

ALTER TABLE Usuario
ADD COLUMN ID_Rol INT NULL,
ADD FOREIGN KEY (ID_Rol) REFERENCES Rol(ID_Rol);

UPDATE Usuario
SET ID_Rol = CASE Rol
    WHEN 'Admin' THEN 1
    WHEN 'Supervisor' THEN 2
    WHEN 'Empleado' THEN 3
END;

-- ===============================
-- PERMISOS
-- ===============================

CREATE TABLE Permiso (
    ID_Permiso INT AUTO_INCREMENT PRIMARY KEY,
    Modulo VARCHAR(100) NOT NULL,
    Descripcion VARCHAR(255),
    UNIQUE(Modulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO Permiso (Modulo, Descripcion) VALUES
('Gestion_Trabajadores', 'Registrar y eliminar empleados'),
('Historial_Entradas_Salidas', 'Ver registros de asistencia'),
('Panel_Nomina', 'Ver y gestionar nómina');

CREATE TABLE Rol_Permiso (
    ID_Rol INT,
    ID_Permiso INT,
    PRIMARY KEY (ID_Rol, ID_Permiso),
    FOREIGN KEY (ID_Rol) REFERENCES Rol(ID_Rol) ON DELETE CASCADE,
    FOREIGN KEY (ID_Permiso) REFERENCES Permiso(ID_Permiso) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DELETE FROM Rol_Permiso;

INSERT INTO Rol_Permiso (ID_Rol, ID_Permiso)
SELECT R.ID_Rol, P.ID_Permiso FROM Rol R
JOIN Permiso P ON P.Modulo='Gestion_Trabajadores'
WHERE R.Nombre_Rol = 'Admin';

INSERT INTO Rol_Permiso (ID_Rol, ID_Permiso)
SELECT R.ID_Rol, P.ID_Permiso FROM Rol R
JOIN Permiso P ON P.Modulo='Historial_Entradas_Salidas';

INSERT INTO Rol_Permiso (ID_Rol, ID_Permiso)
SELECT R.ID_Rol, P.ID_Permiso FROM Rol R
JOIN Permiso P ON P.Modulo='Panel_Nomina'
WHERE R.Nombre_Rol='Admin';
