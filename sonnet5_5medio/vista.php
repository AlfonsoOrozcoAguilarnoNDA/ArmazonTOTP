<?php
// Archivo: vista.php (cabecera y pie comunes) | Generado por Claude Sonnet 5.5 (Anthropic)
function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function vista_inicio(string $titulo, string $clase = 'bg-light'): void {
    header('Cache-Control: no-store');
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<title>' . h($titulo) . '</title>'
       . '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">'
       . '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">'
       . '</head><body class="' . h($clase) . '"><div class="container mt-5">';
}

function vista_fin(string $scripts = ''): void {
    echo '</div><footer class="fixed-bottom text-center p-2 small text-secondary">Generado por '
       . h(MODELO_GENERADOR) . '</footer>' . $scripts . '</body></html>';
}
