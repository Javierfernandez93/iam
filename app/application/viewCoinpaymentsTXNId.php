<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['txn_id'])
    {
        require_once TO_ROOT .'/vendor/autoload.php';

        $CoinpaymentsAPI = new CoinpaymentsAPI(JFStudio\CoinPayments::PRIVATE_KEY, JFStudio\CoinPayments::PUBLIC_KEY, 'json');
        
        try {            
            $result = $CoinpaymentsAPI->GetTxInfoSingle($data['txn_id']);
            
            if ($result['error'] == 'ok') 
            { 
                $data['coinpaymentsResponse'] = $result['result'];
                $data['r'] = "DATA_OK";
                $data['s'] = 1;
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            exit();
        }
    
    } else {
        $data['r'] = "NOT_TRANSACTION_PER_WALLET_ID";
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 