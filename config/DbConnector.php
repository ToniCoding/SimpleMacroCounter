<?php

/**
 * Class DbConnection
 * Manages the database connection using PDO and logs connection status.
 */
class DbConnection {
    private ?PDO $dbPdo = null;
    private string $serverName;
    private string $username;
    private string $password;
    private string $databaseName;
    private readonly Logger $log;

    /**
     * Constructor initializes DB credentials and logger.
     *
     * @param string $serverName Database server hostname.
     * @param string $username Database username.
     * @param string $password Database password.
     * @param string $databaseName Database name.
     */
    public function __construct(string $serverName, string $username, string $password, string $databaseName, Logger $logger) {
        $this->serverName = $serverName;
        $this->username = $username;
        $this->password = $password;
        $this->databaseName = $databaseName;
        $this->log = $logger;
    }

    /**
     * Establishes a PDO connection.
     * It also implements the use of lazy connections.
     *
     * @return PDO Connected PDO instance.
     */
    public function connect(): PDO {
        if($this->dbPdo == null) {
            try {
                $this->dbPdo = new PDO(
                    "mysql:host={$this->serverName};dbname={$this->databaseName};charset=utf8",
                    $this->username,
                    $this->password
                );

                $this->dbPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $this->log->info("Connected successfully to the database: {$this->databaseName}");

                return $this->dbPdo;
            } catch (PDOException $err) {
                echo "Connection failed due to: {$err->getMessage()}";
                exit();
            }
        }

        return $this->dbPdo;
    }
}
