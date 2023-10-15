<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# create customer  
$response = $Sdk->createCustomer([
    'name' => 'name', # @string
    'email' => 'email', # @string
    'whatsapp' => 'whatsapp', # @string
]);
