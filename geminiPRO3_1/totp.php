<?php
// Archivo: totp.php
// Modelo: Gemini (versión desconocida)

function base32_decodificar($cadena) {
    $alfabeto = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $cadena = strtoupper($cadena);
    $binario = '';
    
    foreach (str_split($cadena) as $caracter) {
        if ($caracter === '=') break;
        $posicion = strpos($alfabeto, $caracter);
        if ($posicion === false) continue;
        $binario .= str_pad(decbin($posicion), 5, '0', STR_PAD_LEFT);
    }
    
    $bytes = '';
    foreach (str_split($binario, 8) as $fragmento) {
        if (strlen($fragmento) === 8) {
            $bytes .= chr(bindec($fragmento));
        }
    }
    return $bytes;
}

function generar_secreto_base32($longitud = 16) {
    $alfabeto = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secreto = '';
    for ($i = 0; $i < $longitud; $i++) {
        $secreto .= $alfabeto[random_int(0, 31)];
    }
    return $secreto;
}

function calcular_totp($secreto_b32, $periodo) {
    $llave = base32_decodificar($secreto_b32);
    // Empaqueta el periodo como un entero de 64 bits Big Endian (J)
    $tiempo_bytes = pack('J', $periodo);
    
    $hash = hash_hmac('sha1', $tiempo_bytes, $llave, true);
    
    $offset = ord($hash[19]) & 0xf;
    
    $codigo_entero = (
        ((ord($hash[$offset+0]) & 0x7f) << 24) |
        ((ord($hash[$offset+1]) & 0xff) << 16) |
        ((ord($hash[$offset+2]) & 0xff) << 8) |
        (ord($hash[$offset+3]) & 0xff)
    );
    
    $totp = $codigo_entero % 1000000;
    return str_pad((string)$totp, 6, '0', STR_PAD_LEFT);
}

function validar_totp($secreto_b32, $codigo_usuario, &$periodo_coincidente = null) {
    $tiempo_actual = floor(time() / 30);
    
    // Tolerancia de ±1 ventana (30 segundos antes y después)
    for ($i = -1; $i <= 1; $i++) {
        $periodo_prueba = $tiempo_actual + $i;
        $totp_calculado = calcular_totp($secreto_b32, $periodo_prueba);
        
        if (hash_equals($totp_calculado, (string)$codigo_usuario)) {
            $periodo_coincidente = $periodo_prueba;
            return true;
        }
    }
    return false;
}
?>
