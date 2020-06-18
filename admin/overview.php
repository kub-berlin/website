<?php declare(strict_types=1);

include_once('../common.php');

$pages = array();
$langs = get_langs();

function fetch_pages($id=null, $path='')
{
	global $pages, $langs;
	if ($id != null) {
		$page = get_page_by_id($id);
		$path .= $page['slug'].'/';
		$pages[$path] = array();
		foreach ($langs as $lang) {
			$translation = get_translation($id, $lang['code']);
			$pages[$path][$lang['code']] = $translation && !empty($translation['body']);
		}
	}
	foreach (get_subpages($id, true) as $p) {
		fetch_pages($p['id'], $path);
	}
}

fetch_pages();

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>KuB Administration</title>
	<meta http-equiv="Content-Security-Policy" content="default-src 'self'">
	<link rel="stylesheet" type="text/css" href="../static/kub-ltr.css" />
</head>
<body>
	<table>
		<thead>
			<tr>
				<th></th>
				<?php foreach (get_langs() as $lang) : ?>
					<th><?php e($lang['code']) ?></th>
				<?php endforeach ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($pages as $path => $translations) : ?>
				<tr>
					<th><?php e($path) ?></th>
					<?php foreach ($translations as $lang => $exists) : ?>
						<th><?php e($exists ? 'X' : '') ?></th>
					<?php endforeach ?>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</body>
</html>
