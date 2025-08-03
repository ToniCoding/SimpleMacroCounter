<?php

class DateParser {
    private const DATE_FORMAT = "d-m-Y";
    private const TIME_FORMAT = "H:i:s";

    private DateTime $dateTime;

    public function __construct() {
        $this->dateTime = new DateTime();
    }

    public function getDate(): string {
        return $this->dateTime->format(self::DATE_FORMAT);
    }

    public function getTime(): string {
        return $this->dateTime->format(self::TIME_FORMAT);
    }

    public function getDateTime(): string {
        return $this->getDate() . "T" . $this->getTime();
    }
}
