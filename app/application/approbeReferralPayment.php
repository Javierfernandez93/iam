<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    if($data['invoice_id'])
	{
        if((new DummieTrading\LicencePerUser)->hasAviableLicences($UserLogin->company_id))
        {
            $BuyPerUser = new DummieTrading\BuyPerUser;
            
            if($BuyPerUser->isInvoicePending($data['invoice_id']))
            {
                if($BuyPerUser->loadWhere('invoice_id = ?',$data['invoice_id']))
                {	
                    if(DummieTrading\BuyPerUser::processPayment($BuyPerUser->getId()))
                    {
                        $BuyPerUser->catalog_validation_method_id = DummieTrading\CatalogValidationMethod::INTERNAL_USER;
                        // $BuyPerUser->ipn_data = $data['ipn_data'] ? $data['ipn_data'] : '';
                        $BuyPerUser->approved_date = time();
                        $BuyPerUser->user_support_id = $data['user_support_id'] ? $data['user_support_id'] : $BuyPerUser->user_support_id;
                        $BuyPerUser->status = DummieTrading\BuyPerUser::VALIDATED;
    
                        if($BuyPerUser->save())
                        {
                            if(DummieTrading\LicencePerUser::assignLicence($UserLogin->company_id,$BuyPerUser->user_login_id))
                            {
                                $data['licenceReleased'] = true;
                            }

                            // if(sendEmail((new DummieTrading\UserLogin)->getEmail($BuyPerUser->user_login_id),$BuyPerUser->invoice_id))
                            // {
                            //     $data['mail_sent'] = true;
                            // }

                            $data['status'] = $BuyPerUser->status;
                            $data['s'] = 1;
                            $data['r'] = 'SAVE_OK';
                        } else {
                            $data['s'] = 0;
                            $data['r'] = 'NOT_UPDATE';
                        }
                    } else {
                        $data['s'] = 0;
                        $data['r'] = 'NOT_PROCESSED';
                    }
                } else {
                    $data['s'] = 0;
                    $data['r'] = 'NOT_SAVE';
                } 		
            } else {
                $data['s'] = 0;
                $data['r'] = 'NOT_PEDNING';
            } 		
        } else {
            $data['s'] = 0;
            $data['r'] = 'NOT_LICENCES';
        } 
	} else {
		$data['s'] = 0;
		$data['r'] = 'NOT_INVOICE_ID';
	}
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function sendEmail(string $email = null,string $invoice_id = null) : bool
{
    if(isset($email,$invoice_id) === true)
    {
        require_once TO_ROOT . '/vendor/autoload.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $Layout = JFStudio\Layout::getInstance();
            $Layout->init("",'buy-franchise',"mail-new",TO_ROOT.'/apps/applications/',TO_ROOT.'/');

            $Layout->setScriptPath(TO_ROOT . '/apps/admin/src/');
    		$Layout->setScript(['']);

            $CatalogMailController = DummieTrading\CatalogMailController::init(1);

            $Layout->setVar([
                "invoice_id" => $invoice_id,
                "email" => $email
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
            $mail->Subject = 'Bienvenido a DummieTrading';
            $mail->Body = $Layout->getHtml();
            $mail->AltBody = strip_tags($Layout->getHtml());

            return $mail->send();
        } catch (Exception $e) {
            
        }
    }

    return false;
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 