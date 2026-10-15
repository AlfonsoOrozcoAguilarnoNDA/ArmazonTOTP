<?php
// Archivo: vincular.php (vinculación inicial) | Generado por Claude Sonnet 5.5 (Anthropic)
// Solo funciona mientras NO haya un dispositivo activo, exige TOKEN_VINCULACION y
// no activa nada hasta confirmar con un código real del celular.
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/totp.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/acceso.php';
require_once __DIR__ . '/vista.php';

sesion_iniciar();
$f = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT secreto, activo FROM acceso WHERE id = 1'));
if (!$f) { http_response_code(500); exit('Ejecuta setup.sql primero.'); }
if ((int)$f['activo'] === 1) {
    http_response_code(403);
    vista_inicio('Vinculación cerrada');
    echo '<div class="alert alert-secondary text-center">El acceso ya está vinculado. Esta página está cerrada.</div>';
    vista_fin();
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        $msg = 'Solicitud inválida. Recarga la página.';
    } elseif (isset($_POST['token'])) {
        if (strlen(TOKEN_VINCULACION) < 16) {
            $msg = 'Configura TOKEN_VINCULACION (mínimo 16 caracteres) en config.php.';
        } elseif (hash_equals(TOKEN_VINCULACION, (string)$_POST['token'])) {
            $_SESSION['vinculando'] = true;
        } else {
            usleep(700000);
            $msg = 'Token incorrecto.';
        }
    } elseif (isset($_POST['codigo']) && !empty($_SESSION['vinculando'])) {
        [$ok, $m] = acceso_validar($conn, preg_replace('/\D/', '', (string)$_POST['codigo']), 'vincular');
        if ($ok) { unset($_SESSION['vinculando']); header('Location: index.php?vinculado=1'); exit; }
        $msg = $m;
    }
}

$vinculando = !empty($_SESSION['vinculando']);
$uri = '';
if ($vinculando) {
    $secreto = $f['secreto'];
    if ($secreto === null || $secreto === '') {
        $secreto = totp_generar_secreto();
        db_ejecutar($conn, 'UPDATE acceso SET secreto = ? WHERE id = 1 AND activo = 0', 's', $secreto);
    }
    $uri = totp_uri($secreto);
}

vista_inicio('Vincular Authenticator'); ?>
<div class="row justify-content-center"><div class="col-md-6"><div class="card shadow text-center">
  <div class="card-header bg-primary text-white"><h4 class="mb-0"><i class="fas fa-qrcode"></i> Vincular Authenticator</h4></div>
  <div class="card-body">
    <?php if ($msg): ?><div class="alert alert-danger"><?= h($msg) ?></div><?php endif; ?>
    <?php if (!$vinculando): ?>
      <form method="post"><?= csrf_campo() ?>
        <div class="form-group"><label for="token">Token de vinculación</label>
          <input type="password" id="token" name="token" class="form-control" autocomplete="off" required></div>
        <button class="btn btn-primary">Continuar</button>
      </form>
    <?php else: ?>
      <p>1. Escanea el QR con Google Authenticator (o captura la clave a mano).</p>
      <canvas id="qr" class="mb-3"></canvas>
      <p class="mb-1"><strong>Clave manual:</strong></p>
      <code class="h5 d-block p-2 border bg-light"><?= h($secreto) ?></code>
      <p class="mt-4">2. Escribe el código que muestra la app para confirmar:</p>
      <form method="post"><?= csrf_campo() ?>
        <div class="form-group"><input type="text" name="codigo" class="form-control text-center"
             inputmode="numeric" autocomplete="off" required></div>
        <button class="btn btn-success">Confirmar y activar</button>
      </form>
    <?php endif; ?>
  </div>
</div></div></div>
<?php vista_fin($vinculando
    ? '<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>'
      . '<script>new QRious({element: document.getElementById("qr"), value: ' . json_encode($uri) . ', size: 200});</script>'
    : '');
