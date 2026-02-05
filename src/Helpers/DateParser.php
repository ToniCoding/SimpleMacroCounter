<?php

namespace src\Helpers;

class DateParser {
    private const DATE_FORMAT = "d-m-Y";
    private const TIME_FORMAT = "H:i:s";

    private \DateTime $dateTime;

    public function __construct() {
        $this->dateTime = new \DateTime();
    }

    public function getDate($format = null): string {
        if($format == null) {
            return $this->dateTime->format(self::DATE_FORMAT);
        }

        return $this->dateTime->format($format);
    }

    public function getTime(): string {
        return $this->dateTime->format(self::TIME_FORMAT);
    }

    public function getDateTime(): string {
        return $this->getDate() . "T" . $this->getTime();
    }
}
