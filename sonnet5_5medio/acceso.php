<?php
// Archivo: acceso.php (validación con bloqueo y anti-repetición) | Generado por Claude Sonnet 5.5 (Anthropic)
// $modo: 'login' exige acceso ya vinculado; 'vincular' exige vinculación pendiente.
// Devuelve [bool exito, string mensaje]. Todo ocurre en una transacción con la fila bloqueada.
function acceso_validar(mysqli $conn, string $codigo, string $modo): array {
    if (!preg_match('/^\d{' . TOTP_DIGITOS . '}$/', $codigo)) {
        return [false, 'El código debe tener ' . TOTP_DIGITOS . ' dígitos.'];
    }
    $ahora = time();
    mysqli_begin_transaction($conn);
    try {
        $f = mysqli_fetch_assoc(mysqli_query($conn,
            'SELECT secreto, activo, intentos, bloqueado_hasta, ultimo_contador FROM acceso WHERE id = 1 FOR UPDATE'));
        $activo = $f && (int)$f['activo'] === 1;
        if (!$f || $f['secreto'] === null || $f['secreto'] === '' || ($modo === 'login') !== $activo) {
            mysqli_rollback($conn);
            return [false, $modo === 'login' ? 'El acceso aún no está vinculado.' : 'No hay una vinculación pendiente.'];
        }
        if ($ahora < (int)$f['bloqueado_hasta']) {
            mysqli_rollback($conn);
            return [false, 'Bloqueado. Intenta en ' . ceil(((int)$f['bloqueado_hasta'] - $ahora) / 60) . ' minuto(s).'];
        }
        $contador = null;
        if (!totp_verificar($f['secreto'], $codigo, $contador)) {
            $n = (int)$f['intentos'] + 1;
            $bloqueo = 0;
            if ($n >= MAX_INTENTOS) { $bloqueo = $ahora + MINUTOS_BLOQUEO * 60; $n = 0; }
            db_ejecutar($conn, 'UPDATE acceso SET intentos = ?, bloqueado_hasta = ? WHERE id = 1', 'ii', $n, $bloqueo);
            mysqli_commit($conn);
            return [false, $bloqueo ? 'Demasiados intentos. Bloqueado ' . MINUTOS_BLOQUEO . ' minutos.' : 'Código incorrecto.'];
        }
        if ($contador <= (int)$f['ultimo_contador']) {
            mysqli_rollback($conn);
            return [false, 'Código ya utilizado. Espera el siguiente.'];
        }
        db_ejecutar($conn, 'UPDATE acceso SET activo = 1, intentos = 0, bloqueado_hasta = 0, ultimo_contador = ? WHERE id = 1', 'i', $contador);
        mysqli_commit($conn);
        return [true, ''];
    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($conn);
        error_log('acceso_validar: ' . $e->getMessage());
        return [false, 'Error interno.'];
    }
}
