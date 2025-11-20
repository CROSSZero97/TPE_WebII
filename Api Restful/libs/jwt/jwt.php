<?php

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function base64url_decode($data) {
    $pad = 4 - (strlen($data) % 4);
    if ($pad < 4) $data .= str_repeat('=', $pad);
    return base64_decode(strtr($data, '-_', '+/'));
}

function createJWT($payload) {
    $secret = 'mi1secreto';
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $hdr = base64url_encode($header);
    $pl = base64url_encode(json_encode($payload));
    $sig = hash_hmac('sha256', $hdr . "." . $pl, $secret, true);
    $sig = base64url_encode($sig);
    return $hdr . "." . $pl . "." . $sig;
}

function validateJWT($jwt) {
    $secret = 'mi1secreto';
    $parts = explode('.', $jwt);
    if (count($parts) != 3) return null;
    list($hdr, $pl, $sig) = $parts;
    $valid_sig = base64url_encode(hash_hmac('sha256', $hdr . "." . $pl, $secret, true));
    if (!hash_equals($valid_sig, $sig)) return null;
    $payloadJson = base64url_decode($pl);
    $payload = json_decode($payloadJson);
    if (!$payload) return null;
    if (!isset($payload->exp) || $payload->exp < time()) return null;
    return $payload;
}