<?php
// Archivo: vincular.php
// Modelo: Gemini (versión desconocida)

require_once 'db.php';
require_once 'totp.php';

// Generar nuevo secreto y guardarlo
$nuevo_secreto = generar_secreto_base32(16);

$stmt = mysqli_prepare($conn, "UPDATE config_sitio SET totp_secreto = ?, intentos = 0, bloqueado_hasta = NULL WHERE id = 1");
mysqli_stmt_bind_param($stmt, "s", $nuevo_secreto);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Generar URI de aprovisionamiento
$emisor_codificado = rawurlencode(TOTP_EMISOR);
$cuenta_codificada = rawurlencode(TOTP_CUENTA);
$uri_otpauth = "otpauth://totp/{$emisor_codificado}:{$cuenta_codificada}?secret={$nuevo_secreto}&issuer={$emisor_codificado}";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vincular Dispositivo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-qrcode"></i> Vincular Authenticator</h4>
                    </div>
                    <div class="card-body">
                        <p>Escanea este código QR con Google Authenticator:</p>
                        <canvas id="qr-code" class="mb-3"></canvas>
                        <p class="mb-1"><strong>Código Manual:</strong></p>
                        <code class="h4 bg-light p-2 border"><?php echo htmlspecialchars($nuevo_secreto); ?></code>
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-success">Ir al Login</a>
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        Generado por Gemini (versión desconocida)
                    </div>
                </div>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i> ¡Importante! Elimina o protege este archivo (vincular.php) después de registrar el código.
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
    <script>
        new QRious({
            element: document.getElementById('qr-code'),
            value: '<?php echo $uri_otpauth; ?>',
            size: 200,
            level: 'M'
        });
    </script>
</body>
</html>
