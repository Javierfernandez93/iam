<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# creates a new payout  
$response = $Sdk->createPayout([
    'payout_id' => 'PayoutId', # @string
    'address' => 'USDT.TRC20WalletAddress', # @string
    'amount' => 47 # @float|int
]);
