<?php
// Archivo: protegida.php
// Modelo: Gemini (versión desconocida)

require_once 'sesion.php';
verificar_acceso();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Zona Protegida</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <span class="navbar-brand mb-0 h1"><i class="fas fa-shield-check"></i> Sistema Seguro</span>
        <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </nav>
    <div class="container mt-5 text-center">
        <h1 class="display-4 text-success"><i class="fas fa-globe-americas"></i> ¡Hola Mundo!</h1>
        <p class="lead mt-3">Has accedido exitosamente validando únicamente tu código TOTP dinámico.</p>
        <hr class="my-4">
        <p class="text-muted">La sesión está estrictamente ligada al servidor y previene el secuestro mediante regeneración de ID.</p>
    </div>
    
    <footer class="fixed-bottom text-center p-3 text-muted small">
        Generado por Gemini (versión desconocida)
    </footer>
</body>
</html>
