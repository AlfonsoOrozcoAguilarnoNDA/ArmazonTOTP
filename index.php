<?php
// Archivo: index.php
// Modelo: Gemini (versión desconocida)

require_once 'db.php';
require_once 'totp.php';
require_once 'sesion.php';

iniciar_sesion_segura();

// Si ya está autenticado, redirigir
if (!empty($_SESSION['autenticado'])) {
    header("Location: protegida.php");
    exit;
}

$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo'])) {
    $codigo_ingresado = preg_replace('/[^0-9]/', '', $_POST['codigo']);
    
    if (strlen($codigo_ingresado) === 6) {
        $stmt = mysqli_prepare($conn, "SELECT totp_secreto, intentos, bloqueado_hasta, ultimo_codigo, ultimo_periodo FROM config_sitio WHERE id = 1");
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $secreto, $intentos, $bloqueo_hasta, $ultimo_codigo, $ultimo_periodo);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        $tiempo_actual = time();
        $esta_bloqueado = false;
        
        if ($bloqueo_hasta !== null) {
            $tiempo_desbloqueo = strtotime($bloqueo_hasta);
            if ($tiempo_actual < $tiempo_desbloqueo) {
                $esta_bloqueado = true;
                $minutos_restantes = ceil(($tiempo_desbloqueo - $tiempo_actual) / 60);
                $mensaje_error = "Sistema bloqueado. Intente nuevamente en {$minutos_restantes} minuto(s).";
            } else {
                // El tiempo de bloqueo expiró, restablecer intentos
                $intentos = 0;
            }
        }
        
        if (!$esta_bloqueado) {
            $periodo_coincidente = null;
            if (validar_totp($secreto, $codigo_ingresado, $periodo_coincidente)) {
                // Prevenir ataque de repetición (Replay Attack)
                if ($ultimo_codigo === $codigo_ingresado && $ultimo_periodo == $periodo_coincidente) {
                    $mensaje_error = "Código ya utilizado. Espere a que genere uno nuevo.";
                } else {
                    // Autenticación exitosa
                    $stmt = mysqli_prepare($conn, "UPDATE config_sitio SET intentos = 0, bloqueado_hasta = NULL, ultimo_codigo = ?, ultimo_periodo = ? WHERE id = 1");
                    mysqli_stmt_bind_param($stmt, "si", $codigo_ingresado, $periodo_coincidente);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    
                    session_regenerate_id(true);
                    $_SESSION['autenticado'] = true;
                    header("Location: protegida.php");
                    exit;
                }
            } else {
                // Código inválido
                $intentos++;
                $nuevo_bloqueo = null;
                
                if ($intentos >= MAX_INTENTOS_FALLIDOS) {
                    $nuevo_bloqueo = date('Y-m-d H:i:s', $tiempo_actual + (MINUTOS_BLOQUEO * 60));
                    $mensaje_error = "Demasiados intentos. Sistema bloqueado por " . MINUTOS_BLOQUEO . " minutos.";
                } else {
                    $restantes = MAX_INTENTOS_FALLIDOS - $intentos;
                    $mensaje_error = "Código incorrecto. Intentos restantes: {$restantes}.";
                }
                
                $stmt = mysqli_prepare($conn, "UPDATE config_sitio SET intentos = ?, bloqueado_hasta = ? WHERE id = 1");
                mysqli_stmt_bind_param($stmt, "is", $intentos, $nuevo_bloqueo);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        $mensaje_error = "El código debe contener exactamente 6 dígitos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Acceso Seguro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
</head>
<body class="bg-dark">
    <div class="container mt-5 pt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white text-center border-bottom border-secondary">
                        <h4><i class="fas fa-lock"></i> Acceso Restringido</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($mensaje_error): ?>
                            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($mensaje_error); ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="index.php">
                            <div class="form-group text-center">
                                <label for="codigo"><i class="fas fa-shield-alt"></i> Ingrese código TOTP</label>
                                <input type="text" class="form-control form-control-lg text-center font-weight-bold shadow-none" 
                                       id="codigo" name="codigo" maxlength="6" autocomplete="off" autofocus required
                                       style="letter-spacing: 10px; font-size: 24px;">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">Entrar</button>
                        </form>
                    </div>
                    <div class="card-footer bg-light text-center text-muted small">
                        Generado por Gemini (versión desconocida)
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
