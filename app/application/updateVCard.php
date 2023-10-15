<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
    if($data['vcard_per_user_id'])
    {
        $VCardPerUser = new DummieTrading\VCardPerUser;
        
        if($VCardPerUser->loadWhere('vcard_per_user_id = ?',$data['vcard_per_user_id']))
        {
            if($VCardPerUser->existRoute($data['route'],$data['vcard_per_user_id']) === false)
            {
                $VCardPerUser->title = $data['title'] ? $data['title'] : $VCardPerUser->title;
                $VCardPerUser->route = $data['route'] ? $data['route'] : $VCardPerUser->route;
                
                if($VCardPerUser->save())
                {
                    if(saveTagsPerVCard($data['catalog_tags_template'],$data['vcard_per_user_id']))
                    {
                        $data['r'] = 'DATA_OK';
                        $data['s'] = 1;
                    } else {
                        $data['r'] = 'NOT_SAVE_TAGS';
                        $data['s'] = 0;
                    }
                } else {
                    $data['r'] = 'NOT_SAVE';
                    $data['s'] = 0;
                }
            } else {
                $data['r'] = 'ROUTE_EXIST';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_VCARD_PER_USER';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_VCARD_PER_USER_ID';
        $data['s'] = 0;
    }
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function saveTagPerVCard(array $catalog_tag_template = null,int $vcard_per_user_id = null) : bool
{
    $TagPerVCard = new DummieTrading\TagPerVCard;
    
    if(!$TagPerVCard->loadWhere('vcard_per_user_id = ? AND catalog_tag_template_id = ?',[$vcard_per_user_id,$catalog_tag_template['catalog_tag_template_id']]))
    {
        $TagPerVCard->vcard_per_user_id = $vcard_per_user_id;
        $TagPerVCard->catalog_tag_template_id = $catalog_tag_template['catalog_tag_template_id'];
        $TagPerVCard->create_date = time();
    }

    $TagPerVCard->value = $catalog_tag_template['value'] ? DummieTrading\TagPerVCard::formatValue($catalog_tag_template) : '';
    
    return $TagPerVCard->save();
}

function saveTagsPerVCard(array $catalog_tags_template = null,int $vcard_per_user_id = null) : bool
{
    $saved = 0;

    foreach ($catalog_tags_template as $catalog_tag_template)
    {
        if(saveTagPerVCard($catalog_tag_template,$vcard_per_user_id))
        {
            $saved++;
        }
    }

    return $saved == sizeof($catalog_tags_template);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 