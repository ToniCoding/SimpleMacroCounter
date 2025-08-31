<?php

// NOTE: UserRepository is not taken into account here as we do not consider it to be used across the necessary files to be taken
// into consideration for the service container.

$globalContainer = new Container();

$globalContainer->setService('dateParser', function($globalContainer): DateParser {
    return new DateParser();
});

$globalContainer->setService('logger', function($globalContainer): Logger {
    $dateParserObj = $globalContainer->getService('dateParser');
    
    return new Logger($dateParserObj);
});

$globalContainer->setService('db', function($globalContainer): DbConnection {
    $loggerObj = $globalContainer->getService('logger');
    $credConfs = $globalContainer->getCredConf();

    return new DbConnection($credConfs['dbHost'], $credConfs['username'], $credConfs['password'], $credConfs['databaseName'], $loggerObj);
});

$globalContainer->setService('authService', function($globalContainer): AuthService {
    $dbPDO = $globalContainer->getService('db');
    
    return new AuthService($dbPDO);
});

$globalContainer->setService('auth', function($globalContainer): Auth { 
    return new Auth($globalContainer);
});
