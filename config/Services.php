<?php

require_once __DIR__ . "/../config.php";

require_once BASE_PATH . 'config/ObjectFactories.php';
require_once BASE_PATH . 'app/logging/Logger.php';
require_once BASE_PATH . 'app/helpers/dateParser.php';
require_once BASE_PATH . "config/db.php";

// NOTE: UserRepository is not taken into account here as we do not consider it to be used across the necessary files to be taken
// into consideration for the service container.

$container = new Container();

$container->setService('dateParser', function($container): DateParser {
    return new DateParser();
});

$container->setService('logger', function($container): Logger {
    $dateParserObj = $container->getService('dateParser');
    
    return new Logger($dateParserObj);
});

$container->setService('db', function($container) {
    $loggerObj = $container->getService('logger');
    $credConfs = $container->getCredConf();

    return new DbConnection($credConfs['dbHost'], $credConfs['username'], $credConfs['password'], $credConfs['databaseName'], $loggerObj);
});
