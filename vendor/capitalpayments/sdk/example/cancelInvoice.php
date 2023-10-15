<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# cancel invoice  
$response = $Sdk->cancelInvoice([
    'invoice_id' => 'InvoiceId', # @string
]);
