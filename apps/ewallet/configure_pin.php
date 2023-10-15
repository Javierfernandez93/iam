<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/login/");
}

$Layout = JFStudio\Layout::getInstance();

$Layout->init('Configurar código Pin','configure_pin','backoffice','',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['animate.css','configure_pin.*']);

$Layout->setVar([
	'black_theme'=>true,
	'nav'=>'backoffice',
	'UserLogin' => $UserLogin,
	'user_login' => $UserLogin->getId()
]);
$Layout();