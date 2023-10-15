<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['package_id'] = DummieTrading\Package::getMonthlyPackage($UserLogin->company_id))
    {
        $Package = new DummieTrading\Package;
        
        if($Package->loadWhere("package_id = ? AND status = ?",[$data['package_id'],JFStudio\Constants::AVIABLE]))
        {
            $data['package'] = $Package->data();
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_MAIN_PACKAGE';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_PACKAGE_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 