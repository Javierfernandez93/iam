<?php define("TO_ROOT", "../../");

require_once TO_ROOT . "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if(isset($data['buy_per_user_id']))
	{
        $BuyPerUser = new DummieTrading\BuyPerUser;

        if($BuyPerUser->loadWhere("buy_per_user_id = ?", $data['buy_per_user_id']))
        {
            if($email = $UserSupport->getMailById($BuyPerUser->user_login_id))
            {
                if(DummieTrading\BuyPerUser::setAsSend($data['buy_per_user_id']))
                {
                    if(sendEmail($email,$BuyPerUser->invoice_id))
                    {
                        $data['mail_sent'] = true; 
                    }
        
                    $data['s'] = 1;
                    $data['r'] = 'SAVE_OK';	
                } else {
                    $data['s'] = 0;
                    $data['r'] = 'NOT_PEDNING';
                } 		
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_EMAIL';
            } 
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_BUY_PER_USER';
        } 		
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_BUY_PER_USER_ID';
	}
} else {
	$data['s'] = 0;
	$data['r'] = 'INVALID_CREDENTIALS';
}

function sendEmail(string $email = null,string $invoice_id = null) : bool
{
    if(isset($email,$invoice_id) === true)
    {
        require_once TO_ROOT . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $Layout = JFStudio\Layout::getInstance();
            $Layout->init("",'package-sent',"mail-new",TO_ROOT.'/apps/applications/',TO_ROOT.'/');

            $Layout->setScriptPath(TO_ROOT . '/apps/admin/src/');
    		$Layout->setScript(['']);

            $CatalogMailController = DummieTrading\CatalogMailController::init(1);

            $Layout->setVar([
                "invoice_id" => $invoice_id,
                "email" => $email
            ]);

            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF; // PHPMailer\PHPMailer\SMTP::DEBUG_SERVER
            $mail->isSMTP(); 

            $mail->Host = $CatalogMailController->host;
            $mail->SMTPAuth = true; 
            $mail->Username = $CatalogMailController->mail;
            $mail->Password =  $CatalogMailController->password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = $CatalogMailController->port; 

            //Recipients
            $mail->setFrom($CatalogMailController->mail, $CatalogMailController->sender);
            $mail->addAddress($email, 'Usuario');     

            //Content
            $mail->isHTML(true);                                  
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Enviamos tus productos';
            $mail->Body = $Layout->getHtml();
            $mail->AltBody = strip_tags($Layout->getHtml());

            return $mail->send();
        } catch (Exception $e) {
            
        }
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 