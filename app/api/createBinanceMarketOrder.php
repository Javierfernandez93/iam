<?php  define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserApi = new DummieTrading\UserApi;

// if($UserApi->logged === true)
if(true)
{
    if(isset($data['symbol']))
    {
        $data['symbol'] = strtoupper($data['symbol']);
        
        if(JFStudio\ApiBinance::isValidSymbol($data['symbol']))
        {
            if(isset($data['side']))
            {
                if(JFStudio\ApiBinance::isValidSide($data['side']))
                {
                    if(isset($data['quantity']))
                    {
                        if($response = JFStudio\ApiBinance::createMarketOrder([
                            'symbol' => $data['symbol'],
                            'side' => $data['side'],
                            'quantity' => $data['quantity'],
                        ])) {
                            d($response);
                        }
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_QUANTITY";
                    }    
                } else {
                    $data["s"] = 0;
                    $data["r"] = "INVALID_SIDE";
                }    
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_SIDE";
            }    
        } else {
            $data["s"] = 0;
            $data["r"] = "INVALID_SYMBOL";
        }   
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_SYMBOL";
    }    
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode($data);