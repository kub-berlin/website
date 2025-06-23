<?php declare(strict_types=1);

ini_set('display_errors', 'On');

$baseurl = '/xi';
$csrf_secret = 'CHANGEME';
$fallback_lang_code = 'de';
$db_dsn = 'sqlite:'.__DIR__.'/db.sqlite';
$db_user = '';
$db_password = '';
$allowed_extensions = array('gif', 'jpeg', 'png', 'svg', 'jpg', 'pdf', 'm4a', 'mp3', 'asc');
$blog_featured_articles = 5;
