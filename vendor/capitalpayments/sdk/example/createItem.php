<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# create item  
$response = $Sdk->createItem([
    'title' => 'title', # @string
    'description' => 'description', # @string
    'price' => 'price', # @int|float
]);
