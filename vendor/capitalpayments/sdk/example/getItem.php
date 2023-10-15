<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the item
$response = $Sdk->getItem([
    'item_id' => 'item_id' # string 
]);
