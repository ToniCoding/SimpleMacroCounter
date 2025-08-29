<?php

require_once __DIR__ . "/config/Services.php";

/** @var DateParser $dateParser */
$dateParser = $container->getService("dateParser");

/** @var Logger $logger */
$logger = $container->getService("logger");

/** @var DbConnection $dbConnection */
$dbConnection = $container->getService("db");
