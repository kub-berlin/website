<?php
$error = 'nocache';

if (isset($_GET["tid"])) {
    $id= $_GET["tid"];
} else {
    $id= "";
}

if (isset($_GET["a"])) {
    $a = $_GET["a"];
} else {
    $a = "";
}

if (isset($_GET["action"])) {
    $action= $_GET["action"];
} else {
    $action= "table";
}

$url = "/sprach-tandem/index.php?lang=${lang['code']}&action=$action&tid=$id&a=$a";
?>
<iframe
    src="<?php e($url) ?>"
    class="wrapper"
    width="100%"
    height="1600"
    frameborder="0"
    scrolling="auto">
</iframe>
