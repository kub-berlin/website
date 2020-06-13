<?php declare(strict_types=1);

include_once('config.php');

class HttpException extends Exception {}

$db = new PDO('sqlite:'.__DIR__.'/db.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$db->query("CREATE TABLE IF NOT EXISTS pages (
	id INTEGER PRIMARY KEY,
	slug TEXT,
	layout TEXT,
	order_by INTEGER,
	show_in_nav BOOLEAN,
	parent INTEGER REFERENCES pages(id) ON DELETE CASCADE,
	UNIQUE(slug, parent)
);");
$db->query("CREATE TABLE IF NOT EXISTS translations (
	id INTEGER PRIMARY KEY,
	title TEXT,
	body TEXT,
	page INTEGER REFERENCES pages(id) ON DELETE CASCADE NOT NULL,
	lang TEXT NOT NULL
);");

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

function path_pop(&$path, $mod=true)
{
	$parts = explode('/', $path);
	$tmp = array_pop($parts);
	$result = array_pop($parts);
	if ($result === null) {
		throw new HttpException('Not Found', 404);
	}
	if ($mod) {
		array_push($parts, $tmp);
		$path = implode('/', $parts);
	}
	return $result;
}

function path_shift(&$path, $mod=true)
{
	$parts = explode('/', $path);
	$tmp = array_shift($parts);
	$result = array_shift($parts);
	if ($result === null) {
		throw new HttpException('Not Found', 404);
	}
	if ($mod) {
		array_unshift($parts, $tmp);
		$path = implode('/', $parts);
	}
	return $result;
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

function fetch_or_404($stmt)
{
	$result = $stmt->fetch();
	if (!$result) {
		throw new HttpException('Not Found', 404);
	}
	return $result;
}

function get_lang($code)
{
	$lang = parse_ini_file(__DIR__."/langs/$code.ini");
	if (!$lang) {
		throw new HttpException('Not Found', 404);
	}
	$lang['code'] = $code;
	return $lang;
}

function get_langs()
{
	$langs = array();
	foreach (array('de', 'en', 'fr', 'es', 'ar', 'fa') as $code) {
		$langs[] = get_lang($code);
	}
	return $langs;
}

function get_page_by_id($id)
{
	global $db;
	$stmt = $db->prepare('SELECT * FROM pages WHERE id=:id');
	$stmt->execute(array('id' => $id));
	return fetch_or_404($stmt);
}

function get_page_by_path($path)
{
	global $db;
	if ($path === '/') {
		$stmt = $db->query('SELECT * FROM pages WHERE id=1');
	} else {
		$slug = path_pop($path);
		$parent = get_page_by_path($path);
		$stmt = $db->prepare('SELECT * FROM pages WHERE slug=:slug AND parent=:parent');
		$stmt->execute(array('slug' => $slug, 'parent' => $parent['id']));
	}
	return fetch_or_404($stmt);
}

function get_subpages($id, $include_hidden=false)
{
	global $db;
	$sql = 'SELECT * FROM pages WHERE ';
	$sql .= $id === null ? 'parent IS :parent' : 'parent=:parent';
	$sql .= $include_hidden ? '' : ' AND show_in_nav=1';
	$sql .= ' ORDER BY order_by';
	$stmt = $db->prepare($sql);
	$stmt->execute(array('parent' => $id));
	return $stmt->fetchAll();
}

function get_translation($page_id, $lang_code)
{
	global $db;
	$stmt = $db->prepare('SELECT * FROM translations WHERE page=:page AND lang=:lang');
	$stmt->execute(array('page' => $page_id, 'lang' => $lang_code));
	return $stmt->fetch();
}

function e($string)
{
	echo htmlspecialchars(strval($string), ENT_QUOTES, 'UTF-8');
}

function cachebust($path)
{
	global $baseurl;
	$hash = hash_file('md5', __DIR__.$path);
	e("$baseurl$path?$hash");
}
