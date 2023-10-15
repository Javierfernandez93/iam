<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\CatalogVar;

class UserVar extends Orm {
  protected $tblName  = 'user_var';

  public function __construct() {
    parent::__construct();
  }
  
  public static function saveVar(array $data = null) : bool
  {
    $UserVar = new self;
    
    $UserVar->loadWhere('user_login_id = ? AND catalog_var_id = ?',[$data['user_login_id'],$data['catalog_var_id']]);
    $UserVar->value = $data['value']['value'];
    
    if(!$UserVar->getId())
    {
      $UserVar->catalog_var_id = $data['catalog_var_id'];
      $UserVar->user_login_id = $data['user_login_id'];
      $UserVar->create_date = time();  
    }

    return $UserVar->save();
  }

  public static function saveVars(array $variables = null,int $user_login_id = null) : bool
  {
    foreach($variables as $variable)
    {
      $variable['user_login_id'] = $user_login_id;
      
      self::saveVar($variable);
    }

    return true;
  }

  public function getAllVars(int $user_login_id = null) : array|bool
  {
    if($_vars = (new CatalogVar)->getAll())
    {
      return array_map(function($var) use($user_login_id) {
        $var['value'] = [
          'user_var_id' => 0,
          'value' => 0,
        ];

        if($value = $this->getVar($user_login_id,$var['catalog_var_id']))
        {
          $var['value'] = $value;
        }

        return $var;
      },$_vars);
    }

    return false;
  }

  public function getVar(int $user_login_id = null,string $catalog_var_id = null) 
  {
    if(isset($user_login_id,$catalog_var_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.value
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.catalog_var_id = '{$catalog_var_id}'
              AND 
                {$this->tblName}.status = '1'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getVarInfoFormatted(int $user_login_id = null) 
  {
    if(isset($user_login_id))
    {
      if($vars = $this->getVarInfo($user_login_id))
      {
        $_vars = [];

        foreach($vars as $var)
        {
          $_vars[] = [
            $var['name'] => $var['value']
          ];
        }

        return $_vars;
      }
    }

    return false;
  }

  public function getVarInfo(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.value,
                catalog_var.name
              FROM 
                {$this->tblName}
              LEFT JOIN 
                catalog_var
              ON 
                catalog_var.catalog_var_id = {$this->tblName}.catalog_var_id
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              ";
              
              
      return $this->connection()->rows($sql);
    }

    return false;
  }

  public function getVarValue(int $user_login_id = null,string $name = null) 
  {
    if(isset($user_login_id) === true)
    {
      if($catalog_var_id = (new CatalogVar)->getVarId($name))
      {
        $sql = "SELECT
                  {$this->tblName}.value
                FROM 
                  {$this->tblName}
                WHERE 
                  {$this->tblName}.user_login_id = '{$user_login_id}'
                AND 
                  {$this->tblName}.catalog_var_id = '{$catalog_var_id}'
                AND 
                  {$this->tblName}.status = '1'
                ";
                
        return $this->connection()->field($sql);
      }

    }

    return '';
  }

  public function getVarValueByIdentificator(int $user_login_id = null,string $identificator = null) 
  {
    if(isset($user_login_id) === true)
    {
      if($catalog_var_id = (new CatalogVar)->getVarIdByIdentificator($identificator))
      {
        $sql = "SELECT
                  {$this->tblName}.value
                FROM 
                  {$this->tblName}
                WHERE 
                  {$this->tblName}.user_login_id = '{$user_login_id}'
                AND 
                  {$this->tblName}.catalog_var_id = '{$catalog_var_id}'
                AND 
                  {$this->tblName}.status = '1'
                ";
                
        return $this->connection()->field($sql);
      }

    }

    return '';
  }
}