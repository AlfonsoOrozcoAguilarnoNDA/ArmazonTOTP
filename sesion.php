<?php
// Archivo: sesion.php
// Modelo: Gemini (versión desconocida)

function iniciar_sesion_segura() {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        session_start();
    }
}

function verificar_acceso() {
    iniciar_sesion_segura();
    if (empty($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
        header("Location: index.php");
        exit;
    }
}
?>
