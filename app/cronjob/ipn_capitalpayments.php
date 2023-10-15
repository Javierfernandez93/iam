<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$api_key = JFStudio\CapitalPayments::API_KEY;
$ipn_secret = JFStudio\CapitalPayments::IPN_SECRET;

if (!isset($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_USER'])) {
    echo json_encode(['error'=>"No api_key authentication sent"]);die;
}

if (!isset($_SERVER['PHP_AUTH_PW']) || empty($_SERVER['PHP_AUTH_PW'])) {
    echo json_encode(['error'=>"No ipn_secret authentication sent"]);die;
}

$request = file_get_contents('php://input');

if ($request === FALSE || empty($request)) {
  die("Error reading POST data");
}

parse_str($request, $output);

processIPN($output);

function processIPN(array $data = null)
{
    if($data['status'] == JFStudio\CapitalPayments::ORDER_PAID)
    {
        validateBuy($data);
    } else if($data['status'] == JFStudio\CapitalPayments::ORDER_CANCELED) {
        deleteBuy($data);
    }

    saveIPN($data);
}

function saveIPN(array $data = null)
{
    
    $Ipn = new DummieTrading\Ipn;
    $Ipn->data = json_encode($data);
    $Ipn->create_date = time();
    $Ipn->status = 1;
    $Ipn->save();
}

function validateBuy(array $data = null)
{
    $url = HCStudio\Connection::getMainPath()."/app/application/validateBuy.php";
    
    $Curl = new JFStudio\Curl;
    $Curl->post($url, [
        'user' => HCStudio\Util::USERNAME,
        'password' => HCStudio\Util::PASSWORD,
        'invoice_id' => $data['invoice_id'],
        'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::CAPITALPAYMENTS_IPN,
        'ipn_data' => json_encode($data),
    ]);
    
    return $Curl->getResponse(true);
}

function deleteBuy(array $data = null)
{
    $url = HCStudio\Connection::getMainPath()."/app/application/deleteBuy.php";

    $Curl = new JFStudio\Curl;
    $Curl->post($url, [
        'user' => HCStudio\Util::USERNAME,
        'password' => HCStudio\Util::PASSWORD,
        'invoice_id' => $data['order_id'],
        'status' => DummieTrading\BuyPerUser::EXPIRED,
        'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::CAPITALPAYMENTS_IPN,
        'ipn_data' => json_encode($data),
    ]);

    return $Curl->getResponse(true);
}