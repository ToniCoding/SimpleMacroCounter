<?php

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
function pathRouter(string $uri, Container $globalContainer, array $specialRoutes): void {
    $sanitizedUri = preg_replace('/[^a-z0-9_-]/', '', $uri);

    if (isset($specialRoutes[$sanitizedUri]) && file_exists($specialRoutes[$sanitizedUri])) {
        require $specialRoutes[$sanitizedUri];
        return;
    }

    $pagePath = BASE_PATH . '/public/pages/' . ($sanitizedUri ?: 'home') . "Page.php";

    if (file_exists($pagePath)) {
        require $pagePath;
        renderPage($globalContainer);
        return;
    }

    require NOT_FOUND_PAGE;
}

pathRouter($uri, $globalContainer, $specialRoutes);
