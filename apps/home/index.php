<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new DummieTrading\UserLogin;

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Home;
$Layout->init(JFStudio\Router::getName($route),'index',"index",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'home.*',
	'home.vue.js',
]);

$Layout->setVar([
	'UserLogin' => $UserLogin
]);
$Layout();