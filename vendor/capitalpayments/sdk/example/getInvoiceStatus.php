<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the invoice status
$response = $Sdk->getInvoiceStatus([
    'invoice_id' => 'invoice_id' # string 
]);
