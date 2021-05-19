<!DOCTYPE html>
<html lang="<?php e($lang['code']) ?>" dir="<?php e($lang['dir']) ?>" data-menu="<?php e($lang['menu']) ?>" class="website">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Security-Policy" content="default-src 'self'">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php e($page['title'])?> &middot; Kontakt- und Beratungsstelle für Flüchtlinge und Migrant_innen e. V.</title>
	<link href="<?php cachebust('/static/favicon.ico') ?>" rel="icon">
	<link href="<?php cachebust("/static/kub-${lang['dir']}.css") ?>" rel="stylesheet" type="text/css" />

	<?php foreach (get_langs() as $l): ?>
		<?php if ($l['code'] !== $lang['code']) : ?>
			<link href="<?php e("https://${_SERVER['HTTP_HOST']}$baseurl/${l['code']}$path") ?>" hreflang="<?php e($l['code']) ?>" rel="<?php e($l['code'] == 'de' ? 'canonical' : 'alternate') ?>" />
		<?php endif ?>
	<?php endforeach ?>
</head>
<body>
	<div id="alert" class="alert">
		<?php echo get_module('alert')['body'] ?>
	</div>

	<div id="header-container">
		<header id="header">
			<a href="<?php e("$baseurl/${lang['code']}/") ?>" class="brand-link" rel="home">
				<img alt="KuB" src="<?php cachebust('/static/logo.svg') ?>" width="331" height="100" />
			</a>
			<div id="header-bottom">
				<?php echo get_module('header-bottom')['body'] ?>
			</div>
			<nav id="language-nav" aria-label="<?php e($lang['languages']) ?>">
				<ul>
					<?php foreach (get_langs() as $l): ?>
						<li><a href="<?php e("$baseurl/${l['code']}$path") ?>" hreflang="<?php e($l['code']) ?>" <?php if ($l['code'] === $lang['code']) : ?>class="selected"<?php endif ?>><?php e($l['name']) ?></a></li>
					<?php endforeach ?>
				</ul>
			</nav>
		</header>
		<nav id="nav">
			<ul>
				<?php foreach (array('/', '/angebote/', '/unterstuetzung/', '/ueber-die-kub/', '/aktuelles/', '/kontakt/') as $navpath) : ?>
					<?php $navpage = get_page_by_path($navpath) ?>
					<?php fetch_translation($navpage, $lang) ?>
					<li><a href="<?php e("$baseurl/${lang['code']}$navpath")?>" <?php if (trim($navpath, '/') === $area) : ?>class="active" aria-current="page"<?php endif ?>><?php e($navpage['title']) ?></a></li>
				<?php endforeach ?>
			</ul>
		</nav>
	</div>

	<?php if ($page['layout'] === 'home') : ?>
		<nav id="shortcuts" aria-label="<?php e($lang['shortcuts']) ?>">
			<ul>
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
							<img src="<?php e($icon) ?>" alt="" width="510" height="510">
							<span class="image-title"><?php e($navpage['title']) ?></span>
						</a>
					</li>
				<?php endforeach ?>
			</ul>
		</nav>
	<?php endif ?>

	<div id="main-container">
		<?php if ($page['layout'] === 'home') : ?>
			<main id="main" class="home">
				<?php include('home.php') ?>
			</main>
		<?php else : ?>
			<main id="main" class="<?php e($page['layout']) ?>">
				<h2><?php e($page['title']) ?></h2>

				<?php if ($page['layout'] === 'contact') : ?>
					<?php include('home.php') ?>
				<?php endif ?>

				<?php if ($page['body_fallback']) : ?>
					<p><em><?php e($lang['missing']) ?></em></p>
				<?php endif ?>
				<?php echo $page['body'] ?>

				<?php if ($page['layout'] === 'module') : ?>
					<?php echo get_module($page['slug'])['body'] ?>
				<?php elseif ($page['layout'] === 'overview') : ?>
					<ul class="subpages">
						<?php foreach (get_subpages($page['id']) as $p) : ?>
							<?php fetch_translation($p, $lang) ?>
							<li><a href="<?php e("${p['slug']}/") ?>"><?php e($p['title']) ?></a></li>
						<?php endforeach ?>
					</ul>
				<?php elseif ($page['layout'] === 'blog') : ?>
					<?php foreach (get_subpages($page['id'], true) as $p) : ?>
						<?php fetch_translation($p, $lang) ?>
						<article>
							<h3><?php e($p['title']) ?></h3>
							<?php echo $p['truncated'] ?>
							<?php if ($p['truncated'] !== $p['body']) : ?>
								<p><a href="<?php e("${p['slug']}/") ?>"><?php e($lang['readmore']) ?>: <?php e($p['title']) ?></a></p>
							<?php endif ?>
						</article>
					<?php endforeach ?>
				<?php elseif ($page['layout'] === 'tandem') : ?>
					<?php include('tandem.php') ?>
				<?php endif ?>
			</main>
			<nav id="section-nav" aria-label="<?php e($lang['section']) ?>">
				<?php render_side_nav() ?>
			</nav>
		<?php endif ?>
	</div>

	<footer id="footer">
		<nav aria-label="<?php e($lang['legal']) ?>">
			<ul>
				<?php foreach (array(
					'/transparenz/',
					'/datenschutz/',
					'/impressum/',
				) as $navpath) : ?>
					<?php $navpage = get_page_by_path($navpath) ?>
					<?php fetch_translation($navpage, $lang) ?>
					<li><a href="<?php e("$baseurl/${lang['code']}$navpath")?>"><?php e($navpage['title']) ?></a></li>
				<?php endforeach ?>
			</ul>
		</nav>
	</footer>

	<script src="<?php cachebust('/static/accordion.js') ?>"></script>
	<script src="<?php cachebust('/static/nav.js') ?>"></script>
</body>
</html>
