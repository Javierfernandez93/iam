<?php define('TO_ROOT', '../../');

require_once TO_ROOT . 'system/core.php'; 

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{	
    $data['catalog_courses'] = (new DummieTrading\CatalogCourse)->getAll();
    $data['catalog_currencies'] = format((new DummieTrading\CatalogCurrency)->getAll());
    $data['r'] = 'DATA_OK';
    $data['s'] = 1;
} else {
	$data['r'] = 'NOT_SESSION';
	$data['s'] = 0;
}

function format(array $catalog_currencies = null) : array 
{
    $Country = new World\Country;

    return array_map(function($catalog_currency) use ($Country){
        $catalog_currency['code'] = $Country->getCountryCode($catalog_currency['country_id'] ?? false);
        return $catalog_currency;
    },$catalog_currencies);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 