<?php declare(strict_types=1);

include_once('../common.php');

$root = '../../images';
$root_url = '/images';

$path = $_GET['path'];
validate_path($path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_FILES['file'])) {
		$file = $_FILES['file'];
		move_uploaded_file($file['tmp_name'], "$root$path${file['name']}");
		header('Location: ', true, 302);
	} elseif (isset($_POST['folder'])) {
		mkdirp("$root$path${_POST['folder']}");
		header('Location: ', true, 302);
	} elseif (isset($_POST['delete'])) {
		rrm("$root$path${_POST['name']}");
		header('Location: ', true, 302);
	}
	exit();
}

function get_files($path)
{
	global $root, $root_url;

	$files = array();
	$p = $root . $path;
	$u = $root_url . $path;
	foreach (scandir($p) as $name) {
		if ($name === '..' && $path !== '/') {
			$parent = $path;
			path_pop($parent);
			$files[] = array(
				'name' => $name,
				'path' => $parent,
				'is_file' => false,
			);
		} elseif ($name !== '.' && $name !== '..') {
			$files[] = array(
				'name' => $name,
				'path' => "$path$name/",
				'url' => "$u$name",
				'is_file' => is_file("$p$name"),
				'is_image' => getimagesize("$p$name"),
			);
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
	<link rel="stylesheet" type="text/css" href="../static/kub-ltr.css" />
</head>
<body>
	<form method="post" enctype="multipart/form-data" class="form-combined">
		<input type="file" name="file">
		<button>Upload file</button>
	</form>
	<form method="post" class="form-combined">
		<input type="text" name="folder">
		<button>Create folder</button>
	</form>

	<ul class="file-list">
		<?php foreach (get_files($path) as $file) : ?>
			<li>
				<?php if ($file['is_file']) : ?>
					<?php if ($file['is_image']) : ?>
						<img src="<?php e($file['url']) ?>" class="file-list__icon" alt="image">
					<?php else : ?>
						<img src="static/file.png" class="file-list__icon" alt="file">
					<?php endif ?>
					<a href="<?php e($file['url']) ?>" target="_blank" class="file-list__main"><?php e($file['name']) ?></a>
				<?php else : ?>
					<img src="static/folder.png" class="file-list__icon" alt="folder">
					<a href="?path=<?php e($file['path']) ?>" class="file-list__main"><?php e($file['name']) ?>/</a>
				<?php endif ?>

				<?php if ($file['name'] != '..') : ?>
					<form method="post">
						<input type="hidden" name="name" value="<?php e($file['name']) ?>">
						<button name="delete" class="button-small">Delete</button>
					</form>
				<?php endif ?>
			</li>
		<?php endforeach ?>
	</ul>
</body>
</html>
