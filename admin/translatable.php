<?php declare(strict_types=1);

// this view allows to download content in a format similar to what we use for
// transifex.

include_once('../datasource.php');
include('auth.php');

$translation = get_translation($_GET['page'], $_GET['lang']);

if (!$translation) {
	http_response_code(404);
	echo "404 Not Found";
	exit(1);
}
?>
<div>
<h1><?php e($translation['title']) ?></h1>
<?php echo $translation['body'] . "\n" ?>
</div>
