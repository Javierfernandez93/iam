<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# creates a new payment and retrieves the data and url payment 
$response = $Sdk->createInvoice([
    'invoice_id' => 'InvioceID-InvoiceNumber', # @string
    'amount' => 47 # float|int
]);
