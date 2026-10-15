<?php
// Archivo: protegida.php | Generado por Claude Sonnet 5.5 (Anthropic)
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/vista.php';

sesion_exigir();
vista_inicio('Zona protegida'); ?>
<div class="d-flex justify-content-end mb-4">
  <form method="post" action="logout.php"><?= csrf_campo() ?>
    <button class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
  </form>
</div>
<div class="text-center">
  <h1 class="display-4 text-success"><i class="fas fa-globe-americas"></i> ¡Hola Mundo!</h1>
  <p class="lead mt-3">Entraste con tu código TOTP.</p>
  <p class="text-muted">La sesión caduca tras <?= SESION_INACTIVIDAD_MIN ?> min sin actividad (máximo <?= SESION_MAXIMA_MIN ?> min).</p>
</div>
<?php vista_fin();
