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
  
  public static function createCupons(array $data = null) 
  {
    for($i = 0; $i < $data['amount']; $i++)
    {
      self::generate($data);
    }
  }

  public static function generate(array $data = null) 
  {
    $Cupon = new self;
    
    $Cupon->code = isset($data['code']) ? $data['code'] : Token::__randomKey(self::TOKEN_LENGHT);
    $Cupon->discount = isset($data['discount']) ? $data['discount'] : 0;
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