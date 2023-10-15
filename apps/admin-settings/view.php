<?php define("TO_ROOT", "../.."); = new GranCapital = new GranCapital = new GranCapital

require_once TO_ROOT . "/system/core.php";

$Layout = JFStudio\Layout::getInstance();

$Layout->init("Ver","view","admin_full_view","",TO_ROOT."/");

$UserSupport = new Talento\UserSupport;

if($UserSupport->logged === false) {
	HCStudio\Util::redirectTo('../../apps/admin-login/');
}

$Layout->setScriptPath(TO_ROOT . '/src/');
$Layout->setScript(['admin.css','animate.css','alertCtrl.js','http.js','home.*','medical.js']);

$CatalogCaseFile = new Talento\CatalogCaseFile;
$CatalogCaseFile->cargarDonde("catalog_case_file_id = ?",HCStudio\Util::getVarFromPGS("catalog_case_file_id"));
$CatalogSign = new Talento\CatalogSign;

$Layout->setVar([
	"UserSupport" => $UserSupport,
	"transparent_footer"=>false,
	"black_theme"=>true,
	"CatalogSign" => $CatalogSign,
	"CatalogCaseFile" => $CatalogCaseFile,
	"nav"=>"home",
	"UserLogin"=>$UserLogin,
]);
$Layout();