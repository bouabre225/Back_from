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
            $this->port = $url['port'] ?? 3306;
        } else {
            $this->host = getenv('DB_HOST');
            $this->user = getenv('DB_USER');
            $this->password = getenv('DB_PASSWORD');
            $this->database = getenv('DB_NAME');
            $this->port = getenv('DB_PORT') ?: 3306;
        }
    }

    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset};port={$this->port}";

            try {
                // Options pour forcer le SSL sur Aiven si nécessaire
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Cette option permet de se connecter à Aiven même si on ne fournit pas le certificat CA localement
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, 
                ];

                $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
            } catch (PDOException $e) {
                // En prod, évite le die() avec le message d'erreur brut (sécurité)
                error_log("Erreur connexion BDD : " . $e->getMessage());
                die("Erreur de connexion au service de données.");
            }
        }
        return $this->pdo;
    }
}