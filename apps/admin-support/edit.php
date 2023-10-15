<?php define("TO_ROOT", "../..");new GranCapital

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init(" » Editar cliente","edit","admin","",TO_ROOT."/");

$UserSupport = new FSA\UserSupport;

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['admin-object.js','admin-admin-edit.js']);

$Layout->setVar([
	'UserSupport' => $UserSupport
]);
$Layout();