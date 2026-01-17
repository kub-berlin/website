<?php declare(strict_types=1);

function sign($s)
{
	global $csrf_secret;
	return hash_hmac('sha256', $s, $csrf_secret);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!isset($_COOKIE['csrf_token']) || $_POST['csrf_token'] !== sign($_COOKIE['csrf_token'])) {
		http_response_code(403);
		die();
	}
}

if (!isset($_COOKIE['csrf_token'])) {
	$_COOKIE['csrf_token'] = bin2hex(random_bytes(5));
	setcookie('csrf_token', $_COOKIE['csrf_token'], [
		'expires' => time() + 60 * 60,
		'secure' => true,
		'httponly' => true,
		'samesite' => 'Strict',
	]);
}
$csrf_token = sign($_COOKIE['csrf_token']);
