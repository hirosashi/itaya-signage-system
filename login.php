<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sentToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (signage_verify_token($sentToken)) {
        signage_json_response([
            'ok' => true,
            'authenticated' => true,
            'csrfToken' => $sentToken,
        ]);
    }
    signage_json_response([
        'ok' => true,
        'authenticated' => signage_is_authenticated(),
        'csrfToken' => signage_is_authenticated() ? signage_csrf_token() : '',
    ]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    signage_json_response(['ok' => false, 'error' => 'Method not allowed'], 405);
}

$raw = file_get_contents('php://input');
$decoded = json_decode($raw ?: '', true);
$password = is_array($decoded) ? (string) ($decoded['password'] ?? '') : '';

if (!password_verify($password, SIGNAGE_ADMIN_PASSWORD_HASH)) {
    signage_json_response(['ok' => false, 'error' => 'Invalid password'], 401);
}

signage_start_session();
session_regenerate_id(true);
$_SESSION[SIGNAGE_AUTH_KEY] = true;
$_SESSION[SIGNAGE_CSRF_KEY] = bin2hex(random_bytes(32));
$token = signage_issue_token();

signage_json_response([
    'ok' => true,
    'authenticated' => true,
    'csrfToken' => $token,
]);
