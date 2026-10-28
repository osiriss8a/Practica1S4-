

CREATE TABLE categorias (
    id INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50),
    PRIMARY KEY (id),
    UNIQUE (nombre)
);

INSERT INTO categorias (nombre) VALUES
('Escolar'),
('Personal'),
('Trabajo'),
('Ocio/Deporte'),
('Salud');

CREATE TABLE eventos (
    id INT NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(150) NOT NULL,
    cliente VARCHAR(150),
    fecha DATE,
    hora TIME,
    categoria_id INT NOT NULL,
    ubicacion VARCHAR(200),
    descripcion TEXT,
    PRIMARY KEY (id),
    CONSTRAINT fk_eventos_categoria
        FOREIGN KEY (categoria_id)
        REFERENCES categorias(id)
        ON DELETE RESTRICT
);