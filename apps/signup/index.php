<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true) {
	HCStudio\Util::redirectTo(TO_ROOT . "/apps/backoffice/");
}
 
$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::Signup;
$Layout->init(JFStudio\Router::getName($route),"index","two_columns","",TO_ROOT."/");

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
	'jquery.mask.js',
	'signup.vue.js',
	'signup.css'
]);

$Layout->setVar([
	'UserLogin' => $UserLogin,
	'Country' => (new World\Country)
]);
$Layout();