<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\TelegramChannel;

class ChannelPerUser extends Orm {
  protected $tblName  = 'channel_per_user';

  public function __construct() {
    parent::__construct();
  }
  
  public function getAll(int $user_login_id = null) : array|bool
  {
    if(isset($user_login_id) === true)
    {
      if($channels = $this->_getAll($user_login_id))
      {
        $TelegramChannel = new TelegramChannel;
        $SignalProvider = new SignalProvider;
        $UserSignalProvider = new UserSignalProvider;
        
        return array_map(function($channel) use($TelegramChannel,$SignalProvider,$UserSignalProvider){
          $channel['telegram_channel'] = $TelegramChannel->getSingle($channel['telegram_channel_id']);
          
          if($channel['telegram_channel'])
          {
            if($signalProvier = $SignalProvider->get($channel['telegram_channel']['telegram_api_id']))
            {
              $channel['followers'] = $UserSignalProvider->getCount($signalProvier['signal_provider_id']);
            }
          }

          return $channel;
        }, $channels);
      }
    }

    return false;
  }
  
  public function _getAll(int $user_login_id = null) : array|bool
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                channel.telegram_channel_id,
                channel.name
              FROM 
                {$this->tblName}
              LEFT JOIN 
                channel
              ON 
                channel.channel_id = {$this->tblName}.channel_id
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->rows($sql);
    }

    return false;
  }
}