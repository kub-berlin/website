<?php declare(strict_types=1);

include_once('utils.php');

$db = new PDO($db_dsn, $db_user, $db_password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, true);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

/* HACK to make this compatible with both mysql and sqlite
 * see https://stackoverflow.com/a/41028314 */
$db->query("CREATE TABLE IF NOT EXISTS pages (
	id INTEGER PRIMARY KEY /*!40101 AUTO_INCREMENT */,
	slug VARCHAR(32),
	layout VARCHAR(32),
	order_by INTEGER,
	published BOOLEAN,
	show_in_nav BOOLEAN,
	parent INTEGER,
	twingle_id VARCHAR(50),
	FOREIGN KEY (parent) REFERENCES pages(id) ON DELETE CASCADE,
	UNIQUE (slug, parent)
);");
$db->query("CREATE TABLE IF NOT EXISTS translations (
	id INTEGER PRIMARY KEY /*!40101 AUTO_INCREMENT */,
	title TEXT,
	body TEXT,
	page INTEGER NOT NULL,
	lang VARCHAR(2) NOT NULL,
	FOREIGN KEY (page) REFERENCES pages(id) ON DELETE CASCADE,
	UNIQUE (page, lang)
);");

try {
	$db->query("INSERT INTO pages
		(id, slug, layout, order_by, published, show_in_nav, parent)
		VALUES (1, '', 'home', 10, 1, 0, NULL)
	;");
} catch (PDOException) {}

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

function get_langs($include_incomplete = false)
{
	$langs = array();
	$codes = array('de', 'en', 'fr', 'es', 'ar', 'fa', 'ru');
	foreach ($codes as $code) {
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

function get_page_by_path($path, $include_pub = false)
{
	global $db;
	if ($path === '/') {
		$stmt = $db->query('SELECT * FROM pages WHERE id=1');
	} else {
		list($parent_path, $slug) = path_pop($path);
		$parent = get_page_by_path($parent_path);
		$sql = 'SELECT * FROM pages WHERE slug=:slug AND parent=:parent';
		$sql .= $include_pub ? '' : ' AND published=1';
		$stmt = $db->prepare($sql);
		$stmt->execute(array('slug' => $slug, 'parent' => $parent['id']));
	}
	return fetch_or_404($stmt);
}

function get_subpages($id, $include_nav = false, $include_pub = false)
{
	global $db;
	$sql = 'SELECT * FROM pages WHERE ';
	$sql .= $id === null ? 'parent IS :parent' : 'parent=:parent';
	$sql .= $include_nav ? '' : ' AND show_in_nav=1';
	$sql .= $include_pub ? '' : ' AND published=1';
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
