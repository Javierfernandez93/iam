<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true) {
	HCStudio\Util::redirectTo('../../apps/admin/');
}

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::AdminLogin;
$Layout->init(JFStudio\Router::getName($route),"index","admin-login","",TO_ROOT."/");

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'admin-login.css',
	'loginsupport.vue.js'
]);

$Layout->setVar("UserSupport",$UserSupport);
$Layout();