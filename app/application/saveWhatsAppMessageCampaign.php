<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['whatsapp_campaign_id'])
    {
        if($data['title'])
        {
            $WhatsAppMessagePerCampaign = new DummieTrading\WhatsAppMessagePerCampaign;
            
            if($data['whatsapp_message_per_campaign_id'])
            {
                $WhatsAppMessagePerCampaign->loadWhere("whatsapp_message_per_campaign_id = ?",$data['whatsapp_message_per_campaign_id']);
            }
            
            $WhatsAppMessagePerCampaign->catalog_message_whatsapp_type_id = $data['catalog_message_whatsapp_type_id'] ? $data['catalog_message_whatsapp_type_id'] : 1;
            $WhatsAppMessagePerCampaign->whatsapp_campaign_id = $data['whatsapp_campaign_id'];
            $WhatsAppMessagePerCampaign->title = $data['title'];
            $WhatsAppMessagePerCampaign->content = $data['content'] ? $data['content'] : '';
            $WhatsAppMessagePerCampaign->image = $data['image'] ? $data['image'] : '';
            $WhatsAppMessagePerCampaign->body = $data['body'] ? $data['body'] : '';
            $WhatsAppMessagePerCampaign->footer = $data['footer'] ? $data['footer'] : '';
            $WhatsAppMessagePerCampaign->button = $data['button'] ? $data['button'] : '';
            $WhatsAppMessagePerCampaign->section = $data['sections'] ? json_encode($data['sections']) : '';
            $WhatsAppMessagePerCampaign->create_date = time();

            $WhatsAppMessagePerCampaign->connection()->stmtQuery("SET NAMES utf8mb4");

            if($WhatsAppMessagePerCampaign->save())
            {
                $data['r'] = 'SAVE_OK';
                $data['s'] = 1;
            }  else {
                $data['r'] = 'NOT_SAVE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_TITLE';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_WHATSAPP_CAMPAIGN_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}


echo json_encode(HCStudio\Util::compressDataForPhone($data)); 