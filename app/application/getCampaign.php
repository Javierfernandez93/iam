<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['campaign_banner_per_user_id'])
    {
        if($campaign = (new DummieTrading\CampaignBannerPerUser)->getSingle($data['campaign_banner_per_user_id']))
        {
            $data['campaign'] = format($campaign);
            $data['r'] = 'DATA_OK';
            $data['s'] = 1;
        } else {
            $data['r'] = 'NOT_CAMPAIGN';
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

function format(array $campaign = null) : array
{
    $campaign['countries'] = [];

    if($campaign['country_ids'])
    {
        $campaign['countries'] = json_decode($campaign['country_ids'],true);

        $Country = new World\Country;

        $campaign['countries'] = array_map(function($country_id) use($Country){
            $country = $Country->getSingleByWeb($country_id);
            return $country;
        },$campaign['countries']);
    }

    return $campaign;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 