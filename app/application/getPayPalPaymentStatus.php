<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['paymentId'])
    {
        if($data['PayerID'])
        {
            if($payment = getPayment($data['paymentId'],$data['PayerID']))
            {
                if($payment->getState() == JFStudio\PayPal::APPROVED)
                {
                    $data['buy_per_user_id'] = $payment->getTransactions()[0]->getInvoiceNumber();

                    // UPDATE TRANSACTION_REQUIREMENT
                    $BuyPerUser = new DummieTrading\BuyPerUser;
                    
                    if($BuyPerUser->loadWhere("buy_per_user_id = ? AND status = ?",[$data['buy_per_user_id'],DummieTrading\BuyPerUser::PENDING]))
                    {
                        $url = HCStudio\Connection::getMainPath()."/app/application/validateBuy.php";

                        $Curl = new JFStudio\Curl;
                        $Curl->post($url, [
                            'user' => HCStudio\Util::USERNAME,
                            'password' => HCStudio\Util::PASSWORD,
                            'invoice_id' => $BuyPerUser->invoice_id,
                            'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::PAYPAL_CDN,
                            'ipn_data' => json_encode($data),
                        ]);

                        if($response = $Curl->getResponse(true))
                        {
                            $data['response'] = $response;
                            $data['s'] = 1;
                            $data['r'] = 'DATA_OK';
                        } else {
                            $data['s'] = 0;
                            $data['r'] = 'NOT_RESPONSE';
                        }
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_TRANSACTION_REQUIREMENT_PER_USER";  
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_APPROVED";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_PAYMENT";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_PAYER_ID";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_PAYMENT_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function getPayment(string $paymentId = null,string $PayerID = null)
{
    require_once TO_ROOT . "/system/vendor/autoload.php";

    $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
            JFStudio\PayPal::CLIENT_ID,
            JFStudio\PayPal::CLIENT_SECRET
        )
    );

    $apiContext->setConfig(['mode' => JFStudio\PayPal::MODE]);

    $payment = PayPal\Api\Payment::get($paymentId, $apiContext);
    $execution = new \PayPal\Api\PaymentExecution();
    $execution->setPayerId($PayerID);

    ini_set('error_reporting', E_ALL ^ E_NOTICE);
    ini_set('display_errors', '1');

    try {
        $payment->execute($execution, $apiContext);
    } catch (Exception $e) {

    }

    return $payment;
}

echo json_encode($data);