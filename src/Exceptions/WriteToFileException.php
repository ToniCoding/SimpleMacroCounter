<?php

namespace src\Exceptions;

use Exception;

class WriteToFileException extends Exception {
    public function __construct($message, ?\Throwable $previous = null) {
        parent::__construct($message, 0, $previous);
    }

    public function __toString(): string {
        $previousInfo = '';
        if ($this->getPrevious()) {
            $previousInfo = sprintf(
                "\nPrevious exception: [%s] %s",
                get_class($this->getPrevious()),
                $this->getPrevious()->getMessage()
            );
        }

        return sprintf(
            "[%s] %s in %s:%d%s\nStack trace:\n%s",
            __CLASS__,
            $this->getMessage(),
            $this->getFile(),
            $this->getLine(),
            $previousInfo,
            $this->getTraceAsString()
        );
    }
}
