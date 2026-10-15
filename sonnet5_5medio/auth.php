<?php
// Archivo: auth.php (sesión y CSRF) | Generado por Claude Sonnet 5.5 (Anthropic)
function sesion_iniciar(): void {
    if (session_status() !== PHP_SESSION_NONE) return;
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('TOTPSESS');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true, 'samesite' => 'Strict',
    ]);
    session_start();
}

function sesion_cerrar(): void {
    sesion_iniciar();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $p['path'], 'domain' => $p['domain'],
            'secure' => $p['secure'], 'httponly' => $p['httponly'], 'samesite' => $p['samesite']]);
    }
    session_destroy();
}

function sesion_abrir(): void {
    sesion_iniciar();
    session_regenerate_id(true);
    $_SESSION = ['auth' => true, 'inicio' => time(), 'actividad' => time()];
}

function sesion_autenticada(): bool {
    sesion_iniciar();
    if (empty($_SESSION['auth'])) return false;
    $t = time();
    if ($t - ($_SESSION['actividad'] ?? 0) > SESION_INACTIVIDAD_MIN * 60
        || $t - ($_SESSION['inicio'] ?? 0) > SESION_MAXIMA_MIN * 60) {
        sesion_cerrar();
        return false;
    }
    $_SESSION['actividad'] = $t;
    return true;
}

function sesion_exigir(): void {
    if (!sesion_autenticada()) { header('Location: index.php'); exit; }
}

function csrf_token(): string {
    sesion_iniciar();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_campo(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_valido(): bool {
    sesion_iniciar();
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
}
