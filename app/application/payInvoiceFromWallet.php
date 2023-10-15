<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['invoice_id'])
    {
        $BuyPerUser = new DummieTrading\BuyPerUser;

		if($BuyPerUser->loadWhere('invoice_id = ?',$data['invoice_id']))
		{
            if($ReceiverWallet = BlockChain\Wallet::getWallet(BlockChain\Wallet::MAIN_EWALLET))
            {
                if($Wallet = BlockChain\Wallet::getWallet($UserLogin->company_id))
                {
                    $message = '';

                    if($transaction_per_wallet_id = $Wallet->createTransaction($ReceiverWallet->public_key,$BuyPerUser->amount,BlockChain\Transaction::prepareData(['@optMessage'=>$message]),true,BlockChain\Transaction::DEFAULT_FEE))
                    {
                        $url = trim(HCStudio\Connection::getMainPath()."/app/application/validateBuy.php");

                        $Curl = new JFStudio\Curl;
                        
                        
                        // d($url."?".http_build_query([
                        //     'user' => HCStudio\Util::USERNAME,
                        //     'password' => HCStudio\Util::PASSWORD,
                        //     'invoice_id' => $data['invoice_id'],
                        //     'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::EWALLET,
                        //     'ipn_data' => json_encode($data),
                        // ]));

                        $Curl->post($url, [
                            'user' => HCStudio\Util::USERNAME,
                            'password' => HCStudio\Util::PASSWORD,
                            'invoice_id' => $data['invoice_id'],
                            'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::EWALLET,
                            'ipn_data' => json_encode($data),
                        ]);

                        
                        // d($url."?".http_build_query([
                        //     'user' => HCStudio\Util::USERNAME,
                        //     'password' => HCStudio\Util::PASSWORD,
                        //     'invoice_id' => $data['invoice_id'],
                        //     'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::EWALLET,
                        //     'ipn_data' => json_encode($data),
                        // ]));

                        if($response = $Curl->getResponse(true)) 
                        {
                            if($response['s'] == 1)
                            {
                                $data['response'] = $response;
                                $data["s"] = 1;
                                $data["r"] = "DATA_OK";
                            } else {
                                $data["response_r"] = $response['r'];
                                $data["s"] = 0;
                                $data["r"] = "error_on_response";
                            }
                        } else {
                            $data["s"] = 0;
                            $data["r"] = "NOT_RESPONSE";
                        }
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_TRANSACTION_PER_WALLET_ID";
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_EWALLET";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_RECEIVER_WALLET";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_BUY_PER_USER";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_INVOICE_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 