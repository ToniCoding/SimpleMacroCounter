<?php

namespace App\Exceptions;

use RuntimeException;

class NoRecordFoundException extends RuntimeException {
    public function __construct() {
        parent::__construct('No record found in database for this query.');
    }
}
