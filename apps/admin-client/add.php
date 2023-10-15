<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init(" » Dar de alta cliente","add","admin","",TO_ROOT."/");

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

if($UserSupport->hasPermission('add_client') === false) {
	HCStudio\Util::redirectTo('../../apps/admin/invalid_permission');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['jquery.mask.js','signature_pad.min.js','client-object.js','admin-client-add.js','admin-signature.css']);

$Layout->setVar([
	'UserSupport' => $UserSupport
]);
$Layout();