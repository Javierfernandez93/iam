<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($campaigns = (new DummieTrading\CampaignBannerPerUser)->getAll($UserLogin->company_id))
    {
        $data['campaigns'] = format($campaigns);
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_CAMPAIGNS';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function format(array $campaigns = null) : array
{
    $ClickPerBanner = new DummieTrading\ClickPerBanner;
    $PrintPerBanner = new DummieTrading\PrintPerBanner;
    $BannerPerCampaign = new DummieTrading\BannerPerCampaign;
    
    return array_map(function (array $campaign) use($PrintPerBanner,$ClickPerBanner,$BannerPerCampaign){
        $campaign['prints'] = 0;
        $campaign['clicks'] = 0;
        
        if($banner_per_campaign_ids = $BannerPerCampaign->getIds($campaign['campaign_banner_per_user_id']))
        {
            $banner_per_campaign_ids = implode(',',$banner_per_campaign_ids);

            $campaign['prints'] = $PrintPerBanner->getCountIn($banner_per_campaign_ids);
            $campaign['clicks'] = $ClickPerBanner->getCountIn($banner_per_campaign_ids);
        }

        $campaign['amount_of_banner'] = $BannerPerCampaign->getCount($campaign['campaign_banner_per_user_id']);
        return $campaign;
    },$campaigns);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 