<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
   if($data['referral_id'])
   {
        if($profile = $UserSupport->getUser($data['referral_id']))
        {
            $data["profile"] = $profile;
            $data["s"] = 1;
            $data["r"] = "LOGGED_OK";
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_PROFILE";
        }
   } else {
        $data["s"] = 0;
        $data["r"] = "NOT_REFERRAL_ID";
   }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 