<?php

namespace App\Exceptions;

use Exception;

class InvalidEmailProviderException extends Exception {
    public function __construct() {
        parent::__construct('The email provider is not valid.');
    }

    public function __toString(): string {
        return \sprintf(
            "[%s] %s in %s:%d\nStack trace:\n%s",
            __CLASS__,
            $this->getMessage(),
            $this->getFile(),
            $this->getLine(),
            $this->getTraceAsString()
        );
    }
}
