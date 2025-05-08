<?php
require_once '../../vendor/autoload.php';
require_once '../../config/stripe_config.php';
require_once "../../config/database.php";

header('Content-Type: application/json');

// Set your secret API key
\Stripe\Stripe::setApiKey(STRIPE_API_KEY);

// Get and parse the request body
$input = json_decode(file_get_contents("php://input"), true);

$reservationId = $input['reservationId'] ?? null;
$amount = $input['amount'] ?? null;

if (!$reservationId || !$amount) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing reservation ID or amount.']);
    exit;
}

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $amount*100,
                'product_data' => [
                    'name' => 'Reservation Payment',
                    'description' => 'Payment for reservation ID: ' . $reservationId,
                ],
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost/filmprjt/view/projet/success_payment.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://localhost/filmprjt/view/projet/cancel_payment.php',
        'client_reference_id' => $reservationId,
        'metadata' => [
            'reservation_id' => $reservationId,
        ],
    ]);

    echo json_encode(['url' => $session->url]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
?>