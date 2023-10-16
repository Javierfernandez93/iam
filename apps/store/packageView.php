<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new DummieTrading\UserLogin;

$Layout = JFStudio\Layout::getInstance();

$layout = $UserLogin->logged === false ? 'single-product' : 'backoffice';

$route = JFStudio\Router::StorePackage;

$Layout->init(JFStudio\Router::getName($route),'packageView',$layout,'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'packageView.css',
	'packageView.vue.js',
]);

$Layout->setVar([
	'route' =>  $route,
	'setApp' =>  true,
	'UserLogin' => $UserLogin
]);
$Layout();