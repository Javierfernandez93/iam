<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{   
    if($profits = (new DummieTrading\CommissionPerUser)->getAll($UserLogin->company_id))
    {
        $data['profits'] = format($profits);
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_DATA";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

function format(array $profits = null) : array {
    $Package = new DummieTrading\Package;
    return array_map(function($profit) use($Package) {
        $profit['package'] = $Package->getPackage($profit['package_id']);
        return $profit;
    },$profits);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 