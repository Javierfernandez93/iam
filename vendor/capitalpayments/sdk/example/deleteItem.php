<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# delete customer by item_id
$response = $Sdk->deleteItem([
    'item_id' => 'item_id', # @string
]);
