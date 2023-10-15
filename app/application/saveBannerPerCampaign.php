<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['campaign_banner_per_user_id'])
    {
        if($data['catalog_banner_id'])
        {
            if($data['source'])
            {
                if($data['link'])
                {
                    $BannerPerCampaign = new DummieTrading\BannerPerCampaign;
                    
                    if(!$BannerPerCampaign->loadWhere("catalog_banner_id = ? AND campaign_banner_per_user_id = ?",[$data['catalog_banner_id'],$data['campaign_banner_per_user_id']]))
                    {
                        $BannerPerCampaign->catalog_banner_id = $data['catalog_banner_id'];
                        $BannerPerCampaign->campaign_banner_per_user_id = $data['campaign_banner_per_user_id'];
                        $BannerPerCampaign->create_date = time();
                    }
                    
                    $BannerPerCampaign->source = $data['source'];
                    $BannerPerCampaign->link = $data['link'];
                    
                    if($BannerPerCampaign->save())
                    {
                        $data['r'] = 'DATA_OK';
                        $data['s'] = 1;
                    } else {
                        $data['r'] = 'NOT_SAVE';
                        $data['s'] = 0;
                    }
                } else {
                    $data['r'] = 'INVALID_LINK';
                    $data['s'] = 0;
                }
            } else {
                $data['r'] = 'INVALID_IMAGE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_CATALOG_BANNER_ID';
            $data['s'] = 1;
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