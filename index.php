<?php

namespace SMC;

use Config\Container;
use App\Logging\Logger;

require_once __DIR__ . "/bootstrap.php";

$uri = strtolower(trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

$specialRoutes = [
    'regprocess'   => BASE_PATH . 'app/auth/ProcessAuth.php',
    'logout'       => BASE_PATH . 'app/auth/ProcessAuth.php',
    'modifygoals'  => BASE_PATH . 'app/view/ModifyGoalsForm.php',
    'modgoals'     => BASE_PATH . 'app/public/pages/ModGoalsPage.php',
];

/**
 * This function works as a simple router to require the files needed for each functionality.
 * @param string $uri The requested URI after the domain name.
 * @param Container $globalContainer The global service container.
 * @param array $specialRoutes Special routes that redirect to actions that cannot be performed in index file.
 * @return void
 */
function pathRouter(string $uri, Container $globalContainer, array $specialRoutes, Logger $log): void {
    $sanitizedUri = preg_replace('/[^a-z0-9_-]/', '', $uri);

    $uri = empty($uri) ? 'home' : $uri;
    $log->info("Trying to route $uri.");

    if (isset($specialRoutes[$sanitizedUri]) && file_exists($specialRoutes[$sanitizedUri])) {
        $log->info("Successfully special route: $uri");
        require $specialRoutes[$sanitizedUri];
        return;
    }

    $pageFile = BASE_PATH . '/public/pages/' . ($sanitizedUri ?: 'Home') . 'Page.php';

    if (file_exists($pageFile)) {
        $log->info("Successfully route: $uri");
        require_once $pageFile;
        $pageClass = 'Public\\Pages\\' . ucfirst($sanitizedUri ?: 'Home') . 'Page';
        $pageInstance = new $pageClass();
        $pageInstance->renderPage($globalContainer);
        return;
    }
    
    $log->info("$uri not found.");
    require NOT_FOUND_PAGE;
}

pathRouter($uri, $globalContainer, $specialRoutes, $log = $globalContainer->getService('logger'));
