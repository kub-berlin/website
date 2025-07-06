<?php declare(strict_types=1);

include_once('../config.php');

session_set_cookie_params([
    'path' => $auth['base_path'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

function redirect($url)
{
    header("Location: $url", true, 303);
    exit;
}

function forbidden()
{
    session_destroy();
    header('HTTP/1.1 403 Forbidden');
    echo "403 Forbidden\n";
    exit;
}

function post($url, $data)
{
    return file_get_contents($url, false, stream_context_create([
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ]));
}

function b64($bytes)
{
    $b64 = base64_encode($bytes);
    return rtrim(strtr($b64, '+/', '-_'), '=');
}

function sha256($bytes)
{
    return b64(hash('sha256', $bytes, true));
}

if (isset($_SERVER['HTTP_AUTHORIZATION']) && str_starts_with($_SERVER['HTTP_AUTHORIZATION'], 'Bearer')) {
    if ($_SERVER['HTTP_AUTHORIZATION'] !== "Bearer ${auth['token']}") {
        forbidden();
    }
} elseif (isset($_GET['code'])) {
    if (time() - $_SESSION['started'] > 5 * 60) {
        forbidden();
    }
    if ($_GET['state'] !== $_SESSION['state']) {
        forbidden();
    }
    $response = post($auth['token_endpoint'], [
        'client_id' => $auth['client_id'],
        'client_secret' => $auth['client_secret'],
        'grant_type' => 'authorization_code',
        'code' => $_GET['code'],
        'code_verifier' => $_SESSION['code_verifier'],
    ]);
    if ($response) {
        $_SESSION['last_activity'] = time();
        $_SESSION['logged_in_at'] = time();
        redirect($auth['base_path']);
    } else {
        forbidden();
    }
} elseif (
    !isset($_SESSION['last_activity'])
    || !isset($_SESSION['logged_in_at'])
    || time() - $_SESSION['last_activity'] > 60 * 30
    || time() - $_SESSION['logged_in_at'] > 60 * 60 * 8
) {
    $_SESSION['started'] = time();
    $_SESSION['state'] = b64(random_bytes(32));
    $_SESSION['code_verifier'] = b64(random_bytes(64));
    redirect($auth['authorization_endpoint'] . '?' . http_build_query([
        'client_id' => $auth['client_id'],
        'redirect_uri' => "https://${_SERVER['HTTP_HOST']}${auth['base_path']}",
        'response_type' => 'code',
        'scope' => 'openid',
        'state' => $_SESSION['state'],
        'code_challenge' => sha256($_SESSION['code_verifier']),
        'code_challenge_method' => 'S256',
    ]));
}
