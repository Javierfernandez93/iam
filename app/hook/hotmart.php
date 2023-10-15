<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

// $data = '{"data":{"product":{"has_co_production":false,"name":"Produto test postback2","id":0,"ucode":"fb056612-bcc6-4217-9e6d-2a5d1110ac2f"},"commissions":[{"currency_value":"BRL","source":"MARKETPLACE","value":149.5},{"currency_value":"BRL","source":"PRODUCER","value":1350.5}],"purchase":{"offer":{"code":"test"},"order_date":1511783344000,"original_offer_price":{"currency_value":"BRL","value":1500},"price":{"currency_value":"BRL","value":1500},"checkout_country":{"iso":"BR","name":"Brasil"},"buyer_ip":"00.00.00.00","order_bump":{"parent_purchase_transaction":"HP02316330308193","is_order_bump":true},"payment":{"installments_number":12,"type":"CREDIT_CARD"},"approved_date":1511783346000,"full_price":{"currency_value":"BRL","value":1500},"transaction":"HP1121336654889","status":"APPROVED"},"affiliates":[{"affiliate_code":"Q58388177J","name":"Affiliate name"}],"producer":{"name":"Producer Test Name"},"subscription":{"subscriber":{"code":"I9OT62C3"},"plan":{"name":"plano de teste","id":123},"status":"ACTIVE"},"buyer":{"name":"Teste Comprador","checkout_phone":"5213317361196","email":"javier.fernandez.pa93@gmail.com"}},"id":"85134416-e4c9-47fc-9403-5474df991cd1","creation_date":1692383419021,"event":"PURCHASE_APPROVED","version":"2.0.0","gzip":true}';
// $data = json_decode($data,true);

DummieTrading\IpnHotmart::add($data);


if($data['event'] ?? false == DummieTrading\Hotmart::PURCHASE_APPROVED)
{
//   if(DummieTrading\BuyPerUser::isValidHotMartProductId($data['data']['product']['id']))
  if(true)
  {
    if(!(new DummieTrading\BuyPerUser)->existBuyByInvoiceId($data['data']['purchase']['transaction']))
    {
        $UserLogin = new DummieTrading\UserLogin(false,false,false);
        $password = DummieTrading\UserLogin::generateRandomKey();

        // $catalog_package_id = DummieTrading\Code::getHotmartPackageId($data['data']['purchase']['price']['value']);
        // $package_name = (new DummieTrading\CatalogPackage)->getName($catalog_package_id);

        $names = $data['data']['buyer']['name'] ? $data['data']['buyer']['name'] : 'DummieTrader';

        if($user = $UserLogin->getUserDataByEmail($data['data']['buyer']['email']))
        {
            $user['phone'] = (new DummieTrading\UserContact)->getWhatsApp($user['user_login_id']);
            $user['sendPassword'] = false;
        } else if ($user = doSignup([
                'email' => $data['data']['buyer']['email'],
                'phone' => $data['data']['buyer']['checkout_phone'],
                'names' => $names,
                'utm' => '15trial',
                'password' => $password
            ])) {  

            $user['sendPassword'] = true;
        }
        
        $tokenUrl = DummieTrading\UserLogin::generateLoginToken([
            'email' => $user['email'],
            'password' => sha1($user['password'])
        ]);

        $user['company_id'] = $UserLogin->getUserIdByEmail($user['email']);

        $tokenUrl = (new DummieTrading\ShortUrl)->getShortUrl($user['company_id'] ?? 1,$tokenUrl,'Login-DT',"Ingresar".rand(1000,5000));

        sendEmail([
            'email' => $user['email'],
            'names' => $user['names'],
            'password' => $user['password'],
            'tokenUrl' => $tokenUrl
        ]);

        if(sendWhatsApp($user['phone'],$tokenUrl))
        {
            $data['whatsapp_sent'] = true;
        }

        if(sendWhatsAppNewSign('5213111055643',$user['names']))
        {
            $data['whatsapp_sent_to_main_user'] = true;
        }
        
        if(sendWhatsAppNewSign('5213317361196',$user['names']))
        {
            $data['whatsapp_sent_to_main_user'] = true;
        }

        $Curl = new JFStudio\Curl;
          
        $url = HCStudio\Connection::getMainPath()."/app/application/saveBuyQuick.php";

        $Curl->get($url,[
            'user' => HCStudio\Util::USERNAME,
            'password' => HCStudio\Util::PASSWORD,
            'user_login_id' => $user['company_id'],
            'checkout_data' => [
                'site_name' => HCStudio\Connection::proyect_name,
                'buy_per_user_id' => 0
            ],
            'country_id' => 159,
            'package_id' => DummieTrading\Package::TRIAL
        ]);

        if($response = $Curl->getResponse(true))
        {
            return $response['s'] == 1;
        }
    }
  }
}

function doSignup(array $data = null) : array|bool
{
    $url = HCStudio\Connection::getMainPath()."/app/application/do_signup.php";

    $Curl = new JFStudio\Curl;
    $Curl->post($url, [
        'names' => $data['names'],
        'password' => $data['password'],
        'utm' => $data['utm'],
        'email' => $data['email'],
        'phone' => $data['phone']
    ]);

    $Curl->getResponse(true);

    return $data;
}

function sendEmail(array $data = null) : bool
{
    if(isset($data) === true)
    {
        require_once TO_ROOT . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $Layout = JFStudio\Layout::getInstance();
            $Layout->init("",'welcome-hotmart',"mail-new",TO_ROOT.'/apps/applications/',TO_ROOT.'/');

            $Layout->setScriptPath(TO_ROOT . '/apps/admin/src/');
    		$Layout->setScript(['']);

            $CatalogMailController = DummieTrading\CatalogMailController::init(1);

            $Layout->setVar($data);

            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF; // PHPMailer\PHPMailer\SMTP::DEBUG_SERVER
            $mail->isSMTP(); 
            // $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Host = $CatalogMailController->host;
            $mail->SMTPAuth = true; 
            $mail->Username = $CatalogMailController->mail;
            $mail->Password =  $CatalogMailController->password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = $CatalogMailController->port; 

            //Recipients
            $mail->setFrom($CatalogMailController->mail, $CatalogMailController->sender);
            $mail->addAddress($data['email'], $data['names']);     

            //Content
            $mail->isHTML(true);                                  
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Bienvenido a DummieTrading';
            $mail->Body = $Layout->getHtml();
            $mail->AltBody = strip_tags($Layout->getHtml());

            return $mail->send();
        } catch (Exception $e) {
            
        }
    }

    return false;
}

function sendWhatsApp(string $phone = null,string $tokenUrl = null) : bool|array
{
    return DummieTrading\ApiWhatsApp::sendWhatsAppMessage([
        'message' => DummieTrading\ApiWhatsAppMessages::getWelcomeTrialMessage(),
        'image' => null,
        'contact' => [
            "phone" => $phone,
            "extra" => $tokenUrl,
            "name" => 'Usuario'
        ]
    ]);
}

function sendWhatsAppNewSign(string $phone = null,string $names = null) : bool|array
{
    return DummieTrading\ApiWhatsApp::sendWhatsAppMessage([
        'message' => "¡Hola! registro nuevo de {$names}, activó su trial",
        'image' => null,
        'contact' => [
            "phone" => $phone,
            "name" => 'Javier'
        ]
    ]);
}

echo json_encode($data);