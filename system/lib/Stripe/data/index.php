<?php 

\Stripe\Stripe::setApiKey('sk_test_dL2vzQGh2cPGxDOQo6nBs85z');

$intent = \Stripe\PaymentIntent::create([
    'amount' => 1099,
    'currency' => 'usd',
]);