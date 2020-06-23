<?php declare(strict_types=1);

include_once('../common.php');
include_once('csrf.php');

function render_side_nav($id=null, $slug='', $maxdepth=10)
{
	global $page_id, $langs;
	$translations = array();
	foreach ($langs as $lang) {
		$translation = get_translation($id, $lang['code']);
		$translations[$lang['code']] = $translation && !empty($translation['body']);
	}
?>
	<?php if ($id !== null) : ?>
		<li>
			<a <?php if ($id == $page_id) : ?>class="active"<?php endif ?> href="<?php e("?page=$id") ?>">
				<span class="langs-available">
					<?php foreach ($translations as $code => $exists) : ?>
						<?php e($exists ? $code : '') ?>
					<?php endforeach ?>
				</span>
				<?php e(empty($slug) ? 'home' : $slug) ?>
			</a>
		</li>
	<?php endif ?>
	<ul>
		<?php if ($maxdepth > 0) : ?>
			<?php foreach (get_subpages($id, true, true) as $p) : ?>
				<?php render_side_nav($p['id'], $p['slug'], $maxdepth - 1) ?>
			<?php endforeach ?>
		<?php endif ?>
	</ul>
<?php }

$page_id = isset($_GET['page']) ? intval($_GET['page']) : 1;
$lang = get_lang(isset($_GET['lang']) ? $_GET['lang'] : $fallback_lang_code);
$langs = get_langs();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	rrm('../cache');

	if ($_GET['action'] === 'create-page') {
		$parent = isset($_GET['page']) ? $page_id : null;
		$stmt = $db->prepare('INSERT INTO pages (slug, parent, order_by, published, show_in_nav) VALUES (:slug, :parent, 10, 1, 1)');
		$stmt->execute(array('slug' => $_POST['slug'], 'parent' => $parent));
		$id = $db->lastInsertId();
		header("Location: ?page=$id&lang=${lang['code']}", true, 302);
	} elseif ($_GET['action'] === 'delete-page') {
		$stmt = $db->prepare('DELETE FROM pages WHERE id=:id');
		$stmt->execute(array('id' => $page_id));
		header("Location: ?", true, 302);
	} elseif ($_GET['action'] === 'edit-page') {
		$stmt = $db->prepare('UPDATE pages SET slug=:slug, layout=:layout, order_by=:order_by, published=:published, show_in_nav=:show_in_nav WHERE id=:id');
		$stmt->execute(array(
			'slug' => $_POST['slug'],
			'layout' => $_POST['layout'],
			'order_by' => $_POST['order_by'],
			'published' => isset($_POST['published']),
			'show_in_nav' => isset($_POST['show_in_nav']),
			'id' => $page_id,
		));
		header("Location: ?page=$page_id&lang=${lang['code']}", true, 302);
	} elseif ($_GET['action'] === 'edit-translation') {
		$stmt = $db->prepare('UPDATE translations SET title=:title, body=:body WHERE page=:page AND lang=:lang');
		$stmt->execute(array(
			'title' => $_POST['title'],
			'body' => $_POST['body'],
			'page' => $page_id,
			'lang' => $lang['code'],
		));
		header("Location: ?page=$page_id&lang=${lang['code']}", true, 302);
	}
} else {
	$page = get_page_by_id($page_id);
	$root = get_page_by_id(1);

	$translation = get_translation($page_id, $lang['code']);
	if (!$translation) {
		$translation = array('page' => $page_id, 'lang' => $lang['code'], 'title' => '', 'body' => '');
		$stmt = $db->prepare('INSERT INTO translations (page, lang, title, body) VALUES (:page, :lang, :title, :body)');
		$stmt->execute($translation);
	}

	include('template.php');
}
