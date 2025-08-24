<?php declare(strict_types=1);

include_once('config.php');

class HttpException extends Exception {}

function validate_path($path)
{
	if (
		(strlen($path) > 1 && $path[0] !== '/') ||
		substr($path, -1) !== '/' ||
		strpos($path, '..') !== false
	) {
		throw new HttpException('Not Found', 404);
	}
}

function path_pop($path)
{
	$parts = explode('/', $path);
	$tmp = array_pop($parts);
	$tail = array_pop($parts);
	if ($tail === null) {
		throw new HttpException('Not Found', 404);
	}
	array_push($parts, $tmp);
	$head = implode('/', $parts);
	return array($head, $tail);
}

function path_shift($path)
{
	$parts = explode('/', $path);
	$tmp = array_shift($parts);
	$head = array_shift($parts);
	if ($head === null) {
		throw new HttpException('Not Found', 404);
	}
	array_unshift($parts, $tmp);
	$tail = implode('/', $parts);
	return array($head, $tail);
}

function rrm($path)
{
	if (!file_exists($path)) {
		return;
	} elseif (is_dir($path)) {
		foreach (scandir($path) as $fn) {
			if ($fn !== '.' && $fn !== '..') {
				rrm("$path/$fn");
			}
		}
		return rmdir($path);
	} else {
		return unlink($path);
	}
}

function mkdirp($path)
{
	if (!file_exists($path)) {
		mkdir($path, 0777, true);
	}
}

function e($string)
{
	echo htmlspecialchars(strval($string), ENT_QUOTES, 'UTF-8');
}

function cachebust($path)
{
	$hash = hash_file('md5', __DIR__.$path);
	e("$path?$hash");
}
