<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

if($data["email"])
{
    $UserLogin = new DummieTrading\UserLogin;

    if($UserLogin->isUniqueMail($data['email']) === false)
    {
        if($token = getToken($data['email']))
        {
            if(sendEmail($data['email'],$token))
            {
                $data["s"] = 1;
                $data["r"] = "SEND_OK";
            } else {
                $data["s"] = 0;
                $data["r"] = "NOT_SENT";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_TOKEN";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_FOUND_MAIL";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function getToken(string $email = null) : string 
{
    $Token = new HCStudio\Token;
    $token = $Token->getToken([
        'time' => time(),
        'email' => $email
    ]);

    return "{$token['token']}[{$token['key']}]";
}

function sendEmail(string $email = null,string $token = null) : bool
{
    if(isset($email) === true)
    {
        require_once TO_ROOT . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $Layout = JFStudio\Layout::getInstance();
            $Layout->init("","recoverPassword","mail",TO_ROOT.'/apps/applications/',TO_ROOT.'/');

            $Layout->setScriptPath(TO_ROOT . '/apps/admin/src/');
    		$Layout->setScript(['']);

            $CatalogMailController = DummieTrading\CatalogMailController::init(1);        

            $Layout->setVar([
                "email" => $email,
                "token" => $token
            ]);

            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF; // PHPMailer\PHPMailer\SMTP::DEBUG_SERVER
            $mail->isSMTP(); 
            // $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Host = $CatalogMailController->host;
            $mail->SMTPAuth = true; 
            $mail->Username = $CatalogMailController->mail;
            $mail->Password =  $CatalogMailController->password;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port = $CatalogMailController->port; 

            // d($CatalogMailController);

            //Recipients
            $mail->setFrom($CatalogMailController->mail, $CatalogMailController->sender);
            $mail->addAddress($email, 'Usuario de DummieTrading');     

            //Content
            $mail->isHTML(true);                                
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "Recuperar contraseña";
            $mail->Body = $Layout->getHtml();
            $mail->AltBody = strip_tags($Layout->getHtml());

            return $mail->send();
        } catch (Exception $e) {
            
        }
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 