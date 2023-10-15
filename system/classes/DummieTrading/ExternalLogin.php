<?php

namespace DummieTrading;

use HCStudio\Util;
use JFStudio\Curl;

class ExternalLogin {
  public function __construct() {
  }

  public static function getExternalUserByEmail(string $email = null,string $url = null) 
  {
    if(isset($email) === true)
    {
      $Curl = new Curl;
      $Curl->setBasicAuthentication(Util::USERNAME,Util::PASSWORD);
      $Curl->post($url, [
        'email' => $email
      ]);

      $response = $Curl->getResponse(true);
      
      return $response['s'] == 1 ? $response['user_login_id'] : false;
    }
    
    return false;
  }

  public static function generateApis(int $user_login_id = null,string $url = null) 
  {
    if(isset($user_login_id) === true)
    {
      $Curl = new Curl;
      $Curl->setBasicAuthentication(Util::USERNAME,Util::PASSWORD);
      $Curl->post($url, [
        'user_login_id' => $user_login_id
      ]);

      $response = $Curl->getResponse(true);
      
      return $response['s'] == 1 ? $response['apiCredential'] : false;
    }
    
    return false;
  }

  public static function signupChecker(string $email = null,string $url = null) : bool
  {
    if(isset($email) === true)
    {
      $Curl = new Curl;
      $Curl->setBasicAuthentication(Util::USERNAME,Util::PASSWORD);
      $Curl->post($url, [
        'email' => $email
      ]);

      return $Curl->getResponse(true)['s'] == 1;
    }
    
    return false;
  }
  
  public static function signupExternal(array $data = null,string $url = null) : bool
  {
    if(isset($data) === true)
    {
      $Curl = new Curl;
      $Curl->setBasicAuthentication(Util::USERNAME,Util::PASSWORD);
      $Curl->post($url, $data);

      return $Curl->getResponse(true)['s'] == 1;
    }

    return false;
  }
}
