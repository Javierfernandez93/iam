<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\TelegramChannel;

class TelegramMessageChannel extends Orm {
  protected $tblName = 'telegram_message_channel';

  public function __construct() {
    parent::__construct();
  }
  
  public static function add(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $TelegramMessageChannel = new self;
      $TelegramMessageChannel->telegram_channel_id = $data['telegram_channel_id'];
      $TelegramMessageChannel->message = $data['message'];
      $TelegramMessageChannel->message_id = $data['message_id'] ?? 0; 
      $TelegramMessageChannel->data = $data['data'] ?? ''; 
      $TelegramMessageChannel->create_date = time();

      return $TelegramMessageChannel->save();
    }

    return false;
  }

  public function get(int $telegram_api_id = null) 
  {
    if(isset($telegram_api_id) === true)
    {
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

  public function getAllWithChannels() 
  {
    $TelegramChannel = new TelegramChannel;

    return array_map(function($api) use($TelegramChannel) {
      $api['channels'] = $TelegramChannel->getAllFromApi($api['telegram_api_id']);

      return $api;
    },$this->getAll());
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
}