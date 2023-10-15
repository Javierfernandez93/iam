<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['tag'])
    {
        $CatalogTagIntent = new DummieTrading\CatalogTagIntent;

        if($CatalogTagIntent->loadWhere("tag = ?",$data['tag']) == false)
        {
            $CatalogTagIntent->has_response = 1;
            $CatalogTagIntent->tag = $data['tag'];
            $CatalogTagIntent->main_node = 1;
            $CatalogTagIntent->create_date = time();
        }

        if($CatalogTagIntent->save())
        {
            if(saveIntents($data['intents'],$CatalogTagIntent->getId()) === true)
            {
                if(saveReplysPerCatalogTagIntent($data['replys_per_catalog_tag_intent'],$CatalogTagIntent->getId()) === true)
                {
                    $data['s'] = 1;
                    $data['r'] = "SAVE_OK";
                } else {
                    $data['s'] = 0;
                    $data['r'] = "NOT_SAVE_REPLYS_PER_CATALOG_TANG_INTENT";
                }
            } else {
                $data['s'] = 0;
                $data['r'] = "NOT_SAVE_INTENTS";
            }
        } else {
            $data['s'] = 0;
            $data['r'] = "NOT_SAVE";
        }
    } else {
        $data['s'] = 0;
        $data['r'] = "NOT_TAG";
    }
} else {
	$data['r'] = 'INVALID_CREDENTIALS';
	$data['s'] = 0;
}

function saveIntents(array $intents = null,int $catalog_tag_intent_id = null) : bool
{
    $saved = 0;

    foreach ($intents as $intent) 
    {
        if(empty($intent['words']) === false)
        {
            $Intent = new DummieTrading\Intent;
            $Intent->catalog_tag_intent_id = $catalog_tag_intent_id;
            $Intent->words = $intent['words'];
            $Intent->create_date = time();
            
            if($Intent->save())
            {
                $saved++;
            }
        }
    }

    return $saved == sizeof($intents);
}

function saveReplysPerCatalogTagIntent(array $replys_per_catalog_tag_intent = null,int $catalog_tag_intent_id ) : bool
{
    $saved = 0;

    foreach ($replys_per_catalog_tag_intent as $reply_per_catalog_tag_intent) 
    {
        if(empty($reply_per_catalog_tag_intent['reply']) === false)
        {
            $ReplyPerCatalogTagIntent = new DummieTrading\ReplyPerCatalogTagIntent;
            $ReplyPerCatalogTagIntent->catalog_tag_intent_id = $catalog_tag_intent_id;
            $ReplyPerCatalogTagIntent->reply = $reply_per_catalog_tag_intent['reply'];
            $ReplyPerCatalogTagIntent->create_date = time();

            if($ReplyPerCatalogTagIntent->save())
            {
                $saved++;
            }
        }
    }

    return $saved == sizeof($replys_per_catalog_tag_intent);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 