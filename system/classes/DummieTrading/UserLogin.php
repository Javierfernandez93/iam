<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Session;
use HCStudio\Token;
use HCStudio\Util;
use HCStudio\Connection;

use JFStudio\Cookie;
use JFStudio\ApiBinance;

use World\Country;

use DummieTrading\AdviceType;
use DummieTrading\BuyPerUser;
use DummieTrading\UserPlan;
use DummieTrading\Exercise;
use DummieTrading\UserVar;
use DummieTrading\UserTradingAccount;
use DummieTrading\TransactionRequirementPerUser;

class UserLogin extends Orm {
  protected $tblName  = 'user_login';
  private $Session = false;
  private $Token   = false;
  
  public $_data = [];
  public $_parent = [];
  public $_parent_id = false;
  public $save_class = false;
  public $logged  = false;

  const SALT_LENGHT = 5;
  const PASSWORD_GENERATOR_LENGHT = 5;
  const DEFAULT_FIELD_SESSION = 'email';
  const PID_NAME = 'pidUser';

  private $field_control = 'password';
  private $field_session = 'email';
  private $_field_type = 'email';

  const FREE = 0;
  const DEMO = 1;
  const TRADING = 2;
  const DELETE = -1;

  /* signup */
  const SIGNUP_DAYS = 7;
  const REFERRAL_PATH = 'apps/signup/?uid=';

  public function __construct(bool $save_class = false,bool $autoLoad = true,bool $redir = true) {
    parent::__construct();
    
    $this->save_class = $save_class;
    $this->Session = new Session($this->tblName);
    $this->Token = new Token;

    if($autoLoad === true)
    {
      if($this->logoutRequest()) return false;

      if($this->loginRequest())
      {
        $this->login();
      } else if($this->hasPid()) {
        if($this->isValidPid())
        {
          $this->login($this->Token->params[$this->field_session],$this->Token->params[$this->field_control]);
        }
      } else if($this->hasPidRequest() === true) {
        $this->loginWithPid($_GET[self::PID_NAME]);
      } else if($this->hasPidRequestCookie() === true) {
        $this->loginWithPid(Cookie::get(self::PID_NAME));
      }
    }
  }
  
  public function hasPidRequestCookie() : bool
  {
    return Cookie::get(self::PID_NAME) ? true : false;
  }

  public function hasPidRequest()
  {
    return isset($_GET[self::PID_NAME]) === true && is_array($_GET[self::PID_NAME]) ? true : false;
  }
  
  public function loginWithPid(array $pid = null)
  {
    if($this->Token->checkToken($pid) == true)
    {
      $this->login($this->Token->params[$this->field_session], $this->Token->params['password']);
    }
  }

  public function hasPermission(string $permission = null) : bool
  {
    if($this->logged === true)
    {
      if(isset($permission) === true)
      {
        return (new PermissionPerUserSupport)->_hasPermission($this->getId(),$permission);
      }
    }
  }
  
  public function hasPid() : bool {
    return $this->Session->get(self::PID_NAME) ? true : false;
  }

  public function setFieldSession(string $field_session = null) {
    $this->field_session = $field_session ?? self::DEFAULT_FIELD_SESSION;
  }

  public function getFieldSession() {
    return ['fieldsession'=>$this->field_session,'field_type'=>$this->_field_type];
  }

  public function logoutRequest() {
    $logout = Util::getVarFromPGS('logout');

    if($logout) {
      if($this->hasPidRequestCookie())
      {
        Cookie::destroy(self::PID_NAME);
      }

      return $this->logout();
    }
  }

  public function deleteSession() {
    $this->Session->destroy();
  }

  public function isAbiableToSingUp() : bool
  {
    if(!$this->logged)
      if($this->getId() === 0)
        if(!$this->_data && !$this->_parent)
          return true;

    return false;
  }

  public function getDataForSignupExternal() : array
  {
    if($this->logged === true)
    {
      return [
        'email' => $this->email,
        'password' => $this->password,
        'names' => $this->_data['user_data']['names'],
        'image' => $this->_data['user_account']['image'],
        'phone' => $this->_data['user_contact']['phone'],
        'country_id' => $this->_data['user_address']['country_id'],
      ];
    }

    return [];
  }

  public function getCountryId()
  {
    if($this->getId())
    {
      return $this->_data['user_address']['country_id'] ? $this->_data['user_address']['country_id'] : 159;// default mx
    }
  }
  
  public function logout(bool $reload = true) 
  {
    $this->deleteSession();

    if($reload) 
      header("Refresh: 0;url=./index.php");
  }

  /* starts security */
  public function getPassForUser() {
    return $this->isAbiableSalt((new Token())->randomKey(10));
  }

  private function getUniqueSalt() {
    if($salt = $this->isAbiableSalt((new Token())->randomKey(self::SALT_LENGHT))) return $salt;

    $this->getUniqueSalt();
  }

  private function isAbiableSalt($salt) {
    $sql = "SELECT {$this->tblName}.salt FROM {$this->tblName} WHERE {$this->tblName}.salt = '{$salt}'";

    if($this->connection()->field($sql)) return false;

    return $salt;
  }

  public function isUserOnline($company_id = false) {
    if($company_id)
    {
      $sql = "SELECT {$this->tblName}.last_login_date FROM {$this->tblName} WHERE {$this->tblName}.company_id = '{$company_id}'";
      return $this->isOnline($this->connection()->field($sql));
    }

    return false;
  }

  public function isOnline(int $last_login_date = null) : bool
  {
    return $last_login_date >= strtotime("-5 minutes");
  }

  private function needChangeControlData() {
    if(!$this->last_login_date) return true;

    return strtotime('+ '.$this->expiration_salt_date.' minutes',$this->last_login_date) < time();
  }

  public function renewSalt() : bool {
    return $this->setSalt(true);
  }

  private function setSalt(bool $force_to_set_salt = false) : bool {
    if($this->needChangeControlData() || $force_to_set_salt)
    {
      $this->salt = $this->getUniqueSalt();

      return $this->save();
    }

    return false;
  }

  private function saveControlData() 
  {
    if($this->needChangeControlData())
    {
      $this->ip_user_address = $_SERVER['REMOTE_ADDR'];
      $this->last_login_date = time();

      return $this->save();
    }

    return false;
  }

  private function doLogin() 
  {
    if($this->hasLogged())
    {
      if($this->setSalt(true))
      {
        if($this->setPid())
        {
          if($this->saveControlData())
          {
            if($this->loadProfile())
            {
              $this->logged = true;
            }
          }
        }
      }
    }

    return $this->logged;
  }

  public function login(string $field_session = null,string $field_control = null) 
  {
    $field_session = isset($field_session) ? $field_session : Util::getVarFromPGS($this->field_session,false);
    $field_control = isset($field_control) ? $field_control : sha1(Util::getVarFromPGS($this->field_control,false));
    
    if($this->loadWhere("{$this->field_session}=? AND {$this->field_control}=?",[$field_session,$field_control]))
    {
      return $this->doLogin();
    }
  }

  public function createPid()
  {
    return $this->Token->getToken([
      $this->field_session => $this->{$this->field_session},
      $this->field_control => $this->{$this->field_control},
      "securitySalt" => sha1($this->last_login . $this->ip_user_address . $this->salt),
    ],true,true);
  }

  public function loadDataByClassName($ClassName,$var)
  {
    if($ClassName && $var)
    {
      if(!isset($this->_data[$var]))
      {
        $_parent_id = ($this->_parent_id) ? $this->_parent_id : $this->getId();

        $Class = new $ClassName();
        $Class->loadWhere('user_login_id = ?',$_parent_id);

        if(!$Class->getId()) $Class->user_login_id = $_parent_id;

        $this->_data[$var] = $Class->atributos();

        if($this->save_class) {
          if(!$Class->getId()) $Class->user_login_id = $this->getId();

          $this->_parent[$var] = $Class;
        }

        return true;
      }
    }
    return false;
  }

  public function loadProfile() : bool
  {
    $this->loadDataByClassName(__NAMESPACE__.'\UserData','user_data');
    $this->loadDataByClassName(__NAMESPACE__.'\UserAddress','user_address');
    $this->loadDataByClassName(__NAMESPACE__.'\UserContact','user_contact');
    $this->loadDataByClassName(__NAMESPACE__.'\UserAccount','user_account');

    return true;
  }

  public function getUniqueToken($lenght = 5, $field = 'secret', $table = 'user_login', $field_as = 'total')
  {
    if($token = $this->Token->randomKey($lenght))
    {
      $sql = "SELECT count({$table}.{$field}) as {$field_as} FROM {$table} WHERE {$table}.{$field} = '{$token}'";

      if($this->connection()->field($sql)) $this->getUniqueToken();
      else return $token;
    }

    return false;
  }

  public function setPid() : bool {
    $this->Session->set(self::PID_NAME,$this->createPid());

    return true;
  }

  public function hasLogged() {
    return ($this->getId() == 0) ? false : true;
  }

  public function loginFacebookRequest() {
    if(isset($_GET['user_key']) || isset($_POST['user_key']))
      return true;

    return false;
  }

  public function loginRequest() {
    
    if(isset($_GET[$this->field_session]) || isset($_POST[$this->field_session]))
    {
      if(isset($_GET[$this->field_control]) || isset($_POST[$this->field_control])) {
        return true;
      }
    }

    return false;
  }

  public function isValidPid() {
    $pid = $this->Session->get(self::PID_NAME);  

    return ($this->Token->checkToken($pid)) ? true : false;
  }

  public function hasData($data)
  {
    if(is_array($data))
    {
      foreach ($data as $field)
        if(!isset($field) || empty($field)) return false;

    } else if(!$data || $data == "") return false;

    return true;
  }

  public static function redirectTo(string $route_name = null)
  {
    Util::redirectTo(TO_ROOT."/apps/login/",[
      'page' => Util::getCurrentURL(),
      'route_name' => $route_name
    ]);
  }

  public static function redirectToLogin()
  {
	  $url = Util::getCurrentURL();

    Util::redirectTo(TO_ROOT."/apps/login/?page={$url}");
  }

  function checkRedirection()
  {
    // @todo
  }

  public function getPid()
  {
    if(isset($this->Session)) {
      return $this->Session->get(self::PID_NAME);
    }

    return false;
  }

  public function isValidMail(string $email = null) {
    $sql = "
            SELECT 
              {$this->tblName}.email
            FROM 
              {$this->tblName}
            WHERE
              {$this->tblName}.email = '{$email}'
            ";

    return $this->connection()->field($sql) ? false : true;
  }
  
  public function isUniqueMail($email = false) {
    $sql = "SELECT 
              {$this->tblName}.email 
            FROM 
              {$this->tblName}
            WHERE 
              {$this->tblName}.email = '{$email}'
            AND  
              {$this->tblName}.status = '1'
            LIMIT 1
            ";
    return ($this->connection()->field($sql)) ? false : true;
  }

  public function doSignup(array $data = null) 
  {
    $UserLogin = new UserLogin(false,false);
    $UserLogin->email = $data['email'];

    if(isset($data['encrpyt']) && $data['encrpyt'] == false)
    {
      $UserLogin->password = $data['password'];
    } else {
      $UserLogin->password = sha1($data['password']);
    }

    $UserLogin->catalog_campaign_id = isset($data['catalog_campaign_id']) ? $data['catalog_campaign_id'] : 0;
    $UserLogin->signup_date = time();
    
    if($UserLogin->save())
    {
      $UserLogin->company_id = $UserLogin->getId();
      
      if($UserLogin->save())
      {
        $UserData = new UserData;
        $UserData->user_login_id = $UserLogin->company_id;
        $UserData->names = trim($data['names']);
        
        if($UserData->save())
        {
          $UserContact = new UserContact;
          $UserContact->user_login_id = $UserLogin->company_id;
          $UserContact->phone = $data['phone'];
          
          if($UserContact->save())
          {
            $UserAddress = new UserAddress;
            $UserAddress->user_login_id = $UserLogin->company_id;
            $UserAddress->address = '';
            $UserAddress->colony = '';
            $UserAddress->city = '';
            $UserAddress->state = '';
            $UserAddress->country = '';
            $UserAddress->country_id = $data['country_id'];
            
            if($UserAddress->save())
            {
              $UserAccount = new UserAccount;
              $UserAccount->user_login_id = $UserLogin->company_id;
              $UserAccount->landing = isset($data['user_account']['landing']) ? $data['user_account']['landing'] : '';
              $UserAccount->image = UserAccount::DEFAULT_IMAGE;

              if(isset($data['referral']))
              {
                $UserReferral = new UserReferral;
                $UserReferral->referral_id = $data['referral']['user_login_id'];
                $UserReferral->user_login_id = $UserLogin->company_id;
                $UserReferral->catalog_level_id = 0;
                $UserReferral->status = UserReferral::WAITING_FOR_PAYMENT;
                $UserReferral->create_date = time();
                $UserReferral->save();
              }

              if($UserAccount->save())
              {
                return $UserLogin->company_id;
              }
            }
          }
        }
      }
    }

    return false;
  }

  public function getEmail(int $user_login_id = null) 
  {
    if (isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.email
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
      return $this->connection()->field($sql); 
    }

    return false;
  }

  public function getFirsNameLetter() 
  {
    if ($this->getId()) 
    {
      return mb_substr((new UserData)->getNames($this->company_id), 0, 1);
    }
  }

  public function getNames() 
  {
    if ($this->getId()) 
    {
      return ucfirst((new UserData)->getNames($this->company_id));
    }
  }

  /* profile fun */  
  public function getPlan()
  {
    return (new UserPlan)->getPlan($this->company_id);
  }
  
  public function hasPlan() : bool
  {
    return (new UserPlan)->hasPlan($this->company_id);
  }

  public function hasCard() : bool
  { 
    return (new UserCard)->hasCard($this->company_id);
  }

  public function getLanding() : string 
  {
    if($this->getId())
    {
      return self::_getLanding($this->company_id);
    }
  }

  public static function _getLanding(int $user_login_id = null) : string 
  {
    if(isset($user_login_id) === true)
    {
      return Connection::getMainPath().'/apps/signup/?uid='.$user_login_id;
    }
  }

  public function getReferralId() : string 
  {
    if($this->getId())
    {
      return (new UserReferral)->getReferralId($this->company_id);
    }
  }

  /* profile fun */  
  public function getProfile(int $user_login_id = null)
  {
    if(isset($user_login_id) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.email,
                {$this->tblName}.user_login_id,
                user_contact.phone,
                user_referral.commission,
                user_address.country_id,
                user_data.names,
                user_account.image
              FROM 
                {$this->tblName}
              LEFT JOIN
                user_data 
              ON 
                user_data.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN
                user_contact 
              ON 
                user_contact.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN
                user_address 
              ON 
                user_address.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN
                user_account 
              ON 
                user_account.user_login_id = {$this->tblName}.user_login_id
              LEFT JOIN
                user_referral 
              ON 
                user_referral.referral_id = {$this->tblName}.user_login_id
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
      
      return $this->connection()->row($sql);
    }
  }

  /* profile fun */  
  public function getReferralCount()
  {
    if($this->getId())
    {
      return (new UserReferral)->getReferralCount($this->company_id);
    }

    return 0;
  }
  
  public function getReferral()
  {
    if($this->getId())
    {
      return (new UserReferral)->getReferral($this->company_id);
    }

    return 0;
  }
  
  public function getLastTransactions()
  {
    if($this->getId())
    {
      return (new TransactionRequirementPerUser)->getLastTransactions($this->company_id,"LIMIT 5");
    }
  }

  public function getSignupDate(int $company_id = null)
  {
    if(isset($company_id))
    {
      $sql = "SELECT
                {$this->tblName}.signup_date
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$company_id}'";

      return $this->connection()->field($sql);
    }

    return 0;
  }

  public static function _isActive(int $company_id = null) : bool
  {
    return (new BuyPerUser)->isActive($company_id);
  }

  public function isActive() : bool
  {
    if($this->getId())
    {
      return self::_isActive($this->company_id);
    }

    return false;
  }

  public function getLastSigned()
  {
    if($users = $this->_getLastSigned())
    {
      $Country = new Country;

      return array_map(function($user) use($Country){
        $user['advice_type'] = AdviceType::SIGNUP;
        $user['showed'] = false;
        $user['country'] = $Country->getCountryName($user['country_id']);
        return $user;
      },$users);
    }

    return false;
  }

  public function _getLastSigned()
  {
    $minSignupDate = strtotime("-".self::SIGNUP_DAYS." days");
  
    $sql = "SELECT
              {$this->tblName}.signup_date,
              user_data.names,
              user_address.country_id
            FROM
              {$this->tblName}
            LEFT JOIN
              user_data
            ON 
              user_data.user_login_id = {$this->tblName}.user_login_id
            LEFT JOIN
              user_address
            ON 
              user_address.user_login_id = {$this->tblName}.user_login_id
            WHERE 
              {$this->tblName}.signup_date >= '{$minSignupDate}'";

    return $this->connection()->rows($sql);
  }

  public function getUserData(int $user_login_id = null)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                user_data.user_login_id,
                user_data.names,
                user_account.image
              FROM
                user_data
              LEFT JOIN
                user_account
              ON 
                user_account.user_login_id = user_data.user_login_id
              WHERE 
                user_data.user_login_id = '{$user_login_id}'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getCatalogCampaignIdByUserId(int $user_login_id = null)
  {
    if(isset($user_login_id) === true && $user_login_id)
    {
      $sql = "SELECT
                {$this->tblName}.catalog_campaign_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";

      return $this->connection()->field($sql);
    }

    return 1;
  }
  
  public function getUserIdByEmail(string $email = null)
  {

    if(isset($email) === true)
    {
      $sql = "SELECT
                {$this->tblName}.company_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.email = '{$email}'
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }


  public function getUserDataByEmail(string $email = null)
  {
    if(isset($email) === true)
    {
      $sql = "SELECT
                {$this->tblName}.user_login_id,
                {$this->tblName}.company_id,
                {$this->tblName}.password,
                {$this->tblName}.email,
                user_data.names
              FROM
                {$this->tblName}
              LEFT JOIN
                user_data
              ON 
                user_data.user_login_id = {$this->tblName}.user_login_id
              WHERE 
                {$this->tblName}.email = '{$email}'
              ";

      if($user = $this->connection()->row($sql))
      {
        return $user;
      }
    }

    return false;
  }

  public function getBuysForAdvices()
  {
    return (new BuyPerUser)->getBuysForAdvices();
  }

  public function getPidQuery() : string
  {
    return $this->logged === true ? "?".http_build_query(["pidUser" => $this->getPid()]) : '';
  }

  public function getTimeZone()
  {
    if($this->hasTimeZoneConfigurated())
    {
      return (new UserAccount)->getTimeZone($this->company_id);
    }

    return UserAccount::DEFAULT_TIME_ZONE;
  }

  public function hasTimeZoneConfigurated()
  {
    if($this->logged === true)
    {
      return $this->_data['user_account']['catalog_timezone_id'] ? true : false;
    }
  }

  public function getReferralLanding()
  {
    if($this->logged === true)
    {
      return $this->_data['user_account']['landing'] ? $this->_data['user_account']['landing'] : self::REFERRAL_PATH.$this->company_id;
    }
  }

  public static function getAccountType(int $user_login_id = null) : int
  { 
    if((new Exercise)->hasExerciseStatus($user_login_id,"'".Exercise::IN_PROGRESS."','".Exercise::WAITING."'"))
    {
      return self::DEMO;
    } else if((new UserTradingAccount)->hasAccountStatus($user_login_id,"'".Exercise::IN_PROGRESS."','".Exercise::WAITING."'")) {
      return self::TRADING;
    } else {
      return self::FREE;
    }
  }

  public function getData($company_id = false,$filter = '')  
  {
    if($company_id)
    {
      $sql = "SELECT
                {$this->tblName}.company_id,
                {$this->tblName}.names,
                {$this->tblName}.last_login_date,
                user_settings.background,
                user_settings.personal_message,
                user_settings.gender,
                user_settings.country_id,
                user_settings.image
              FROM
                {$this->tblName}
              LEFT JOIN
                user_settings
              ON
                user_settings.user_login_id = {$this->tblName}.company_id
              WHERE
                {$this->tblName}.company_id = {$company_id}
              AND
                {$this->tblName}.status = '1'
                {$filter}";

      return $this->connection()->row($sql);
    }
    return false;
  }

  public function isEducator() : bool
  {
    if($this->logged || $this->getId())
    {
      return (new UserEducator)->isEducator($this->company_id);
    }

    return false;
  }

  public function getCompanyIdByMail(string $email = null) 
  {
    if(isset($email) === true)
    {
      $sql = "SELECT
                {$this->tblName}.company_id
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.email = '{$email}'";

      return $this->connection()->field($sql);
    }
    return false;
  }
  
  public function isOwnerTradingAccount(int $user_trading_account_id = null,bool $demo = null) : bool
  {
    if(isset($user_trading_account_id) === true)
    {
      if($this->logged || $this->getId())
      {
        return (new UserTradingAccount)->isOwner($user_trading_account_id,$this->company_id);
      }
    }

    return false;
  }

  public function getTradingAccount(int $user_trading_account_id = null,bool $demo = null) : array|bool
  {
    if(isset($user_trading_account_id) === true)
    {
      if($this->logged || $this->getId())
      {
        $UserTradingAccount = new UserTradingAccount;

        if($UserTradingAccount->isOwner($user_trading_account_id,$this->company_id))
        {
          return $UserTradingAccount->getTradingAccount($user_trading_account_id,$demo);
        }
      }
    }

    return false;
  }
  
  public function getBinanceBalance(int $user_trading_account_id = null,bool $demo = null) : array|bool
  {
    if(isset($user_trading_account_id) === true)
    {
      if($this->logged || $this->getId())
      {
        $UserTradingAccount = new UserTradingAccount;

        if($UserTradingAccount->isOwner($user_trading_account_id,$this->company_id))
        {
          if($account = $UserTradingAccount->getTradingAccountLogin($user_trading_account_id,$demo))
          {
            if($response = ApiBinance::accountGetBalance([
              'apiKey' => $account['login'],
              'apiSecret' => $account['password']
            ])) {
              if($response['s'] == 1)
              {
                return $response['balance'];
              }
            }

            return false;
          }
        }
      }
    }

    return false;
  }
  
  public function getBinanceAccount(int $user_trading_account_id = null,bool $demo = null) : array|bool
  {
    if(isset($user_trading_account_id) === true)
    {
      if($this->logged || $this->getId())
      {
        $UserTradingAccount = new UserTradingAccount;

        if($UserTradingAccount->isOwner($user_trading_account_id,$this->company_id))
        {
          if($account = $UserTradingAccount->getTradingAccountLogin($user_trading_account_id,$demo))
          {
            if($response = ApiBinance::accountGet([
              'apiKey' => $account['login'],
              'apiSecret' => $account['password']
            ])) {
              if($response['s'] == 1)
              {
                return $response['accountInfo'];
              }
            }

            return false;
          }
        }
      }
    }

    return false;
  }
  
  public function getBinanceTradeFee(int $user_trading_account_id = null,bool $demo = null) : array|bool
  {
    if(isset($user_trading_account_id) === true)
    {
      if($this->logged || $this->getId())
      {
        $UserTradingAccount = new UserTradingAccount;

        if($UserTradingAccount->isOwner($user_trading_account_id,$this->company_id))
        {
          if($account = $UserTradingAccount->getTradingAccountLogin($user_trading_account_id,$demo))
          {
            if($response = ApiBinance::getBinanceTradeFee([
              'apiKey' => $account['login'],
              'apiSecret' => $account['password']
            ])) {
              if($response['s'] == 1)
              {
                return $response['accountInfo'];
              }
            }

            return false;
          }
        }
      }
    }

    return false;
  }
  
  public function hasProductIdActive(int $product_id = null) : bool
  {
    if($this->logged)
    {
      return (new BuyPerUser)->hasProductIdActive($this->company_id, $product_id);
    }

    return false;
  }

  public function getAllVars() : array|bool
  {
    if($this->logged)
    {
      return (new UserVar)->getAllVars($this->company_id);
    }

    return false;
  }
  
  public function getVar(string $identificator = null) : string|bool
  {
    if($this->logged)
    {
      return (new UserVar)->getVarValueByIdentificator($this->company_id,$identificator);
    }

    return false;
  }

  public function hasSuscriptionActive(array $package_ids = null) : bool
  {
    if($this->logged)
    {
      return (new BuyPerUser)->hasPackagesBuy($this->company_id, $package_ids);
    }

    return false;
  }

  public function getBinanceTrades(int $user_trading_account_id = null,bool $demo = null) : array|bool
  {
    if(isset($user_trading_account_id) === true)
    {
      if($this->logged || $this->getId())
      {
        $UserTradingAccount = new UserTradingAccount;

        if($UserTradingAccount->isOwner($user_trading_account_id,$this->company_id))
        {
          if($account = $UserTradingAccount->getTradingAccountLogin($user_trading_account_id,$demo))
          {
            // if($response = ApiBinance::getBinanceTrades([
            if($response = ApiBinance::accountTradesListen([
              'apiKey' => $account['login'],
              'apiSecret' => $account['password']
            ])) {
              if($response['s'] == 1)
              {
                return $response['accountInfo'];
              }
            }

            return false;
          }
        }
      }
    }

    return false;
  }

  public function getUtm()
  {
    if($this->logged || $this->getId())
    {
      return $this->catalog_campaign_id;
    }
  }
 
  public function hasCampaign(array|int $catalog_campaign_id = null) : bool
  {
    if($this->logged || $this->getId())
    {
      if(is_array($catalog_campaign_id))
      {
        return in_array($this->catalog_campaign_id,$catalog_campaign_id);
      }

      return $this->catalog_campaign_id == $catalog_campaign_id;
    }

    return false;
  }

  public function getBrokers()
  {
    if($this->logged || $this->getId())
    {
      return (new CatalogBroker)->getAllByCampaign($this->catalog_campaign_id);
    }
  }

  public function getTempVar(string $name = null)
  {
    if($this->logged || $this->getId())
    {
      return UserTemp::getVar($this->company_id,$name);
    }
  }

  public function setTempVar(string $name = null,string $value = null)
  {
    if($this->logged || $this->getId())
    {
      return UserTemp::setVar($this->company_id,$name,$value);
    }
  }

  public static function generateLoginToken(array $data = null) : string|bool
  {
    if(isset($data))
    {
      $url = Connection::getMainPath()."/app/application/loginExternal.php";

      $Token = new Token;
      
      if($token = $Token->getToken($data))
      {
        return $url."?".http_build_query($token);
      }
    }

    return false;
  }

  public function setAsStarted()
  {
    if($this->logged)
    {
      $UserAccount = new UserAccount;

      $UserAccount->loadWhere("user_login_id = ?",$this->company_id);
      
      if(!$UserAccount->getId())
      {
        $UserAccount->user_login_id = $this->company_id;
      }

      $UserAccount->is_start = 1;
      
      return $UserAccount->save();
    }
  }

  public function isStarted()
  {
    if($this->logged)
    {
      return $this->_data['user_account']['is_start'];
    }
  }

  public static function generateRandomKey()
  {
    return Token::__randomKey(self::PASSWORD_GENERATOR_LENGHT);
  }

	public static function applyFilterByCatalogCampaignId(array $data = null,int $catalog_campaign_id = null) : array
	{
		if(isset($data) && is_array($data) && !empty($data))
		{
			return array_values(array_filter($data, function($value) use($catalog_campaign_id) {
        if($value['catalog_campaign_ids'])
        {
          $catalog_campaign_ids = json_decode($value['catalog_campaign_ids'],true);
  
          return in_array($catalog_campaign_id,$catalog_campaign_ids);
        } else {
          return true;
        }
			}));
		}

		return [];
	}
}