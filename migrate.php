<?php
// On affiche les erreurs pour voir ce qui se passe
ini_set('display_errors', 1);
error_log("Tentative de migration...");

require_once './Database.php'; // Assure-toi que le chemin est correct

try {
    $database = new Database();
    $db = $database->connect();

    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        contact VARCHAR(50),
        sexe VARCHAR(10),
        eglise VARCHAR(255),
        leader_nom VARCHAR(255),
        leader_contact VARCHAR(50),
        paiement VARCHAR(50) DEFAULT 'en_attente',
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $db->exec($sql);
    echo "<h1 style='color:green'>Succès ! La table 'users' est prête sur Aiven.</h1>";
    
} catch (Exception $e) {
    echo "<h1 style='color:red'>Erreur :</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}