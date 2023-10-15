<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\ParserOrder;

use DummieTrading\TelegramChannel;
use DummieTrading\SignalProvider;
use DummieTrading\TelegramApi;
use DummieTrading\IpnTelegram;

use Exception;

class TelegramMessage extends Orm {
  protected $tblName = 'telegram_message';

  public function __construct() {
    parent::__construct();
  }
  
  public static function add(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $TelegramMessage = new self;
      $TelegramMessage->connection()->stmtQuery("SET NAMES utf8mb4");
      $TelegramMessage->catalog_trading_account_id = isset($data['catalog_trading_account_id']) ? $data['catalog_trading_account_id'] : 1;
      $TelegramMessage->signal_provider_id = $data['signal_provider_id'];
      $TelegramMessage->message = $data['message'];
      $TelegramMessage->message_id = $data['message_id'] ?? 0; 
      $TelegramMessage->data = $data['data'] ?? ''; 
      $TelegramMessage->create_date = time();

      return $TelegramMessage->save();
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
  
  public function getByMessageId(int $message_id = null) 
  {
    if(isset($message_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.data,
                {$this->tblName}.catalog_trading_account_id,
                {$this->tblName}.create_date,
                {$this->tblName}.message
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.message_id = '{$message_id}'
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

  public static function sendSignalToUser(array $data = null)
  {
    require_once TO_ROOT . '/vendor/autoload.php';

    if(isset($data['signal']))
    {
        if(isset($data['telegram_api_id']))
        {
            if($signalProvider = (new SignalProvider)->get($data['signal_provider_id']))
            {
                $data['signal'] = [
                    ...['provider' => "{$signalProvider['name']}\n"],
                    ...$data['signal']
                ];

                $data['message'] = ParserOrder::parse($data['signal']);

                $TelegramApi = new TelegramApi;
                
                if($api = $TelegramApi->get($data['telegram_api_id']))
                {
                    require_once TO_ROOT . '/vendor/autoload.php';
                    
                    try {
                        $telegram = new \Longman\TelegramBot\Telegram($api['api_key'],$api['user_name']);
                        
                        $result = \Longman\TelegramBot\Request::sendMessage([
                            'chat_id' => $data['chat_id'],
                            'text' => $data['message'],
                        ]);
    
                        if($result->ok == 1)
                        {
                            TelegramMessage::add([
                                'signal_provider_id' => $data['signal_provider_id'],
                                'message_id' => $result->result->message_id,
                                'catalog_trading_account_id' => $data['signal']['market_type'],
                                'message' => $data['message'],
                                'data' => json_encode($data['signal']),
                            ]);
                            
                            return true;
                        } else {
                            $data["result"] = $result;
                            throw new Exception('NOT_RESULT');
                        } 
                    } catch (\Longman\TelegramBot\Exception\TelegramException $e) {
                        IpnTelegram::add(["response"=>$e->getMessage()]);
                    }
                } else {
                  throw new Exception('NOT_API');
                } 
            } else {
              throw new Exception('NOT_SIGNALPROVIDER');
            } 
        } else {
          throw new Exception('NOT_TELEGRAM_API_ID');
        }    
    } else {
      throw new Exception('NOT_SIGNAL');
    }  
  }
}