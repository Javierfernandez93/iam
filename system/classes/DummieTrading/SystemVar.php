<?php

namespace DummieTrading;

use HCStudio\Orm;

class SystemVar extends Orm {
  protected $tblName  = 'system_var';

  public function __construct() {
    parent::__construct();
  }
  
  public static function saveOrUpdate(array $data = null) : bool
  {
    if(!isset($data))
    {
        return false;
    }

    $SystemVar = new self;
    
    if(!$SystemVar->loadWhere("system_var_id = ?",$data['system_var_id']))
    {
        $SystemVar->name = $data['name'];
        $SystemVar->crate_date = time();
    }
    
    $SystemVar->val = $data['val'];
    
    return $SystemVar->save();
  }
  
  public static function saveOrUpdateAll(array $data = null) 
  {
    if(!isset($data))
    {
        return false;
    }

    foreach($data as $system_var)
    {
        self::saveOrUpdate($system_var);
    }
  }

  public function getValue(string $name = null) 
  {
    if(!isset($name))
    {
        return false;
    }

    return $this->connection()->field("
      SELECT 
        {$this->tblName}.val
      FROM 
        {$this->tblName}
      WHERE 
        {$this->tblName}.name = '{$name}'
      AND 
        {$this->tblName}.status = '1'
    ");
  }
  
  public static function _getValue(string $name = null) 
  {
    if(!isset($name))
    {
        return false;
    }

    return (new self)->connection()->field("
      SELECT 
        system_var.val
      FROM 
        system_var
      WHERE 
        system_var.name = '{$name}'
      AND 
        system_var.status = '1'
    ");
  }
}