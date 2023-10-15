<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;
use HCStudio\Token;

class UserTelegram extends Orm {
  protected $tblName  = 'user_telegram';

  const DEFAULT_DUMMY_TRADER_API_ID = 1;
  public function __construct() {
    parent::__construct();
  }
  
  public static function getTelegramConfigForConnection(int $user_login_id = null) : array
  {
    if($user = self::_getUser($user_login_id))
    {
      $user['url'] = "https://t.me/Autocapitaltradingbot?start={$user['token_key']}";
      $user['token'] = "token={$user['token_key']}";

      return $user;
    }

    return [];
  }

  public static function disconnect(array $data = null) : bool
  {
    $UserTelegram = new self;

    if($UserTelegram->loadWhere("user_login_id = ?",$data['user_login_id']))
    {
      $UserTelegram->chat_id = '';
      
      return $UserTelegram->save();
    }

    return false;
  }

  public static function attachChatId(array $data = null) : bool
  {
    $UserTelegram = new self;

    if($UserTelegram->loadWhere("token_key = ?",$data['key']))
    {
      $UserTelegram->chat_id = $data['chat_id'];
      
      return $UserTelegram->save();
    }

    return false;
  }

  public static function createUser(int $user_login_id = null) : bool
  {
    if(isset($user_login_id) === true)
    {
        $UserTelegram = new self;
        
        if(!$UserTelegram->exist($user_login_id))
        {
            $Token = new Token;
            
            if($token = $Token->getToken([
                'user_login_id' => $user_login_id,
                'unix' => time(),
            ]))
            {
                $UserTelegram->user_login_id = $user_login_id;
                
                $UserTelegram->token = $token['token'];
                $UserTelegram->token_key = $token['key'];
                $UserTelegram->telegram_api_id = self::DEFAULT_DUMMY_TRADER_API_ID;
                
                $UserTelegram->create_date = time();
                
                return $UserTelegram->save();
            }
        }
    }

    return false;
  }

  public function getChatId(int $user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.chat_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getUserId(int $chat_id = null)
  {
    if(isset($chat_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.chat_id = '{$chat_id}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ORDER BY
                {$this->tblName}.create_date
              DESC 
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
    
  public function exist(int $user_login_id = null) : bool
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.user_login_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }
  
  public static function _getUser(int $user_login_id = null) : array|bool
  {
    $UserTelegram = new self;
    
    if($user = $UserTelegram->getUser($user_login_id))
    {
      return $user;
    } else {
      UserTelegram::createUser($user_login_id);
    }

    return $UserTelegram->getUser($user_login_id);
  }

  public function getUser(int $user_login_id = null) : array|bool
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.token_key,
                {$this->tblName}.chat_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }

  public function isConnected(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $sql = "SELECT
                {$this->tblName}.chat_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$data['user_login_id']}'
              AND 
                {$this->tblName}.telegram_api_id = '{$data['telegram_api_id']}'
              AND
                {$this->tblName}.status = '".Constants::AVIABLE."'
              ";
              
      if($chat_id = $this->connection()->field($sql))
      {
        return isset($chat_id) && !empty($chat_id);
      }
    }

    return false;
  }
}