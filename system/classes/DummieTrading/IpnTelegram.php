<?php

namespace DummieTrading;

use HCStudio\Orm;

class IpnTelegram extends Orm
{
  protected $tblName  = 'ipn_telegram';

  public function __construct()
  {
    parent::__construct();
  }

  public static function add(array $data = null)
  {
    if (isset($data) === true) {
      $IpnTelegram = new IpnTelegram;
      $IpnTelegram->query = http_build_query($data);
      $IpnTelegram->data = json_encode($data);
      $IpnTelegram->create_date = time();
      $IpnTelegram->save();
    }
  }

  public function existByQuery(string $query = null): bool
  {
    if (isset($query) === true) {
      $time = strtotime("-2 minutes");

      $sql = "SELECT 
                  {$this->tblName}.{$this->tblName}_id
                FROM 
                  {$this->tblName}
                WHERE 
                  {$this->tblName}.query = '{$query}'
                AND 
                  {$this->tblName}.status = '1'
                AND 
                  {$this->tblName}.create_date >= '{$time}'
                ";

      if($exist = $this->connection()->field($sql))
      {
        return $exist;
      }
    }

    return false;
  }
}
