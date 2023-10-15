<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['name'])
    {
        if($data['description'])
        {
            $CampaignBannerPerUser = new DummieTrading\CampaignBannerPerUser;
            $CampaignBannerPerUser->user_login_id = $UserLogin->company_id;
            $CampaignBannerPerUser->name = $data['name'];
            $CampaignBannerPerUser->country_ids = format($data['countries']);
            $CampaignBannerPerUser->description = $data['description'];
            $CampaignBannerPerUser->create_date = time();
            
            if($CampaignBannerPerUser->save())
            {
                $data['campaign_banner_per_user_id'] = $CampaignBannerPerUser->getId();
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_SAVE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_DESCRIPTION';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_NAME';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function format(array $countries = null)
{
    $countries = array_column($countries,'country_id');

    return json_encode($countries);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 