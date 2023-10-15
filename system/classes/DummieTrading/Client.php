<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\DemoPerClient;
use DummieTrading\ServicePerClient;
use HCStudio\Token;
use DummieTrading\UserData;
use DummieTrading\ApiWhatsApp;
use DummieTrading\ApiWhatsAppMessages;

class Client extends Orm {
  protected $tblName  = 'client';
  const TOKEN_LENGHT = 3;
  public function __construct() {
    parent::__construct();
  }
  
  public static function addCredentials(array $data = null) : bool
  {
    $Client = new Client;
    
    if($Client->loadWhere('client_id = ?',$data['client_id']))
    {
        $Client->user_name = $data['user_name'];
        $Client->client_password = $data['client_password'];
        $Client->active_date = time();

        return $Client->save();
    }

    return false;
  }

  public static function sendDemo(int $demo_per_client_id = null) 
  {
    return DemoPerClient::sendDemo($demo_per_client_id);
  }
  
  public static function sendService(int $demo_per_client_id = null) 
  {
    return ServicePerClient::sendService($demo_per_client_id);
  }

  public static function generateUserName(string $name = null) : string
  {
    return isset($name) ? explode(" ",$name)[0].Token::__randomKey(self::TOKEN_LENGHT) : Token::__randomKey(self::TOKEN_LENGHT*2);
  }

  public static function add(array $data = null) 
  {
    $Client = new Client;
    
    if(!$Client->exist($data['email']))
    {
        $Client->user_login_id = $data['user_login_id'];
        $Client->whatsapp = $data['whatsapp'];
        $Client->name = $data['name'];
        $Client->email = $data['email'];
        $Client->create_date = time();

        if($Client->save())
        {
          if(filter_var($data['demo']['enabled'], FILTER_VALIDATE_BOOLEAN))
          {
            if($demo_per_client_id = DemoPerClient::add(array_merge($data['demo'],[
              'client_id' => $Client->getId(),
              'adult' => $data['adult']
            ])))
            {
              return self::sendDemo($demo_per_client_id);
            }
          } else {
            if($service_per_client_id = ServicePerClient::add(array_merge($data['service'],[
              'user_login_id' => $data['user_login_id'],
              'client_id' => $Client->getId(),
              'adult' => $data['adult']
            ])))
            {
              return self::sendService($service_per_client_id);
            }
          }
        }
    }

    return false;
  }

  public static function sendWhatsAppDemo(int $user_login_id = null)
  {
      return ApiWhatsApp::sendWhatsAppMessage([
          'message' => ApiWhatsAppMessages::getNewDemoMessage(),
          'image' => null,
          'contact' => [
              "phone" => '573503637342',
              "name" => (new UserData)->getName($user_login_id)
          ]
      ]);
  }
  
  public static function sendWhatsAppRenovation(int $user_login_id = null)
  {
      return ApiWhatsApp::sendWhatsAppMessage([
          'message' => ApiWhatsAppMessages::getRenovationMessage(),
          'image' => null,
          'contact' => [
              "phone" => '5213317361196',
              // "phone" => '573503637342',
              "name" => (new UserData)->getName($user_login_id)
          ]
      ]);
  }
  
  public static function sendWhatsAppService(int $user_login_id = null)
  {
      return ApiWhatsApp::sendWhatsAppMessage([
          'message' => ApiWhatsAppMessages::getNewServiceMessage(),
          'image' => null,
          'contact' => [
              "phone" => '573503637342',
              "name" => (new UserData)->getName($user_login_id)
          ]
      ]);
  }
  public function getNames(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                LOWER(CONCAT_WS(' ',
                  {$this->tblName}.names,
                  {$this->tblName}.last_name,
                  {$this->tblName}.sur_name
                )) as names
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function exist(string $email = null) : bool
  {
    if(isset($email) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.email = '{$email}'
              AND 
                {$this->tblName}.status = '1'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.name,
                {$this->tblName}.user_name,
                {$this->tblName}.client_password,
                {$this->tblName}.whatsapp,
                {$this->tblName}.email,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              ";
      
      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public function _getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      if($clients = $this->getAll($user_login_id))
      {
        $DemoPerClient = new DemoPerClient;
        $ServicePerClient = new ServicePerClient;
        
        return array_map(function($client) use($DemoPerClient,$ServicePerClient) {
          if($demo = $DemoPerClient->getDemo($client['client_id']))
          {
            $client['demo'] = $demo;
            $minutes = DemoPerClient::calculateLeftMinutes($demo['active_date']);
            
            $client['demo']['left'] = [
              'active' => DemoPerClient::isActive($demo['active_date']),
              'minutes' => $minutes,
              'percentaje' => $minutes * 100 / DemoPerClient::DEMO_DURATION_MINUTES
            ];
          } else {
            $client['demo'] = false;
          }
          
          if($service = $ServicePerClient->getService($client['client_id']))
          {
            unset($client['demo']);
            $client['service'] = $service;

            $days = ServicePerClient::calculateLeftDays($service['active_date'],$service['day']);
            $client['service']['left'] = [
              'active' => ServicePerClient::isActive($service['active_date'],$service['day']),
              'days' => $days,
              'percentaje' => $days && $service['day']  ? $days * 100 / $service['day'] : 0
            ];
          } else {
            $client['service'] = false;
          }

          return $client;
        },$clients);
      }
    }

    return false;
  }

  public function getClient(int $client_id = null) 
  {
    if(isset($client_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.name,
                {$this->tblName}.user_login_id,
                {$this->tblName}.user_name,
                {$this->tblName}.client_password,
                {$this->tblName}.email,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.client_id = '{$client_id}'
              ORDER BY 
                {$this->tblName}.create_date
              DESC
              ";
      
      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getAllForAdmin() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.name,
              {$this->tblName}.user_login_id,
              {$this->tblName}.user_name,
              {$this->tblName}.client_password,
              {$this->tblName}.whatsapp,
              {$this->tblName}.email,
              {$this->tblName}.create_date
            FROM 
              {$this->tblName}
            ORDER BY 
              {$this->tblName}.create_date
            DESC
            ";
    
    return $this->connection()->rows($sql);
  }

  public function _getAllForAdmin() 
  {
    if($clients = $this->getAllForAdmin())
    {
      $DemoPerClient = new DemoPerClient;
      $ServicePerClient = new ServicePerClient;
      
      return array_map(function($client) use($DemoPerClient,$ServicePerClient) {
        if($demo = $DemoPerClient->getDemo($client['client_id']))
        {
          $client['demo'] = $demo;
          
          $minutes = DemoPerClient::calculateLeftMinutes($demo['active_date']);
          $client['demo']['left'] = [
            'active' => DemoPerClient::isActive($demo['active_date']),
            'minutes' => $minutes,
            'percentaje' => $minutes * 100 / DemoPerClient::DEMO_DURATION_MINUTES
          ];
        } else {
          $client['demo'] = false;
        }
        
        if($service = $ServicePerClient->getService($client['client_id']))
        {
          unset($client['demo']);
          $client['service'] = $service;

          $days = ServicePerClient::calculateLeftDays($service['active_date'],$service['day']);
          $client['service']['left'] = [
            'active' => ServicePerClient::isActive($service['active_date'],$service['day']),
            'days' => $days,
            'percentaje' => $days * 100 / ServicePerClient::SERVICE_DURATION_DAYS
          ];
        } else {
          $client['service'] = false;
        }
        
        $client['sponsor']['names'] = (new UserData)->getName($client['user_login_id']);

        return $client;
      },$clients);
    }

    return false;
  }
}