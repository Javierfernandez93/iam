<?php

namespace DummieTrading;

use HCStudio\Orm;

class InvitationPerUser extends Orm {
  protected $tblName  = 'invitation_per_user';

  const PENDING = 0;
  const SENT = 1;

  public function __construct() {
    parent::__construct();
  }
  
  public static function updateInvitationAsSent(int $invitation_per_user_id = null) : bool
  {
    if(isset($invitation_per_user_id))
    {
      $InvitationPerUser = new self;
      
      if($InvitationPerUser->loadWhere("invitation_per_user_id = ?", $invitation_per_user_id))
      {
        $InvitationPerUser->send_date = time();
        $InvitationPerUser->status = self::SENT;
        
        return $InvitationPerUser->save();
      }
    }

    return false;
  }

  public static function add(array $data = null) : int|bool 
  {
    if (isset($data) === true) 
    {
      $InvitationPerUser = new self;
      $InvitationPerUser->user_login_id = $data['user_login_id'];
      $InvitationPerUser->contact = $data['contact'];
      $InvitationPerUser->catalog_channel_id = $data['catalog_channel_id'];
      $InvitationPerUser->catalog_invitation_template_id = $data['catalog_invitation_template_id'];
      $InvitationPerUser->status = self::PENDING;
      $InvitationPerUser->create_date = time();
      
      return $InvitationPerUser->save() ? $InvitationPerUser->getId() : false;
    }

    return false;
  }

  public function getAll(int $user_login_id = null) 
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.contact,
                {$this->tblName}.status,
                {$this->tblName}.send_date,
                {$this->tblName}.create_date,
                catalog_channel.channel,
                catalog_invitation_template.title,
                catalog_invitation_template.template
              FROM
                {$this->tblName}
              LEFT JOIN 
                catalog_channel
              ON 
                catalog_channel.catalog_channel_id = {$this->tblName}.catalog_channel_id
              LEFT JOIN 
                catalog_invitation_template
              ON 
                catalog_invitation_template.catalog_invitation_template_id
              WHERE
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '1'
              ";
      return $this->connection()->rows($sql);
    }

    return false;
  }
}