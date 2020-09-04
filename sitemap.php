<?php declare(strict_types=1);

include_once('./common.php');

function render_sitemap($parent_id, $parent_url)
{
?>	<url>
		<loc><?php e($parent_url) ?></loc>
	</url>
<?php
	foreach (get_subpages($parent_id, true, false) as $p) {
		render_sitemap($p['id'], "$parent_url${p['slug']}/");
	}
}

?><?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php render_sitemap(1, "https://kub-berlin.org$baseurl/$fallback_lang_code/") ?>
</urlset>
