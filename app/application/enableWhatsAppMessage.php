<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['whatsapp_message_per_campaign_id'])
    {
        $WhatsAppMessagePerCampaign = new DummieTrading\WhatsAppMessagePerCampaign;
        
        if($WhatsAppMessagePerCampaign->loadWhere('whatsapp_message_per_campaign_id = ?',$data['whatsapp_message_per_campaign_id']))
        {
            $data['status'] = JFStudio\Constants::DISABLED;

            $WhatsAppMessagePerCampaign->status = $data['status'];

            if($WhatsAppMessagePerCampaign->save())
            {
                $data['r'] = 'SAVE_OK';
                $data['s'] = 1;
            }  else {
                $data['r'] = 'NOT_SAVE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_WHATSAPP_LIST_PER_USER';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_WHATSAPP_MESSAGE_PER_CAMPAIGN_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}


echo json_encode(HCStudio\Util::compressDataForPhone($data)); 