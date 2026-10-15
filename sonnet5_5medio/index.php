<?php
// Archivo: index.php (login) | Generado por Claude Sonnet 5.5 (Anthropic)
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/totp.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/acceso.php';
require_once __DIR__ . '/vista.php';

sesion_iniciar();
if (sesion_autenticada()) { header('Location: protegida.php'); exit; }

$msg = '';
$info = isset($_GET['vinculado']) ? 'Dispositivo vinculado. Ya puedes entrar.' : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valido()) {
        $msg = 'Solicitud inválida. Recarga la página.';
    } else {
        $codigo = preg_replace('/\D/', '', (string)($_POST['codigo'] ?? ''));
        [$ok, $m] = acceso_validar($conn, $codigo, 'login');
        if ($ok) { sesion_abrir(); header('Location: protegida.php'); exit; }
        $msg = $m;
    }
}
vista_inicio('Acceso', 'bg-dark'); ?>
<div class="row justify-content-center"><div class="col-md-4"><div class="card shadow-lg">
  <div class="card-header bg-dark text-white text-center"><h4 class="mb-0"><i class="fas fa-lock"></i> Acceso restringido</h4></div>
  <div class="card-body">
    <?php if ($info): ?><div class="alert alert-success"><?= h($info) ?></div><?php endif; ?>
    <?php if ($msg): ?><div class="alert alert-danger"><?= h($msg) ?></div><?php endif; ?>
    <form method="post" action="index.php"><?= csrf_campo() ?>
      <div class="form-group text-center">
        <label for="codigo"><i class="fas fa-shield-alt"></i> Código de Google Authenticator</label>
        <input type="text" id="codigo" name="codigo" class="form-control form-control-lg text-center"
               inputmode="numeric" autocomplete="one-time-code" autofocus required>
      </div>
      <button class="btn btn-primary btn-block btn-lg">Entrar</button>
    </form>
  </div>
</div></div></div>
<?php vista_fin();
