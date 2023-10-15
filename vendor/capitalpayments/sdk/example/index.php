<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

error_reporting(E_ALL);

include __DIR__ . "/../vendor/autoload.php";

echo "<pre>";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

print_r($Sdk->getBalance());
// print_r($Sdk->getEnvironment());
// print_r($Sdk->createInvoice([
//     'invoice_id' => 'GC-123',
//     'amount' => 120
// ]));
// print_r($Sdk->login());
