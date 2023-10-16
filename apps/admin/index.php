<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserSupport = new DummieTrading\UserSupport;

require TO_ROOT . '/vendor/autoload.php';

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

if($UserSupport->hasPermission('list_dash') === false) {
	HCStudio\Util::redirectTo('../../apps/admin-users/');
}

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::AdminDash;
$Layout->init(JFStudio\Router::getName($route),"index","admin","",TO_ROOT."/");

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'chart.js',
	'admindash.vue.js',
]);

$Layout->setVar([
	'route' => $route,
	'UserSupport' => $UserSupport
]);
$Layout();