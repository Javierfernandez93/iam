<?php

namespace DummieTrading;

use HCStudio\Orm;

class Log extends Orm {
  protected $tblName  = 'log';

  public function __construct() {
    parent::__construct();
  }

  public static function addNewRecord(int $user_support_id = null,array $additional_data = null,int $log_type = null) : bool
  {
    if(isset($user_support_id,$log_type) === true)
    {
        $Log = new Log;
        $Log->user_support_id = $user_support_id;
        $Log->additional_data = json_encode($additional_data);
        $Log->log_type = $log_type;
        $Log->create_date = time();
        
        return $Log->save();
    }

    return false;
  }
}