<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# delete customer by customer_id
$response = $Sdk->deleteCustomer([
    'customer_id' => 'customer_id', # @string
]);
