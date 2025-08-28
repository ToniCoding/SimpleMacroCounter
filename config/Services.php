<?php

require_once __DIR__ . "/../config.php";

require_once BASE_PATH . 'config/ObjectFactories.php';
require_once BASE_PATH . 'app/logging/Logger.php';
require_once BASE_PATH . 'app/helpers/dateParser.php';

$container = new Container();

$container->setService('dateParser', function($container) {
    return new DateParser();
});

$container->setService('logger', function($container) {
    $dateParserObj = $container->getService('dateParser');
    
    return new Logger($dateParserObj);
});
