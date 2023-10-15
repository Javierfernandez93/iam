<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

use DummieTrading\Package;
use DummieTrading\GainPerUser;
use DummieTrading\UserContact;

class UserTradingAccount extends Orm {
  protected $tblName  = 'user_trading_account';
  const ACELERATED = 1;

  const DELETED = -1;
  const WAITING = 0;
  const IN_PROGRESS = 1;
  const FINISH = 2;
  const EXPIRED = 3;
  const DECLINED = 4;
  const CANCELED_BY_EA = 5;
  
  const DEFAULT_INITIAL_DRAWDOWN = 5;

  const DEMO = 1;
  const LIVE = 0;

  const DEFAULT_ADDITIONAL_DATA = [
    [
      'name' => 'Objetivo',
      'editable' => false,
      'value' => 0
    ],
    [
      'name' => 'Días para cumplir el objetivo',
      'editable' => false,
      'value' => 0
    ]
  ];

  public function __construct() {
    parent::__construct();
  }

  public static function getBinanceAlias() : string
  {
    return "Binance";
  }

  public static function setBinanceAlias(array $data = null) : bool
  {
    return self::changeAccountAlias([
      'user_trading_account_id' => $data['user_trading_account_id'],
      'alias' => self::getBinanceAlias()
    ]);
  }

  public static function deleteAccount(string $user_trading_account_id = null) : bool
  {
    $UserTradingAccount = new self;

    if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$user_trading_account_id))
    {
      $UserTradingAccount->status = self::DELETED;
      
      return $UserTradingAccount->save();
    }

    return false;
  }

  public static function disconnectFromMetaTrader(string $id = null) : bool
  {
    $UserTradingAccount = new self;

    if($UserTradingAccount->loadWhere("id = ?",$id))
    {
      $UserTradingAccount->id = '';
      
      return $UserTradingAccount->save();
    }

    return false;
  }

  public static function watchDrawDown(int $user_trading_account_id = null) : bool
  {
    $UserTradingAccount = new self;

    if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$user_trading_account_id))
    {
      $UserTradingAccount->watch_drawdown = 1;
      
      return $UserTradingAccount->save();
    }

    return false;
  }

  public static function appendListenerId(array $data = null) : bool
  {
    $UserTradingAccount = new self;

    if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$data['user_trading_account_id']))
    {
      $UserTradingAccount->listener_id = $data['listener_id'];
      
      return $UserTradingAccount->save();
    }

    return false;
  }

  public static function changeAccountAlias(array $data = null) : bool
  {
    $UserTradingAccount = new self;

    if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$data['user_trading_account_id']))
    {
      $UserTradingAccount->alias = $data['alias'];
      return $UserTradingAccount->save();
    }

    return false;
  }

  public static function updateData(array $data = null) : bool
  {
    $UserTradingAccount = new self;

    if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$data['user_trading_account_id']))
    {
      if($UserTradingAccount->balance !=  $data['balance'] || $UserTradingAccount->equity != $data['equity'] || !$UserTradingAccount->initial_balance) 
      {
        $UserTradingAccount->balance = $data['balance'];
        $UserTradingAccount->equity = $data['equity'];
        $UserTradingAccount->leverage = $data['leverage'];
        $UserTradingAccount->drawdown = $data['drawdown'] ?? $UserTradingAccount->drawdown;

        if(!$UserTradingAccount->initial_balance)
        {
          $UserTradingAccount->initial_balance = $data['balance'];
        }
  
        return $UserTradingAccount->save();
      }
    }

    return false;
  }

  public static function attachIdToAccount(array $data = null) : bool
  {
    $UserTradingAccount = new self;
    
    if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$data['user_trading_account_id']))
    {
      $UserTradingAccount->id = $data['id'];
      
      return $UserTradingAccount->save();
    }

    return false;
  }

  public static function setAccountAsDrawdownReached(int $user_trading_account_id = null) : bool
  {
    $UserTradingAccount = new self;
    
    if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$user_trading_account_id))
    {
      $UserTradingAccount->status = self::CANCELED_BY_EA;
      
      if($UserTradingAccount->save())
      {
        return self::addComment($user_trading_account_id,[
          'drawdownReached' => true
        ]);
      }
    }

    return false;
  }

  public static function addComment(int $user_trading_account_id = null,array $comment = null) : bool
  {
    $UserTradingAccount = new self;

    if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$user_trading_account_id))
    {
      $UserTradingAccount->comment = json_encode($comment);
      
      return $UserTradingAccount->save();
    }

    return false;
  }

  public static function sanitizeAdditionalData(array $additional_data = null) : array
  {
    return array_filter($additional_data,function($data){
      return $data['name'] && $data['value'];
    });
  }

  public static function updateAdditionalData(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $UserTradingAccount = new self;
      
      if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$data['user_trading_account_id']))
      {
        $UserTradingAccount->additional_data = json_encode(self::sanitizeAdditionalData($data['additional_data']));
        $UserTradingAccount->drawdown = $data['drawdown'];
        $UserTradingAccount->balance = $data['balance'];
 
        return $UserTradingAccount->save();
      }
    }

    return false;
  }

  public static function updateBalance(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $UserTradingAccount = new self;
      
      if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$data['user_trading_account_id']))
      {
        $UserTradingAccount->balance = $data['balance'];
        
        return $UserTradingAccount->save();
      }
    }

    return false;
  }
  
  public static function updateDrawdown(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $UserTradingAccount = new self;
      
      if($UserTradingAccount->loadWhere("user_trading_account_id = ?",$data['user_trading_account_id']))
      {
        $UserTradingAccount->drawdown = abs($data['drawdown']);
        
        return $UserTradingAccount->save();
      }
    }

    return false;
  }

  public function _getAllForDisperseGains() 
  {
    if($users = $this->getAll("'".self::IN_PROGRESS."'"))
    {
      $UserData = new UserData;
      $GainPerUser = new GainPerUser;
      
      return array_map(function($user) use($UserData,$GainPerUser) {
        $user['names'] = $UserData->getNames($user['user_login_id']);
        $user['hasGain'] = $GainPerUser->hasGainOnWeek($user['user_login_id']);
        
        if($user['hasGain'])
        {
          $user['gain'] = $GainPerUser->getGainOnWeek($user['user_login_id']);
        }

        return $user;
      },$users);
    }

    return false;
  }

  public function getAllWithGains(string $statusIn = null,int $demo = null) 
  {
    if($users = $this->getAll($statusIn,$demo))
    {
      $GainPerUser = new GainPerUser;
      $UserData = new UserData;
      $UserLogin = new UserLogin(false,false);

      return array_map(function($user) use($UserData,$UserLogin,$GainPerUser) {
        $user['names'] = $UserData->getNames($user['user_login_id']);
        $user['emailUser'] = $UserLogin->getEmail($user['user_login_id']);
        $user['hasGain'] = false;

        if($user['hasGain'] = $GainPerUser->hasGainOnWeek($user['user_login_id']))
        {
          $user['gain'] = $GainPerUser->getGainOnWeek($user['user_login_id']);
        }

        return $user;
      },$users);
    }

    return false;
  }

  public function _getAll(string $statusIn = null,int $demo = null) 
  {
    if($users = $this->getAll($statusIn,$demo))
    {
      $Package = new Package;
      $UserData = new UserData;
      $UserLogin = new UserLogin(false,false);

      return array_map(function($user) use($UserData,$UserLogin,$Package) {
        $user['names'] = $UserData->getNames($user['user_login_id']);
        $user['emailUser'] = $UserLogin->getEmail($user['user_login_id']);

        if($user['acelerated'])
        {
          if($user['package_id'])
          {
            $user['package_acelerated'] = $Package->_getPackage($user['package_id']);
          }
        }

        return $user;
      },$users);
    }

    return false;
  }
   
  public function getTradingAccount(int $user_trading_account_id = null,int $demo = null) 
  {
    if(isset($user_trading_account_id) == true)
    {
      if($account = $this->_getTradingAccount($user_trading_account_id,$demo))
      {
        $account['names'] = (new UserData)->getNames($account['user_login_id']);

        if($additional_data = json_decode($account['additional_data'],true))
        {
          $account['additional_data'] = array_map(function($data){
            $data['editable'] = filter_var($data['editable'], FILTER_VALIDATE_BOOL);
            return $data;
          },$additional_data);
        } else {
          $account['additional_data'] = self::DEFAULT_ADDITIONAL_DATA;
        }

        return $account;
      }
    }

    return false;
  }

  public function getAll(string $statusIn = null,int $demo = null) 
  {
    $filter = isset($statusIn) ? "WHERE {$this->tblName}.status IN({$statusIn})" : '';

    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.user_login_id,
              {$this->tblName}.login,
              {$this->tblName}.password,
              {$this->tblName}.server,
              {$this->tblName}.trader,
              {$this->tblName}.drawdown,
              {$this->tblName}.balance,
              {$this->tblName}.acelerated,
              {$this->tblName}.package_id,
              {$this->tblName}.create_date,
              {$this->tblName}.demo,
              {$this->tblName}.status
            FROM
              {$this->tblName}
              {$filter}
            AND 
              {$this->tblName}.demo = '{$demo}'
            ";
            
    return $this->connection()->rows($sql);
  }
  
  public function getAllAccountsFromUserIds(int $user_login_id = null) 
  {
    if(isset($user_login_id))
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status != '".Constants::DELETE."'
              ";
              
      return $this->connection()->column($sql);
    }

    return false;
  }

  public function getAllAccountsFromUser(int $user_login_id = null,string $filter = null) 
  {
    if(isset($user_login_id))
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.login,
                {$this->tblName}.password,
                {$this->tblName}.server,
                {$this->tblName}.watch_drawdown,
                {$this->tblName}.alias,
                {$this->tblName}.trader,
                {$this->tblName}.id,
                {$this->tblName}.acelerated,
                {$this->tblName}.drawdown,
                {$this->tblName}.comment,
                {$this->tblName}.catalog_platform_id,
                {$this->tblName}.initial_drawdown,
                {$this->tblName}.follow,
                {$this->tblName}.initial_balance,
                {$this->tblName}.balance,
                {$this->tblName}.package_id,
                {$this->tblName}.additional_data,
                {$this->tblName}.create_date,
                {$this->tblName}.status,
                catalog_platform.type,
                catalog_trading_account.type as account_type
              FROM
                {$this->tblName}
              LEFT JOIN
                catalog_trading_account
              ON 
                catalog_trading_account.catalog_trading_account_id = {$this->tblName}.catalog_trading_account_id
              LEFT JOIN
                catalog_platform
              ON 
                catalog_platform.catalog_platform_id = {$this->tblName}.catalog_platform_id
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status != '".Constants::DELETE."'
                {$filter}
              GROUP BY 
                {$this->tblName}.{$this->tblName}_id
              ";
              
      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public function _getTradingAccount(int $user_trading_account_id = null,int $demo = null) 
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.login,
                {$this->tblName}.password,
                {$this->tblName}.server,
                {$this->tblName}.trader,
                {$this->tblName}.drawdown,
                {$this->tblName}.initial_drawdown,
                {$this->tblName}.initial_balance,
                {$this->tblName}.balance,
                {$this->tblName}.additional_data,
                {$this->tblName}.create_date,
                {$this->tblName}.status
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              AND 
                {$this->tblName}.demo = '{$demo}'
              AND 
                {$this->tblName}.status = '".self::IN_PROGRESS."'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function isAccountInProgress(int $user_trading_account_id = null) : bool
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              AND 
                {$this->tblName}.status = '".self::IN_PROGRESS."'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }
  
  public function getTradingAccountLogin(int $user_trading_account_id = null,int $demo = null) 
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.login,
                {$this->tblName}.password
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              AND 
                {$this->tblName}.demo = '{$demo}'
              AND 
                {$this->tblName}.status = '".self::IN_PROGRESS."'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getSimpleTradingAccount(int $user_trading_account_id = null) 
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.id,
                {$this->tblName}.initial_drawdown,
                {$this->tblName}.drawdown,
                {$this->tblName}.login,
                {$this->tblName}.password,
                {$this->tblName}.equity,
                {$this->tblName}.balance,
                {$this->tblName}.initial_balance
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getBroker(int $user_trading_account_id = null) 
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.server
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
  
  public static function addAceleratedAccount(array $data = null) 
  {
    $UserTradingAccount = new UserTradingAccount;
    
    $UserTradingAccount->user_login_id = $data['user_login_id'];
    $UserTradingAccount->acelerated = self::ACELERATED;
    $UserTradingAccount->package_id = $data['package_id'];
    $UserTradingAccount->status = self::WAITING;
    $UserTradingAccount->create_date = time();
    
    return $UserTradingAccount->save();
  }

  public static function add(array $data = null) : int|bool
  {
    if(isset($data) === true)
    { 
      // if(!(new UserTradingAccount)->existByLogin($data['login']) || isset($data['user_trading_account_id']))
      if(isset($data))
      {
        $UserTradingAccount = new UserTradingAccount;
        
        if(isset($data['user_trading_account_id']))
        {
          $UserTradingAccount->loadWhere("user_trading_account_id = ?", $data['user_trading_account_id']);
        } else if(isset($data['login'],$data['user_login_id'])) {
          $UserTradingAccount->loadWhere("user_login_id = ? AND login = ?", [$data['user_login_id'],$data['login']]);
        }

        $UserTradingAccount->user_login_id = $data['user_login_id'];
        $UserTradingAccount->login = $data['login'];
        $UserTradingAccount->password = $data['password'];
        $UserTradingAccount->initial_drawdown = $data['initial_drawdown'] ?? self::DEFAULT_INITIAL_DRAWDOWN;
        $UserTradingAccount->catalog_trading_account_id = $data['catalog_trading_account_id'] ?? 0;
        $UserTradingAccount->initial_balance = $data['initial_balance'] ?? 0;;
        $UserTradingAccount->catalog_platform_id = isset($data['catalog_platform_id']) ? $data['catalog_platform_id'] : 0;
        $UserTradingAccount->server = isset($data['server']) ? $data['server'] : '';
        $UserTradingAccount->trader = isset($data['trader']) ? $data['trader'] : '';
        $UserTradingAccount->follow = $UserTradingAccount->getCountAccounts($data['user_login_id']) == 0 ? 1 : 0;
        $UserTradingAccount->create_date = time();
        $UserTradingAccount->status = self::IN_PROGRESS;
        
        if($UserTradingAccount->save())
        {
          if(isset($data['sendCredentials']))
          {
            return self::sendCredentials($data);
          } else {
            return $UserTradingAccount->getId();
          }
        }
      }
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
                {$this->tblName}.status != '".Constants::DELETE."'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function existByLogin(string $login = null) : bool 
  {
    if(isset($login) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.login
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.login = '{$login}'
              AND
                {$this->tblName}.status != '".Constants::DELETE."'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getIdByUserName(string $login = null) : bool|int
  {
    if(isset($login) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.login = '{$login}'
              AND
                {$this->tblName}.status != '".Constants::DELETE."'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
  
  public function getUserLoginIdByUserName(int $user_trading_account_id = null) : bool|int
  {
    if(isset($user_trading_account_id) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.user_login_id
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              AND
                {$this->tblName}.status != '".Constants::DELETE."'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function hasAccountStatus(int $user_login_id = null,string $statusIn = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $filter = $statusIn ? "AND {$this->tblName}.status IN({$statusIn})" : '';

      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
                {$filter}
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public static function setTraddingAccountAs(int $user_trading_account_id = null,int $status = null) : bool
  {
    if(isset($user_trading_account_id,$status) == true)
    {
      $UserTradingAccount = new UserTradingAccount;

      if($UserTradingAccount->loadWhere('user_trading_account_id = ?',$user_trading_account_id))
      {
        $UserTradingAccount->status = $status;
        
        return $UserTradingAccount->save();
      }
    }

    return false;
  }

  public static function sendWhatsAppActivation(array $data = null) 
  {
    return ApiWhatsApp::sendWhatsAppMessage([
        'message' => ApiWhatsAppMessages::getUserPendingActivationMessage(),
        'image' => null,
        'contact' => [
            "phone" => (new UserContact)->getWhatsApp($data['user_login_id']),
            "name" => $data['names'] ?? 'Miembro de DummieTrading',
        ]
    ]);
  }

  public static function sendCredentials(array $data = null) 
  {
    return ApiWhatsApp::sendWhatsAppMessage([
        'message' => ApiWhatsAppMessages::getUserTradingCredentialsMessage(),
        'image' => null,
        'contact' => [
            "phone" => (new UserContact)->getWhatsApp($data['user_login_id']),
            "name" => (new UserData)->getName($data['user_login_id']),
            "login" => $data['login'] ?? 'Error',
            "client_password" => $data['password'] ?? 'Error',
            "trader" => $data['trader'] ?? 'Error',
            "server" => $data['server'] ?? 'Error'
        ]
    ]);
  }

  public function _getAllAccountsFromUser(int $user_login_id = null,string $filter = null) 
  {
    if(isset($user_login_id))
    {
      if($accounts = $this->getAllAccountsFromUser($user_login_id,$filter))
      {
        return array_map(function($account){
          if($additional_data = json_decode($account['additional_data'],true))
          {
            $account['additional_data'] = array_map(function($data){
              $data['editable'] = filter_var($data['editable'], FILTER_VALIDATE_BOOL);
              return $data;
            },$additional_data);
          } else {
            $account['additional_data'] = self::DEFAULT_ADDITIONAL_DATA;
          }

          if($comment = json_decode($account['comment'],true))
          {
            $account['comment'] = $comment;
          } 
          
          return $account;
        },$accounts);
      }
    }

    return false;
  }

  public function getBalance(int $user_trading_account_id = null) : float
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.balance
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }
 
  public function getBalances(int $user_trading_account_id = null) : float|array
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.balance,
                {$this->tblName}.equity
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ";
              
      return $this->connection()->row($sql);
    }

    return 0;
  }
  
  public function getInitialBalance(int $user_trading_account_id = null) : float
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.initial_balance
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }
  
  public function getIdByLogin(string $login = null) : float
  {
    if(isset($login) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.login = '{$login}'
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }
  
  public function getDrawdown(int $user_trading_account_id = null) : float
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.drawdown
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }
  
  public function getDrawdowns(int $user_trading_account_id = null) : bool|array
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.drawdown,
                {$this->tblName}.initial_drawdown
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getLastTradingAccount(int $user_login_id = null) : int
  {
    if(isset($user_login_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.user_trading_account_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }

  public function getTradingAccountData(int $user_login_id = null) : array|bool
  {
    if(isset($user_login_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.login,
                {$this->tblName}.password,
                {$this->tblName}.alias,
                {$this->tblName}.server
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND
                {$this->tblName}.status != '-1'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public function getLastTradingAccountData(int $user_login_id = null) : array
  {
    if(isset($user_login_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.login,
                {$this->tblName}.password,
                {$this->tblName}.alias,
                {$this->tblName}.server
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND
                {$this->tblName}.status != '-1'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->row($sql);
    }

    return 0;
  }
  
  public function getTradingAccountFollowing(int $user_login_id = null,int $catalog_trading_account_id = null) : int
  {
    if(isset($user_login_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.user_trading_account_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.catalog_trading_account_id = '{$catalog_trading_account_id}'
              AND 
                {$this->tblName}.follow = '1'
              AND 
                {$this->tblName}.status = '1'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }
  
  public function getTradingAccountFollowingLogin(int $user_login_id = null) : int
  {
    if(isset($user_login_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.login
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.follow = '1'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }

  public function getByLogin(string $login = null) : bool|array
  {
    if(isset($login) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.login,
                {$this->tblName}.drawdown,
                {$this->tblName}.initial_drawdown,
                {$this->tblName}.status
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.login = '{$login}'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }

  public static function unfollowAll(int $user_login_id = null)
  {
    $UserTradingAccount = new self;
    
    if($accounts = $UserTradingAccount->getToUnfollowAll($user_login_id))
    {
      foreach($accounts as $user_trading_account_id)
      {
        if($UserTradingAccount->loadWhere("user_trading_account_id = ?",[$user_trading_account_id]))
        {
          $UserTradingAccount->follow = 0;
          $UserTradingAccount->save();
        }
      }
    }
  }
  
  public function getToUnfollowAll(int $user_login_id = null)
  {
    if(isset($user_login_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
              
      return $this->connection()->column($sql);
    }

    return false;
  }

  public static function follow(array $data = null)
  {
    $UserTelegram = new UserTelegram;

    if($user_login_id = $UserTelegram->getUserId($data['chat_id']))
    {
      self::unfollowAll($user_login_id);

      $UserTradingAccount = new self;
      
      if($UserTradingAccount->loadWhere("user_login_id = ? AND login = ?",[$user_login_id,$data['login']]))
      {
        $UserTradingAccount->follow = 1;

        return $UserTradingAccount->save();
      }
    }

    return false;
  }

  public function getAccountById(int $user_trading_account_id = null) : string
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.login
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              AND 
                {$this->tblName}.status != '-1'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->field($sql);
    }

    return 0;
  }
  
  public function getIdById(int $user_trading_account_id = null) : string
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->field($sql);
    }
  }

  public function getUserIdById(int $user_trading_account_id = null) : string
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.user_login_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->field($sql);
    }
  }

  public function getStrategyId(int $user_trading_account_id = null) : string
  {
    if(isset($user_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.strategy_id 
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->field($sql);
    }
  }

  public function getAccountFilterByCatalogTradingAccount(int $user_login_id = null,int $catalog_trading_account_id = null) : array|bool
  {
    if(isset($user_login_id,$catalog_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.id,
                {$this->tblName}.login,
                {$this->tblName}.password,
                {$this->tblName}.alias
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.catalog_trading_account_id = '{$catalog_trading_account_id}'
              AND 
                {$this->tblName}.status = '1'
              ORDER BY 
                {$this->tblName}.follow 
              DESC 
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getAccountFilterByCatalogTradingAccounts(int $catalog_trading_account_id = null) : array|bool
  {
    if(isset($catalog_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.id,
                {$this->tblName}.balance,
                {$this->tblName}.login,
                {$this->tblName}.password,
                {$this->tblName}.alias
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.catalog_trading_account_id = '{$catalog_trading_account_id}'
              AND 
                {$this->tblName}.status = '1'
              ";
              
      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public function getAccountFilterByCatalogTradingAccountsWatchingDrawDown(int $catalog_trading_account_id = null) : array|bool
  {
    if(isset($catalog_trading_account_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.id,
                {$this->tblName}.balance,
                {$this->tblName}.login,
                {$this->tblName}.password,
                {$this->tblName}.alias
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.catalog_trading_account_id = '{$catalog_trading_account_id}'
              AND 
                {$this->tblName}.watch_drawdown = '1'
              AND 
                {$this->tblName}.status = '1'
              ";
              
      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public function getCountAccounts(int $user_login_id = null) : int
  {
    if(isset($user_login_id) === true)
    { 
      $sql = "SELECT
                COUNT({$this->tblName}.{$this->tblName}_id) as c
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              ";
              
      if($count = $this->connection()->field($sql))
      {
        return $count;
      }
    }

    return 0;
  }

  public function isOwner(int $user_trading_account_id = null,int $user_login_id = null) : bool
  {
    if(isset($user_trading_account_id,$user_login_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.user_trading_account_id = '{$user_trading_account_id}'
              AND 
                {$this->tblName}.status = '1'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function searchAccountByLoginOrAlias(string $words = null,int $user_login_id = null) : string
  {
    if(isset($words,$user_login_id) === true)
    { 
      $sql = "SELECT
                {$this->tblName}.login
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                (
                  {$this->tblName}.alias IN({$words})
                  OR 
                  {$this->tblName}.login IN({$words})
                )
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public static function getFixedSymbol(int $user_trading_account_id = null,string $symbol = null) 
  {
    if(isset($user_trading_account_id,$symbol) === true)
    { 
      if($broker = (new self)->getBroker($user_trading_account_id))
      {
        return SymbolParserPerBroker::sanitize($broker,$symbol);
      }
    }

    return false;
  }
}