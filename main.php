<?php declare(strict_types=1);

include_once('common.php');

function truncate($body, $truncate) {
	$marker = '<hr class="system-read-more" />';
	$parts = explode($marker, $body);
	if ($truncate) {
		return $parts[0];
	} else {
		return implode('', $parts);
	}
}

function add_content(&$page, $lang)
{
	global $fallback_lang;
	$translation = get_translation($page['id'], $lang['code']);
	$fallback = get_translation($page['id'], $fallback_lang['code']);

	foreach (array('title', 'body') as $key) {
		if ($translation && $translation[$key] !== '') {
			$page[$key] = $translation[$key];
		} elseif ($fallback && $fallback[$key] !== '') {
			if ($key === 'body') {
				$page[$key] = str_replace("/${fallback_lang['code']}/", "/${lang['code']}/", $fallback[$key]);
				$page[$key.'_fallback'] = true;
			} else {
				$page[$key] = $fallback[$key];
			}
		} else {
			$page[$key] = '';
		}
	}

	$body = $page['body'];
	$page['body'] = truncate($body, false);
	$page['truncated'] = truncate($body, true);
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
	add_content($page, $lang);
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
		add_content($p, $lang);
		$ppath = $rootpath . $p['slug'] . '/';
		?>
			<li>
				<a <?php if ($ppath === $path) : ?>class="active"<?php endif ?> href="<?php e("$baseurl/${lang['code']}$ppath") ?>"><?php e($p['title']) ?></a>
				<?php if ($maxdepth > 0) : ?>
					<?php render_side_nav($p, $ppath, $maxdepth - 1) ?>
				<?php endif ?>
			</li>
		<?php
	}
	echo '</ul>';
}

$fallback_lang = get_lang($fallback_lang_code);
$lang = $fallback_lang;

try {
	list($lang_code, $path) = path_shift('/' . $_GET['path']);
	$lang = get_lang($lang_code);
	validate_path($path);

	$page = get_page_by_path($path);
	add_content($page, $lang);
	$area = path_shift($path)[0];
	$error = null;
} catch (HttpException $e) {
	http_response_code(404);
	$page = get_module('404');
	$area = null;
	$error = $e;
}

include('templates/main.php');
