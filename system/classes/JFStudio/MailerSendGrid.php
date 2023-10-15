<?php

namespace JFStudio;

use HCStudio\Orm;
use Talento\ModelErrorsEmail;
use Exception;

class MailerSendGrid extends Orm
{
	protected $tblName = 'catalog_mail_controller';
	public static $MAIN_EMAIL = "leqjl93@hotmail.com";
	protected $service_email = [
	];
	protected $sandbox_email = [
	    0 => 'leqjl93@hotmail.com'
	];
	private $message_id;
	private $Email;
	private $SendGrid;
	protected $sandbox = false;
	public static $ERROR = "error";
	public static $SUCCESS = 200;
	protected $service = true;
	public function __construct($catalog_mail_controller_id = 1,$service = false,$sandbox = false)
	{
		parent::__construct();

		if($this->cargarDonde('catalog_mail_controller_id = ?',$catalog_mail_controller_id))
		{
			$this->service = $service;
			$this->sandbox = $sandbox;
		}
	}

	public function _setMesssageId($message_id = null)
	{
		if(isset($message_id) === true) {
			$this->setMesssageId(explode(": ",$message_id)[1]);
		}

	}
	public function setMesssageId($message_id = null)
	{	
		$this->message_id = $message_id;
	}

	public function getMesssageId()
	{
		return $this->message_id;
	}

	public function _sendMail($data = false,$Email = false,$SendGrid = false)
	{
		if(is_array($data) && is_object($Email) && is_object($SendGrid))
		{
			$Email->setFrom($data["mail_from"],$data["name_from"]);
			$Email->setSubject($data["subject"]);
			$Email->addContent("text/html", $data['body']);
			$Email->addTo($data['mail_to'], $data['name']);


			try {
			    $response = $SendGrid->send($Email);	


				if($response->statusCode() == 202) {
					$this->_setMesssageId($response->headers()[5]);

					return true;
				}
			} catch (Exception $e) {
			    echo 'Caught exception: '. $e->getMessage() ."\n";
			}

		}

		return false;
	}

	public function setSendGrid($SendGrid = null)
	{
		if (isset($SendGrid)) {
			$this->SendGrid = $SendGrid;
		}
	}
	public function setEmail($Email = null)
	{
		if (isset($Email)) {
			$this->Email = $Email;
		}
	}
	public function getSendGrid()
	{
		return $this->SendGrid;
	}
	public function getEmail()
	{
		return $this->Email;
	}
	public function loadMailAviable()
	{
		if($catalog_mail_controller_id = $this->getMailAviable())
		{
			if($this->cargarDonde('catalog_mail_controller_id = ?',$catalog_mail_controller_id))
			{
				$this->getSendGrid()->init($this->mail,$this->password);
				return true;
			}
		}

		return false;
	}
	public function sendMailRecursive($data = null)
	{
		if($this->loadMailAviable() === true)
		{
			if(is_array($data) && isset($data))
			{
				if(filter_var($data['mail_to'], FILTER_VALIDATE_EMAIL)) 
				{
					if($this->sandbox === true) {
						$this->getEmail()->addTo($this->sandbox_email);
					} else {
						$this->getEmail()->addTo($data['mail_to']);
					}

					$this->getEmail()->setFrom($data["mail_from"]);
					$this->getEmail()->setFromName($data["name_from"]);
					$this->getEmail()->setSubject($data["subject"]);
					$this->getEmail()->setHtml($data['body']);

					if($this->service === true) {
						foreach ($this->service_email as $email) {
					  		$this->getEmail()->addBcc($this->getEmail());			
						}
					}
					
					try {
						$response = $this->getSendGrid()->send($this->getEmail());
						
						if($response->getCode() == self::$SUCCESS) 
						{
							if($this->updateStockMailAviable() === true)
							{
								return true;
							}
						}
					} catch (Exception $e) {
						$response = json_decode($e->getMessage(),true);
						
						if($response['message'] == self::$ERROR) 
						{
							if($response['errors'][0] == "Maximum credits exceeded") 
							{
								$this->disabeCatalog();
								$this->sendMailRecursive($data);
								// throw new Exception(ModelErrorsEmail::$MAXIMUM_EXCEDED);
							}
						}
					}
				} else {
			      throw new Exception(ModelErrorsEmail::$INVALID_DESTINATION_EMAIL);
			    }
			} else {
		      throw new Exception(ModelErrorsEmail::$BAD_DATA_REQUEST);
		    }
		} else {
	      throw new Exception(ModelErrorsEmail::$MAIL_NOT_AVIABLE);
	    }
	}

	public function sendMail($data = false,$Email = false,$SendGrid = false)
	{
		if(is_array($data) && is_object($Email) && is_object($SendGrid))
		{
			if($this->sandbox) 
				$Email->addTo($this->sandbox_email);
			else 
				$Email->addTo($data['mail_to']);

			$Email->setFrom($data["mail_from"]);
			$Email->setFromName($data["name_from"]);
			$Email->setSubject($data["subject"]);
			$Email->setHtml($data['body']);

			if($this->service === true) {
				foreach ($this->service_email as $email) {
			  		$Email->addBcc($email);			
				}
			}
			try {
				if($SendGrid->send($Email)->getCode() == 200) {
					return true;
				}
			} catch (Exception $e) {
				$response = json_decode($e->getMessage(),true);
				
				if($response['message'] == self::$ERROR) {
					return false;
				}
			}

		}

		return false;
	}

	public function getMailAviable()
	{
	    if($catalog_mail_controller_id = $this->getAviableCatalogMainControllerId())
	    {
	      return $catalog_mail_controller_id;
	    } else {
	      return false;
	    }
	}

	public function getAviableCatalogMainControllerId()
	{
	    $sql = "SELECT 
	              catalog_mail_controller_id
	            FROM
	              catalog_mail_controller
	            WHERE 
	              catalog_mail_controller.capacity > 0
	            AND
	              catalog_mail_controller.active = '1'
	            AND
	             catalog_mail_controller.available_to_sent = '1'
	            LIMIT 1";

	    return $this->connection()->field($sql);    
	}

	public function updateStockMailAviable()
	{
		if ($this->getId()) 
		{
	       $this->capacity = ($this->capacity)-1;
	       if($this->capacity == 0) {
	       		$this->active = '-1';
	       }
	       return $this->save();
		}
	}
	public function disabeCatalog()
	{
		if ($this->getId()) 
		{
	       $this->capacity = 0;
	       $this->active = '-1';
	       return $this->save();
		}
	}
}