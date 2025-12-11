<?php declare(strict_types=1);

include_once('../datasource.php');
include_once('csrf.php');
include('auth.php');

function render_side_nav($page = null, $maxdepth = 10)
{
	global $page_id, $langs;
	$translations = array();
	foreach ($langs as $lang) {
		$translation = get_translation($page['id'] ?? null, $lang['code']);
		$translations[$lang['code']] = $translation && !empty($translation['body']);
	}
?>
	<?php if ($page !== null) : ?>
		<li>
			<a <?php if ($page['id'] == $page_id) : ?>class="active"<?php endif ?> href="<?php e("?page={$page['id']}") ?>">
				<span class="langs-available">
					<?php foreach ($translations as $code => $exists) : ?>
						<?php e($exists ? $code : '') ?>
					<?php endforeach ?>
				</span>
				<?php if ($page['published']) : ?>
					<?php e(empty($page['slug']) ? 'home' : $page['slug']) ?>
				<?php else : ?>
					<del><?php e(empty($page['slug']) ? 'home' : $page['slug']) ?></del>
				<?php endif ?>
			</a>
		</li>
	<?php endif ?>
	<ul>
		<?php if ($maxdepth > 0) : ?>
			<?php foreach (get_subpages($page['id'] ?? null, true, true) as $p) : ?>
				<?php render_side_nav($p, $maxdepth - 1) ?>
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
		header("Location: ?page=$id&lang={$lang['code']}", true, 302);
	} elseif ($_GET['action'] === 'delete-page') {
		$stmt = $db->prepare('DELETE FROM pages WHERE id=:id');
		$stmt->execute(array('id' => $page_id));
		header("Location: ?", true, 302);
	} elseif ($_GET['action'] === 'edit-page') {
		$stmt = $db->prepare('UPDATE pages SET slug=:slug, layout=:layout, order_by=:order_by, published=:published, show_in_nav=:show_in_nav, twid=:twid WHERE id=:id');
		$stmt->execute(array(
			'slug' => $_POST['slug'],
			'layout' => $_POST['layout'],
			'order_by' => $_POST['order_by'],
			'published' => isset($_POST['published']),
			'show_in_nav' => isset($_POST['show_in_nav']),
			'twid' => !empty($_POST['twid']) ? $_POST['twid'] : null,
			'id' => $page_id,
		));
		header("Location: ?page=$page_id&lang={$lang['code']}", true, 302);
	} elseif ($_GET['action'] === 'edit-translation') {
		$stmt = $db->prepare('UPDATE translations SET title=:title, body=:body WHERE page=:page AND lang=:lang');
		$stmt->execute(array(
			'title' => $_POST['title'],
			'body' => $_POST['body'],
			'page' => $page_id,
			'lang' => $lang['code'],
		));
		header("Location: ?page=$page_id&lang={$lang['code']}", true, 302);
	}

	exit();
} else {
	$page = get_page_by_id($page_id);
	$root = get_page_by_id(1);

	$translation = get_translation($page_id, $lang['code']);
	if (!$translation) {
		$translation = array('page' => $page_id, 'lang' => $lang['code'], 'title' => '', 'body' => '');
		$stmt = $db->prepare('INSERT INTO translations (page, lang, title, body) VALUES (:page, :lang, :title, :body)');
		$stmt->execute($translation);
	}
}

?>
<!DOCTYPE html>
<html class="admin">
<head>
	<meta charset="utf-8" />
	<title>KuB Administration</title>
	<meta http-equiv="Content-Security-Policy" content="default-src 'self'">
	<link rel="stylesheet" type="text/css" href="../static/kub.css" />
</head>
<body>
	<nav class="nav-langs" aria-label="Languages">
		<?php foreach (get_langs(true) as $l) : ?>
			<a href="<?php e("?page=$page_id&lang={$l['code']}") ?>" class="button <?php if ($l['code'] !== $lang['code']) : ?>button-light<?php endif ?>"><?php e($l['code']) ?></a>
		<?php endforeach ?>
	</nav>

	<main>
		<form method="post" action="<?php e("?action=edit-translation&page=$page_id&lang={$lang['code']}") ?>">
			<input type="hidden" name="csrf_token" value="<?php e($GLOBALS['csrf_token']) ?>">

			<label>
				Title
				<input name="title" value="<?php e($translation['title']) ?>" lang="<?php e($lang['code']) ?>" dir="<?php e($lang['dir']) ?>" required>
			</label>

			<label>
				Body
				<textarea name="body" lang="<?php e($lang['code']) ?>" dir="<?php e($lang['dir']) ?>"><?php e($translation['body']) ?></textarea>
			</label>

			<button>Save</button>
		</form>
	</main>

	<aside>
		<form method="post" action="<?php e("?action=edit-page&page=$page_id&lang={$lang['code']}") ?>">
			<h3>Edit this page</h3>
			<input type="hidden" name="csrf_token" value="<?php e($GLOBALS['csrf_token']) ?>">

			<label>
				Slug
				<input name="slug" value="<?php e($page['slug']) ?>" <?php if ($page_id === 1) : ?>readonly<?php else : ?>required<?php endif ?>>
			</label>

			<label>
				Layout
				<select name="layout" id="layout">
					<?php foreach (array('default', 'overview', 'blog', 'home', 'contact', 'accordion', 'tandem', 'spenden') as $value): ?>
						<option <?php if ($page['layout'] === $value) : ?>selected<?php endif ?>><?php e($value) ?></option>
					<?php endforeach ?>
				</select>
			</label>

			<label id="twid">
				Spenden ID (Twingle)
				<input name="twid" id="twid_input" type="text" value="<?php e($page['twid']) ?>">
			</label>

			<label>
				Order
				<input name="order_by" type="number" value="<?php e($page['order_by']) ?>" required>
			</label>

			<label>
				<input name="published" type="checkbox" <?php if ($page['published'] === '1') : ?>checked<?php endif ?>>
				Published
			</label>

			<label>
				<input name="show_in_nav" type="checkbox" <?php if ($page['show_in_nav'] === '1') : ?>checked<?php endif ?>>
				Show in navigation
			</label>

			<button>Save</button>
		</form>

		<form method="post" action="<?php e("?action=delete-page&page=$page_id") ?>" data-js="confirm">
			<h3>Delete this page</h3>
			<input type="hidden" name="csrf_token" value="<?php e($GLOBALS['csrf_token']) ?>">
			<button>Delete</button>
		</form>

		<form method="post" action="<?php e("?action=create-page&page=$page_id") ?>">
			<h3>Create a new subpage</h3>
			<input type="hidden" name="csrf_token" value="<?php e($GLOBALS['csrf_token']) ?>">
			<label>
				Slug
				<input name="slug" required>
			</label>
			<button>Create</button>
		</form>

		<form method="post" action="<?php e("?action=create-page") ?>">
			<h3>Create a new module</h3>
			<input type="hidden" name="csrf_token" value="<?php e($GLOBALS['csrf_token']) ?>">
			<label>
				Slug
				<input name="slug" required>
			</label>
			<button>Create</button>
		</form>

		<a class=" button" href="files.php">Manage files</a>
	</aside>

	<nav id="section-nav" class="nav-pages" aria-label="Pages">
		<?php render_side_nav() ?>
	</nav>

	<script src="tinymce/tinymce.js"></script>
	<script src="static/admin.js"></script>
</body>
</html>
