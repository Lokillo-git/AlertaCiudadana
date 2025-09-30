CREATE DATABASE alertaciudadana;
USE alertaciudadana;

-- ========================
-- TABLAS PRINCIPALES
-- ========================

-- Usuarios normales
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    genero ENUM('masculino', 'femenino', 'otro') DEFAULT NULL,
    direccion VARCHAR(300) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    foto_perfil LONGBLOB,
    latitud DECIMAL(10, 7),
    longitud DECIMAL(10, 7),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Administradores
CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    foto_perfil LONGBLOB,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categorías
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    encargado_id INT DEFAULT NULL
) ENGINE=InnoDB;

-- Agentes
CREATE TABLE agentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    foto_perfil LONGBLOB,
    datos_faciales LONGBLOB,
    categoria_id INT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_agente_categoria FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Vincular categoría con agente encargado
ALTER TABLE categorias
    ADD CONSTRAINT fk_categoria_encargado FOREIGN KEY (encargado_id)
    REFERENCES agentes(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE;

-- ========================
-- TABLAS DE PROCESOS
-- ========================

-- Denuncias
CREATE TABLE denuncias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    categoria_id INT NOT NULL,
    agente_id INT DEFAULT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    ubicacion VARCHAR(255),
    latitud DECIMAL(10, 7),
    longitud DECIMAL(10, 7),
    estado ENUM('pendiente', 'en_proceso', 'resuelto') DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_denuncia_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_denuncia_categoria FOREIGN KEY (categoria_id)
        REFERENCES categorias(id),
    CONSTRAINT fk_denuncia_agente FOREIGN KEY (agente_id)
        REFERENCES agentes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Evidencias de denuncias
CREATE TABLE evidencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    denuncia_id INT NOT NULL,
    tipo ENUM('foto', 'video') NOT NULL,
    archivo_path LONGBLOB NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_evidencia_denuncia FOREIGN KEY (denuncia_id)
        REFERENCES denuncias(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Pasos por tipo de denuncia
CREATE TABLE pasos_denuncia (
    id_paso INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT NOT NULL,
    descripcion_paso TEXT NOT NULL,
    orden INT NOT NULL,
    CONSTRAINT fk_paso_categoria FOREIGN KEY (id_categoria)
        REFERENCES categorias(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seguimiento de denuncias
CREATE TABLE seguimiento_denuncia (
    id_seguimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_denuncia INT NOT NULL,
    id_paso INT NOT NULL,
    completado BOOLEAN DEFAULT FALSE,
    fecha_completado DATETIME DEFAULT NULL,
    CONSTRAINT fk_seguimiento_denuncia FOREIGN KEY (id_denuncia)
        REFERENCES denuncias(id) ON DELETE CASCADE,
    CONSTRAINT fk_seguimiento_paso FOREIGN KEY (id_paso)
        REFERENCES pasos_denuncia(id_paso) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Evidencias dentro del seguimiento
CREATE TABLE evidencias_seguimiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_seguimiento INT NOT NULL,
    descripcion VARCHAR(255),
    archivo_path LONGBLOB NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_evidencia_seguimiento FOREIGN KEY (id_seguimiento)
        REFERENCES seguimiento_denuncia(id_seguimiento) ON DELETE CASCADE
) ENGINE=InnoDB;

--
INSERT INTO categorias (nombre, encargado_id) VALUES 
('Iluminación Pública Deficiente', NULL),
('Señalización Vial Dañada o Faltante', NULL),
('Baches y Daños en el Pavimento', NULL),
('Semáforos en Mal Estado', NULL),
('Cruces Peatonales Peligrosos', NULL);

-- Categorías de Infraestructura Urbana
INSERT INTO categorias (nombre, encargado_id) VALUES 
('Alcantarillas y Drenajes Tapados', NULL),
('Aceras en Mal Estado', NULL),
('Mobiliario Urbano Dañado', NULL),
('Árboles o Ramas Peligrosas', NULL),
('Postes o Torres en Riesgo de Caída', NULL);

-- Categorías de Seguridad Ciudadana
INSERT INTO categorias (nombre, encargado_id) VALUES 
('Falta de Vigilancia Policial', NULL),
('Zonas con Alta Incidencia Delictiva', NULL),
('Espacios Públicos Abandonados', NULL),
('Alumbrado Público Insuficiente', NULL),
('Cámaras de Seguridad No Operativas', NULL);

-- Categorías de Construcción y Obras
INSERT INTO categorias (nombre, encargado_id) VALUES 
('Obras Públicas sin Señalizar', NULL),
('Construcciones Ilegales', NULL),
('Escombros en la Vía Pública', NULL),
('Maquinaria Pesada en Calles', NULL),
('Andamios o Estructuras Inseguras', NULL);

-- Categorías de Riesgos Ambientales
INSERT INTO categorias (nombre, encargado_id) VALUES 
('Inundaciones en Vías Públicas', NULL),
('Contaminación Acústica', NULL),
('Quemas o Fogatas Ilegales', NULL),
('Residuos Peligrosos en la Vía', NULL),
('Fugas de Agua o Gas', NULL);

-- Categorías de Comercio y Espacio Público
INSERT INTO categorias (nombre, encargado_id) VALUES 
('Comercio Ambulante Ilegal', NULL),
('Vehículos Abandonados', NULL),
('Publicidad en Mal Estado', NULL),
('Obstrucción de Vías Públicas', NULL),
('Ruido por Establishments Nocturnos', NULL);

