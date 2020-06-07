<!DOCTYPE html>
<html lang="<?php e($lang['code']) ?>" dir="<?php e($lang['dir']) ?>" prefix="s: http://schema.org/ og: http://ogp.me/ns#">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Security-Policy" content="default-src 'self'">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php e($page['title'])?> - KuB</title>
	<link href="/templates/kub/favicon.ico" rel="shortcut icon" type="image/x-icon">
	<link href="<?php e($baseurl) ?>/static/kub-<?php e($lang['dir']) ?>.css" rel="stylesheet" type="text/css" />

	<?php foreach (get_langs() as $l): ?>
		<?php if ($l['code'] !== $lang['code']) : ?>
			<link href="<?php e("$baseurl/${l['code']}$path") ?>" hreflang="<?php e($l['code']) ?>" rel="<?php e($l['code'] == 'de' ? 'canonical' : 'alternate') ?>" />
		<?php endif ?>
	<?php endforeach ?>
</head>
<body>
	<div id="header-container">
		<header id="header">
			<a href="<?php e("$baseurl/${lang['code']}/") ?>" class="brand-link" rel="home">
				<picture>
					<source srcset="/templates/kub/images/logo.svg" type="image/svg+xml" />
					<img alt="KuB" src="/templates/kub/images/logo.png" />
				</picture>
			</a>
			<div id="header-bottom">
				<address id="address">
					<ul>
						<li>Oranienstr. 159</li>
						<li>10969 Berlin</li>
						<li>Tel: 030 / 614 94 00</li>
						<li>Fax: 030 / 615 45 34</li>
						<li><a href="mailto:kontakt@kub-berlin.org">kontakt@kub-berlin.org</a></li>
					</ul>
				</address>
			</div>
		</header>
		<nav id="nav">
			<div id="search" role="search">
				<?php foreach (get_langs() as $l): ?>
					<a href="<?php e("$baseurl/${l['code']}$path") ?>" hreflang="<?php e($l['code']) ?>" <?php if ($l['code'] === $lang['code']) : ?>class="selected"<?php endif ?>><?php e($l['name']) ?></a>
				<?php endforeach ?>
			</div>
			<ul>
				<?php foreach (array('/', '/angebote/', '/unterstuetzung/', '/ueber-die-kub/', '/aktuelles/', '/kontakt/') as $navpath) : ?>
					<?php $navpage = get_page_by_path($navpath) ?>
					<?php fetch_translation($navpage, $lang) ?>
					<li><a href="<?php e("$baseurl/${lang['code']}$navpath")?>"><?php e($navpage['title']) ?></a></li>
				<?php endforeach ?>
			</ul>
		</nav>
	</div>
	<?php if ($page['layout'] === 'home') : ?>
		<div id="top">
			<ul class="nav">
				<?php foreach (array(
					'/angebote/beratung/asyl-und-aufenthalt/' => '/images/icons/Beratung_Asyl_Aufenthalt.svg',
					'/angebote/beratung/frauen/' => '/images/icons/Frauen_beratung.svg',
					'/angebote/beratung/psychosozial/' => '/images/icons/Psychosoziale_Beratung.svg',
					'/angebote/deutschkurse/anmeldung-und-stundenplan/' => '/images/icons/Deutschkurse.svg',
					'/angebote/deutschkurse/sprach-tandem/' => '/images/icons/Sprachtandem.svg',
					'/angebote/formulare/' => '/images/icons/Formularprojekt.svg',
				) as $navpath => $icon) : ?>
					<?php $navpage = get_page_by_path($navpath) ?>
					<?php fetch_translation($navpage, $lang) ?>
					<li>
						<a href="<?php e("$baseurl/${lang['code']}$navpath")?>">
							<img src="<?php e($icon) ?>" alt="">
							<span class="image-title"><?php e($navpage['title']) ?></span>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
	<?php endif ?>

	<div id="main-container">
		<?php if ($page['layout'] === 'home') : ?>
			<main id="main">
				<div class="homeRow">
					<?php include('home.php') ?>
				</div>
			</main>
		<?php else : ?>
			<nav id="section-nav">
				<?php render_side_nav() ?>
			</nav>
			<main id="main" class="m-sidenav">
				<div class="item-page <?php if ($page['layout'] === 'accordion') : ?>accordion<?php endif ?>">
					<h2><?php e($page['title']) ?></h2>
					<?php echo $page['body'] ?>
				</div>

				<?php if ($page['layout'] === 'overview') : ?>
					<ul class="subpages">
						<?php foreach (get_subpages($page['id']) as $p) : ?>
							<?php fetch_translation($p, $lang) ?>
							<li class="subpage"><a href="<?php e("${p['slug']}/") ?>"><?php e($p['title']) ?></a></li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
			</main>
		<?php endif ?>
	</div>

	<footer id="footer">
		<div id="footer2">
			<?php foreach (array('/ueber-die-kub/transparenz/', '/datenschutz/', '/impressum/') as $navpath) : ?>
				<?php $navpage = get_page_by_path($navpath) ?>
				<?php fetch_translation($navpage, $lang) ?>
				<a href="<?php e("$baseurl/${lang['code']}$navpath")?>"><?php e($navpage['title']) ?></a></li>
			<?php endforeach ?>
		</div>
	</footer>

	<script src="<?php e($baseurl) ?>/static/accordion.js"></script>
	<script src="<?php e($baseurl) ?>/static/nav.js"></script>
	<script src="<?php e($baseurl) ?>/static/table.js"></script>
</body>
</html>
