<?php declare(strict_types=1);

ini_set('display_errors', 'On');

class HttpException extends Exception {}

$db = new PDO('sqlite:'.__DIR__.'/db.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$db->query("CREATE TABLE IF NOT EXISTS langs (
	id INTEGER PRIMARY KEY,
	name TEXT NOT NULL,
	code TEXT NOT NULL UNIQUE,
	dir TEXT NOT NULL,
	missing TEXT
);");
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
	lang INTEGER REFERENCES langs(id) ON DELETE CASCADE NOT NULL
);");

function path_pop(&$path, $mod=true)
{
	$parts = explode('/', $path);
	$tmp = array_pop($parts);
	$result = array_pop($parts);
	if ($result === NULL) {
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
	if ($result === NULL) {
		throw new HttpException('Not Found', 404);
	}
	if ($mod) {
		array_unshift($parts, $tmp);
		$path = implode('/', $parts);
	}
	return $result;
}

function fetch_or_404($stmt)
{
	$result = $stmt->fetch();
	if (!$result) {
		throw new HttpException('Not Found', 404);
	}
	return $result;
}

function get_lang_by_id($id)
{
	global $db;
	$stmt = $db->prepare('SELECT * FROM langs WHERE id=:id');
	$stmt->execute(array('id' => $id));
	return fetch_or_404($stmt);
}

function get_lang_by_code($code)
{
	global $db;
	$stmt = $db->prepare('SELECT * FROM langs WHERE code=:code');
	$stmt->execute(array('code' => $code));
	return fetch_or_404($stmt);
}

function get_langs()
{
	global $db;
	return $db->query('SELECT * FROM langs')->fetchAll();
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

function get_translation($page_id, $lang_id)
{
	global $db;
	$stmt = $db->prepare('SELECT * FROM translations WHERE page=:page AND lang=:lang');
	$stmt->execute(array('page' => $page_id, 'lang' => $lang_id));
	return $stmt->fetch();
}

function e($string)
{
	echo htmlspecialchars(strval($string), ENT_QUOTES, 'UTF-8');
}
