<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === false) {
	HCStudio\Util::redirectTo(TO_ROOT."/apps/login/");
}

$Layout->init('Actualizar datos','update_profile','backoffice','',TO_ROOT.'/');

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['update_data.js','update_profile.css']);

$Layout->setVar([
	'nav' => 'backoffice',
	'pill' => HCStudio\Util::getVarFromPGS("pill"),
	'UserLogin' => $UserLogin,
	'url' => HCStudio\Util::getCurrentURL(),
	'UserNotificationSetting' => new Talento\UserNotificationSetting,
	'CatalogNotification' => new Talento\CatalogNotification,
	'Country' => new World\Country,
]);
$Layout();