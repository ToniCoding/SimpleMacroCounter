<?php

namespace src\Exceptions;

use RuntimeException;

class NoRecordFoundException extends RuntimeException {
    public function __construct() {
        parent::__construct('No record found in database for this query.');
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
