<?php
// Archivo: logout.php
// Modelo: Gemini (versión desconocida)

require_once 'sesion.php';
iniciar_sesion_segura();

// Vaciar arreglo de sesión
$_SESSION = array();

// Destruir cookie de sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir sesión en servidor
session_destroy();

header("Location: index.php");
exit;
?>
