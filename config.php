<?php declare(strict_types=1);

ini_set('display_errors', 'On');

$csrf_secret = 'CHANGEME';
$fallback_lang_code = 'de';
$db_dsn = 'sqlite:'.__DIR__.'/db.sqlite';
$db_user = '';
$db_password = '';
$allowed_extensions = array('gif', 'jpeg', 'png', 'svg', 'jpg', 'pdf', 'm4a', 'mp3', 'asc');
$blog_featured_articles = 5;
$auth = null;
// $auth = [
//     'base_path' => '/admin/',
//     'client_id' => 'website',
//     'client_secret' => 'CHANGEME',
//     'token' => 'CHANGEME',
//     'authorization_endpoint' => 'https://kub.dyndns.berlin/sso/login/',
//     'token_endpoint' => 'https://kub.dyndns.berlin/sso/token/',
// ];
