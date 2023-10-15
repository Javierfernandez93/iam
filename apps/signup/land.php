<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true) {
	HCStudio\Util::redirectTo(TO_ROOT . "/apps/backoffice/");
}

 
$Layout = JFStudio\Layout::getInstance();
$Layout->init("Únete ahora mismo","land","land","",TO_ROOT."/");

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['theme.min.css','home.css','land.*','header-transparent.css'],true);

$Session = new HCStudio\Session('utm');
$sponsor_id = $Session->get('sponsor_id');
$utm = $Session->get('utm');

if(!empty(HCStudio\Util::getVarFromPGS('sponsor_id'))) {
	$sponsor_id = HCStudio\Util::getVarFromPGS('sponsor_id');
}

$Layout->setVar([
	"nav" => 'land',
	'UserLogin' => $UserLogin,
	'sponsor_id' => $sponsor_id,
	'Country' => (new World\Country)
]);

$Layout();