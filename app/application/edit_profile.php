<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserLogin = new DummieTrading\UserLogin;

if($UserLogin->logged === true)
{
    $UserLogin->email = $data['email'];

    if($UserLogin->save())
    {
        if(updateUserData($data,$UserLogin->company_id))
        {
            if(updateUserContact($data,$UserLogin->company_id))
            {
                if(updateUserAccount($data,$UserLogin->company_id))
                {
                    if(updateUserAddress($data,$UserLogin->company_id))
                    {
                        if(updatePaymentMethodPerUser($data,$UserLogin->company_id))
                        {
                            $data["payment_method"] = true;
                        }
                        $data["s"] = 1;
                        $data["r"] = "UPDATED_OK";
                    } else {
                        $data["s"] = 0;
                        $data["r"] = "NOT_UPDATED_USER_ADDRESS";
                    }  
                } else {
                    $data["s"] = 0;
                    $data["r"] = "NOT_UPDATED_USER_ACCOUNT";
                }            
            }  else {
                $data["s"] = 0;
                $data["r"] = "NOT_UPDATED_USER_CONTACT";
            }
        } else {
            $data["s"] = 0;
            $data["r"] = "NOT_UPDATED_USER_DATA";
        }
    } else {
        $data["s"] = 0;
        $data["r"] = "NOT_UPDATED_USER_LOGIN";
    }
} else {
	$data["s"] = 0;
	$data["r"] = "NOT_FIELD_SESSION_DATA";
}

function updateUserData($data = null,$company_id = null)
{
    $UserData = new DummieTrading\UserData;   
        
    if($UserData->cargarDonde("user_login_id = ?",$company_id))
    {
        $UserData->names = $data['names'];
        
        return $UserData->save();
    }

    return true;
}

function updateUserContact($data = null,$company_id = null)
{
    $UserContact = new DummieTrading\UserContact;   
        
    if($UserContact->cargarDonde("user_login_id = ?",$company_id))
    {
        $UserContact->phone = $data['phone'];
        
        return $UserContact->save();    
    }

    return true;
}

function updateUserAccount($data = null,$company_id = null)
{
    $UserAccount = new DummieTrading\UserAccount;   
        
    if(!$UserAccount->loadWhere("user_login_id = ?",$company_id))
    {
        $UserAccount->user_login_id = $company_id;
    }

    $UserAccount->referral_notification = filter_var($data['referral_notification'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    $UserAccount->referral_email = filter_var($data['referral_email'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    
    if(!$UserAccount->existLanding($company_id,$data['landing']))
    {
        $UserAccount->landing = $data['landing'];
    } 

    $UserAccount->landing = $UserAccount->landing ? $UserAccount->landing : '';

    $UserAccount->image = isset($data['image']) ? $data['image'] : '';
    $UserAccount->catalog_timezone_id = isset($data['catalog_timezone_id']) ? $data['catalog_timezone_id'] : '';
    $UserAccount->info_email = filter_var($data['info_email'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
    
    return $UserAccount->save();
}

function updateUserAddress($data = null,$company_id = null)
{
    $UserAddress = new DummieTrading\UserAddress;   
        
    if(!$UserAddress->loadWhere("user_login_id = ?",$company_id))
    {
        $UserAddress->user_login_id = $company_id;
    }
    
    $UserAddress->country_id = isset($data['country_id']) ? $data['country_id'] : '';
    $UserAddress->address = isset($data['address']) ? $data['address'] : '';
    $UserAddress->colony = isset($data['colony']) ? $data['colony'] : '';
    $UserAddress->zip_code = isset($data['zip_code']) ? $data['zip_code'] : '';
    $UserAddress->city = isset($data['city']) ? $data['city'] : '';
    $UserAddress->state = isset($data['state']) ? $data['state'] : '';
    $UserAddress->country = isset($data['country']) ? $data['country'] : '';
    
    return $UserAddress->save();
}

function updatePaymentMethodPerUser($data = null,$company_id = null)
{
    $PaymentMethodPerUser = new DummieTrading\PaymentMethodPerUser;   
        
    if(!$PaymentMethodPerUser->cargarDonde("user_login_id = ?",$company_id))
    {
        $PaymentMethodPerUser->user_login_id = $company_id;
    }
    
    $PaymentMethodPerUser->bank = isset($data['bank']) ? $data['bank'] : $PaymentMethodPerUser->bank;
    $PaymentMethodPerUser->account = isset($data['account']) ? $data['account'] : $PaymentMethodPerUser->account;
    $PaymentMethodPerUser->clabe = isset($data['clabe']) ? $data['clabe'] : $PaymentMethodPerUser->clabe;
    $PaymentMethodPerUser->paypal = isset($data['paypal']) ? $data['paypal'] : $PaymentMethodPerUser->paypal;
        
    return $PaymentMethodPerUser->save();
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 