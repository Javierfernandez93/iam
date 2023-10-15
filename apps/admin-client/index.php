<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init(" » Clientes","index","admin","",TO_ROOT."/");

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

$user_support_id = HCStudio\Util::getVarFromPGS('usid');

if($user_support_id && $UserSupport->hasPermission('list_clients_per_seller') === false) 
{
	HCStudio\Util::redirectTo('../../apps/admin/invalid_permission');
}

if($UserSupport->hasPermission('list_client') === false) {
	HCStudio\Util::redirectTo('../../apps/admin/invalid_permission');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['searcheable-object.js','comment-object.js','admin-client-list.*']);

$Layout->setVar([
	'UserSupport' => $UserSupport
]);
$Layout();