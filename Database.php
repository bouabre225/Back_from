<?php

class Database {
    
    private $host;
    private $user;
    private $password;
    private $database;
    private $port;
    private $charset = "utf8mb4";

    private ?PDO $pdo = null;

    public function __construct()
    {
        $databaseurl = getenv('DATABASE_URL');

        if ($databaseurl) {
            $url = parse_url($databaseurl);
            $this->host = $url['host'];
            $this->user = $url['user'];
            $this->password = $url['pass'];
            $this->database = ltrim($url['path'], '/');
            $this->port = $url['port'];
        } else {
            $this->host = getenv('DB_HOST');
            $this->user = getenv('DB_USER');
            $this->password = getenv('DB_PASSWORD');
            $this->database = getenv('DB_NAME');
            $this->port = getenv('DB_PORT');
        }
    }

    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset};port={$this->port}";

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