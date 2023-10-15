<?php

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the environment : response >= int $sandobox (0 or 1)
$response = $Sdk->getEnvironment();