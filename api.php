<?php

require_once './User.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

// Validation
if (
    empty($data['name']) ||
    empty($data['email']) ||
    empty($data['password']) ||
    empty($data['contact']) ||
    empty($data['sexe']) ||
    empty($data['eglise']) ||
    empty($data['leader_nom']) ||
    empty($data['leader_contact']) ||
    empty($data['paiement'])
) {
    http_response_code(400);
    echo json_encode(['error' => 'Champs manquants']);
    exit;
}

// Préparation données
$data['email'] = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

$user = new User();

// Création
$user->create($data);

// Mail
$user->sendMail(
    $data['email'],
    'Confirmation',
    $user->template($data)
);

http_response_code(201);
echo json_encode([
    'message' => 'Utilisateur créé',
    'mail' => 'Mail envoyé'
]);
