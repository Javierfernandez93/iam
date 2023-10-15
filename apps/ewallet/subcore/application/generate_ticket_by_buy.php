<?php define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$Token = new HCStudio\Token;
$UserLogin = new DummieTrading\UserLogin;

if($Token->checkToken(['token'=>$data['token'],'key'=>$data['key']]) || $UserLogin->logged === true)
{
	if($UserLogin->logged === false)
	{
		$UserLoginTemp = new DummieTrading\UserLogin;
		$UserLoginTemp->_setPid(['token'=>$data['token'],'key'=>$data['key']]);
		$UserLogin = new DummieTrading\UserLogin;
	}

	if($UserLogin->logged === true)
	{
		if($data['buy_per_user_login_id'])
		{
			$BuyPerUser = new DummieTrading\BuyPerUser;

			if($BuyPerUser->loadWhere('buy_per_user_login_id = ?',[$data['buy_per_user_login_id']]))
			{
				# Payment Methods
				if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$PAYPAL){
					$data["payment_html"] = getPayPalForm($UserLogin,[
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'reissue' => $BuyPerUser->reissue,
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'shipping_address' => $BuyPerUser->shipping_address,
					],$BuyPerUser);
				} else if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$PAYU){
					$data["payment_html"] = getPayU($UserLogin,[
						'package_id' => $BuyPerUser->package_id,
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'shipping_address' => $BuyPerUser->shipping_address,
					]);
				} else if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$DEPOSIT){
					$data["payment_html"] = getDepositForm($UserLogin,[
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'shipping_address' => $BuyPerUser->shipping_address,
						'purchases' => $data['purchases'],
					]);
				} else if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$LOCALBITCOINS){
					$data["payment_html"] = getLocalBitcoin($UserLogin,[
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'shipping_address' => $BuyPerUser->shipping_address,
					]);
				} else if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$CAPITALIKA){
					$data["payment_html"] = getCapitalika($UserLogin,[
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'shipping_address' => $BuyPerUser->shipping_address,
					]);
				} else if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$BITSO){
					$data["payment_html"] = getBisto($UserLogin,[
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'shipping_address' => $BuyPerUser->shipping_address,
					]);
				} else if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$AIRTM){
					$data["payment_html"] = getAirTm($UserLogin,[
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'shipping_address' => $BuyPerUser->shipping_address,
					]);
				} else if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$OXXO){
					$data["payment_html"] = getOxxo($UserLogin,[
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'date_create' => $BuyPerUser->date_create,
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'shipping_address' => $BuyPerUser->shipping_address,
					]);
				} else if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$EWALLET){
					$data["payment_html"] = getEwallet($UserLogin,[
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'date_create' => $BuyPerUser->date_create,
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'payment_reference' => $BuyPerUser->payment_reference,
						'shipping_address' => $BuyPerUser->shipping_address,
					]);
				} else if($BuyPerUser->payment_method == Jcart\ModelPaymentMethod::$STRIPE){
					$data["payment_html"] = getStripe($UserLogin,[
						'buy_per_user_login_id' => $BuyPerUser->getId(),
						'date_create' => $BuyPerUser->date_create,
						'shipping' => $BuyPerUser->shipping,
						'amount' => $BuyPerUser->ammount,
						'payment_reference' => $BuyPerUser->payment_reference,
						'shipping_address' => $BuyPerUser->shipping_address,
					]);
				}
			} else {
				$data['s'] = 0;
				$data['r'] = 'NOT_BUY_PER_USER_LOGIN';
			}
		} else {
			$data['s'] = 0;
			$data['r'] = 'NOT_BUY_PER_USER_LOGIN_ID';
		}
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_USER_LOGIN';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'NOT_TOKEN';
}

function getCapitalika($UserLogin = null,$data = null)
{
	if($UserLogin->isUserLoginFromOutboard($data['shipping_address']) === false) {
		// @todo change for catalog_currency_id value
		$currency_code = "MXN";
	} else {
		$data['shipping'] = DummieTrading\Currency::getAmountOnUSD($data['shipping']);
		$data['amount'] = DummieTrading\Currency::getAmountOnUSD($data['amount']);
		// @todo change for catalog_currency_id value
		$currency_code = "USD";
	}

	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","capitalika-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'Api' => BTC\Api::getInstance(),
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'currency_code' => $currency_code,
		'shipping' => $data['shipping'] + $data['reissue'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

function getLocalBitcoin($UserLogin = null,$data = null)
{
	// if($UserLogin->isUserLoginFromOutboard($data['shipping_address']) === false) {
	if(false) {
		// @todo change for catalog_currency_id value
		$currency_code = "MXN";
	} else {
		$data['shipping'] = DummieTrading\Currency::getAmountOnUSD($data['shipping']);
		$data['amount'] = DummieTrading\Currency::getAmountOnUSD($data['amount']);
		// @todo change for catalog_currency_id value
		$currency_code = "USD";
	}

	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","localbitcoins-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'Api' => BTC\Api::getInstance(),
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'currency_code' => $currency_code,
		'shipping' => $data['shipping'] + $data['reissue'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

function getBisto($UserLogin = null,$data = null)
{
	// if($UserLogin->isUserLoginFromOutboard($data['shipping_address']) === false) {
	if(false) {
		// @todo change for catalog_currency_id value
		$currency_code = "MXN";
	} else {
		$data['shipping'] = DummieTrading\Currency::getAmountOnUSD($data['shipping']);
		$data['amount'] = DummieTrading\Currency::getAmountOnUSD($data['amount']);
		// @todo change for catalog_currency_id value
		$currency_code = "USD";
	}

	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","bitso-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'Api' => BTC\Api::getInstance(),
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'currency_code' => $currency_code,
		'shipping' => $data['shipping'] + $data['reissue'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

function getAirTm($UserLogin = null,$data = null)
{
	if($UserLogin->isUserLoginFromOutboard($data['shipping_address']) === false) {
		// @todo change for catalog_currency_id value
		$currency_code = "MXN";
	} else {
		$data['shipping'] = DummieTrading\Currency::getAmountOnUSD($data['shipping']);
		$data['amount'] = DummieTrading\Currency::getAmountOnUSD($data['amount']);
		// @todo change for catalog_currency_id value
		$currency_code = "USD";
	}

	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","airtm-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'currency_code' => $currency_code,
		'shipping' => $data['shipping'] + $data['reissue'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

function getPayPalForm($UserLogin = null,$data = null,$BuyPerUser = null)
{

	$data['total_amount'] = $data['shipping'] + $data['amount'] + $data['reissue'];
	
	// if($UserLogin->isUserLoginFromOutboard($data['shipping_address']) === false) {
	if(true) {
		// @todo change for catalog_currency_id value
		$currency_code = "MXN";
	} else {
		$data['shipping'] = DummieTrading\Currency::getAmountOnUSD($data['shipping']);
		$data['amount'] = DummieTrading\Currency::getAmountOnUSD($data['amount']);
		$data['total_amount'] = DummieTrading\Currency::getAmountOnUSD($data['total_amount']);
		// @todo change for catalog_currency_id value
		$currency_code = "USD";
	}

	require_once TO_ROOT . "/system/vendor/autoload.php";

	$apiContext = new \PayPal\Rest\ApiContext(
	    new \PayPal\Auth\OAuthTokenCredential(
	        JFStudio\PayPal::$CLIENT_ID,
	        JFStudio\PayPal::$CLIENT_SECRET
	    )
	);

	$apiContext->setConfig(
		[
			'mode' => 'live'
		]
	);

	$payer = new \PayPal\Api\Payer;

	$payer->setPaymentMethod('paypal');
	$amount = new \PayPal\Api\Amount();

	$amount->setTotal((string)$data['total_amount']);
	$amount->setCurrency($currency_code);
	$amount->setDetails($details);

	$transaction = new \PayPal\Api\Transaction();
	$transaction->setAmount($amount);
	$redirectUrls = new \PayPal\Api\RedirectUrls();
	$redirectUrls->setReturnUrl("http://www.sumateahora.com/apps/paypal/index.php")
	    ->setCancelUrl("https://www.sumateahora.com/apps/backoffice");

	$payment = new \PayPal\Api\Payment();

	$payment->setIntent('sale')
	    ->setPayer($payer)
	    ->setTransactions(array($transaction))
	    ->setRedirectUrls($redirectUrls);

	try {
	    $payment->create($apiContext);
	} catch (\PayPal\Exception\PayPalConnectionException $ex) {
	    echo $ex->getData();
	}

	$BuyPerUser->payment_reference = $payment->getId();
	print_r($BuyPerUser);
	die;
	$BuyPerUser->save();

	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","paypal-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'payment_id' => $payment->getId(),
		'link' => $payment->getApprovalLink(),
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['total_amount'],
		'currency_code' => $currency_code,
		'shipping' => $data['shipping'] + $data['reissue'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

function getDepositForm($UserLogin = null,$data = null)
{	
	if(count($data['purchases'])>1){
		$data['amount']=0;
		foreach($data['purchases'] as  $key => $total){
			$data['amount']+=$total['total_to_pay'];
		}
	}	 
	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","deposit-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'currency_code' => "MXN",
		'shipping' => $data['shipping'] + $data['reissue'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

function getOxxo($UserLogin = null,$data = null)
{								
	$unix = strtotime('+3 day',strtotime($data['date_create']));
	$expiration_date = explode(" ",date('Y-m-d H:i:s', $unix));

	$oxxo_data = array(
		'shipping_number' => $data['buy_per_user_login_id'],
		'expiration_date' => $expiration_date[0],
		'ammount' => $data['shipping'] + $data['amount'] + $data['reissue'],
	);

	$oxxo_data = [
	    'code_id' => (new DummieTrading\Oxxo(46,1))->createCodeId($oxxo_data),
	    'email' => $UserLogin->mail,
		'file_name' => 'Bill_From_'.$UserLogin->company_id,
		'buy_date' => $data['date_create'],
		'shipping_number' => $data['buy_per_user_login_id'],
		'expiration_date' => $expiration_date[0],
		'ammount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'name' => $UserLogin->getFullNames(),
	];

	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","oxxo-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");

	$Layout->setVar([
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'currency_code' => "MXN",
		'shipping' => $data['shipping'] + $data['reissue'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'oxxo_data' => $oxxo_data,
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

function getPayU($UserLogin = null,$data = null)
{	
	if($UserLogin->isUserLoginFromOutboard($data['shipping_address']) === false) {
		// @todo change for catalog_currency_id value
		$currency_code = "MXN";
	} else {
		$data['shipping'] = DummieTrading\Currency::getAmountOnUSD($data['shipping']);
		$data['amount'] = DummieTrading\Currency::getAmountOnUSD($data['amount']);
		// @todo change for catalog_currency_id value
		$currency_code = "USD";
	}

	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","payu-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'currency_code' => $currency_code,
		'shipping' => $data['shipping'] + $data['reissue'],
		'package_id' => $data['package_id'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

function getEwallet($UserLogin = null,$data = null)
{	
	if($UserLogin->isUserLoginFromOutboard($data['shipping_address']) === false) {
		// @todo change for catalog_currency_id value
		$currency_code = "MXN";
	} else {
		$data['shipping'] = DummieTrading\Currency::getAmountOnUSD($data['shipping']);
		$data['amount'] = DummieTrading\Currency::getAmountOnUSD($data['amount']);
		// @todo change for catalog_currency_id value
		$currency_code = "USD";
	}

	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","ewallet-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'currency_code' => $currency_code,
		'shipping' => $data['shipping'] + $data['reissue'],
		'payment_reference' => $data['payment_reference'],
		'package_id' => $data['package_id'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

function getStripe($UserLogin = null,$data = null)
{	
	if($UserLogin->isUserLoginFromOutboard($data['shipping_address']) === false) {
		// @todo change for catalog_currency_id value
		$currency_code = "MXN";
	} else {
		$data['shipping'] = DummieTrading\Currency::getAmountOnUSD($data['shipping']);
		$data['amount'] = DummieTrading\Currency::getAmountOnUSD($data['amount']);
		// @todo change for catalog_currency_id value
		$currency_code = "USD";
	}

	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","stripe-form","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'company_id' => $UserLogin->company_id,
		'total_amount' => $data['shipping'] + $data['amount'] + $data['reissue'],
		'currency_code' => $currency_code,
		'shipping' => $data['shipping'] + $data['reissue'],
		'payment_reference' => $data['payment_reference'],
		'package_id' => $data['package_id'],
		'buy_per_user_login_id' => $data['buy_per_user_login_id'],
		'amount' => $data['amount']
	]);

	return $Layout->getHtml();
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 