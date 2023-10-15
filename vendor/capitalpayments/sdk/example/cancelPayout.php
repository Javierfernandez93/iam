<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# cancel payout  
$response = $Sdk->cancelPayout([
    'payout_id' => 'PayoutId', # @string
]);