<?php declare(strict_types=1);

include_once('../common.php');

function rrmdir($path)
{
	if (!file_exists($path)) {
		return;
	} elseif (is_dir($path)) {
		foreach (scandir($path) as $fn) {
			if ($fn !== '.' && $fn !== '..') {
				rrmdir("$path/$fn");
			}
		}
		return rmdir($path);
	} else {
		return unlink($path);
	}
}

function render_side_nav($page, $current, $path='/', $maxdepth=10)
{
?>
	<li <?php if ($page['id'] == $current) : ?>class="current"<?php endif ?>>
		<a href="<?php e("?page=${page['id']}") ?>"><?php e($path) ?></a>
	</li>
<?php
	if ($maxdepth > 0) {
		foreach (get_subpages($page['id']) as $p) {
			render_side_nav($p, $current, $path . $p['slug'] . '/', $maxdepth - 1);
		}
	}
}

$page_id = isset($_GET['page']) ? intval($_GET['page']) : 1;
$lang_id = isset($_GET['lang']) ? intval($_GET['lang']) : 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	rrmdir('../cache');

	if ($_GET['action'] === 'create-page') {
		$stmt = $db->prepare('INSERT INTO pages (slug, parent) VALUES (:slug, :parent)');
		$stmt->execute(array('slug' => $_POST['slug'], 'parent' => $page_id));
		$id = $db->lastInsertId();
		header("Location: ?page=$id&lang=$lang_id", true, 302);
	} else if ($_GET['action'] === 'delete-page') {
		$stmt = $db->prepare('DELETE FROM pages WHERE id=:id');
		$stmt->execute(array('id' => $page_id));
		header("Location: ?", true, 302);
	} else if ($_GET['action'] === 'edit-page') {
		$stmt = $db->prepare('UPDATE pages SET slug=:slug, layout=:layout WHERE id=:id');
		$stmt->execute(array(
			'slug' => $_POST['slug'],
			'layout' => $_POST['layout'],
			'id' => $page_id,
		));
		header("Location: ?page=$page_id&lang=$lang_id", true, 302);
	} else if ($_GET['action'] === 'edit-translation') {
		$stmt = $db->prepare('UPDATE translations SET title=:title, body=:body WHERE page=:page AND lang=:lang');
		$stmt->execute(array(
			'title' => $_POST['title'],
			'body' => $_POST['body'],
			'page' => $page_id,
			'lang' => $lang_id,
		));
		header("Location: ?page=$page_id&lang=$lang_id", true, 302);
	}
} else {
	$page = get_page_by_id($page_id);
	$lang = get_lang_by_id($lang_id);
	$root = get_page_by_id(1);

	$translation = get_translation($page_id, $lang_id);
	if (!$translation) {
		$translation = array('page' => $page_id, 'lang' => $lang_id, 'title' => '', 'body' => '');
		$stmt = $db->prepare('INSERT INTO translations (page, lang, title, body) VALUES (:page, :lang, :title, :body)');
		$stmt->execute($translation);
	}

	include('template.php');
}
