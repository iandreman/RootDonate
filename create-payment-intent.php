<?php
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_live_mk_1TeUTtAYd3hJ6fUDkS7pb4nq');

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $input['amount'],
        'currency' => 'gbp',
        'automatic_payment_methods' => ['enabled' => true],
        'metadata' => [
            'name' => $input['name'],
            'email' => $input['email'],
            'steps' => $input['steps'],
            'message' => $input['message']
        ]
    ]);

    echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>