<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\TelegramChannel;

class TelegramApi extends Orm
{
  protected $tblName = 'telegram_api';

  public function __construct()
  {
    parent::__construct();
  }

  public function get(int $telegram_api_id = null)
  {
    if (isset($telegram_api_id) === true) {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.api_key,
                {$this->tblName}.user_name
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.telegram_api_id = '{$telegram_api_id}'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getByName(string $user_name = null)
  {
    if (isset($user_name) === true) {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.api_key,
                {$this->tblName}.user_name
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_name = '{$user_name}'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getAllWithChannels()
  {
    $TelegramChannel = new TelegramChannel;

    return array_map(function ($api) use ($TelegramChannel) {
      $api['channels'] = $TelegramChannel->getAllFromApi($api['telegram_api_id']);

      return $api;
    }, $this->getAll());
  }

  public function getAll()
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.api_key,
              {$this->tblName}.user_name
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            ";

    return $this->connection()->rows($sql);
  }
  
  public function getAllList()
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.user_name
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            AND 
              {$this->tblName}.visible = '1'
            ";

    return $this->connection()->rows($sql);
  }
}
