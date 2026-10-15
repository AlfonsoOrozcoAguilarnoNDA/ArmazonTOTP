<?php
// Archivo: config.php | Generado por Claude Sonnet 5.5 (Anthropic)
const MODELO_GENERADOR = 'Claude Sonnet 5.5 (Anthropic)';

const DB_HOST = 'localhost';
const DB_USER = 'usuario_db';
const DB_PASS = 'password_db';
const DB_NAME = 'nombre_db';

const TOTP_EMISOR = 'MiSitioProtegido';
const TOTP_CUENTA = 'Administrador';
const TOTP_DIGITOS = 6;
const TOTP_PERIODO = 30;        // segundos
const TOTP_VENTANA = 1;         // +-1 periodo de tolerancia
const TOTP_BYTES_SECRETO = 20;  // 160 bits

const MAX_INTENTOS = 5;         // fallos antes del bloqueo (contador global)
const MINUTOS_BLOQUEO = 15;

const SESION_INACTIVIDAD_MIN = 20;
const SESION_MAXIMA_MIN = 480;

// Token de un solo uso para poder vincular. Mínimo 16 caracteres aleatorios.
const TOKEN_VINCULACION = '';
