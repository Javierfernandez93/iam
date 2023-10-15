<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if(isset($data['product_id']))
    {
        $Product = new DummieTrading\Product;
        
        if($Product->where("product_id","=",$data['product_id'])->updateStatus($data['status']))
        {
            $data["s"] = 1;
            $data["r"] = "DATA_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_UPDATE";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_PRODUCTS";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode($data);