<?php

namespace src\Exceptions;

use Exception;

class ExceededMacroLimitException extends Exception {
    public function __construct($message) {
        parent::__construct($message);
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
