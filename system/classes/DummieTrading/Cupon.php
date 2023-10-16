<?php

namespace DummieTrading;

use HCStudio\Token;
use HCStudio\Orm;

class Cupon extends Orm {
  protected $tblName  = 'cupon';

  const TOKEN_LENGHT = 6;
  public function __construct() {
    parent::__construct();
  }
  
  public static function generate() 
  {
    $Cupon = new self;
    
    $Cupon->code = Token::__randomKey(self::TOKEN_LENGHT);
    $Cupon->create_date = time();
    
    return $Cupon->save();
  }
    
  public function exist(string $code = null) 
  {
    if(isset($code) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.code = '{$code}'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
}