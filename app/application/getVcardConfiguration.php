<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php"; 

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{	
	if($data['vcard_per_user_id'])
	{
        if($template_id = (new DummieTrading\VCardPerUser)->getTemplateId($data['vcard_per_user_id']))
        {
            if($catalog_tags_template = (new DummieTrading\CatalogTagTemplate)->getAll($template_id))
            {
                $data['catalog_tags_template'] = format($catalog_tags_template,$data['vcard_per_user_id'],$UserLogin->getCountryId());
                $data['r'] = 'DATA_OK';
                $data['s'] = 1;
            } else {
                $data['r'] = 'NOT_TAGS_TEMPLATE';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_TEMPLATE_ID';
            $data['s'] = 0;
        }
	} else {
		$data['r'] = 'NOT_VCARD_PER_USER_id';
		$data['s'] = 0;
	}
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

function format(array $catalog_tags_template = null,int $vcard_per_user_id = null,int $country_id = null) : array
{
    $TagPerSheet = new DummieTrading\TagPerVCard;

    return array_map(function($catalog_tag_template) use($TagPerSheet,$vcard_per_user_id,$country_id) {
        $catalog_tag_template['value'] = '';

        if($value = $TagPerSheet->getValue($vcard_per_user_id, $catalog_tag_template['catalog_tag_template_id']))
        {
            $catalog_tag_template['value'] = $value;
        }

        $catalog_tag_template['value'] = DummieTrading\TagPerVCard::unFormatValue($catalog_tag_template);
        
        return $catalog_tag_template;
    },$catalog_tags_template);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 