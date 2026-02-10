<?php

require_once './Database.php';

class User {
    private PDO $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    /*public function findByEmail(string $email) {
        $stmt = $this->db->prepare('select * from users where email = :email');
        $stmt->execute([
            'email' => $email
        ]);

        return $stmt->fetch();
    }*/

    public function create(array $data) {
        $stmt = $this->db->prepare('insert into users (name, email, password) values (:name, :email, :password)');
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password']
        ]);
    }
}