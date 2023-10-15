<?php

namespace DummieTrading;

use HCStudio\Orm;

class PaymentMethodPerUser extends Orm {
  protected $tblName  = 'payment_method_per_user';

  public function __construct() {
    parent::__construct();
  }
  
  public static function add(array $data = null) : bool
  {
    $Client = new Client;
    
    if(!$Client->exist($data['email']))
    {
        $Client->user_login_id = $data['user_login_id'];
        $Client->name = $data['name'];
        $Client->email = $data['email'];
        $Client->adult = $data['adult'] ? 1 : 0;
        $Client->create_date = time();
        
        return $Client->save();
    }

    return false;
  }

  public function getNames(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                LOWER(CONCAT_WS(' ',
                  {$this->tblName}.names,
                  {$this->tblName}.last_name,
                  {$this->tblName}.sur_name
                )) as names
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function exist(string $email = null) : bool
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
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function _get(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.bank,
                {$this->tblName}.account,
                {$this->tblName}.clabe,
                {$this->tblName}.paypal
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
      
      return $this->connection()->row($sql);
    }

    return false;
  }
}