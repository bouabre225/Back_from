<?php

require_once './User.php';

$user = new User();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->create($data);   
} 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

//recupération des donnes json ou form-data

$data = json_decode(file_get_contents('php://input'), true);

//si données json
if (!$data) $data = $_POST;

//vérification des champs
$name = $data['name'];
$email = $data['email'];
$password = $data['password'];


//vérification des champs
if (empty($name) || empty($email) || empty($password)) {
    http_response_code(400);
    exit;
}

//reponse APi
http_response_code(201);
echo json_encode([
    'message' => 'User created successfully'
]);