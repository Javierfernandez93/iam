<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['catalog_payment_method_id'])
    {
        $CatalogPaymentMethod = new DummieTrading\CatalogPaymentMethod;

        if($CatalogPaymentMethod->loadWhere('catalog_payment_method_id = ?',$data['catalog_payment_method_id']))
        {
            $CatalogPaymentMethod->fee = $data['fee'];

            if($CatalogPaymentMethod->save())
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_UPDATE";       
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_CATALOG_PAYMENT_METHOD";   
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_CATALOG_PAYMENT_METHOD_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);