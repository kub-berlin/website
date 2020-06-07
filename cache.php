<?php declare(strict_types=1);

ob_start();
include('index.php');
$html = ob_get_clean();

$dir = 'cache/' . $_GET['path'];
if (!file_exists($dir)) {
	mkdir($dir, 0777, true);
}
file_put_contents("$dir/index.html", $html);

echo $html;
