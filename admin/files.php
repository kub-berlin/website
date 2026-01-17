<?php declare(strict_types=1);

include_once('../utils.php');
include('auth.php');

$root = '../../images';
$root_url = '/images';

$path = isset($_GET['path']) ? $_GET['path'] : '/';
validate_path($path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_FILES['file'])) {
		$file = $_FILES['file'];
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		if (!in_array($ext, $allowed_extensions)) {
			throw new HttpException('Invalid File Extension', 400);
		}
		move_uploaded_file($file['tmp_name'], "$root$path{$file['name']}");
		header('Location: ', true, 302);
	} elseif (isset($_POST['folder'])) {
		mkdirp("$root$path{$_POST['folder']}");
		header('Location: ', true, 302);
	} elseif (isset($_POST['delete'])) {
		rrm("$root$path{$_POST['name']}");
		header('Location: ', true, 302);
	}
	exit();
}

function is_image($path)
{
	$p = explode('.', $path);
	$ext = strtolower(array_pop($p));
	return in_array($ext, array('png', 'jpg', 'jpeg', 'gif', 'svg'));
}

function get_files($path)
{
	global $root, $root_url;

	$files = [];
	$p = $root . $path;
	$u = $root_url . $path;
	foreach (scandir($p) as $name) {
		if ($name === '..' && $path !== '/') {
			$parent = path_pop($path)[0];
			$files[] = [
				'name' => $name,
				'path' => $parent,
				'is_file' => false,
			];
		} elseif ($name !== '.' && $name !== '..') {
			$files[] = [
				'name' => $name,
				'path' => "$path$name/",
				'url' => "$u$name",
				'is_file' => is_file("$p$name"),
				'is_image' => is_image("$p$name"),
			];
		}
	}
	return $files;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<title>KuB Administration</title>
	<meta http-equiv="Content-Security-Policy" content="default-src 'self'">
	<link rel="stylesheet" type="text/css" href="../static/kub.css" />
</head>
<body>
	<form method="post" enctype="multipart/form-data" class="form-combined">
		<input type="file" name="file" required>
		<button>Upload file</button>
	</form>
	<form method="post" class="form-combined">
		<input type="text" name="folder" required>
		<button>Create folder</button>
	</form>

	<ul class="file-list">
		<?php foreach (get_files($path) as $file) : ?>
			<li>
				<?php if ($file['is_file']) : ?>
					<a href="<?php e($file['url']) ?>" target="_blank">
						<?php if ($file['is_image']) : ?>
							<img src="<?php e($file['url']) ?>" class="file-list__icon" alt="image">
						<?php else : ?>
							<img src="static/file.png" class="file-list__icon" alt="file">
						<?php endif ?>
						<?php e($file['name']) ?>
					</a>
				<?php else : ?>
					<a href="?path=<?php e($file['path']) ?>">
						<img src="static/folder.png" class="file-list__icon" alt="folder">
						<?php e($file['name']) ?>/
					</a>
				<?php endif ?>

				<?php if ($file['name'] != '..') : ?>
					<form method="post" data-js="confirm">
						<input type="hidden" name="name" value="<?php e($file['name']) ?>">
						<button name="delete" class="button-small">Delete</button>
					</form>
				<?php endif ?>
			</li>
		<?php endforeach ?>
	</ul>

	<script src="static/admin.js"></script>
</body>
</html>
