<?php

namespace DummieTrading;

use HCStudio\Orm;

class Suscriber extends Orm {
  protected $tblName  = 'suscriber';

  public function __construct() {
    parent::__construct();
  }
  
  public static function add(array $data = null) 
  {
    if(!isset($data))
    {
        return false;
    }

    $Suscriber = new self;
    
    if($Suscriber->exist($data['email']))
    {
        return false;
    }

    $Suscriber->email = $data['email'];
    $Suscriber->create_date = time();
    
    return $Suscriber->save();
  }
    
  public function exist(string $email = null) 
  {
    if(isset($email) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.email = '{$email}'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
}