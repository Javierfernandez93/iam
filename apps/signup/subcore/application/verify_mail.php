<?php define("TO_ROOT","../../../..");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data["mail"]){

	$UserLogin = new DummieTrading\UserLogin;

	if($UserLogin->isUniqueMail($data["mail"]))
	{
		$data["s"] = 1;
		$data["r"] = 'MAIL_OK';
	} else {
		$data["s"] = 0;
		$data["r"] = 'ALREADY_TAKEN';		
	}
} else {
	$data["s"] = 0;
	$data["r"] = 'NOT_EMAIL';
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 