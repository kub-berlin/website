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
		<form method="post" action="<?php e("?action=edit-translation&page=$page_id&lang=$lang_id") ?>">
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
		<form method="post" action="<?php e("?action=edit-page&page=$page_id&lang=$lang_id") ?>">
			<h3>Edit this page</h3>
			<label>
				Slug
				<input name="slug" value="<?php e($page['slug']) ?>" <?php if ($page_id === 1) : ?>readonly<?php else : ?>required<?php endif ?>>
			</label>

			<label>
				Layout
				<select name="layout">
					<?php foreach (array('default', 'overview', 'home', 'accordion') as $value) : ?>
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
			<button>Delete</button>
		</form>

		<form method="post" action="<?php e("?action=create-page&page=$page_id") ?>">
			<h3>Create a new subpage</h3>
			<label>
				Slug
				<input name="slug" required>
			</label>
			<button>Create</button>
		</form>

		<form method="post" action="<?php e("?action=create-page") ?>">
			<h3>Create a new module</h3>
			<label>
				Slug
				<input name="slug" required>
			</label>
			<button>Create</button>
		</form>
	</aside>

	<nav class="nav-langs" aria-label="Languages">
		<?php foreach (get_langs() as $l) : ?>
			<a href="<?php e("?page=$page_id&lang=${l['id']}") ?>" class="button <?php if ($l['id'] != $lang_id) : ?>button-light<?php endif ?>"><?php e($l['code']) ?></a>
		<?php endforeach ?>
	</nav>

	<nav id="section-nav" class="nav-pages" aria-label="Pages">
		<ul>
			<?php render_side_nav($root, $page_id) ?>
		</ul>
	</nav>

	<script src="tinymce/tinymce.js"></script>
	<script src="admin.js"></script>
</body>
</html>
