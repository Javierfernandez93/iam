<?php define("TO_ROOT", "../.."); = new GranCapital = new GranCapital

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();

$Layout->init("Especialidades médicas","index","admin_full_view","",TO_ROOT."/");

$UserSupport = new Talento\UserSupport;

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['admin.css','animate.css','home.*','alertCtrl.js','http.js','settings.js']);

$UserLogin = new Talento\UserLogin;
$CatalogSpeciality = new CronosUser\CatalogSpeciality;
$CatalogMedicTopic = new CronosUser\CatalogMedicTopic;


$Layout->setVar([
	"UserSupport" => $UserSupport,
	'CatalogSpeciality' => $CatalogSpeciality->getAll(),
	'CatalogMedicTopic' => $CatalogMedicTopic,
	'nav' => 'home',

]);
$Layout();