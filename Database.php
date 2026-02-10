<?php

class Database {
    
    private $host = "127.0.0.1";
    private $user = "root";
    private $password = "";
    private $database = "form";
    private $charset = "utf8mb4";

    private ?PDO $pdo = null;

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