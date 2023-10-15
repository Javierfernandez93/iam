<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\CreditPerUser;
use DummieTrading\Cout;
use DummieTrading\Client;

class ServicePerClient extends Orm {
  protected $tblName  = 'service_per_client';

  /* CONSTANTS */
  const DELETE = -1;
  const FOR_ACTIVATE = 0;
  const IN_USE = 1;
  const EXPIRED = 2;
  const EXPIRED_FOR_RENOVATION = 3;
  const SERVICE_DURATION_DAYS = 30;
  const DEFAULT_CONNECTIONS = 3;

  const REQUEST_RENOVATION = 1;
  const RENOVATION_SENT = 0;

  public function __construct() {
    parent::__construct();
  }
  
  public static function setAsRenovated(int $client_id = null) : bool
  {
    if(isset($client_id) == true) 
    {
      $ServicePerClient = new ServicePerClient;
      
      if($ServicePerClient->loadWhere('client_id = ?',$client_id))
      {
        $ServicePerClientNew = new ServicePerClient;
        $ServicePerClientNew->client_id = $ServicePerClient->client_id;
        $ServicePerClientNew->connection = $ServicePerClient->connection;
        $ServicePerClientNew->month = $ServicePerClient->month;
        $ServicePerClientNew->adult = $ServicePerClient->adult;
        $ServicePerClientNew->create_date = time();
        $ServicePerClientNew->expiration = 0;
        $ServicePerClientNew->day = date("t");
        $ServicePerClientNew->request_renovation = self::RENOVATION_SENT;
        $ServicePerClientNew->active_date = time();
        $ServicePerClientNew->status = self::IN_USE;

        if($ServicePerClientNew->save())
        {
          return self::expireServiceForRenovation($ServicePerClient->getId());
        }
      }
    }

    return false;
  }

  public static function requestRenovation(int $client_id = null,int $user_login_id = null) : bool
  {
    if(isset($client_id) == true) 
    {
      $ServicePerClient = new ServicePerClient;

      if($ServicePerClient->loadWhere('client_id = ?',$client_id))
      {
        if(Client::sendWhatsAppRenovation($user_login_id))
        {
          if(CreditPerUser::restCredits($user_login_id,1))
          {
            $ServicePerClient->request_renovation = self::REQUEST_RENOVATION;

            return $ServicePerClient->save();
          }
        }
      }
    }

    return false;
  }

  public static function expireService(int $service_per_client_id = null) : bool
  {
    if(isset($service_per_client_id) == true) 
    {
      $ServicePerClient = new ServicePerClient;

      if($ServicePerClient->loadWhere('service_per_client_id = ?',$service_per_client_id))
      {
        $ServicePerClient->status = self::EXPIRED;
    
        return $ServicePerClient->save();
      }
    }

    return false;
  }

  public static function expireServiceForRenovation(int $service_per_client_id = null) : bool
  {
    if(isset($service_per_client_id) == true) 
    {
      $ServicePerClient = new ServicePerClient;

      if($ServicePerClient->loadWhere('service_per_client_id = ?',$service_per_client_id))
      {
        $ServicePerClient->request_renovation = 0;
        $ServicePerClient->status = self::EXPIRED_FOR_RENOVATION;
    
        return $ServicePerClient->save();
      }
    }

    return false;
  }

  public static function setUpService(array $data = null) : bool
  {
    $ServicePerClient = new ServicePerClient;

    if($ServicePerClient->loadWhere('client_id = ?',$data['client_id']))
    {
      $ServicePerClient->active_date = time();
      $ServicePerClient->status = self::IN_USE;
  
      return $ServicePerClient->save();
    }

    return false;
  }

  public static function add(array $data = null)
  {
    $ServicePerClient = new ServicePerClient;

    if($data['demo'])
    {
      DemoPerClient::expireDemo($data['demo']['demo_per_client_id']);
    }

    $ServicePerClient->client_id = $data['client_id'];
    $ServicePerClient->connection = $data['connection'] ? $data['connection'] : self::DEFAULT_CONNECTIONS;
    $ServicePerClient->month = $data['month'] ? $data['month'] : 1;
    $ServicePerClient->adult = $data['adult'] ? 1 : 0;
    $ServicePerClient->status = self::FOR_ACTIVATE;
    $ServicePerClient->create_date = time();

    if($ServicePerClient->save())
    {
      if(CreditPerUser::restCredits($data['user_login_id'],1))
      {
        return $ServicePerClient->getId();
      }
    }
  }

  public function hasDemo(int $client_id = null) : bool
  {
    if(isset($client_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.client_id = '{$client_id}'
              AND 
                {$this->tblName}.status IN(".self::IN_USE.",".self::FOR_ACTIVATE.")
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

	public function getService(int $client_id = null)
	{
	  if(isset($client_id) === true)
	  {
      $sql = "SELECT
            {$this->tblName}.{$this->tblName}_id,
            {$this->tblName}.adult,
            {$this->tblName}.connection,
            {$this->tblName}.day,
            {$this->tblName}.expiration,
            {$this->tblName}.status,
            {$this->tblName}.active_date,
            {$this->tblName}.request_renovation,
            {$this->tblName}.create_date
          FROM 
            {$this->tblName}
          WHERE 
            {$this->tblName}.client_id = '{$client_id}'
          AND 
            {$this->tblName}.status NOT IN(".self::DELETE.",".self::EXPIRED_FOR_RENOVATION.")
          ";
          
      return $this->connection()->row($sql);
	  }
  
	  return false;
	}

  public static function calculateLeftDays(int $active_date = null,int $day = null) 
  {
    $endSuscription = strtotime("+".$day." days", $active_date);

    return round(($endSuscription- time()) / (60 * 60 * 24));
  }

  public static function isActive(int $active_date = null,int $day = null) 
  {
    return self::calculateLeftDays($active_date,$day) > 0;
  }

  public static function sendService(int $service_per_client_id = null) 
  {
    $ServicePerClient = new ServicePerClient;
    
    if($ServicePerClient->loadWhere('service_per_client_id = ?',$service_per_client_id))
    {
      $Client = new Client;
      
      if($Client->loadWhere('client_id = ?',$ServicePerClient->client_id))
      {
        $Client->user_name = $Client->user_name ? $Client->user_name : Client::generateUserName($Client->name);

        if($client = ApiDummieTrading::generateService($Client->user_name))
        {
          $Client->external_client_id = $client['user']['ID'];
          $Client->client_password = $client['user']['PASSWORD'];

          if($Client->save())
          {
            $ServicePerClient->expiration = strtotime($client['user']['EXPIRATION']);
            $ServicePerClient->day = date("t");
            $ServicePerClient->active_date = time();
            $ServicePerClient->status = self::IN_USE;

            if($ServicePerClient->save())
            {
              return self::sendServiceCredentials($Client->data());
            }
          }
        }
      }
    }

    return false;
  }

  public static function sendServiceCredentials(array $data = null) 
  {
    return ApiWhatsApp::sendWhatsAppMessage([
        'message' => ApiWhatsAppMessages::getIptvSetUpMessage(),
        'image' => null,
        'contact' => [
            "phone" => $data['whatsapp'],
            "name" => $data['name'],
            "user_name" => $data['user_name'],
            "client_password" => $data['client_password']
        ]
    ]);
  }

	public function getAllServices(int $status = null)
	{
	  if(isset($status) === true)
	  {
      $sql = "SELECT
            {$this->tblName}.{$this->tblName}_id,
            {$this->tblName}.adult,
            {$this->tblName}.connection,
            {$this->tblName}.day,
            {$this->tblName}.status,
            {$this->tblName}.active_date,
            {$this->tblName}.create_date
          FROM 
            {$this->tblName}
          WHERE 
            {$this->tblName}.status = '{$status}'
          ";
          
      return $this->connection()->rows($sql);
	  }
  
	  return false;
	}
	
  public function getAllServicesSold(int $user_login_id = null)
	{
	  if(isset($user_login_id) === true)
	  {
      if($date = (new Cout)->getActual(true))
      {
        $sql = "SELECT
                  {$this->tblName}.{$this->tblName}_id,
                  {$this->tblName}.adult,
                  {$this->tblName}.connection,
                  {$this->tblName}.status,
                  {$this->tblName}.active_date,
                  {$this->tblName}.create_date
                FROM 
                  {$this->tblName}
                LEFT JOIN 
                  client 
                ON 
                  client.client_id = {$this->tblName}.client_id
                WHERE 
                  {$this->tblName}.status = '".self::IN_USE."'
                AND 
                  {$this->tblName}.active_date 
                BETWEEN 
                  '{$date['start_date']}'
                AND 
                  '{$date['end_date']}'
                AND 
                  client.user_login_id = '{$user_login_id}'
                ";
        return $this->connection()->rows($sql);
      }
	  }
  
	  return false;
	}
}