<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === false) {
	DummieTrading\UserLogin::redirectToLogin();
}

if(!$UserLogin->isStarted())
{
	HCStudio\Util::redirectTo("../../apps/backoffice/start");
}

// d(JFStudio\OrderIdentificator::identify("orden de mercado venta con 0.1231 de lotaje 26624 de tp y 26621 de stop loss"));

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Backoffice;
$Layout->init(JFStudio\Router::getName($route),'index',"backoffice",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'chart.js',
	'backoffice.css',
	'backoffice.vue.js',
]);

$Layout->setVar([
	'route' =>  $route,
	'setApp' =>  true,
	'UserLogin' => $UserLogin
]);
$Layout();