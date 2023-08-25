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
	<a href="#main" class="button skip"><?php e($lang['skip']) ?></a>

	<div id="content-container">
		<?php if ($alert = get_module('alert')) : ?>
			<div id="alert" class="alert">
				<?php echo $alert['body'] ?>
			</div>
		<?php endif ?>

		<header>
			<div id="header">
				<h1 class="brand">
					<a href="<?php e("$baseurl/${lang['code']}/") ?>" class="brand-link" rel="home">
						<img lang="de" alt="Logo: KuB - Kontakt- und Beratungsstelle für Flüchtlinge und Migrant_innen e.V." src="<?php cachebust('/static/logo.svg') ?>" width="331" height="100" />
					</a>
				</h1>
				<div id="header-bottom">
					<?php echo get_module('header-bottom')['body'] ?>
				</div>
				<nav id="language-nav" aria-label="<?php e($lang['languages']) ?>">
					<ul>
						<?php foreach (get_langs() as $l): ?>
							<li><a href="<?php e("$baseurl/${l['code']}$path") ?>" lang="<?php e($l['code']) ?>" hreflang="<?php e($l['code']) ?>" <?php if ($l['code'] === $lang['code']) : ?>class="selected"<?php endif ?>><?php e($l['name']) ?></a></li>
						<?php endforeach ?>
					</ul>
				</nav>
			</div>
			<nav id="nav">
				<ul>
					<?php foreach (array('/', '/angebote/', '/mitmachen/', '/spenden/', '/ueber-die-kub/', '/aktuelles/', '/kontakt/') as $navpath) : ?>
						<?php $navpage = get_page_by_path($navpath) ?>
						<?php add_content($navpage, $lang) ?>
						<li><a href="<?php e("$baseurl/${lang['code']}$navpath")?>" <?php if (trim($navpath, '/') === $area) : ?>class="active" aria-current="page"<?php endif ?>><?php e($navpage['title']) ?></a></li>
					<?php endforeach ?>
				</ul>
			</nav>
		</header>

		<?php if ($page['layout'] === 'home') : ?>
			<nav id="shortcuts" aria-label="<?php e($lang['shortcuts']) ?>">
				<ul>
					<?php foreach ([
						['/angebote/beratung/asyl-und-aufenthalt/', '/images/icons/Beratung_Asyl_Aufenthalt.svg', true],
						['/angebote/beratung/frauen/', '/images/icons/Frauen_beratung.svg', true],
						['/angebote/beratung/psychosozial/', '/images/icons/Psychosoziale_Beratung.svg', true],
						['/angebote/deutschkurse/anmeldung-und-stundenplan/', '/images/icons/Deutschkurse.svg', false],
						['/angebote/deutschkurse/sprach-tandem/', '/images/icons/Sprachtandem.svg', false],
						['/angebote/formulare/', '/images/icons/Formularprojekt.svg', true],
					] as [$navpath, $icon, $mirror]) : ?>
						<?php $navpage = get_page_by_path($navpath) ?>
						<?php add_content($navpage, $lang) ?>
						<li>
							<a href="<?php e("$baseurl/${lang['code']}$navpath")?>">
								<img src="<?php e($icon) ?>" alt="" width="100" height="100" class="<?php e($mirror ? 'rtl-mirror' : '') ?>">
								<span class="image-title"><?php e($navpage['title']) ?></span>
							</a>
						</li>
					<?php endforeach ?>
				</ul>
			</nav>
		<?php endif ?>

		<div id="main-container">
			<?php if ($page['layout'] === 'home') : ?>
				<main id="main" class="l-full home">
					<?php include('home.php') ?>
				</main>
			<?php else : ?>
				<main id="main" class="l-main <?php e($page['layout']) ?>">
					<h2><?php e($page['title']) ?></h2>

					<?php if ($page['layout'] === 'contact') : ?>
						<?php include('home.php') ?>
					<?php endif ?>

					<?php if (!empty($page['body_fallback'])) : ?>
						<p><em><?php e($lang['missing']) ?></em></p>
						<div dir="ltr" lang="de" class="untranslated">
							<?php echo $page['body'] ?>
						</dir>
					<?php else : ?>
						<?php echo $page['body'] ?>
					<?php endif ?>

					<?php if ($page['layout'] === 'overview') : ?>
						<ul class="subpages">
							<?php foreach (get_subpages($page['id']) as $p) : ?>
								<?php add_content($p, $lang) ?>
								<li><a href="<?php e("${p['slug']}/") ?>"><?php e($p['title']) ?></a></li>
							<?php endforeach ?>
						</ul>
					<?php elseif ($page['layout'] === 'blog') : ?>
						<?php $articles = get_subpages($page['id'], true) ?>
						<?php foreach (array_slice($articles, 0, $blog_featured_articles) as $p) : ?>
							<?php add_content($p, $lang) ?>
							<article>
								<h3><a href="<?php e("${p['slug']}/") ?>" class="nolink"><?php e($p['title']) ?></a></h3>
								<?php if (!empty($p['body_fallback'])) : ?>
									<p><em><?php e($lang['missing']) ?></em></p>
									<div dir="ltr" lang="de" class="untranslated">
										<?php echo $p['truncated'] ?>
									</dir>
								<?php else : ?>
									<?php echo $p['truncated'] ?>
								<?php endif ?>
								<?php if ($p['truncated'] !== $p['body']) : ?>
									<p><a href="<?php e("${p['slug']}/") ?>"><?php e($lang['readmore']) ?>: <?php e($p['title']) ?></a></p>
								<?php endif ?>
							</article>
						<?php endforeach ?>
						<?php if (count($articles) > $blog_featured_articles) : ?>
							<h3><?php e($lang['archive']) ?></h3>
							<ul>
								<?php foreach (array_slice($articles, $blog_featured_articles) as $p) : ?>
									<?php add_content($p, $lang) ?>
									<li><a href="<?php e("${p['slug']}/") ?>"><?php e($p['title']) ?></a></li>
								<?php endforeach ?>
						<?php endif ?>
					<?php elseif ($page['layout'] === 'tandem') : ?>
						<?php include('tandem.php') ?>
					<?php endif ?>
				</main>
				<?php if (has_side_nav()) : ?>
					<nav id="section-nav" class="l-side" aria-label="<?php e($areapage['title']) ?>">
						<?php render_side_nav() ?>
					</nav>
				<?php endif ?>
			<?php endif ?>
		</div>

		<footer id="footer">
			<nav aria-label="<?php e($lang['socialmedia']) ?>" class="social-nav">
				<ul>
					<li><a href="https://www.facebook.com/KuBFM/" rel="noreferrer">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
							<path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
						</svg>
						Facebook
					</a></li>
					<li><a href="https://twitter.com/KuB_Berlin" rel="noreferrer">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
							<path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
						</svg>
						Twitter
					</a></li>
					<li><a href="https://berlin.social/@KuB" rel="noreferrer">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
							<path d="M11.19 12.195c2.016-.24 3.77-1.475 3.99-2.603.348-1.778.32-4.339.32-4.339 0-3.47-2.286-4.488-2.286-4.488C12.062.238 10.083.017 8.027 0h-.05C5.92.017 3.942.238 2.79.765c0 0-2.285 1.017-2.285 4.488l-.002.662c-.004.64-.007 1.35.011 2.091.083 3.394.626 6.74 3.78 7.57 1.454.383 2.703.463 3.709.408 1.823-.1 2.847-.647 2.847-.647l-.06-1.317s-1.303.41-2.767.36c-1.45-.05-2.98-.156-3.215-1.928a3.614 3.614 0 0 1-.033-.496s1.424.346 3.228.428c1.103.05 2.137-.064 3.188-.189zm1.613-2.47H11.13v-4.08c0-.859-.364-1.295-1.091-1.295-.804 0-1.207.517-1.207 1.541v2.233H7.168V5.89c0-1.024-.403-1.541-1.207-1.541-.727 0-1.091.436-1.091 1.296v4.079H3.197V5.522c0-.859.22-1.541.66-2.046.456-.505 1.052-.764 1.793-.764.856 0 1.504.328 1.933.983L8 4.39l.417-.695c.429-.655 1.077-.983 1.934-.983.74 0 1.336.259 1.791.764.442.505.661 1.187.661 2.046v4.203z"/>
						</svg>
						Mastodon
					</a></li>
					<li><a href="https://www.instagram.com/kub_berlin/" rel="noreferrer">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
							<path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>
						</svg>
						Instagram
					</a></li>
				</ul>
			</nav>
			<nav aria-label="<?php e($lang['legal']) ?>">
				<ul>
					<?php foreach (array(
						'/datenschutz/',
						'/impressum/',
					) as $navpath) : ?>
						<?php $navpage = get_page_by_path($navpath) ?>
						<?php add_content($navpage, $lang) ?>
						<li><a href="<?php e("$baseurl/${lang['code']}$navpath") ?>"><?php e($navpage['title']) ?></a></li>
					<?php endforeach ?>
				</ul>
			</nav>

			<div class="footer-logos">
				<a href="<?php e("$baseurl/${lang['code']}/ueber-die-kub/transparenz/") ?>">
					<img src="/images/Logos/Transparente_Zivilgesellschaft_bw_inverted.svg" alt="Initiative Transparente Zivilgesellschaft">
				</a>
			</div>
		</footer>
	</div>

	<script src="<?php cachebust('/static/accordion.js') ?>" type="module"></script>
	<script src="<?php cachebust('/static/nav.js') ?>" type="module"></script>
</body>
</html>
