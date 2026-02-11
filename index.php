<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once './User.php';

use Dotenv\Dotenv;

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

/*echo json_encode([
    'mail_from' => getenv('MAIL_USERNAME'),
    'ffj' => getenv('MAIL_FROM_NAME'),
    'key' => getenv('RESEND_API_KEY'),
    'host' => getenv('DB_HOST'),
    'user' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
    'database' => getenv('DB_NAME')
]);
exit;*/

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
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
    echo json_encode(['error' => 'Champs manquants ']);
    exit;
}

// Préparation données
$data['email'] = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$data['name'] = trim(strip_tags($data['name']));
$data['eglise'] = trim(strip_tags($data['eglise']));
$data['leader_nom'] = trim(strip_tags($data['leader_nom']));
$data['paiement'] = trim(strip_tags($data['paiement']));
$data['sexe'] = trim(strip_tags($data['sexe']));
$data['contact'] = preg_replace('/\D+/', '', $data['contact']);
$data['leader_contact'] = preg_replace('/\D+/', '', $data['leader_contact']);

$user = new User();


try {
    $user->create($data);
    echo json_encode([
        'db' => 'ok'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'db' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

// Création
$created = $user->create($data);

if (!$created) {
    http_response_code(500);
    echo json_encode([
        'error' => "Erreur lors de l'insertion de la BDD"
    ]);
    exit;
}

// Mail
$mailsent = $user->sendMail(
    $data['email'],
    'Confirmation',
    $user->template($data)
);

http_response_code(201);
echo json_encode([
    'message' => 'Utilisateur créé',
    'mail' => $mailsent
]);
