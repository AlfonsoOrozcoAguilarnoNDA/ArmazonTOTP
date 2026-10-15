<?php
// Archivo: config.php
// Modelo: Gemini (versión desconocida)

define('DB_HOST', 'localhost');
define('DB_USER', 'usuario_db');
define('DB_PASS', 'password_db');
define('DB_NAME', 'nombre_db');

define('TOTP_EMISOR', 'MiSitioProtegido');
define('TOTP_CUENTA', 'Administrador');

define('MAX_INTENTOS_FALLIDOS', 3);
define('MINUTOS_BLOQUEO', 15);
?>
