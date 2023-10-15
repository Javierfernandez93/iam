<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['company_id'])
    {
        $UserPlan = new DummieTrading\UserPlan;

        if($UserPlan->cargarDonde("user_login_id = ?",$data['company_id']))
        {   
            $UserPlan->catalog_plan_id = 0;
            $UserPlan->ammount = 0;
            $UserPlan->sponsor_profit = 0;
            $UserPlan->aditional_profit = 0;
            
            if($UserPlan->save())
            {
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = '0';
                $data["r"] = "NOT_SAVE";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_USER_PLAN";
        }
    } else {
        $data['r'] = "NOT_COMPANY_ID";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 