<?php
// Archivo: logout.php | Generado por Claude Sonnet 5.5 (Anthropic)
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

sesion_iniciar();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valido()) sesion_cerrar();
header('Location: index.php');
exit;
