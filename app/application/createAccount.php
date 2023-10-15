<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{       
    $data['type'] = (new DummieTrading\CatalogPlatform)->getType($data['catalog_platform_id']);
    
    $data['profile_id'] = (new DummieTrading\CatalogBroker)->getProfileId($data['server']);

    if($response = Api\MT4::createAccount([
        'login' => $data['login'],
        'password' => $data['password'],
        'catalog_platform_id' => $data['catalog_platform_id'],
        'type' => $data['type'],
        'profile_id' => $data['profile_id'],
        'server' => $data['server'],
        'name' => $UserLogin->getNames()
    ]))
    {
        if($response['s'] == 1)
        {
            if(DummieTrading\UserTradingAccount::attachIdToAccount([
                'user_trading_account_id' => $data['user_trading_account_id'],
                'id' => $response['accountId']
            ]))
            {
                if(isset($response['account']))
                {
                    $response['account']['user_trading_account_id'] = $data['user_trading_account_id'];

                    DummieTrading\UserTradingAccount::updateData($response['account']);
                }

                $data["id"] = $response['accountId'];
                $data["s"] = 1;
                $data["r"] = "DATA_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_ATTACHED_ACCOUNT";
            }            
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_SUCCESS";
            $data["metaTraderResponse"] = $response;
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_RESPONSE";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 