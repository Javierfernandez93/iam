<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# create payout from array
$response = $Sdk->createPayouts([
    [
        'payout_id' => 'PayoutId-1', # @string
        'wallet' => 'USDT.TRC20WalletAddress', # string
        'amount' => 47 # float|int
    ],
    [
        'payout_id' => 'PayoutId-2', # @string
        'wallet' => 'USDT.TRC20WalletAddress', # string
        'amount' => 47 # float|int
    ]
]);
