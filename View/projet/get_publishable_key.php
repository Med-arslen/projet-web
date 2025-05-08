<?php
require 'config/stripe_config.php';
header('Content-Type: application/json');
echo json_encode([
    'publishableKey' => STRIPE_PUBLISHABLE_KEY,
]);

?>