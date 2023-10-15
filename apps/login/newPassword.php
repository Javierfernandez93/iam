<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true) {
    HCStudio\Util::redirectTo(TO_ROOT."/apps/backoffice/");
}

$Layout = JFStudio\Layout::getInstance();

$route = JFStudio\Router::NewPassword;
$Layout->init(JFStudio\Router::getName($route),'newPassword',"two_columns",'',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript([
    'login.css',
    'newPassword.vue.js'
]);

$Layout->setVar([
	'UserLogin' => $UserLogin,
]);
$Layout();