<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['user_support_id'])
    {
        $UserSupportUpdate = new DummieTrading\UserSupport;

        if($UserSupportUpdate->cargarDonde("user_support_id = ?",$data['user_support_id'])) 
        {
            $UserSupportUpdate->status = 0;

            if($UserSupportUpdate->save())
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data['r'] = "NOT_SAVE";
                $data['s'] = 0;    
            }
        } else {
            $data['r'] = "NOT_LOADED";
            $data['s'] = 0;    
        }
    } else {
        $data['r'] = "DATA_ERROR";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 