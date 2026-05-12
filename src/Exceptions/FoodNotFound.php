<?php

namespace src\Exceptions;

use Exception;

class FoodNotFound extends Exception {
    public function __construct() {
        parent::__construct('Food not found.');
    }
}
