<?php define("TO_ROOT", "../..");new GranCapital

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init(" » Administradores","index","admin","",TO_ROOT."/");

$UserSupport = new FSA\UserSupport;

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

if($UserSupport->hasPermission('list_admin') === false) {
	HCStudio\Util::redirectTo('../../apps/admin/invalid_permission');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['searcheable-object.js','admin-support-list.*']);

$Layout->setVar([
	'UserSupport' => $UserSupport
]);
$Layout();