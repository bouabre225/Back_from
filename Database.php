<?php

class Database {
    
    private $host;
    private $user;
    private $password;
    private $database;
    private $charset = "utf8mb4";

    private ?PDO $pdo = null;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->user = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->database = $_ENV['DB_NAME'];
    }

    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";

            try {
                $this->pdo = new PDO($dsn, $this->user, $this->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                die("Erreur connexion BDD : " . $e->getMessage());
            }
        }

        return $this->pdo;
    }
}