<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($notices = format((new DummieTrading\Notice)->getAllPublished(),$UserLogin->isActive()))
    {   
        $data["notices"] = $notices;
        $data["s"] = 1;
        $data["r"] = "DATA_OK";
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_NOTICES";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function isAviable(array $notice = null,bool $active = null) : bool
{
    $targetSelected = $notice['target'] != DummieTrading\Notice::ALL ? $notice['target'] == $active : true;

    if($targetSelected)
    {
        if($notice['start_date'] == 0 && $notice['end_date'] == 0)
        {
            return true;
        }
        
        $time = time();
    
        return $time >= $notice['start_date'] && $time <= $notice['end_date'];
    }

    return $targetSelected;
}

function format(array $notices = null,bool $active = null)
{
    if(is_array($notices) && sizeof($notices) > 0)
    {
        $_notices = [];
        
        foreach($notices as $notice)
        {
            if(isAviable($notice,$active))
            {
                $_notices[] = $notice;
            }
        }

        return $_notices;
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 