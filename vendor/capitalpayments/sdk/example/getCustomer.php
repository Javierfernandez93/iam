<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the customer
$response = $Sdk->getCustomer([
    'customer_id' => 'customer_id' # string 
]);
