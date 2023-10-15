<?php define("TO_ROOT", "../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$data=$data["data"];

$UserLogin = new DummieTrading\UserLogin;
$Cart = Jcart\Cart::getInstance($UserLogin->company_id); // @todo change for 

error_reporting(1);
# get the HTML
ob_start();
include(dirname(__FILE__).'/template/payment.php');
$content = ob_get_clean();

# convert in PDF
require_once(dirname(__FILE__).'/html2pdf.class.php');
try {
    $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(15, 5, 15, 5));
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->writeHTML($content, isset($_GET['vars']));
    $html2pdf->Output($data["file_name"].'.pdf','F');
	$data['s'] = 1;

	$data['file_path_pdf'] = TO_ROOT.'/subcore/bcode/'.$data['file_name'].'.pdf';
	$data['html'] = getHtml($data['file_path_pdf']);
}

catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}

function getHtml($file_path_pdf = null)
{
	if(isset($file_path_pdf))
	{	
		$Layout = JFStudio\Layout::getInstance();
		$Layout->init("","oxxo-buttons","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
		$Layout->setVar([
			'file_path_pdf' => $file_path_pdf,
		]);

		return $Layout->getHtml();
	}
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 