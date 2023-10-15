<?php

use SendGrid\Mail\To;

 define("TO_ROOT", "../../");

require_once TO_ROOT . 'system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

if($response = JFStudio\ApiBinance::getExchangeInfo())
{
    if($response['s'] == 1)
    {
        if(isset($response['exchangeInfo']))
        {
            if(isset($response['exchangeInfo']['symbols']))
            {
                $symbols = [];

                foreach($response['exchangeInfo']['symbols'] as $symbol)
                {
                    $symbols[] = $symbol['symbol'];
                }
                
                if (file_put_contents(TO_ROOT."/src/files/symbols/symbols.json", json_encode($symbols)))
                {
                    $data['s'] = 1;
                    $data['data'] = 'DATA_OK';
                } else {
                    $data['s'] = 0;
                    $data['data'] = 'NOT_SAVED';
                }
            }
        }
    }
}

echo json_encode($data);