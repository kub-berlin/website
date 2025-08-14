<?php declare(strict_types=1);

// this file serves a similar purpose to .htaccess for the dev server

$path = ltrim($_SERVER['REQUEST_URI'], '/');
if (
	str_starts_with($path, 'static/')
	|| str_starts_with($path, 'images/')
	|| str_starts_with($path, 'admin/')
) {
	return false;
}
if ($path !== '' && !str_ends_with($path, '/')) {
	header("Location: /$path/");
} else {
	$_GET['path'] = $path;
	if ($path[2] === '/') {
		include('main.php');
	} else {
		include('negotiation.php');
	}
}
