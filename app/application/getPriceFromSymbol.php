<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['symbol'])
    {
        $UserTradingAccount = new DummieTrading\UserTradingAccount;
			
        if($user_trading_account_id = $UserTradingAccount->getTradingAccountFollowing($UserLogin->company_id,DummieTrading\CatalogTradingAccount::METATRADER))
        {
            if($data['id'] = $UserTradingAccount->getIdById($user_trading_account_id))
            {
                if ($response = Api\MT4::getMarketPrice([
                    'id' => $data['id'],
                    'symbol' => $data['symbol']
                ])) {
                    $data["ask"] = $response['price']['ask'];
                    $data["s"] = 1;
                    $data["r"] = "DATA_OK";
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_RESPONSE";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_ID";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_USER_TRADING_ACCOUTN_ID";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_SYMBOL";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 