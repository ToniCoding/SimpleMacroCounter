<?php

namespace src\Exceptions;

use Exception;

class AgeNotAllowedException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
    }
}
