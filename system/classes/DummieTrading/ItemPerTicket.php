<?php

namespace DummieTrading;

use HCStudio\Orm;

class ItemPerTicket extends Orm {
  protected $tblName  = 'item_per_ticket';

  const SEND_FROM_USER = 0;
  const SEND_FROM_ADMIN = 1;

  public function __construct() {
    parent::__construct();
  }

  public static function saveItem(array $data = null) : bool
  {
    $ItemPerTicket = new ItemPerTicket;
    $ItemPerTicket->ticket_per_user_id = $data['ticket_per_user_id'];
    $ItemPerTicket->message = $data['message'];
    $ItemPerTicket->send_from = $data['send_from'];
    $ItemPerTicket->user_support_id = $data['user_support_id'] ? $data['user_support_id'] : 0;
    $ItemPerTicket->create_date = time();

    return $ItemPerTicket->save();
  }


  public function getAll(int $ticket_per_user_id = null) 
  {
    if(isset($ticket_per_user_id) == true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.message,
                {$this->tblName}.user_support_id,
                {$this->tblName}.send_from,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.ticket_per_user_id = '{$ticket_per_user_id}'
              AND 
                {$this->tblName}.status = '1'
              ORDER BY 
                {$this->tblName}.create_date 
              ASC
              ";
              
      return $this->connection()->rows($sql);
    }

    return false;
  }
}