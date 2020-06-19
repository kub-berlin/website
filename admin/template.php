<!DOCTYPE html>
<html class="admin">
<head>
	<meta charset="utf-8" />
	<title>KuB Administration</title>
	<meta http-equiv="Content-Security-Policy" content="default-src 'self'">
	<link rel="stylesheet" type="text/css" href="../static/kub-ltr.css" />
</head>
<body>
	<main>
		<form method="post" action="<?php e("?action=edit-translation&page=$page_id&lang=${lang['code']}") ?>">
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
		<form method="post" action="<?php e("?action=edit-page&page=$page_id&lang=${lang['code']}") ?>">
			<h3>Edit this page</h3>
			<input type="hidden" name="csrf_token" value="<?php e($GLOBALS['csrf_token']) ?>">

			<label>
				Slug
				<input name="slug" value="<?php e($page['slug']) ?>" <?php if ($page_id === 1) : ?>readonly<?php else : ?>required<?php endif ?>>
			</label>

			<label>
				Layout
				<select name="layout">
					<?php foreach (array('default', 'overview', 'home', 'contact', 'accordion', 'module') as $value) : ?>
						<option <?php if ($page['layout'] === $value) : ?>selected<?php endif ?>><?php e($value) ?></option>
					<?php endforeach ?>
				</select>
			</label>

			<label>
				Order
				<input name="order_by" type="number" value="<?php e($page['order_by']) ?>" required>
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
	</aside>

	<nav class="nav-langs" aria-label="Languages">
		<?php foreach (get_langs(true) as $l) : ?>
			<a href="<?php e("?page=$page_id&lang=${l['code']}") ?>" class="button <?php if ($l['code'] !== $lang['code']) : ?>button-light<?php endif ?>"><?php e($l['code']) ?></a>
		<?php endforeach ?>
	</nav>

	<nav id="section-nav" class="nav-pages" aria-label="Pages">
		<ul>
			<?php render_side_nav() ?>
		</ul>
	</nav>

	<script src="tinymce/tinymce.js"></script>
	<script src="static/admin.js"></script>
</body>
</html>
