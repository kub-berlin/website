<?php declare(strict_types=1);

include_once('common.php');

function fetch_translation(&$page, $lang, $show_fallback_text=true)
{
	global $fallback_lang;
	$translation = get_translation($page['id'], $lang['id']);
	$fallback = get_translation($page['id'], $fallback_lang['id']);

	foreach (array('title', 'body') as $key) {
		if ($translation && $translation[$key] !== '') {
			$page[$key] = $translation[$key];
		} elseif ($fallback && $fallback[$key] !== '') {
			if ($key === 'body') {
				$page[$key] = str_replace("/${fallback_lang['code']}/", "/${lang['code']}/", $fallback[$key]);
				if ($show_fallback_text) {
					$page[$key] = '<p><em>' . htmlspecialchars($lang['missing']) . "</em></p>\n" . $page[$key];
				}
			} else {
				$page[$key] = $fallback[$key];
			}
		} else {
			$page[$key] = '';
		}
	}
}

function get_module($slug)
{
	global $db, $lang;
	$stmt = $db->prepare('SELECT * FROM pages WHERE slug=:slug AND parent IS NULL');
	$stmt->execute(array('slug' => $slug));
	$page = $stmt->fetch();
	if (!$page) {
		return '';
	}
	fetch_translation($page, $lang, false);
	return $page;
}

function render_side_nav($root=null, $rootpath='', $maxdepth=4)
{
	global $path, $lang, $area, $baseurl;
	if (empty($area)) {
		return;
	}
	if ($root === null) {
		$rootpath = "/$area/";
		$root = get_page_by_path($rootpath);
	}

	echo '<ul>';
	foreach (get_subpages($root['id']) as $p) {
		fetch_translation($p, $lang);
		$ppath = $rootpath . $p['slug'] . '/';
		?>
			<li <?php if ($ppath === $path) : ?>class="current"<?php endif ?>>
				<a href="<?php e("$baseurl/${lang['code']}$ppath") ?>"><?php e($p['title']) ?></a>
				<?php if ($maxdepth > 0) : ?>
					<?php render_side_nav($p, $ppath, $maxdepth - 1) ?>
				<?php endif ?>
			</li>
		<?php
	}
	echo '</ul>';
}

$baseurl = '/xi';
$fallback_lang = get_lang_by_code($fallback_lang_code);
$lang = $fallback_lang;
$error = null;

try {
	$path = '/' . $_GET['path'];
	$code = path_shift($path);
	$area = path_shift($path, false);
	$lang = get_lang_by_code($code);
	$page = get_page_by_path($path);
	fetch_translation($page, $lang);
} catch (HttpException $e) {
	$area = null;
	$page = get_module('404');
	$error = $e;
}

include('templates/main.php');
