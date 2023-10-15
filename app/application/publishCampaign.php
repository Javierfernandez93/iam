<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['campaign_banner_per_user_id'])
    {
        $CampaignBannerPerUser = new DummieTrading\CampaignBannerPerUser;
        
        if($CampaignBannerPerUser->loadWhere('campaign_banner_per_user_id = ?',$data['campaign_banner_per_user_id']))
        { 
            $data['status'] = DummieTrading\CampaignBannerPerUser::PUBLISHED;

            $CampaignBannerPerUser->status = $data['status'];
            
            if($CampaignBannerPerUser->save())
            {
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_SAVE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_CAMPAIGN_BANNER_PER_USER';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_CAMPAIGN_BANNER_PER_USER_ID';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 