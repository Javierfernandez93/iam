<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

if($UserSupport->hasPermission('list_dash') === false) {
	HCStudio\Util::redirectTo('../../apps/admin-users/');
}

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::AdminStats;
$Layout->init(JFStudio\Router::getName($route),"stats","admin","",TO_ROOT."/");

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'chart.js',
	'adminStats.vue.js',
]);

$Layout->setVar([
	'route' => $route,
	'UserSupport' => $UserSupport
]);
$Layout();