<?php

namespace DummieTrading;

use HCStudio\Orm;

class TelegramChannel extends Orm {
  protected $tblName  = 'telegram_channel';

  public function __construct() {
    parent::__construct();
  }
  
  public function getAllFromApi(int $telegram_api_id = null) 
  {
    if(isset($telegram_api_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.chat_id,
                {$this->tblName}.name
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.telegram_api_id = '{$telegram_api_id}'
              ";
              
      return $this->connection()->rows($sql);
    }

    return false;
  }

  public function getSingle(int $telegram_channel_id = null) 
  {
    if(isset($telegram_channel_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.chat_id,
                {$this->tblName}.name,
                telegram_api.telegram_api_id,
                telegram_api.api_key,
                telegram_api.user_name
              FROM 
                {$this->tblName}
              LEFT JOIN 
                telegram_api 
              ON 
                telegram_api.telegram_api_id = {$this->tblName}.telegram_api_id
              WHERE 
                {$this->tblName}.telegram_channel_id = '{$telegram_channel_id}'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getChatId(string $name = null) 
  {
    if(isset($name) === true)
    {
      $sql = "SELECT
                {$this->tblName}.chat_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.name = '{$name}'
              ";
              
      return $this->connection()->field($sql);
    }
  }
    
  public function getName(int $chanel_id = null) 
  {
    if(isset($chanel_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.names
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.chanel_id = '{$chanel_id}'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
  
  public function get(int $telegram_channel_id = null) 
  {
    if(isset($telegram_channel_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.chat_id,
                {$this->tblName}.name
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.telegram_channel_id = '{$telegram_channel_id}'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getAll() 
  {
    $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.chat_id,
                {$this->tblName}.names
            FROM 
                {$this->tblName}
            WHERE 
                {$this->tblName}.status = '1'
            ";
            
    return $this->connection()->rows($sql);
  }
}