<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\TelegramChannel;
use DummieTrading\SignalProvider;

class UserSignalProvider extends Orm
{
  protected $tblName = 'user_signal_provider';

  const UNFOLLOWING = 0;
  const FOLLOWING = 1;
  public function __construct()
  {
    parent::__construct();
  }

  public static function toggleFollowSignal(array $data = null) : bool
  {
    $UserSignalProvider = new self;
    $UserSignalProvider->loadWhere('signal_provider_id = ? AND user_login_id = ?',[$data['signal_provider_id'],$data['user_login_id']]);

    if(!$UserSignalProvider->getId())
    {
        $UserSignalProvider->signal_provider_id = $data['signal_provider_id'];
        $UserSignalProvider->user_login_id = $data['user_login_id'];
        $UserSignalProvider->create_date = time();
    }
    
    $UserSignalProvider->status = $data['status'];

    return $UserSignalProvider->save();
  }

  public static function unFollowSignal(array $data = null) : bool
  {
    $data['status'] = self::UNFOLLOWING;

    return self::toggleFollowSignal($data);
  }

  public static function followSignal(array $data = null) : bool
  {
    $data['status'] = self::FOLLOWING;

    return self::toggleFollowSignal($data);
  }

  public function getAllFollowing(int $signal_provider_id = null) : array|bool
  {
    if (isset($signal_provider_id) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                user_telegram.user_login_id,
                user_telegram.user_telegram_id,
                user_telegram.chat_id
              FROM 
                {$this->tblName}
              LEFT JOIN 
                user_telegram 
              ON 
                user_telegram.user_login_id = {$this->tblName}.user_login_id
              WHERE 
                {$this->tblName}.signal_provider_id = '{$signal_provider_id}'
              AND 
                {$this->tblName}.status = '".self::FOLLOWING."'
              ";

      return $this->connection()->rows($sql);
    }

    return false;
  }

  public function isFollowing(int $user_login_id = null,int $signal_provider_id = null) : bool
  {
    if (isset($user_login_id,$signal_provider_id) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.signal_provider_id = '{$signal_provider_id}'
              AND 
                {$this->tblName}.status = '".self::FOLLOWING."'
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getByName(string $name = null)
  {
    if (isset($name) === true) {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.name
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.name = '{$name}'
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
              {$this->tblName}.name
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.status = '1'
            ";

    return $this->connection()->rows($sql);
  }

  public function getCount(int $signal_provider_id = null) : int
  {
    if(isset($signal_provider_id))
    {
      $sql = "SELECT
                COUNT({$this->tblName}.{$this->tblName}_id) as c
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.signal_provider_id = '{$signal_provider_id}'
              AND
                {$this->tblName}.status = '1'
              ";
  
      return $this->connection()->field($sql);
    }

    return 0;
  }

  public function isSuscribed(int $user_login_id = null,int $signal_provider_id = null) : bool
  {
    if($user_trading_account_ids = (new UserTradingAccount)->getAllAccountsFromUserIds($user_login_id))
    {
      return (new SignalProviderSuscriptor)->isSuscribedIn(implode(",",$user_trading_account_ids),$signal_provider_id);
    }

    return false;
  }

	public static function applyFilterByCatalogCampaignId(array $packages = null,int $catalog_campaign_id = null) : array
	{
		if(isset($packages) && is_array($packages) && !empty($packages))
		{
			return array_values(array_filter($packages, function($package) use($catalog_campaign_id) {
				$catalog_campaign_ids = json_decode($package['catalog_campaign_ids'],true);

				return in_array($catalog_campaign_id,$catalog_campaign_ids);
			}));
		}

		return [];
	}

  public function getAllList(int $user_login_id = null,int $catalog_signal_provider_id = null) : array|bool
  {
    if(isset($user_login_id))
    {
        if($signal_providers = (new SignalProvider)->getAllList($catalog_signal_provider_id))
        {
          $UserSignalProvider = new UserSignalProvider;
            return array_map(function($signal_provider) use($user_login_id,$UserSignalProvider) {
                $signal_provider['isFollowing'] = $this->isFollowing($user_login_id,$signal_provider['signal_provider_id']);
                $signal_provider['isSuscribed'] = $this->isSuscribed($user_login_id,$signal_provider['signal_provider_id']);
                $signal_provider['followers'] = $UserSignalProvider->getCount($signal_provider['signal_provider_id']);

                return $signal_provider;
            },$signal_providers);
        }
    }

    return false;
  }
}
