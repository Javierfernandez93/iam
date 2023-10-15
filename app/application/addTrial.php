<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    Jcart\Cart::deleteCarts();

    $Cart = Jcart\Cart::hasInstances() ? Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE) : Jcart\Cart::getInstance();
	$Cart->setVar('country_id',(new DummieTrading\UserAddress)->getCountryId($data['user_login_id']));
	$Cart->loadFromSession();
    
    if($Cart->save())
    {
        $Package = new DummieTrading\Package;
	
        if($Package->loadWhere("package_id = ? AND status != ?",[DummieTrading\Package::TRIAL,JFStudio\Constants::DELETE]))
        {
            $Cart = Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE);
            $Cart->loadFromSession();	

            if($Cart->addItem($Package,1))
            {
                if($Cart->save())
                {
                    $CatalogPaymentMethod = new DummieTrading\CatalogPaymentMethod;
	
                    if($CatalogPaymentMethod->loadWhere("catalog_payment_method_id = ? AND status != ?",[DummieTrading\CatalogPaymentMethod::EWALLET,-1]))
                    {
                        $Cart = Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE);
                        $Cart->loadFromSession();    
                        $Cart->setVar('catalog_payment_method_id',DummieTrading\CatalogPaymentMethod::EWALLET);

                        $Cart->setVar('fee',$Cart->calculateFee());

                        if($Cart->save())
                        {
                            if($BuyPerUser = saveBuy($Cart,$data['user_login_id']))
                            {	
                                $data['buy_per_user_id'] = $BuyPerUser->getId();
                                $data['invoice_id'] = $BuyPerUser->invoice_id;

                                if($BuyPerUser->save())
                                {
                                    $Cart->delete();

                                    $data['s'] = 1;
                                    $data['r'] = 'SAVE_OK';
                                } else {
                                    $data['s'] = 0;
                                    $data['r'] = 'NOT_UPDATE';
                                } 
                            } else {
                                $data['s'] = 0;
                                $data['r'] = 'NOT_SAVE';
                            } 
                        } else {
                            $data['s'] = 0;
                            $data['r'] = 'NOT_SAVE';
                        } 
                    } else {
                        $data['r'] = 'NOT_CATALOG_PAYMENT_METHOD';
                        $data['s'] = 0;
                    }
                } else {
                    $data['r'] = 'NOT_SAVED';
                    $data['s'] = 0;	
                }
            } else {
                $data['r'] = 'NOT_ADDED';
                $data['s'] = 0;
            }
        } else {
            $data['r'] = 'NOT_MAIN_PACKAGE';
            $data['s'] = 0;
        }
    } else {
        $data['r'] = 'NOT_SAVE';
        $data['s'] = 0;
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}


function createTransaction(DummieTrading\BuyPerUser $BuyPerUser = null)
{
	if($BuyPerUser->catalog_payment_method_id == DummieTrading\CatalogPaymentMethod::COINPAYMENTS)
	{
		return createTransactionFromCoinPayments($BuyPerUser);
	} else if($BuyPerUser->catalog_payment_method_id == DummieTrading\CatalogPaymentMethod::EWALLET) {
		return createTransactionFromEwallet($BuyPerUser);
	} else if($BuyPerUser->catalog_payment_method_id == DummieTrading\CatalogPaymentMethod::PAYPAL) {
		return createTransactionPayPal($BuyPerUser);
	} else if($BuyPerUser->catalog_payment_method_id == DummieTrading\CatalogPaymentMethod::AIRTM) {
		return createTransactionAirtm($BuyPerUser);
	} else if($BuyPerUser->catalog_payment_method_id == DummieTrading\CatalogPaymentMethod::CAPITALPAYMENTS) {
		return createTransactionCapitalPayments($BuyPerUser);
	}
}

function createTransactionCapitalPayments(DummieTrading\BuyPerUser $BuyPerUser = null)
{
    require_once TO_ROOT .'/vendor/autoload.php';

	$Sdk = new \CapitalPayments\Sdk\Sdk(JFStudio\CapitalPayments::API_KEY,JFStudio\CapitalPayments::API_SECRET);

	$response = $Sdk->createInvoice([
		'amount' => $BuyPerUser->amount,
		'invoice_id' => $BuyPerUser->invoice_id,
		'unique_id' => $BuyPerUser->user_login_id,
		'whatsapp' => (new DummieTrading\UserContact)->getWhatsApp($BuyPerUser->user_login_id),
		'name' => (new DummieTrading\UserData)->getName($BuyPerUser->user_login_id),
		'email' => (new DummieTrading\UserLogin)->getEmail($BuyPerUser->user_login_id)
	]);

	if ($response['status'] == JFStudio\CapitalPayments::STATUS_200) {
		return $response['invoice'];
	} 

	return false;
}

function createTransactionAirtm(DummieTrading\BuyPerUser $BuyPerUser = null,DummieTrading\UserLogin $UserLogin = null)
{
	return [
		'amount' => $BuyPerUser->amount,
		'txn_id' => $BuyPerUser->invoice_id,
		'email' => JFStudio\Airtm::CUSTOMER_EMAIL,
		'unix_time' => time(),
		// 'checkout_url' => "http://localhost:8888/Wise/apps/airtm/process".$UserLogin->getPidQuery()."&txn_id={$BuyPerUser->invoice_id}"
		'checkout_url' => "https://www.wisedigital.co/apps/airtm/process".$UserLogin->getPidQuery()."&txn_id={$BuyPerUser->invoice_id}"
	];
}

function createTransactionFromEwallet(DummieTrading\BuyPerUser $BuyPerUser = null,DummieTrading\UserLogin $UserLogin = null)
{
	return [
		'amount' => $BuyPerUser->amount,
		'txn_id' => $BuyPerUser->invoice_id,
		'unix_time' => time(),
		'checkout_url' => "../../apps/ewallet/process?txn_id={$BuyPerUser->invoice_id}"
	];
}

function createTransactionFromCoinPayments(DummieTrading\BuyPerUser $BuyPerUser = null,DummieTrading\UserLogin $UserLogin = null)
{
	try {
		require_once TO_ROOT .'/vendor2/autoload.php';

		$CoinpaymentsAPI = new CoinpaymentsAPI(JFStudio\CoinPayments::PRIVATE_KEY, JFStudio\CoinPayments::PUBLIC_KEY, 'json');

		$req = [
			'amount' => ceil($BuyPerUser->amount),
			'currency1' => 'USD',
			'currency2' => $BuyPerUser->getCurrency(),
			'buyer_name' => $UserLogin->getNames(),
			'buyer_email' => $UserLogin->email,
			'item_name' => "Pago de orden {$BuyPerUser->invoice_id}",
			'custom' => $BuyPerUser->invoice_id,
			'item_number' => $BuyPerUser->invoice_id,
			'address' => '', // leave blank send to follow your settings on the Coin Settings page
			'ipn_url' => 'https://www.wisedigital.co/app/cronjob/ipn_coinpayments.php',
		];
						
		$result = $CoinpaymentsAPI->CreateCustomTransaction($req);

		if ($result['error'] == 'ok') {
	
			return $result['result'];
		} else {
			print 'Error: '.$result['error']."\n";
		}
	} catch (Exception $e) {
		echo 'Error: ' . $e->getMessage();
		exit();
	}	
}

function createTransactionPayPal(DummieTrading\BuyPerUser $BuyPerUser = null,DummieTrading\UserLogin $UserLogin = null)
{
	require_once TO_ROOT . "/system/vendor/autoload.php";

	$apiContext = new \PayPal\Rest\ApiContext(
	    new \PayPal\Auth\OAuthTokenCredential(
	        JFStudio\PayPal::CLIENT_ID,
	        JFStudio\PayPal::CLIENT_SECRET
	    )
	);

	$apiContext->setConfig(['mode' => JFStudio\PayPal::MODE]);

	$payer = new \PayPal\Api\Payer;
	
    $payer->setPaymentMethod('paypal');

    $total = $BuyPerUser->amount+$BuyPerUser->fee; 
	
    $amount = new \PayPal\Api\Amount;
	$amount->setTotal((string)$total);
	$amount->setCurrency('USD');
	$amount->setDetails($details);

	$transaction = new \PayPal\Api\Transaction;
	$transaction->setAmount($amount);
    $transaction->setInvoiceNumber($BuyPerUser->getId());
    
	$redirectUrls = new \PayPal\Api\RedirectUrls;
	$redirectUrls->setReturnUrl(JFStudio\PayPal::RETURN_URL)
	    ->setCancelUrl(JFStudio\PayPal::CANCEL_URL);

	$payment = new \PayPal\Api\Payment;
	$payment->setIntent('sale')
	    ->setPayer($payer)
	    ->setTransactions(array($transaction))
	    ->setRedirectUrls($redirectUrls);

	try {
	    $payment->create($apiContext);

		return [
			'checkout_url' => $payment->getApprovalLink(),
			'txn_id' => $payment->getId(),
			'fee' => $BuyPerUser->fee,
			'unix_time' => time(),
			'amount' => $BuyPerUser->amount,
			'total' => $total
		];
	} catch (\PayPal\Exception\PayPalConnectionException $ex) {
	    echo $ex->getData();
	}
}

function saveBuy($Cart = null,int $user_login_id = null)
{
	$BuyPerUser = new DummieTrading\BuyPerUser;
	$BuyPerUser->user_login_id = $user_login_id;
	$BuyPerUser->fee = $Cart->getVar('fee');
	$BuyPerUser->item = $Cart->getFormatedItems();
	$BuyPerUser->checkout_data = json_encode([]);
	$BuyPerUser->ipn_data = json_encode([]);
	$BuyPerUser->invoice_id = $Cart->_instance_id;
	$BuyPerUser->shipping = 0;
	$BuyPerUser->catalog_payment_method_id = $Cart->getVar('catalog_payment_method_id');
	$BuyPerUser->catalog_currency_id = $Cart->getVar('catalog_currency_id') ? $Cart->getVar('catalog_currency_id') : DummieTrading\CatalogCurrency::USD;
	$BuyPerUser->amount = $Cart->getTotalAmount(null,null,['fee'=>false]);
	$BuyPerUser->create_date = time();
	$BuyPerUser->status = DummieTrading\BuyPerUser::VALIDATED;
	$BuyPerUser->approved_date = time();
	$BuyPerUser->catalog_validation_method_id = DummieTrading\CatalogValidationMethod::ADMINISTRATOR;

	return $BuyPerUser->save() ? $BuyPerUser : false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 