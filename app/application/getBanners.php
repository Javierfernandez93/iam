<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($banners = (new DummieTrading\CatalogBanner)->getAll())
    {
        $data['banners'] = format($banners,$data['campaign_banner_per_user_id']);
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

function format(array $catalogBanners = null,int $campaign_banner_per_user_id = null) : array
{
    $BannerPerCampaign = new DummieTrading\BannerPerCampaign;

    return array_map(function($catalogBanner) use($BannerPerCampaign,$campaign_banner_per_user_id){

        $catalogBanner['link'] = '';
        $catalogBanner['source'] = '';

        if($banner = $BannerPerCampaign->getBanner($campaign_banner_per_user_id,$catalogBanner['catalog_banner_id']))
        {
            $catalogBanner = array_merge($catalogBanner,$banner);
        }

        return $catalogBanner;
    },$catalogBanners);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 