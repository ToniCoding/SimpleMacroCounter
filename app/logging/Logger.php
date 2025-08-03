<?php

require_once __DIR__ . '/../helpers/dateParser.php';

class Logger {
    private string $mode;
    private string $msg;
    private readonly string $logFile;
    private readonly DateParser $dateParser;
    private DateTime $dateTime;

    public function __construct() {
        $this->logFile = __DIR__ . '/../../data/program.log';
        $this->dateParser = new DateParser();
    }

    private function formatLogMessage(string $message, string $mode): string {
        return "[" . $mode . "] " . $this->dateParser->getDateTime() . " >>> " . $message . "\n"; 
    }

    public function info($contents): void {
        $this->mode = "info";
        if (!file_put_contents(
            $this->logFile,
            $this->formatLogMessage($contents, strtoupper($this->mode)),
            FILE_APPEND)) {
                throw new Exception("There was an error writing to log.");
        }
    }
}
