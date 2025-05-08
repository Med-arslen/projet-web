<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // Load Stripe PHP library

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // Load environment variables
$dotenv->load();
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']); // Set your Stripe secret key

?>