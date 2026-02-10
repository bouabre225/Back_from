<?php

require_once __DIR__ . '/vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

return [
    'host' => $_ENV['MAIL_HOST'],
    'port' => $_ENV['MAIL_PORT'],
    'username' => $_ENV['MAIL_USERNAME'],
    'password' => $_ENV['MAIL_PASSWORD'],
    'from_email' => $_ENV['MAIL_USERNAME'],
    'from_name' => $_ENV['MAIL_FROM_NAME']
];
