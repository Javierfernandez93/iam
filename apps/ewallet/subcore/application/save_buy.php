<?php use Umbrella\Product;

define("TO_ROOT", "../../../..");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new CronosUser\UserLogin;

if($UserLogin->logged === true)
{
	$Cart = new Jcart\Cart;

	$date = date('Y-m-d H:i:s');

	$BuyPerUser->cargarArray([
		'user_login_id' => $UserLogin->company_id,
		'buy_type' => "qualification",
		'goal' => '9',
		'payment_method' => $Cart->getPaymentMethod(),
		'shipping_address' => "1",
		'products' => $Cart->getFormatedItems(),
		'ammount' => $Cart->getAmount(),
		'points' => "0",
		'shipping' => $Cart->getShippingAmount(),
		'reissue' => "0",
		'shipping_company' => $Cart->getShippingMethod(),
		'charges' => "0",
		'package_id' => "", // PackageID
		'date_create' => $date,
		'virtualPoints' => 1,
		'virtualDiscount' => 0,
		'buy_from' => "" // PackageID,
	]);

	if($BuyPerUser->save())
	{
		//si el metodo de pago es paypal o OxxO actualizo Fechas
		if($Cart->payment_method == Jcart\ModelPaymentMethod::$PAYPAL || $Cart->payment_method == Jcart\ModelPaymentMethod::$OXXO || $Cart->payment_method == Jcart\ModelPaymentMethod::$EWALLET || $Cart->payment_method == Jcart\ModelPaymentMethod::$STRIPE)
		{
			$prefix = $Cart->getPaymentMethodNames($BuyPerUser->payment_method);

			$BuyPerUser->payment_reference = $prefix.'-'.$BuyPerUser->getId().'-'.($Cart->total_ammount+$reissue);

			$BuyPerUser->payment_date = $date;
			$BuyPerUser->date_reg = $date;
			$BuyPerUser->save();
		}

		$data['html'] = getHtmlPaymentDone($purchases,$Cart->payment_method);
		$data['payment_method'] = $Cart->payment_method;

		//limpio la Variable $BuyPerUser
		// unset($BuyPerUser);
	}		

	$url = HCStudio\Connection::$protocol."://www.".HCStudio\Connection::$proyect_url."/apps/ewallet/subcore/application/generate_ticket_by_buy.php";

	if($token = $UserLogin->getPid())
	{
		$curl = new JFStudio\Curl;
		$curl->post($url, array(
		    'token' => $token['token'],
		    'key' => $token['key'],
		    'buy_per_user_login_id' => $BuyPerUser->getId(),
			'purchases' => $purchases
		));

		$data["payment_html"] = $curl->getResponse(true)['payment_html'];
	}

	$data['s'] = 1;
	$data['r'] = 'SAVE_OK';

} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function getHtmlPaymentDone($purchases = false,$payment_method = false)
{
	$Layout = JFStudio\Layout::getInstance();
	$Layout->init("","payment-done","clean",TO_ROOT.'/apps/applications/',TO_ROOT."/");
	$Layout->setVar([
		'purchases' => $purchases,
		'payment_method' => $payment_method
	]);

	return $Layout->getHtml();
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 