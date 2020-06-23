<?php declare(strict_types=1);

ob_start();
include('main.php');
$html = ob_get_clean();

if ($error === null) {
	$dir = 'cache/' . $_GET['path'];
	mkdirp($dir);
	file_put_contents("$dir/index.html", $html);
}

echo $html;
