<?php
// Archivo: db.php | Generado por Claude Sonnet 5.5 (Anthropic)
require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('DB: ' . $e->getMessage());
    http_response_code(500);
    exit('Error interno.');
}

function db_ejecutar(mysqli $c, string $sql, string $tipos = '', ...$valores): void {
    $s = mysqli_prepare($c, $sql);
    if ($tipos !== '') mysqli_stmt_bind_param($s, $tipos, ...$valores);
    mysqli_stmt_execute($s);
    mysqli_stmt_close($s);
}
