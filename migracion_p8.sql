
USE agenda2;

-- Agregar el propietario a cada evento
ALTER TABLE eventos
ADD COLUMN usuario_id INT NULL;

-- Asignar los eventos actuales a Osiris (usuario id 1)
UPDATE eventos
SET usuario_id = 1
WHERE usuario_id IS NULL;

-- Agregar la llave foránea
ALTER TABLE eventos
ADD CONSTRAINT fk_eventos_usuarios
FOREIGN KEY (usuario_id)
REFERENCES usuarios(id)
ON DELETE CASCADE;

-- Verificar los resultados
SELECT id, titulo, usuario_id
FROM eventos;
