<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Token;

use DummieTrading\ItemPerTicket;
use DummieTrading\UserSupport;
use DummieTrading\UserLogin;

class TicketPerUser extends Orm {
  protected $tblName  = 'ticket_per_user';

  /* constants */
  const UNIQUE_ID_LENGHT = 5;

  const AWAIT_FOR_SUPPORT = 0;
  const SUPPORTING = 1;
  const FINISHED = 2;
  const DELETED = -1;

  public function __construct() {
    parent::__construct();
  }

  public static function saveTicket(array $data = null) : bool
  {
    $TicketPerUser = new TicketPerUser;
    $TicketPerUser->unique_id = Token::__randomKey(self::UNIQUE_ID_LENGHT);
    $TicketPerUser->user_login_id = $data['user_login_id'];
    $TicketPerUser->subject = $data['subject'];
    $TicketPerUser->create_date = time();
    
    if($TicketPerUser->save())
    {
      return ItemPerTicket::saveItem([
          'ticket_per_user_id' => $TicketPerUser->getId(),
          'message' => $data['message'],
          'send_from' => ItemPerTicket::SEND_FROM_USER
      ]);
    }

    return false;
  }

  public function getAll(int $user_login_id = null,string $status_in = null) 
  {
    if(isset($user_login_id) == true)
    {
      if($tickets = $this->_getAll($user_login_id,$status_in))
      {
        return array_map(function($ticket){
          if($items = (new ItemPerTicket)->getAll($ticket['ticket_per_user_id']))
          {
            $main = $items[0];

            if(sizeof($items) > 1)
            {
              unset($items[0]);

              $items = array_values($items);

              $items = array_map(function($item){
                if($item['send_from'] == ItemPerTicket::SEND_FROM_ADMIN)
                {
                  $item['sender'] = (new UserSupport)->getSupportData($item['user_support_id']);
                } else if($item['send_from'] == ItemPerTicket::SEND_FROM_USER) {
                  $item['sender'] = [
                    'names' => 'Tú'
                  ];
                }

                return $item;
              },$items);  
            }

            $ticket['items'] = [
              'main' => $main,
              'items' => $items
            ];
          }

          // 
          $ticket['reply'] = [
            'message' => null
          ];

          return $ticket;
        },$tickets);
      }
    }

    return false;
  }
  
  public function getAllFromUsers() 
  {
    if($tickets = $this->_getAllFromUsers())
    {
      $UserLogin = new UserLogin(false,false);

      return array_map(function($ticket) use($UserLogin) {
        $ticket['user'] = $UserLogin->getUserData($ticket['user_login_id']);

        if($items = (new ItemPerTicket)->getAll($ticket['ticket_per_user_id']))
        {
          $main = $items[0];

          $items = array_map(function($item){
            if($item['send_from'] == ItemPerTicket::SEND_FROM_ADMIN)
            {
              $item['sender'] = (new UserSupport)->getSupportData($item['user_support_id']);
            } else if($item['send_from'] == ItemPerTicket::SEND_FROM_USER) {
              $item['sender'] = [
                'names' => 'Tú'
              ];
            }

            return $item;
          },$items);  

          unset($items[0]);

          if(sizeof($items) > 1)
          {
            $items = array_values($items);
          }

          $ticket['items'] = [
            'main' => $main,
            'items' => $items,
          ];
        }

        // 
        $ticket['reply'] = [
          'message' => null
        ];

        return $ticket;
      },$tickets);
    }

    return false;
  }
  
  public function _getAll(int $user_login_id = null,string $status_in = null) 
  {
    if(isset($user_login_id) == true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.unique_id,
                {$this->tblName}.subject,
                {$this->tblName}.status,
                {$this->tblName}.create_date,
                user_support.names,
                user_support.image
              FROM 
                {$this->tblName}
              LEFT JOIN 
                user_support 
              ON 
                user_support.user_support_id = {$this->tblName}.user_support_id
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status IN({$status_in})
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";
              
      return $this->connection()->rows($sql);
    }

    return false;
  }

  public function _getAllFromUsers() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.unique_id,
              {$this->tblName}.user_login_id,
              {$this->tblName}.subject,
              {$this->tblName}.status,
              {$this->tblName}.create_date,
              user_support.names,
              user_support.image
            FROM 
              {$this->tblName}
            LEFT JOIN 
              user_support 
            ON 
              user_support.user_support_id = {$this->tblName}.user_support_id
            ORDER BY 
              {$this->tblName}.create_date 
            DESC 
            ";
            
    return $this->connection()->rows($sql);
  }
}