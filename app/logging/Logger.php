<?php

namespace App\Logging;

use App\Exceptions\WriteToFileException;
use App\Helpers\DateParser;

use DateTime;

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
        return '[' . $mode . '] ' . $this->dateParser->getDateTime() . ' >>> ' . $message . "\n"; 
    }

    /**
     * Puts the contents in arguments in a log file.
     *
     * @param string $logFile The file where the message is going to be logged.
     * @param string $contents The message that will be logged.
     * @throws WriteToFileException
     */
    private function putContents(string $logFile, string $contents): void {
        try{
            if (!file_put_contents($logFile, $contents, FILE_APPEND)) {
                throw new WriteToFileException('There was an error writing to log file.');
            }
        } catch (WriteToFileException) {
            error_log('The logging system failed after trying to write to the log file.');
        }
    }

    /**
     * Records an information level message into the log file.
     *
     * @param string $contents Content to log.
     */
    public function info($contents): void {
        $this->mode = 'info';
        $this->putContents($this->logFile,
                $this->formatLogMessage($contents, strtoupper($this->mode)));
    }
    
    /**
     * Records a warn level message into the log file.
     *
     * @param string $contents Content to log.
     */
    public function warn($contents): void {
        $this->mode = 'warn';
        $this->putContents($this->logFile,
                $this->formatLogMessage($contents, strtoupper($this->mode)));
    }

    /**
     * Records an error level message into the log file.
     *
     * @param string $contents Content to log.
     */
    public function error($contents): void {
        $this->mode = 'error';
        $this->putContents($this->logFile,
                $this->formatLogMessage($contents, strtoupper($this->mode)));
    }
}
