-- Archivo: setup.sql | Generado por Claude Sonnet 5.5 (Anthropic)
CREATE TABLE IF NOT EXISTS acceso (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    secreto VARCHAR(64) NULL,
    activo TINYINT(1) NOT NULL DEFAULT 0,
    intentos INT UNSIGNED NOT NULL DEFAULT 0,
    bloqueado_hasta BIGINT UNSIGNED NOT NULL DEFAULT 0,
    ultimo_contador BIGINT UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO acceso (id) VALUES (1);
-- Para volver a vincular otro celular (solo por SQL, nunca por web):
-- UPDATE acceso SET secreto=NULL, activo=0, intentos=0, bloqueado_hasta=0, ultimo_contador=0 WHERE id=1;
