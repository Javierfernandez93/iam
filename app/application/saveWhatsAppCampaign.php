<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['title'])
    {
        if($data['content'])
        {
            $CampaignWhatsapp = new DummieTrading\CampaignWhatsapp;
            $CampaignWhatsapp->title = $data['title'];
            $CampaignWhatsapp->content = $data['content'];
            $CampaignWhatsapp->create_date = time();
            
            if($CampaignWhatsapp->save())
            {
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_SAVE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_CONTENT';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_TITLE';
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