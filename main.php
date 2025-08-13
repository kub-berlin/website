<?php declare(strict_types=1);

include_once('datasource.php');

function truncate($body, $truncate) {
	$marker = '|<hr class="system-read-more" */?>|';
	$parts = preg_split($marker, $body);
	if ($truncate) {
		return $parts[0];
	} else {
		return implode('', $parts);
	}
}

function add_modules($body)
{
	$pattern = '|<div class="system-module">([^<]*)</div>|';
	return preg_replace_callback($pattern, function($match) {
		$mod = get_module($match[1]);
		return $mod ? $mod['body'] : '';
	}, $body);
}

function add_content(&$page, $lang, $add_modules=true)
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
	if ($add_modules) {
		$body = add_modules($body);
	}
	$page['body'] = truncate($body, false);
	$page['truncated'] = truncate($body, true);
}

function get_page($path, $lang) {
	try {
		$page = get_page_by_path($path);
		add_content($page, $lang);
		return $page;
	} catch (HttpException) {
		return null;
	}
}

function get_module($slug, $include_pub=false)
{
	global $db, $lang;
	$sql = 'SELECT * FROM pages WHERE slug=:slug AND parent IS NULL';
	$sql .= $include_pub ? '' : ' AND published=1';
	$stmt = $db->prepare($sql);
	$stmt->execute(array('slug' => $slug));
	$page = $stmt->fetch();
	if (!$page) {
		return '';
	}
	// do not add modules inside modules to avoid infinite loops
	add_content($page, $lang, false);
	return $page;
}

function has_side_nav()
{
	global $area, $areapage;
	if (empty($area)) {
		return false;
	}

	$subpages = get_subpages($areapage['id']);
	return count($subpages) > 0;
}

function render_side_nav($root=null, $rootpath='', $maxdepth=4)
{
	global $path, $lang, $area, $areapage, $baseurl;
	if (empty($area)) {
		return;
	}
	if ($root === null) {
		$rootpath = "/$area/";
		$root = $areapage;
	}

	$subpages = get_subpages($root['id']);
	if (count($subpages) === 0) {
		return;
	}

	echo '<ul>';
	foreach ($subpages as $p) {
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

function spenden($twid)
{
	?>
	<iframe
		src="https://spenden.twingle.de/kontakt-und-beratungsstelle-fur-fluchtlinge-und-migrant-innen-e-v/<?php e($twid) ?>/widget"
		width="100%"
		height="0"
		frameborder="0"
		scrolling="auto">
	</iframe>
	<script src="/static/embed.js" type="module"></script>
	<?php
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
	if (!empty($area)) {
		$areapage = get_page_by_path("/$area/");
		add_content($areapage, $lang);
	}
	$error = null;
} catch (HttpException $e) {
	http_response_code(404);
	$page = get_module('404');
	$area = null;
	$error = $e;
}

include('templates/main.php');
