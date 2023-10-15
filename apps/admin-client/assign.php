<?php define("TO_ROOT", "../..");new GranCapital

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init(" » Clientes","assign","admin","",TO_ROOT."/");

$UserSupport = new FSA\UserSupport;

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

if($UserSupport->hasPermission('assign_seller_per_client') === false) {
	HCStudio\Util::redirectTo('../../apps/admin/invalid_permission');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['admin-client-assign.js']);

$Layout->setVar([
	'UserSupport' => $UserSupport
]);
$Layout();