<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# creates a new payment and retrieves the data and url payment 
$response = $Sdk->createInvoices([
    [
        'invoice_id' => 'InvioceID-InvoiceNumber1', # @string
        'amount' => 47 # float|int
    ],
    [
        'invoice_id' => 'InvioceID-InvoiceNumber2', # @string
        'amount' => 47 # float|int
    ]
]);
