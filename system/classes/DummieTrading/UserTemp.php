<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserTemp extends Orm {
  protected $tblName  = 'user_temp';

  const MINUTES_FOR_EXPIRATION = 1; // minutes

  public function __construct() {
    parent::__construct();
  }

  public static function clearVars(int $user_login_id = null) : bool
  {
    $UserTemp = new self;
  
    $UserTemp->loadWhere('user_login_id = ?',$user_login_id);
    $UserTemp->data = '';

    return $UserTemp->save();
  }
  
  public static function getVar(int $user_login_id = null,string $name = null) : string|bool
  { 
    $UserTemp = new self;

    if($UserTemp->loadWhere("user_login_id = ?",$user_login_id))
    {
        $UserTemp->data = json_decode($UserTemp->data,true);
        
        if(isset($UserTemp->data[$name]))
        {
            if(time() < $UserTemp->data['expiration'])
            {
                return $UserTemp->data[$name];
            }
        }
    }

    return false;
  }

  public static function setVar(int $user_login_id = null,string $name = null,string $value = null) : bool
  {
    $UserTemp = new self;
    
    $UserTemp->loadWhere("user_login_id = ?",$user_login_id);
    
    $UserTemp->user_login_id = $user_login_id;
    $UserTemp->data = json_encode([
        $name => $value,
        'expiration' => strtotime("+".self::MINUTES_FOR_EXPIRATION." minutes"),
    ]);
    $UserTemp->create_date = time();
    
    return $UserTemp->save();
  }
}