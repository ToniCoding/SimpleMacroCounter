<?php

/** 
 * Path router.
 * This route works with Apache's mod_rewrite directives so in order to work correctly the corresponding
 * configuration must be tested and done.
 * The require paths will work with all the paths within the project.
*/
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = strtolower(trim($uri, '/'));

$basePath = __DIR__ . '/public/pages/' . ($uri ?: 'home');
$htmlPath = "$basePath.html";
$phpPath = "$basePath.php";

if (file_exists($htmlPath)) {
    require $htmlPath;    
} elseif(file_exists($phpPath)) {
    require $phpPath;
} else {
    require __DIR__ . '/pages/404.html';
}
