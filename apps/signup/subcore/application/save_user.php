<?php define("TO_ROOT","../../../..");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$data = $data["data"];

$UserLogin = new DummieTrading\UserLogin;

if($data["use_login"]["email"])
{
	$UserLogin->deleteSession();
	
	if($UserLogin->isAbiableToSingUp())
	{
		if($UserLogin->isUniqueMail($data["use_login"]["email"]))
		{
		 	$data["use_login"]["password"] = sha1($data["use_login"]["password"]);			 	

			$UserLogin->cargarArray($data["use_login"]);
			$UserLogin->active = '1';
			$UserLogin->fillFields($UserLogin);

			if($UserLogin->save())
			{	
				$user_login_id = $UserLogin->getId();
				$UserLogin->company_id = $user_login_id;

				if($UserLogin->save())
				{
					$UserData = new DummieTrading\UserData;
					$UserData->cargarArray($data["use_data"]);
					$UserData->user_login_id=$user_login_id;
					$UserLogin->fillFields($UserData);

					if($UserData->save())
					{
						$UserAddrees = new DummieTrading\UserAddress;
						
						$Country = new World\Country;
						
						$UserAddrees->cargarArray($data["user_address"]);
						$UserAddrees->user_login_id=$user_login_id;
						$UserAddrees->country = $Country->getAllById($data["user_address"]['country_id']);
						$UserLogin->fillFields($UserAddrees);

						if($UserAddrees->save())
						{
							$UserContact = new DummieTrading\UserContact;
							$UserContact->cargarArray($data["user_contact"]);
							$UserContact->user_login_id = $user_login_id;		
							$UserLogin->fillFields($UserContact);			

							if($UserContact->save())
							{
								$UserAccount = new DummieTrading\UserAccount;									
								
								if($data['user_account']["sponsor_id"])
								{
									$UserAccount->sponsor_id = $data['user_account']["sponsor_id"];
									$UserLogin->fillFields($UserAccount);

									if(saveNotification("{$UserData->names} ahora es parte de tu equipo, dale bienvenida a Los Talentos.",$UserAccount->sponsor_id))
									{
										$data["notification_sent_sponsor"] = true;
									}
								} else {
									$UserAccount->cargarArray($data['user_account']);
								}

								$UserAccount->user_login_id=$user_login_id;
								$UserLogin->fillFields($UserAccount);
								
								if($UserAccount->save())
								{
									$UserBank = new DummieTrading\UserBank();
									$UserBank->user_login_id = $user_login_id;	
									$UserLogin->fillFields($UserBank);							
									
									if($UserBank->save())
									{
										$UserSetting = new DummieTrading\UserSetting();
										$UserSetting->user_login_id = $user_login_id;	
										$UserLogin->fillFields($UserSetting);							
										
										if($UserSetting->save())
										{								
											// sendMailwelcome($user_login_id);

											$Session = new HCStudio\Session('welcome');
											$Session->setFlash('welcome',true);

											if(saveNotification("Hola {$UserData->names}, bienvenido a Los Talentos.",$UserLogin->company_id))
											{
												$data["notification_sent"] = true;
											}

											$data["company_id"] = $user_login_id;
											$data["email"] = $data["use_login"]["email"];
											$data["s"]=1;
						 					$data["r"]='<t>Tu cuenta fue creada correctamente. Tu número de distribuidor es el</t><h2>'.$user_login_id.'</h2>Redireccionando a tu oficina virtual</t>';

										} else {
						 					$data["s"]=0;
						 					$data["r"]='NOT_SAVE_USER_SETTING';
						 				}
									} else {
					 					$data["s"]=0;
					 					$data["r"]='NOT_SAVE_USER_BANK';
					 				}

								} else {
				 					$data["s"]=0;
				 					$data["r"]='NOT_SAVE_USER_ACCOUNT';
				 				}
			 				} else {
			 					$data["s"]=0;
			 					$data["r"]='No se pudo guardar tus datos de contacto';
			 				}
						} else {
							$data["s"]=0;
			 				$data["r"]='No se pudo guardar tu dirección';
						}
					} else {
						$data["s"]=0;
						$data["r"]='No se pudo guardar tu información personal';				
					}
				} else {
					$data["s"] = 0;
					$data["r"] = 'NOT_SAVE_USER_LOGIN';				
				}

			} else {
				$data["s"]=0;
		 		$data["r"]='ingrese sus datos de sesión correctamente';
				
			}		
		} else {
			$data["s"]=0;
			$data["r"]='Correo ya registrado';
		}			
	} else {
		$data["s"]=0;
		$data["r"]='Cierra tu Sesion Primero';
	}
} else {
	$data["s"]=0;
	$data["r"]='Correo Invalido';
}

function sendMailwelcome($user_login_id){
	
	require TO_ROOT . '/system/vendor/autoload.php';
	require TO_ROOT . '/system/lib/SendGrid.php';
	require TO_ROOT . '/system/lib/Client.php';

	$Mailer = new JFStudio\MailerSendGrid(3,true,false);
	$SendGrid = new SendGrid($Mailer->mail,$Mailer->password);
	$Email = new SendGrid\Email();

	$MailLayout = JFStudio\Layout::getInstance();
	$MailLayout->init("Bienvenido a Franquicias Veggie","mail_welcome","mail","",TO_ROOT."/",TO_ROOT."/apps/mail/");
	
	$UserLogin=new Talento\UserLogin();	
	$UserLogin->cargarDonde("company_id=?",$user_login_id);
	$UserData=new Talento\UserData();
	$UserLogin->cargarDonde("user_login_id=?",$user_login_id);


	$MailLayout->setVar("user_login_id", $user_login_id);
	$MailLayout->setVar("names", strtoupper($UserData->getNames($user_login_id)));
	
	# compania
    $data_mailer["mail_from"] = 'info@Cronos.com';
    $data_mailer["name_from"] = 'Soporte MtkMéxico';

	# quien se registra
    // $data_mailer["name_to"] = $user_data['names']. ' ' .$user_data['last_name'];
    $data_mailer["name_to"] = strtoupper($UserData->getNames($user_login_id));
    $data_mailer["mail_to"] = $UserLogin->mail;
    // $data_mailer["mail_to"] = $user_data['mail'];

    $data_mailer["subject"] = 'Bienvenido a Los Talentos';
	$data_mailer["body"] = $MailLayout->getHtml();
	
	if($Mailer->sendMail($data_mailer,$Email,$SendGrid))
		return true;

}

function saveNotification($message = null,$company_id = null)
{
	if(isset($message,$company_id) === true)
	{
		$NotificationPerUser = new DummieTrading\NotificationPerUser;
		$NotificationPerUser->company_id = $company_id;
		$NotificationPerUser->catalog_notification_id = 2;
		$NotificationPerUser->message = $message;
		$NotificationPerUser->create_date = time();

		return $NotificationPerUser->save();
	}
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 