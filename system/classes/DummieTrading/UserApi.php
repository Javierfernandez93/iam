<?php

namespace DummieTrading;

use HCStudio\Orm;

use HCStudio\Connection;
use HCStudio\Token;

use Exception;
use JFStudio\Constants;

class UserApi extends Orm {
  protected $tblName  = 'user_api';
  public $logged = false;

  public function __construct() {
    parent::__construct();

    if($this->loginRequest())
    {
        return $this->login($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
    }
  }

  public function loginRequest() 
  {
    if(isset($_SERVER['PHP_AUTH_USER']))
    {
        if(isset($_SERVER['PHP_AUTH_PW']))
        {
            return true;
        }
    }
  }

  public function login(string $api_key = null, string $api_secret = null) : bool
  {
    if($this->loadWhere('api_key = ? AND api_secret = ?', [$api_key,$api_secret]))
    {
      $this->logged = true;
    }

    return $this->logged;
  }
}