<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::PayPal;
$Layout->init(JFStudio\Router::getName($route),"index","backoffice","",TO_ROOT."/");

$UserLogin = new DummieTrading\UserLogin;

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['paypal.vue.js']);

$Layout->setVar([
  	'route' => $route,
	'UserLogin' => $UserLogin
	
]);
$Layout();