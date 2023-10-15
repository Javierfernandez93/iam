<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($referrals = (new DummieTrading\UserReferral)->getReferrals($UserLogin->company_id))
    {
        $data['referrals'] = formatData($referrals,$UserLogin->company_id);
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

function formatData(array $referrals = null,int $user_login_id = null) : array {
    $Exercise = new DummieTrading\Exercise;
    $CommissionPerUser = new DummieTrading\CommissionPerUser;
    $UserTradingAccount = new DummieTrading\UserTradingAccount;
    $Country = new World\Country;
    
    return array_map(function($referral) use($Country,$Exercise,$UserTradingAccount,$CommissionPerUser,$user_login_id) {
        $referral['country'] = $Country->getCountryName($referral['country_id']);
        $referral['phone_code'] = $Country->getPhoneCodeByCountryId($referral['country_id']);
        
        $referral['hasDemo'] = false;
        $referral['hasAccount'] = false;

        if($hasDemo = $Exercise->hasExerciseStatus($referral['user_login_id'],"'".DummieTrading\Exercise::WAITING."','".DummieTrading\Exercise::IN_PROGRESS."'"))
        {
            $referral['hasDemo'] = $hasDemo;
        } else if($hasAccount = $UserTradingAccount->hasAccountStatus($referral['user_login_id'],"'".DummieTrading\UserTradingAccount::WAITING."','".DummieTrading\UserTradingAccount::IN_PROGRESS."'"))
        {
            $referral['hasAccount'] = $hasAccount;
        }
        
        $referral['commission'] = $CommissionPerUser->getSumFull($user_login_id,$referral['user_login_id']) ?? 0;

        return $referral;
    },$referrals);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 