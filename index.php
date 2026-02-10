<?php

require_once './User.php';

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


$data = json_decode(file_get_contents('php://input'), true);
if (!$data) $data = $_POST;

// Validation
if (
    empty($data['name']) ||
    empty($data['email']) ||
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
$data['contact'] = filter_var($data['contact'], FILTER_VALIDATE_INT);
$data['paiement'] = filter_var($data['paiement'], FILTER_VALIDATE_INT);
$data['sexe'] = filter_var($data['sexe'], FILTER_VALIDATE_INT);
$data['leader_contact'] = filter_var($data['leader_contact'], FILTER_VALIDATE_INT);
$data['leader_nom'] = filter_var($data['leader_nom'], FILTER_SANITIZE_STRING);
$data['eglise'] = filter_var($data['eglise'], FILTER_SANITIZE_STRING);
$data['name'] = filter_var($data['name'], FILTER_SANITIZE_STRING);

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
