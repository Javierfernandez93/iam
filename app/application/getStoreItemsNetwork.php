<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if(Jcart\Cart::hasInstances())
    {
        $Cart = Jcart\Cart::getInstance(Jcart\Cart::LAST_INSTANCE);
        $Cart->loadFromSession();

        if($catalog_package_type_id = (new DummieTrading\CatalogPackageType)->getIdbyPackageType($data['package_type']))
        {
            $filter = "AND package.catalog_package_type_id = '{$catalog_package_type_id}'";

            if($items = (new DummieTrading\Package)->getAll($filter))
            {
                $data['items'] = format($items,$Cart);
                $data['s'] = 1;
                $data['r'] = 'DATA_OK';
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_ITEMS';
            }
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_CATALOG_PACKAGE_TYPE_ID';
        }
    } else {
        $data['r'] = 'NOT_INSTANCES';
        $data['s'] = 0;
    }
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function format(array $items = null,Jcart\Cart $Cart = null) : array
{
    return array_map(function($item) use($Cart) {

        $item['product_ids'] = json_decode($item['product_ids'],true);
        $item['products'] = DummieTrading\Product::unformatProducts($item['product_ids']);
        $item['selected'] = $Cart->getPackage($item['package_id']) ? true : false;

        return $item;
    },$items);
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 