<?php

namespace src\Exceptions;

use Exception;

class MarketNotAllowed extends Exception {
    public function __construct() {
        parent::__construct('Market not allowed.');
    }
}
