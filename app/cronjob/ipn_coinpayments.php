<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$merchant_id = JFStudio\CoinPayments::MERCHANT_ID;
$secret = JFStudio\CoinPayments::IPN_SECRET;

if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
  die("No HMAC signature sent");
}

$merchant = isset($_POST['merchant']) ? $_POST['merchant']:'';

if (empty($merchant)) {
  die("No Merchant ID passed");
}

if ($merchant != $merchant_id) {
  die("Invalid Merchant ID");
}

$request = file_get_contents('php://input');

if ($request === FALSE || empty($request)) {
  die("Error reading POST data");
}

$hmac = hash_hmac("sha512", $request, $secret);

if ($hmac != $_SERVER['HTTP_HMAC']) {
  die("HMAC signature does not match");
}

parse_str($request, $output);

processIPN($output);

function processIPN(array $data = null)
{
    if($data['status'] == JFStudio\CoinPayments::COMPLETE)
    {
        validateBuy($data);
    } else if($data['status'] == JFStudio\CoinPayments::EXPIRED) {
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
        'invoice_id' => $data['item_number'],
        'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::COINPAYMENTS_IPN,
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
        'invoice_id' => $data['item_number'],
        'status' => DummieTrading\BuyPerUser::EXPIRED,
        'catalog_validation_method_id' => DummieTrading\CatalogValidationMethod::COINPAYMENTS_IPN,
        'ipn_data' => json_encode($data),
    ]);

    return $Curl->getResponse(true);
}