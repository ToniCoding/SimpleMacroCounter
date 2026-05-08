<?php

namespace src\Exceptions;

use RuntimeException;

class UnrecognizedMacroException extends RuntimeException {
    public function __construct() {
        parent::__construct('Unrecognized macro-nutrient type.');
    }
}
