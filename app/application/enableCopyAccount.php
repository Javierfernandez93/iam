<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{       
    if($data['user_trading_account_id'])
    {
        if($data['user_trading_account_provider_id'])
        {
            if((new DummieTrading\SignalProviderSuscriptor)->isAviableToSuscribe($data['user_trading_account_id']))
            {
                if(DummieTrading\SignalProvider::isAbleToEnableCopy($UserLogin->company_id,$data['user_trading_account_id']))
                {
                    if($response = DummieTrading\SignalProvider::enableCopy([
                        'user_trading_account_id' => $data['user_trading_account_id'],
                        'user_trading_account_provider_id' => $data['user_trading_account_provider_id']
                    ]))
                    {
                        $data["response"] = $response;

                        if($response['s'] == 1)
                        {
                            if(DummieTrading\SignalProviderSuscriptor::attachSuscriptor([
                                'user_trading_account_id' => $data['user_trading_account_id'],
                                'signal_provider_id' => (new DummieTrading\SignalProvider)->getSignalProviderId($data['user_trading_account_provider_id'])
                            ]))
                            {
                                $data["s"] = 1;
                                $data["r"] = "DATA_OK";
                            } else {
                                $data["s"] = 0;
                                $data["r"] = "NOT_ATTACHED";
                            }
                        } else {
                            $data["s"] = 0;
                            $data["r"] = "NOT_COPIED_BY_RESPONSE";
                        }
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_COPIED";
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_AVIABLE_TO_ENABLE_COPY";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "MAX_CONNECTIONS_AUTOCOPY_REACHED";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_USER_TRADING_ACCOUNT_PROVIDER_ID";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_TRADING_ACCOUNT_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 