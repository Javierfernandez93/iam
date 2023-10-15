<?php

namespace DummieTrading;

use HCStudio\Orm;

class WhatsAppMessageSendPerContact extends Orm {
    protected $tblName = 'whatsapp_message_send_per_contact';

    /* constants */
    const SENT = 1;
    const FOR_SEND = 0;
    const LIMIT = 10;

    public function __construct() {
        parent::__construct();
    }

    public static function saveMessage(array $data = null) : bool
    {
        $WhatsAppMessageSendPerContact = new WhatsAppMessageSendPerContact;
        $WhatsAppMessageSendPerContact->connection()->stmtQuery("SET NAMES utf8mb4");
        $WhatsAppMessageSendPerContact->message = $data['message'] ? $data['message'] : '';
        $WhatsAppMessageSendPerContact->whatsapp_message_schedule_id = $data['whatsapp_message_schedule_id'] ? $data['whatsapp_message_schedule_id'] : 0;
        $WhatsAppMessageSendPerContact->contact_per_whatsapp_list_id = $data['contact_per_whatsapp_list_id'];
        $WhatsAppMessageSendPerContact->create_date = time();
        $WhatsAppMessageSendPerContact->status = isset($data['status']) === true ? $data['status'] : self::SENT;

        return $WhatsAppMessageSendPerContact->save();
    }

    public static function saveMessages(array $data = null) : bool
    {
        if (isset($data) === true) 
        {
            $saved = 0;
            
            foreach ($data['contacts'] as $contact)
            {   
                if(self::saveMessage([
                    'contact_per_whatsapp_list_id' => $contact['contact_per_whatsapp_list_id'],
                    'message' => $data['message'],
                ]))
                {
                    $saved++;
                }
            }

            return $saved == sizeof($data['contacts']);
        }

        return false;
    }

    public static function pushMessages(array $data = null) : bool
    {
        if (isset($data) === true) 
        {   
            foreach ($data['lists'] as $list)
            {
                foreach ($list['contacts'] as $contact)
                {   
                    if(!(new WhatsAppMessageSendPerContact)->exist($data['whatsapp_message_per_campaign_id'],$contact['contact_per_whatsapp_list_id']))
                    {
                        self::saveMessage([
                            'contact_per_whatsapp_list_id' => $contact['contact_per_whatsapp_list_id'],
                            'whatsapp_message_schedule_id' => $data['whatsapp_message_schedule_id'],
                            'status' => self::FOR_SEND,
                        ]);
                    } 
                }
            }

            return true;
        }

        return false;
    }

    public function exist(int $whatsapp_message_per_campaign_id = null,$contact_per_whatsapp_list_id = null) : bool
    {
        if(isset($whatsapp_message_per_campaign_id,$contact_per_whatsapp_list_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_message_per_campaign_id = '{$whatsapp_message_per_campaign_id}'
                    AND 
                        {$this->tblName}.contact_per_whatsapp_list_id = '{$contact_per_whatsapp_list_id}'
                    AND 
                        {$this->tblName}.status != '".self::SENT."' 
                    ";
                    
            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }

    public function getPendingForSchedule(int $whatsapp_message_schedule_id = null)
    {
        if(isset($whatsapp_message_schedule_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.contact_per_whatsapp_list_id,
                        whatsapp_contact.phone
                    FROM 
                        {$this->tblName} 
                    LEFT JOIN 
                        contact_per_whatsapp_list
                    ON 
                        contact_per_whatsapp_list.contact_per_whatsapp_list_id = {$this->tblName}.contact_per_whatsapp_list_id
                    LEFT JOIN 
                        whatsapp_contact
                    ON 
                        whatsapp_contact.whatsapp_contact_id = contact_per_whatsapp_list.whatsapp_contact_id
                    WHERE 
                        {$this->tblName}.whatsapp_message_schedule_id = '{$whatsapp_message_schedule_id}'
                    AND 
                        {$this->tblName}.status = '".self::FOR_SEND."' 
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function hasMessagesPending(int $whatsapp_message_schedule_id = null) : bool
    {
        if(isset($whatsapp_message_schedule_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_message_schedule_id = '{$whatsapp_message_schedule_id}'
                    AND 
                        {$this->tblName}.status = '".self::FOR_SEND."' 
                    ";
            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }

    public static function setMessagesAsSent(array $messages = null)
    {
        array_map(function($message){
            self::setAsSent($message['whatsapp_message_send_per_contact_id']);
        },$messages);
    }

    public static function setAsSent(int $whatsapp_message_send_per_contact_id = null)
    {
        $WhatsAppMessageSendPerContact = new WhatsAppMessageSendPerContact;
        
        if($WhatsAppMessageSendPerContact->loadWhere('whatsapp_message_send_per_contact_id = ?',$whatsapp_message_send_per_contact_id))
        {
            $WhatsAppMessageSendPerContact->status = self::SENT;
            $WhatsAppMessageSendPerContact->save();
        }
    }
}