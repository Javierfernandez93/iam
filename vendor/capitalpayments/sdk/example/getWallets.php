<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# retrives all wallets attached to api 
$response = $Sdk->getWallets();
