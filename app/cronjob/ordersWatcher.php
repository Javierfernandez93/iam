<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

// if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->logged === true)
if(true)
{
    if($accounts = (new DummieTrading\UserTradingAccount)->getAccountFilterByCatalogTradingAccounts(DummieTrading\CatalogTradingAccount::METATRADER))
    {
        $UserTradingAccount = new DummieTrading\UserTradingAccount;

        foreach($accounts as $account)
        {
            $response = Api\MT4::getAccount($account['id']);
            
            if($response['s'] == 1)
            {
                // $response = Api\MT4::getLastOrder($account['id']);

                $drawdown = Api\MT4::calculateDrawDown($account['balance'],$response['account']['equity']);

                if($drawdown < 0)
                {
                    if($drawdown <= -5)
                    {
                        

                        // disable account
                    }
                }
            }
        }
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 