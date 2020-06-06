<?php declare(strict_types=1);

include_once('common.php');

function fetch_translation(&$page, $lang)
{
	global $fallback_lang;
	$translation = get_translation($page['id'], $lang['id']);
	$fallback = get_translation($page['id'], $fallback_lang['id']);

	$fallback_text = "<p><em>Sorry, no tanslation available</em></p>\n";  // TODO translate

	foreach (array('title', 'body') as $key) {
		if ($translation && $translation[$key] !== '') {
			$page[$key] = $translation[$key];
		} elseif ($fallback && $fallback[$key] !== '') {
			if ($key === 'body') {
				$page[$key] = $fallback_text . str_replace("/${fallback_lang['code']}/", "/${lang['code']}/", $fallback[$key]);
			} else {
				$page[$key] = $fallback[$key];
			}
		} else {
			$page[$key] = '';
		}
	}
}

function render_side_nav($root=NULL, $rootpath='', $maxdepth=4)
{
	global $path, $lang, $baseurl;
	if ($root === NULL) {
		$slug = path_shift($path, False);
		$rootpath = "/$slug/";
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
$fallback_lang = get_lang_by_code('de');

$path = '/' . $_GET['path'];
$code = path_shift($path);
$lang = get_lang_by_code($code);
$page = get_page_by_path($path);
fetch_translation($page, $lang);

include('templates/main.php');
