<?php

/**
 * Logger class to record messages into a log file.
 */
class Logger {
    private string $mode;
    private string $msg;
    private readonly string $logFile;
    private readonly DateParser $dateParser;
    private DateTime $dateTime;

    /**
     * Constructor that initializes the log file and date parser.
     */
    public function __construct(DateParser $dateParser) {
        $this->logFile = __DIR__ . '/../../data/program.log';
        $this->dateParser = $dateParser;
    }

    /**
     * Formats a log message with mode and date.
     *
     * @param string $message Message to log.
     * @param string $mode Log level or mode (INFO, ERROR, etc).
     * @return string Formatted message.
     */
    private function formatLogMessage(string $message, string $mode): string {
        return "[" . $mode . "] " . $this->dateParser->getDateTime() . " >>> " . $message . "\n"; 
    }

    /**
     * Records an info level message into the log file.
     *
     * @param string $contents Content to log.
     * @throws Exception If writing to the log file fails.
     */
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
