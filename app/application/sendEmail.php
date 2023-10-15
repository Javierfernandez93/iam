<?php define('TO_ROOT', '../../');

require_once TO_ROOT. '/system/core.php';

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if($data['email'])
    {
        if($data['campaign_email_id'])
        {
            $CampaignEmail = new DummieTrading\CampaignEmail;
            $CampaignEmail->connection()->stmtQuery("SET NAMES utf8mb4");
            
            if($campaign = $CampaignEmail->get($data['campaign_email_id']))
            {
                $names = 'Socio DummieTrading';

                if($company_id = (new DummieTrading\UserLogin)->getCompanyIdByMail($data['email']))
                {
                    $names = (new DummieTrading\UserData)->getNames($company_id);
                }

                if(sendEmail($data['email'],$campaign['title'],$campaign['content'],$names))
                {
                    if(DummieTrading\EmailPerCampaign::addEmailRecord($company_id,$data['email'],$campaign['campaign_email_id']))
                    {
                        $data['s'] = 1;
                        $data['r'] = 'DATA_OK';
                    }  else {
                        $data['s'] = 0;
                        $data['r'] = 'NOT_SENT';
                    }
                } else {
                    $data['s'] = 0;
                    $data['r'] = 'NOT_EMAIL';
                }
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_CAMPAIGN';
            }
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_CAMPAIGN_EMAIL_ID';
        }
    } else {
        $data['s'] = 0;
        $data['r'] = 'NOT_EMAIL';
    }
} else {
    $data['s'] = 0;
    $data['r'] = 'NOT_FIELD_SESSION_DATA';
}

function sendEmail(string $email = null,string $subject = null,$content = null,string $names = null) : bool
{
    if(isset($email,$subject,$content,$names) === true)
    {
        require_once TO_ROOT . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $Layout = JFStudio\Layout::getInstance();
            $Layout->init("",'blank',"mail-new",TO_ROOT.'/apps/applications/',TO_ROOT.'/');

            $Layout->setScriptPath(TO_ROOT . '/apps/admin/src/');
    		$Layout->setScript(['']);

            $CatalogMailController = DummieTrading\CatalogMailController::init(1);

            $content = $Layout->replaceTags([
               'names' => ucwords($names)
            ],$content);

            $Layout->setVar([
                "names" => $names,
                "content" => $content
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

            //Recipients
            $mail->setFrom($CatalogMailController->mail, $CatalogMailController->sender);
            $mail->addAddress($email, $names);     

            //Content
            $mail->isHTML(true);                                  
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $Layout->getHtml();
            $mail->AltBody = strip_tags($Layout->getHtml());

            return $mail->send();
        } catch (Exception $e) {
            
        }
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 