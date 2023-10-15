<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Token;

use JFStudio\Constants;

use DummieTrading\UserData;

class LicencePerUser extends Orm {
  protected $tblName  = 'licence_per_user';

  /* constants */
  const AVIABLE = 1;
  const USED = 2;
  const UNAVIABLE = 0;
  const DELETED = -1;
  const EXPIRED = 3;
  const LICENCE_KEY_LENGTH = 10;

  const LICENCE_DURATION_DAYS = 30;

  public function __construct() {
    parent::__construct();
  }
  
  public static function assignLicence(int $user_login_id = null,int $user_login_id_to = null) : bool
  {
    $LicencePerUser = new LicencePerUser;
    
    if($LicencePerUser->isActiveSomeTime($user_login_id_to))
    {
      if($licence = $LicencePerUser->getLicenceActive($user_login_id_to))
      {
        $leftDays = $LicencePerUser->calculateLeftDays($licence['active_date']);

        if($LicencePerUser->loadWhere('licence_per_user_id = ?',$licence['licence_per_user_id']))
        {
          if(self::cloneLicence($LicencePerUser,$leftDays))
          {
            return self::setLicenceAsExpired($licence['licence_per_user_id']);
          }
        } 
       }
    } else {
      return self::assignRandomLicence($user_login_id,$user_login_id_to);
    }
  }

  public static function setLicenceAsExpired(int $licence_per_user_id = null) : bool
  {
    $LicencePerUser = new LicencePerUser;

    if($LicencePerUser->loadWhere('licence_per_user_id = ?',$licence_per_user_id))
    {
      $LicencePerUser->status = self::EXPIRED;

      return $LicencePerUser->save();
    }

    return false; 
  }

  public static function cloneLicence(LicencePerUser $LicencePerUserToClone = null,int $leftDays = 0) : bool
  {
    $unix = time();
    $LicencePerUser = new LicencePerUser;
    $LicencePerUser->user_login_id = $LicencePerUserToClone->user_login_id;
    $LicencePerUser->user_login_id_to = $LicencePerUserToClone->user_login_id_to;
    $LicencePerUser->code = $LicencePerUserToClone->code;
    $LicencePerUser->create_date = strtotime("+{$leftDays} days",$unix);
    $LicencePerUser->active_date = strtotime("+{$leftDays} days",$unix);
    $LicencePerUser->status = self::USED;

    return $LicencePerUser->save();
  }

  public static function assignRandomLicence(int $user_login_id = null,int $user_login_id_to = null) : bool
  {
    if((new LicencePerUser)->hasAviableLicences($user_login_id))
    {
      if($licence_per_user_id = (new LicencePerUser)->getAviableLicenceId($user_login_id)) 
      {
        $LicencePerUser = new LicencePerUser;

        if($LicencePerUser->loadWhere('licence_per_user_id = ?',$licence_per_user_id))
        {
          $LicencePerUser->user_login_id_to = $user_login_id_to;
          $LicencePerUser->active_date = time();
          $LicencePerUser->status = self::USED;

          return $LicencePerUser->save();
        }
      }
    }

    return false;
  }

  public static function makeLicences(int $user_login_id = null,int $amount) 
  {
    for($i = 0;$i < $amount;$i++)
    {
        self::make($user_login_id);
    }
    
    return true;
  }

  public static function make(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $LicencePerUser = new LicencePerUser;

      $code = Token::__randomKey(self::LICENCE_KEY_LENGTH);
      
      if(!$LicencePerUser->exist($code))
      {
        $LicencePerUser->user_login_id = $user_login_id;
        $LicencePerUser->create_date = time();
        $LicencePerUser->code = $code;
        return $LicencePerUser->save();
      } else {
        self::make($user_login_id);
      }
    }

    return false;
  }
  
  public function exist(string $code = null) : bool
  {
    if(isset($code) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.code = '{$code}'
              AND 
                {$this->tblName}.code != '".Constants::DELETE."'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getLicence(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.code,
                {$this->tblName}.active_date,
                {$this->tblName}.create_date,
                {$this->tblName}.user_login_id_to,
                {$this->tblName}.status
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id_to = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '".self::USED."'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.code,
                {$this->tblName}.active_date,
                {$this->tblName}.create_date,
                {$this->tblName}.user_login_id_to,
                {$this->tblName}.status
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status NOT IN('".Constants::DELETE."')
              ORDER BY 
                {$this->tblName}.active_date 
              DESC 
              ";

      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public function getAllLicences(int $status = null) 
  {
    if(isset($status) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.code,
                {$this->tblName}.active_date,
                {$this->tblName}.create_date,
                {$this->tblName}.user_login_id_to,
                {$this->tblName}.status
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.status = '{$status}'
              ";

      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public static function calculateLeftDays(int $active_date = null) 
  {
    $endSuscription = strtotime("+".self::LICENCE_DURATION_DAYS." days", $active_date);

    $days = round(($endSuscription- time()) / (60 * 60 * 24));

    return $days > 0 ? $days : 0;
  }

  public function _getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      if($licences = $this->getAll($user_login_id))
      {
        $UserData = new UserData;
        return array_map(function($licence) use($UserData) {
          if($licence['status'] == self::USED) 
          {
            $days = self::calculateLeftDays($licence['active_date']);

            $licence['left'] = [
              'days' => $days,
              'percentaje' => $days * 100 / 30
            ];
            $licence['names'] = $UserData->getNames($licence['user_login_id_to']);
          }

          return $licence;
        },$licences);
      }
    }

    return false;
  }
  
  public function hasAviableLicences(int $user_login_id = null) : bool
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
                {$this->tblName}.status = '".self::AVIABLE."'
              ";
              
      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getAviableLicenceId(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
            AND 
                {$this->tblName}.status = '".self::AVIABLE."'
              ";
              
      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getLicenceInUse(int $user_login_id_to = null) 
  {
    if(isset($user_login_id_to) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id_to = '{$user_login_id_to}'
            AND 
                {$this->tblName}.status = '".self::USED."'
              ";
              
      return $this->connection()->field($sql);
    }

    return false;
  }
  
  public function getLicencesCount(int $user_login_id = null) 
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
                    {$this->tblName}.status = '".self::AVIABLE."'
                ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getGains(int $user_login_id = null) 
  {
    return $this->getLicencesSoldCount($user_login_id)*10;
  }

  public function getLicencesSoldCount(int $user_login_id = null) 
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
                    {$this->tblName}.status IN('".self::USED."','".self::EXPIRED."')
                ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public function hasLicence(int $user_login_id_to = null) : bool
  {
    if(isset($user_login_id_to) === true)
    {
      $sql = "SELECT
                    {$this->tblName}.{$this->tblName}_id
                FROM 
                    {$this->tblName}
                WHERE 
                    {$this->tblName}.user_login_id_to = '{$user_login_id_to}'
                AND 
                    {$this->tblName}.status IN('".self::USED."','".self::EXPIRED."')
                ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function isActive(int $user_login_id = null) 
  {
    if($licence = $this->getLicence($user_login_id))
    {
      return $this->calculateLeftDays($licence['active_date']) > 0;
    }

    return false;
  }
  
  public function isActiveSomeTime(int $user_login_id = null) 
  {
    if($licence = $this->getLicence($user_login_id))
    {
      return true;
    }

    return false;
  }
  
  public function getLicenceActive(int $user_login_id = null) 
  {
    if($licence = $this->getLicence($user_login_id))
    {
      return $licence;
    }

    return false;
  }
 
  public function isActiveSoonToExpire(int $user_login_id = null) 
  {
    if($licence = $this->getLicence($user_login_id))
    {
      return $this->calculateLeftDays($licence['active_date']) <= 10;
    }

    return false;
  }

  public static function expire(int $licence_per_user_id = null) : bool
  {
    if(isset($licence_per_user_id) == true) 
    {
      $LicencePerUser = new LicencePerUser;

      if($LicencePerUser->loadWhere('licence_per_user_id = ?',$licence_per_user_id))
      {
        $LicencePerUser->status = self::EXPIRED;
    
        return $LicencePerUser->save();
      }
    }

    return false;
  }
}