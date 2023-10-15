<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

if(true)
{	
    if($data['vcard_per_user_id'])
    {
        $TagPerVCard = new DummieTrading\TagPerVCard;

        $data['socials'] = [
            'facebook' => $TagPerVCard->getValueByTag($data['vcard_per_user_id'],'facebook'),
            'twitter' => $TagPerVCard->getValueByTag($data['vcard_per_user_id'],'twitter'),
            'youtube' => $TagPerVCard->getValueByTag($data['vcard_per_user_id'],'youtube'),
            'instagram' => $TagPerVCard->getValueByTag($data['vcard_per_user_id'],'instagram'),
            'pinterest' => $TagPerVCard->getValueByTag($data['vcard_per_user_id'],'pinterest'),
            'reddit' => $TagPerVCard->getValueByTag($data['vcard_per_user_id'],'reddit'),
            'whatsapp' => DummieTrading\TagPerVCard::unFormatPhone($TagPerVCard->getValueByTag($data['vcard_per_user_id'],'whatsapp')),
            'linkedin' => $TagPerVCard->getValueByTag($data['vcard_per_user_id'],'linkedin'),
        ];
        $data['r'] = 'DATA_OK';
        $data['s'] = 1;
    } else {
        $data['r'] = 'NOT_VCARD_PER_USER_ID';
        $data['s'] = 1;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 