<?php define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getHeadersForWebService();

$UserSupport = new DummieTrading\UserSupport;

if($UserSupport->logged === true)
{
    if(updateUserLogin($data['user']))
    {
        if(updateUserData($data['user']))
        {
            if(updateUserContact($data['user']))
            {
                if(updateUserAccount($data['user']))
                {
                    if(updateUserAddress($data['user']))
                    {
                        if(updateUserReferral($data['user']))
                        {
                            $data["s"] = 1;
                            $data["r"] = "UPDATED_OK";
                        } else {
                            $data["s"] = 0;
                            $data["r"] = "NOT_UPDATED_USER_REFERRAL";
                        }  
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

function updateUserData($data = null) : bool
{
    $UserData = new DummieTrading\UserData;   
        
    if($UserData->loadWhere("user_login_id = ?",$data['user_login_id']))
    {
        $UserData->names = $data['names'];
        
        return $UserData->save();
    }

    return true;
}

function updateUserContact($data = null) : bool
{
    $UserContact = new DummieTrading\UserContact;   
        
    if($UserContact->loadWhere("user_login_id = ?",$data['user_login_id']))
    {
        $UserContact->phone = $data['phone'];

        return $UserContact->save();    
    }

    return true;
}


function updateUserAccount($data = null) : bool
{
    $UserAccount = new DummieTrading\UserAccount;   
        
    if($UserAccount->loadWhere("user_login_id = ?",$data['user_login_id']))
    {
        $UserAccount->referral_notification = filter_var($data['referral_notification'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $UserAccount->referral_email = filter_var($data['referral_email'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $UserAccount->info_email = filter_var($data['info_email'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        
        return $UserAccount->save();
    }

    return true;
}

function updateUserAddress($data = null) : bool
{
    $UserAddress = new DummieTrading\UserAddress;   
        
    if($UserAddress->loadWhere("user_login_id = ?",$data['user_login_id']))
    {
        $UserAddress->country_id = $data['country_id'];
        
        return $UserAddress->save();
    }

    return true;
}

function updateUserLogin($data = null) : bool
{
    $UserLogin = new DummieTrading\UserLogin(false,false);   
        
    if($UserLogin->loadWhere("user_login_id = ?",$data['user_login_id']))
    {
        $UserLogin->email = $data['email'];
        $UserLogin->password = $data['password'] ? sha1($data['password']) : $UserLogin->password;
        $UserLogin->signup_date = $data['signup_date'] ? strtotime($data['signup_date']) : $UserLogin->signup_date;
        
        return $UserLogin->save();
    }

    return true;
}

function updateUserReferral($data = null) : bool
{
    $UserReferral = new DummieTrading\UserReferral;   
        
    $UserReferral->loadWhere("user_login_id = ?",$data['user_login_id']);
    
    if(!$UserReferral->getId())
    {
        $UserReferral->user_login_id = $data['user_login_id'];
        $UserReferral->create_date = time();
    }
    
    $UserReferral->referral_id = $data['referral']['user_login_id'];
    $UserReferral->commission = $data['referral']['commission'] ? $data['referral']['commission'] : DummieTrading\UserReferral::DEFAULT_COMMISSION;
    
    return $UserReferral->save();
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 