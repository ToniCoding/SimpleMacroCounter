<?php

namespace src\Exceptions;

use Exception;

class FoodAlreadyRegistered extends Exception {
    public function __construct() {
        parent::__construct('Food already registered.');
    }
}
