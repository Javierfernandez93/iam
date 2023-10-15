<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\TestPerExercise;

use DummieTrading\ApiWhatsApp;
use DummieTrading\ApiWhatsAppMessages;
use DummieTrading\ConfigurationPerExercise;
use DummieTrading\UserContact;

class Exercise extends Orm {
  protected $tblName  = 'exercise';

  const NO_PAYED = -1;
  const WAITING = 0;
  const IN_PROGRESS = 1;
  const FINISH = 2;
  const EXPIRED = 3;
  const DECLINED = 4;

  public function __construct() {
    parent::__construct();
  }

  public static function _add(array $data = null) 
  {
    $Exercise = new Exercise;
    $Exercise->user_login_id = $data['user_login_id'];
    $Exercise->create_date = $data['create_date'] ?? time();
    $Exercise->status = self::NO_PAYED;
    
    if($Exercise->save())
    {
      return $Exercise->getId();
    }

    return false;
  }

  public static function add(array $data = null) 
  {
    if($exercise_id = self::_add($data))
    {
      $data['exercise_id'] = $exercise_id;
      $data['test_amount'] = $data['test_amount'] ?? TestPerExercise::DEFAULT_TESTS;

      if(TestPerExercise::addTests($data))
      {
        if(ConfigurationPerExercise::add($data))
        {
          return true;
        }
      }
    }
  }

  public function getStatus(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      if($exercise = $this->getExercise($user_login_id,implode(",",[self::WAITING,self::IN_PROGRESS,self::FINISH,self::EXPIRED])))
      {
        $exercise['configuration'] = (new ConfigurationPerExercise)->_getConfiguration($exercise['exercise_id']);
        
        return $exercise;
      }
    }

    return false;
  }

  public function getCurrentExercise(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true) 
    {
      if($exercise = $this->getExercise($user_login_id,implode(",",[self::WAITING,self::IN_PROGRESS,self::FINISH,self::EXPIRED])))
      {
        $exercise['tests'] = (new TestPerExercise)->getTest($exercise['exercise_id']);
        $exercise['progress'] = TestPerExercise::getProgress($exercise['tests']);

        return $exercise;
      }
    }

    return false;
  }
  
  public function hasExerciseStatus(int $user_login_id = null,string $statusIn = null) 
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
  
  public function getExercise(int $user_login_id = null,string $statusIn = null) 
  {
    if(isset($user_login_id) === true) 
    {
      $filter = $statusIn ? "AND {$this->tblName}.status IN({$statusIn})" : '';

      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.start_date,
                {$this->tblName}.user_name,
                {$this->tblName}.password,
                {$this->tblName}.server,
                {$this->tblName}.trader,
                {$this->tblName}.end_date,
                {$this->tblName}.create_date,
                {$this->tblName}.status
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
                {$filter}
              ";

              
      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function _getAll(string $statusIn = null) 
  {
    if($exercises = $this->getAll($statusIn))
    {
      $UserData = new UserData;
      $UserLogin = new UserLogin;
      $TestPerExercise = new TestPerExercise;
      $ConfigurationPerExercise = new ConfigurationPerExercise;
      
      return array_map(function($exercise) use($TestPerExercise,$ConfigurationPerExercise,$UserData,$UserLogin) {
        $exercise['configuration'] = $ConfigurationPerExercise->_getConfiguration($exercise['exercise_id']) ?? [];
        $exercise['tests'] = $TestPerExercise->getTest($exercise['exercise_id']) ?? [];
        $exercise['names'] = $UserData->getNames($exercise['user_login_id']);
        $exercise['emailUser'] = $UserLogin->getEmail($exercise['user_login_id']);

        $exercise['tests'] = $TestPerExercise->getTest($exercise['exercise_id']);
        $exercise['progress'] = TestPerExercise::getProgress($exercise['tests']);

        return $exercise;
      },$exercises);
    }

    return false;
  }

  public function getAll(string $statusIn = null) 
  {
    $filter = isset($statusIn) ? "WHERE {$this->tblName}.status IN({$statusIn})" : '';

    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.start_date,
              {$this->tblName}.user_login_id,
              {$this->tblName}.user_name,
              {$this->tblName}.password,
              {$this->tblName}.server,
              {$this->tblName}.trader,
              {$this->tblName}.end_date,
              {$this->tblName}.create_date,
              {$this->tblName}.status
            FROM
              {$this->tblName}
              {$filter}
            ";
            
    return $this->connection()->rows($sql);
  }

  public static function addCredentials(array $data = null) : bool
  {
    $Exercise = new Exercise;

    if($Exercise->loadWhere('exercise_id = ?',$data['exercise_id']))
    {
      $Exercise->user_name = $data['user_name'];
      $Exercise->password = $data['password'];
      $Exercise->server = $data['server'];
      $Exercise->trader = $data['trader'];
      
      return $Exercise->save();
    }
  }

  public static function startExercise(array $data = null) : bool
  {
    if(self::addCredentials($data))
    {
      $Exercise = new Exercise;

      if($Exercise->loadWhere('exercise_id = ?',$data['exercise_id']))
      {
        $Exercise->start_date = $Exercise->start_date ?? time();
        $Exercise->status = self::IN_PROGRESS;
        
        if($Exercise->save())
        {
          if(filter_var($data['sendWhatsApp'], FILTER_VALIDATE_BOOLEAN))
          {
            return self::sendWhatsAppCredentials($data);
          }

          return true;
        }
      }
    }

    return false;
  }
  
  public static function setExerciseAsByBuy(int $buy_per_user_login_id = null,int $status = null) : bool
  {
    if($exercise_id = (new ConfigurationPerExercise)->getExerciseId($buy_per_user_login_id))
    {
      return self::setExerciseAs($exercise_id,$status);
    }

    return false;
  }

  public static function setExerciseAs(int $exercise_id = null,int $status = null) : bool
  {
    if(isset($exercise_id,$status) == true)
    {
      $Exercise = new Exercise;

      if($Exercise->loadWhere('exercise_id = ?',$exercise_id))
      {
        $Exercise->status = $status;
        
        return $Exercise->save();
      }
    }

    return false;
  }
  
  public static function sendWhatsAppCredentials(array $data = null) 
  {
    return ApiWhatsApp::sendWhatsAppMessage([
        'message' => ApiWhatsAppMessages::getExerciseCredentialsMessage(),
        'image' => null,
        'contact' => [
            "phone" => (new UserContact)->getWhatsApp($data['user_login_id']),
            "name" => $data['name'] ??  (new UserData)->getName($data['user_login_id']),
            "user_name" => $data['user_name'] ?? 'Error',
            "client_password" => $data['password'] ?? 'Error',
            "trader" => $data['trader'] ?? 'Error',
            "server" => $data['server'] ?? 'Error'
        ]
    ]);
  }

  public function _getStatus(int $user_login_id = null) 
  {
    if(isset($user_login_id) == true)
    {
      $sql = "SELECT
                {$this->tblName}.status
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status != '-1'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
 
  public function getUserIdByExerciseId(int $exercise_id = null) 
  {
    if(isset($exercise_id) == true)
    {
      $sql = "SELECT
                {$this->tblName}.user_login_id
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.exercise_id = '{$exercise_id}'
              AND 
                {$this->tblName}.status != '-1'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
}