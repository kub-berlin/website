<?php declare(strict_types=1);

function sign($s)
{
	global $secret;
	return hash_hmac('sha256', $s, $secret);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!isset($_POST['csrf_token']) || sign($_POST['csrf_token']) !== $_COOKIE['csrf_token']) {
		http_response_code(403);
		die();
	}
}

$csrf_token = bin2hex(random_bytes(5));
setcookie('csrf_token', sign($csrf_token));
