<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['user']['email'])
    {
        $UserLogin = new DummieTrading\UserLogin;

        if($UserLogin->isUniqueMail($data['user']['email']))
        {
            if($user_login_id = $UserLogin->doSignup($data['user']))
            {
                if(sendEmailUser($data['user']['email'],$data['user']['names'],$data['user']['password']))
                {
                    $data['email_sent'] = true;
                }

                if(sendPushUser($user_login_id,$data['user']['names']))
                {
                    $data['push_sent'] = true;
                }

                if(sendEmailSponsor($data['user']['referral']['user_login_id'],$data['user']['names']))
                {
                    $data['email_sponsor_sent'] = true;
                }

                if(sendPushSponsor($data['user']['referral']['user_login_id'],$data['user']['names']))
                {
                    $data['push_sponsor_sent'] = true;
                }
                
                $data['s'] = 1;
                $data['r'] = 'LOGGED_OK';
            } else {
                $data['s'] = 0;
                $data['r'] = 'ERROR_ON_SIGNUP';
            }
        } else {
            $data['s'] = 0;
            $data['r'] = 'MAIL_ALREADY_EXISTS';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_EMAIL';
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

function sendPush(string $user_login_id = null,string $message = null,int $catalog_notification_id = null) : bool
{
    return DummieTrading\NotificationPerUser::push($user_login_id,$message,$catalog_notification_id,"");
}

function sendPushUser(string $user_login_id = null,string $names = null) : bool
{
    return sendPush($user_login_id,"Bienvenido a bordo {$names}, estamos felices de que te hayas registrado en DummieTrading",DummieTrading\CatalogNotification::ACCOUNT);
}

function sendPushSponsor(string $user_login_id = null,string $names = null) : bool
{
    return sendPush($user_login_id,"Felicitaciones, {$names} se unió a tu grupo de referidos",DummieTrading\CatalogNotification::REFERRAL);
}

function sendEmailSponsor(string $user_login_id = null,string $names = null) : bool
{
    if(isset($user_login_id,$names) === true)
    {
        $UserLogin = new DummieTrading\UserLogin(false,false);

        if($email = $UserLogin->getEmail($user_login_id))
        {
            return sendEmail([
                'email' => $email,
                'names' => $names,
                'subject' => 'Nuevo afiliado en DummieTrading',
                'view' => 'partnerWelcome',
            ]);
        }
    }

    return false;
}

function sendEmailUser(string $email = null,string $names = null,string $password = null) : bool
{
    if(isset($email,$names) === true)
    {
        return sendEmail([
            'email' => $email,
            'names' => $names,
            'password' => $password,
            'subject' => 'Bienvenido a bordo',
            'view' => 'welcome',
        ]);
    }

    return false;
}


function sendEmail(array $data,string $subject = null,string $view = null) : bool
{
    if(isset($data) === true)
    {
        require_once TO_ROOT . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $Layout = JFStudio\Layout::getInstance();
            $Layout->init("",$data['view'],"mail-new",TO_ROOT.'/apps/applications/',TO_ROOT.'/');

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
            $mail->Subject = $data['subject'];
            $mail->Body = $Layout->getHtml();
            $mail->AltBody = strip_tags($Layout->getHtml());

            return $mail->send();
        } catch (Exception $e) {
            
        }
    }

    return false;
}


echo json_encode(HCStudio\Util::compressDataForPhone($data)); 