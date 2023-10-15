<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();
$Layout->init(" » Editar cliente","edit","admin","",TO_ROOT."/");

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['jquery.mask.js','signature_pad.min.js','client-object.js','admin-client-edit.js','admin-signature.css']);

$Layout->setVar([
	'UserSupport' => $UserSupport
]);
$Layout();