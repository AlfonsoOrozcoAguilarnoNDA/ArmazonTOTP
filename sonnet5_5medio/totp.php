<?php
// Archivo: totp.php | Generado por Claude Sonnet 5.5 (Anthropic)
// TOTP (RFC 6238) sobre HOTP (RFC 4226), Base32 (RFC 4648). PHP puro.
const B32_ALFABETO = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

function base32_codificar(string $bin): string {
    $bits = '';
    foreach (str_split($bin) as $c) $bits .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
    $out = '';
    foreach (str_split($bits, 5) as $g) $out .= B32_ALFABETO[bindec(str_pad($g, 5, '0'))];
    return $out;
}

function base32_decodificar(string $s) {
    $s = strtoupper(rtrim($s, '='));
    if ($s === '') return false;
    $bits = '';
    foreach (str_split($s) as $c) {
        $p = strpos(B32_ALFABETO, $c);
        if ($p === false) return false;
        $bits .= str_pad(decbin($p), 5, '0', STR_PAD_LEFT);
    }
    $out = '';
    foreach (str_split($bits, 8) as $b) if (strlen($b) === 8) $out .= chr(bindec($b));
    return $out;
}

function totp_generar_secreto(): string {
    return base32_codificar(random_bytes(TOTP_BYTES_SECRETO));
}

function totp_codigo(string $clave, int $contador): string {
    $h = hash_hmac('sha1', pack('J', $contador), $clave, true);
    $o = ord($h[19]) & 0x0f;
    $n = ((ord($h[$o]) & 0x7f) << 24) | (ord($h[$o + 1]) << 16) | (ord($h[$o + 2]) << 8) | ord($h[$o + 3]);
    return str_pad((string)($n % (10 ** TOTP_DIGITOS)), TOTP_DIGITOS, '0', STR_PAD_LEFT);
}

// Devuelve true si el código es válido y deja en $contador el periodo que coincidió.
function totp_verificar(string $secreto_b32, string $codigo, ?int &$contador = null): bool {
    $clave = base32_decodificar($secreto_b32);
    if ($clave === false || $clave === '' || !preg_match('/^\d{' . TOTP_DIGITOS . '}$/', $codigo)) return false;
    $ahora = intdiv(time(), TOTP_PERIODO);
    for ($i = -TOTP_VENTANA; $i <= TOTP_VENTANA; $i++) {
        if (hash_equals(totp_codigo($clave, $ahora + $i), $codigo)) {
            $contador = $ahora + $i;
            return true;
        }
    }
    return false;
}

function totp_uri(string $secreto): string {
    $e = rawurlencode(TOTP_EMISOR);
    $c = rawurlencode(TOTP_CUENTA);
    return "otpauth://totp/$e:$c?secret=$secreto&issuer=$e&algorithm=SHA1&digits=" . TOTP_DIGITOS . '&period=' . TOTP_PERIODO;
}
