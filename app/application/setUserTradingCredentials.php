<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['user_login_id'])
    {
        if($data['login'])
        {
            if($data['password'])
            {
                if($data['server'])
                {
                    if($data['trader'])
                    {
                        if(DummieTrading\UserTradingAccount::add($data))
                        {
                            if(filter_var($data['sendEmail'], FILTER_VALIDATE_BOOLEAN))
                            {
                                if(sendEmail($data))
                                {
                                    $data["mail_sent"] = true;
                                }
                            }

                            $data["s"] = 1;
                            $data["r"] = "DATA_OK";
                        } else {
                            $data["s"] = 0;
                            $data["r"] = "NOT_SAVE";
                        }
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_TRADER";
                    }
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_SERVER";
                }
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_PASSWORD";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_LOGIN";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_USER_LOGIN_ID";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function sendEmail(array $data = null) : bool
{
    if(isset($data) === true)
    {
        require_once TO_ROOT . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $Layout = JFStudio\Layout::getInstance();
            $Layout->init("",'realAccount',"mail-new",TO_ROOT.'/apps/applications/',TO_ROOT.'/');

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
            $mail->addAddress($data['emailUser'], trim($data['names']));     

            //Content
            $mail->isHTML(true);                                  
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Bienvenido al mundo del trading';
            $mail->Body = $Layout->getHtml();
            $mail->AltBody = strip_tags($Layout->getHtml());

            return $mail->send();
        } catch (Exception $e) {
            
        }
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 