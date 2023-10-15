<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "system/core.php";

if($UserLogin->logged === true)
{	
	if((new DummieTrading\IntentChat)->add($data))
	{
        $data['s'] = 1;
        $data['r'] = 'DATA_OK';
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_SAVE';
    }
} else {
	$data['s'] = 0;
	$data['r'] = "NOT_MESSAGE";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 