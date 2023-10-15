<?php

namespace DummieTrading;

use HCStudio\Orm;
use DummieTrading\ApiWhatsAppMessages;
use DummieTrading\ApiWhatsApp;
use DummieTrading\ApiDummieTrading;
use DummieTrading\Client;

class DemoPerClient extends Orm {
  protected $tblName  = 'demo_per_client';

  /* CONSTANTS */
  const DELETE = -1;
  const FOR_ACTIVATE = 0;
  const IN_USE = 1;
  const EXPIRED = 2;
  const DEFAULT_HOURS = 2;

  const DEMO_DURATION_MINUTES = 120;
  public function __construct() {
    parent::__construct();
  }
  
  public static function expireDemo(int $demo_per_client_id = null) : bool
  {
    $DemoPerClient = new DemoPerClient;

    if($DemoPerClient->loadWhere('demo_per_client_id = ?',$demo_per_client_id))
    {
      $DemoPerClient->status = self::EXPIRED;
  
      return $DemoPerClient->save();
    }

    return true;
  }

  public static function setUpDemo(array $data = null) : bool
  {
    $DemoPerClient = new DemoPerClient;

    if($DemoPerClient->loadWhere('client_id = ?',$data['client_id']))
    {
      $DemoPerClient->active_date = time();
      $DemoPerClient->status = self::IN_USE;
  
      return $DemoPerClient->save();
    }

    return false;
  }

  public static function add(array $data = null) 
  {
    if(isset($data) === true)
    {
      $DemoPerClient = new DemoPerClient;
      
      if(!$DemoPerClient->hasDemo($data['client_id']))
      {
          $DemoPerClient->client_id = $data['client_id'];
          $DemoPerClient->create_date = time();
      }
  
      $DemoPerClient->hour = self::DEFAULT_HOURS;
      $DemoPerClient->adult = $data['adult'] ? 1 : 0;
      $DemoPerClient->status = self::FOR_ACTIVATE;
  
      if($DemoPerClient->save())
      {
        return $DemoPerClient->getId();
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
  
  public function getDemo(int $client_id = null) 
  {
    if(isset($client_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.adult,
                {$this->tblName}.hour,
                {$this->tblName}.status,
                {$this->tblName}.active_date
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.client_id = '{$client_id}'
              AND 
                {$this->tblName}.status != '".self::DELETE."'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }

  public static function calculateLeftMinutes(int $active_date = null) 
  {
    $endSuscription = strtotime("+".self::DEMO_DURATION_MINUTES." minutes", $active_date);

    return round(($endSuscription - time()) / 60);
  }

  public static function isActive(int $active_date = null) 
  {
    return self::calculateLeftMinutes($active_date) > 0;
  }

  public static function sendDemo(int $demo_per_client_id = null) 
  {
    $DemoPerClient = new DemoPerClient;
    
    if($DemoPerClient->loadWhere('demo_per_client_id = ?',$demo_per_client_id))
    {
      $Client = new Client;
      
      if($Client->loadWhere('client_id = ?',$DemoPerClient->client_id))
      {
        $Client->user_name = $Client->user_name ? $Client->user_name : Client::generateUserName($Client->name);

        if($client = ApiDummieTrading::generateDemo($Client->user_name))
        {
          $Client->external_client_id = $client['user']['ID'];
          $Client->client_password = $client['user']['PASSWORD'];

          if($Client->save())
          {
            $DemoPerClient->expiration = strtotime($client['user']['EXPIRATION']);
            $DemoPerClient->active_date = time();
            $DemoPerClient->status = self::IN_USE;

            if($DemoPerClient->save())
            {
              return self::sendDemoCredentials($Client->data());
            }
          }
        }
      }
    }

    return false;
  }

  public static function sendDemoCredentials(array $data = null) 
  {
    return ApiWhatsApp::sendWhatsAppMessage([
        'message' => ApiWhatsAppMessages::getIptvSetUpDemoMessage(),
        'image' => null,
        'contact' => [
            "phone" => $data['whatsapp'],
            "name" => $data['name'],
            "user_name" => $data['user_name'],
            "client_password" => $data['client_password']
        ]
    ]);
  }
}