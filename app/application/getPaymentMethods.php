<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
	$filter = null;
	
	if($data['filter_wallet'] ?? null)
	{
		$filter = " AND catalog_payment_method.catalog_payment_method_id != '".DummieTrading\CatalogPaymentMethod::EWALLET."'";
	}

	if($catalogPaymentMethods = (new DummieTrading\CatalogPaymentMethod)->getAll($filter))
	{
        $data['catalogPaymentMethods'] = format($catalogPaymentMethods);
        $data['s'] = 1;
        $data['r'] = 'DATA_OK';
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_CATALOG_PAYMENT_METHODS';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function format(array $catalogPaymentMethods = null) : array 
{
	$CatalogCurrency = new DummieTrading\CatalogCurrency;

	return array_map(function($catalogPaymentMethod) use($CatalogCurrency) {
		if($catalogPaymentMethod['catalog_currency_ids'])
		{
			$catalogPaymentMethod['catalog_currency_ids'] = json_decode($catalogPaymentMethod['catalog_currency_ids'],true);

			$catalogPaymentMethod['currencies'] = $CatalogCurrency->getIn(implode(',',$catalogPaymentMethod['catalog_currency_ids']));
		}

		return $catalogPaymentMethod;
	},$catalogPaymentMethods);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 