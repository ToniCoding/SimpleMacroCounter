<?php

$serverName = "localhost";
$username = "op_user";
$password = "1234";
$databaseName = "smc";

class DbConnection {
    private PDO $dbPdo;
    private string $serverName;
    private string $username;
    private string $password;
    private string $databaseName;

    public function __construct(string $serverName, string $username, string $password, string $databaseName) {
        $this->serverName = $serverName;
        $this->username = $username;
        $this->password = $password;
        $this->databaseName = $databaseName;
    }

    public function connect(): PDO {
        try {
            $dbPdo = new PDO(
                "mysql:host={$this->serverName}; dbname={$this->databaseName}; charset=utf8",
                $this->username,
                $this->password);

            $dbPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            echo "Connected successfully to the database: {$this->databaseName}";

            return $dbPdo;
        } catch (PDOException $err) {
            echo "Connection failed due to: " . $err->getMessage();
            exit();
        }
    }
}

//$db = new DbConnection($serverName, $username, $password, $databaseName);
$pdo = new DbConnection($serverName, $username, $password, $databaseName);
$test = $pdo->connect();
