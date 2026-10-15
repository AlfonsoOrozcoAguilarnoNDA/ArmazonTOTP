-- Archivo: setup.sql
-- Modelo: Gemini (versión desconocida)

CREATE TABLE IF NOT EXISTS config_sitio (
    id INT PRIMARY KEY,
    totp_secreto VARCHAR(64) NOT NULL,
    intentos INT DEFAULT 0,
    bloqueado_hasta DATETIME DEFAULT NULL,
    ultimo_codigo VARCHAR(6) DEFAULT NULL,
    ultimo_periodo BIGINT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO config_sitio (id, totp_secreto) VALUES (1, '') ON DUPLICATE KEY UPDATE id=1;
