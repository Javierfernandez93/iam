<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the payout status
$response = $Sdk->getPayoutStatus([
    'payout_id' => 'payout_id' # string 
]);
