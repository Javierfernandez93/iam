<?php

namespace JFStudio;

use Talento\BuyPerUser;
use HCStudio\Util;
use JFStudio\ValidatorHelper;
use JFStudio\ModelStripeErrors;
use Jcart\ModelPaymentMethod;
use Exception;

class Stripe {
    // public $URL = "http://localhost:8888/franquicias/apps/admin/subcore/application/validate_buy.php";
    public $URL = "https://www.mtkmexico.com/apps/admin/subcore/application/validate_buy.php";
    public static $SECRET_KEY_SANDBOX = "sk_test_dL2vzQGh2cPGxDOQo6nBs85z"; 
    public static $SECRET_KEY_LIVE = "sk_live_d0NEo5a95zvOvLQknCqvoTs2"; 
    public static $APPROVED = "succeeded";
    public $TEMPORAL_VALIDATION = true;
    private static $instance;
	public static function getInstance()
 	{
    	if(!self::$instance instanceof self)
      		self::$instance = new self;

    	return self::$instance;
 	}
 	public static function formatAmmount($ammount = null)
    {
        if (isset($ammount)) {
            if (strpos($mystring, ".") !== false) {
                return str_replace(".","",$ammount);
            } else {
                return $ammount*100;
            }
        }
    }
    public function getSecretKeySandbox()
    {
        return self::$SECRET_KEY_SANDBOX;
    }
    public function getSecretKeyLive()
    {
        return self::$SECRET_KEY_LIVE;
    }
    public function validateBuy($paymentId = null,&$BuyPerUser = null)
 	{
 		if (isset($paymentId)) 
 		{	
 			if($buy_per_user_login = $BuyPerUser->getBuyPerUserLoginIdByReference($paymentId))
 			{
                if($buy_per_user_login['verified'] == '0')
                {
                    if($buy_per_user_login['payment_method'] == ModelPaymentMethod::$STRIPE)
                    {
                        $BuyPerUser->cargarDonde("buy_per_user_login_id = ?",$buy_per_user_login['buy_per_user_login_id']);

                        if(ValidatorHelper::$TEMPORAL_VALIDATION === true)
                        {
                            $BuyPerUser->second_reference = "validación temporal-".date("Y-m-d H:i:s");
                            
                            if($BuyPerUser->save() == true)
                            {
                                $response['s'] == 1;

                                return $response;
                            } else {
                                throw new Exception(ModelStripeErrors::$VALIDATE_TEMPORAL_ERROR);        
                            }
                        } else {
             				$response = Util::doCurl($this->URL,[
            					'PHP_AUTH_USER' => Util::$username,
                                'PHP_AUTH_PW' => Util::$password,
                                'buy_per_user_login_id' => $buy_per_user_login['buy_per_user_login_id']
            				]);

                            $BuyPerUser->cargarDonde("buy_per_user_login_id = ?",$buy_per_user_login['buy_per_user_login_id']);
                            
                            if($response['s'] == "1")
                            {
                                return true;
                            } else if($response['r'] == 'VALIDATE_ERROR') {
                                throw new Exception(ModelStripeErrors::$VALIDATE_ERROR);        
                            }
                        }
                    } else {
                        throw new Exception(ModelStripeErrors::$NOT_DEPOSIT);
                    }
                } else if($buy_per_user_login['verified'] == '1') {
                    throw new Exception(ModelStripeErrors::$ALREADY_VALIDATED);
                } else if($buy_per_user_login['verified'] == '-1') {
                    throw new Exception(ModelStripeErrors::$NOT_FOUND);
                } else if($buy_per_user_login['verified'] == '2') {
                    throw new Exception(ModelStripeErrors::$NOT_FOUND);
                }
 			} else {
                throw new Exception(ModelStripeErrors::$NOT_FOUND);
            }
 		}

 		return false;
 	}
}