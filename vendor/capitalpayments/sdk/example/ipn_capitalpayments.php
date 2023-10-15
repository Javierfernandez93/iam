<?php

$api_key = 'api_key';
$ipn_secret = 'ipn_secret';

if (!isset($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_USER'])) {
    die("No api_key authentication sent");
}

if (!isset($_SERVER['PHP_AUTH_PW']) || empty($_SERVER['PHP_AUTH_PW'])) {
    die("No ipn_secret authentication sent");
}

$request = file_get_contents('php://input');

if ($request === FALSE || empty($request)) {
  die("Error reading POST data");
}

parse_str($request, $output);

print_r($output);

// @todo