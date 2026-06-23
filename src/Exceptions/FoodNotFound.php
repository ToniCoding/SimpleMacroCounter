<?php

namespace src\Exceptions;

use Exception;

class FoodNotFound extends Exception {
    public function __construct() {
        parent::__construct('Food not found.');
    }

    public function __toString(): string {
        return sprintf(
            "[%s] %s in %s:%d\nStack trace:\n%s",
            __CLASS__,
            $this->getMessage(),
            $this->getFile(),
            $this->getLine(),
            $this->getTraceAsString()
        );
    }
}
