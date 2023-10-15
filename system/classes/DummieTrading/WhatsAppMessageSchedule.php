<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;
use JFStudio\Curl;

use DummieTrading\WhatsAppMessagePerCampaign;
use DummieTrading\WhatsAppMessageSendPerContact;
use DummieTrading\WhatsAppSessionPerUser;

use Exception;

class WhatsAppMessageSchedule extends Orm {
    protected $tblName = 'whatsapp_message_schedule';

    /* consts */
    const DISABLED = 0;
    const FOR_SEND = 1;
    const SENT = 2;
    const WHATSAPP_SERVER = 'http://localhost:3000/';

    const INMEDIATE = 0;

    public function __construct() {
        parent::__construct();
    }
    
    public static function setScheduleAsSent(int $whatsapp_message_schedule_id = null) : bool
    {
        if(isset($whatsapp_message_schedule_id) === true)
        {
            if(!(new WhatsAppMessageSendPerContact)->hasMessagesPending($whatsapp_message_schedule_id))
            {
                $WhatsAppMessageSchedule = new WhatsAppMessageSchedule;
                
                if($WhatsAppMessageSchedule->loadWhere('whatsapp_message_schedule_id = ?',$whatsapp_message_schedule_id))
                {
                    $WhatsAppMessageSchedule->status = self::SENT;
                    $WhatsAppMessageSchedule->send_date = time();
                    
                    if($WhatsAppMessageSchedule->save())
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function getUrlForMessages()
    {
        return self::WHATSAPP_SERVER.'whatsapp/hook/sendMessageToList';
    } 

    public function sendMessage(array $data = null) 
    {
        if(isset($data) === true)
        {
            if($data['id'])
            {
                $Curl = new Curl;
                $Curl->setHeader('Content-Type','application/json');
                $Curl->post(self::getUrlForMessages(),json_encode($data));
                
                $response = $Curl->getResponse(true);

                if($response['s'] == 1)
                {
                    return true;
                } else {
                    throw new Exception($response['r']);
                }
            } else {
                throw new Exception('NOT_ID');
            }
        } else {
            throw new Exception('NOT_DATA');
        }
    }

    public function getAll(int $whatsapp_campaign_id = null) 
    {
        if(isset($whatsapp_campaign_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.name,
                        {$this->tblName}.description,
                        {$this->tblName}.whatsapp_list_per_user_ids,
                        {$this->tblName}.status,
                        {$this->tblName}.send_date,
                        {$this->tblName}.schedule_date
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_campaign_id = '{$whatsapp_campaign_id}'
                    AND 
                        {$this->tblName}.status != '".Constants::DELETE."'
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getAllPending() 
    {
        if($schedules = $this->_getAllPending())
        {
            return array_map(function($schedule){
                $schedule['contacts'] = (new WhatsAppMessageSendPerContact)->getPendingForSchedule($schedule['whatsapp_message_schedule_id']);
                $schedule['message'] = (new WhatsAppMessagePerCampaign)->_get($schedule['whatsapp_message_per_campaign_id']);
                $schedule['sessionName'] = (new WhatsAppSessionPerUser)->getSessionName($schedule['user_login_id']);
            
                return $schedule;
            },$schedules);
        }
    }

    public function _getAllPending() 
    {
        $start_date = strtotime(date("Y/m/d H:00:00"));

        $sql = "SELECT 
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.name,
                    {$this->tblName}.description,
                    {$this->tblName}.whatsapp_message_per_campaign_id,
                    {$this->tblName}.whatsapp_list_per_user_ids,
                    {$this->tblName}.status,
                    {$this->tblName}.schedule_date,
                    whatsapp_message_per_campaign.image,
                    whatsapp_message_per_campaign.content,
                    whatsapp_message_per_campaign.title,
                    whatsapp_campaign.user_login_id
                FROM 
                    {$this->tblName} 
                LEFT JOIN 
                    whatsapp_message_per_campaign
                ON 
                    whatsapp_message_per_campaign.whatsapp_message_per_campaign_id = {$this->tblName}.whatsapp_message_per_campaign_id
                LEFT JOIN 
                    whatsapp_campaign
                ON 
                    whatsapp_campaign.whatsapp_campaign_id = {$this->tblName}.whatsapp_campaign_id
                WHERE 
                    {$this->tblName}.status = '".self::FOR_SEND."'
                AND 
                    (
                        {$this->tblName}.schedule_date >= '{$start_date}'
                    OR 
                        {$this->tblName}.schedule_date = '".self::INMEDIATE."'
                    )
                LIMIT ".WhatsAppMessageSendPerContact::LIMIT."
                ";
                
        return $this->connection()->rows($sql);
    }
    
    public static function getUnformattedLists(array $data = null) : string
    {
        return json_encode(array_column($data, 'whatsapp_list_per_user_id'));
    }

    public static function saveSchedule(array $data = null) 
    {
        if(isset($data) === true)
        {
            $WhatsAppMessageSchedule = new WhatsAppMessageSchedule;

            if($data['whatsapp_message_schedule_id'])
            {
                $WhatsAppMessageSchedule->loadWhere('whatsapp_message_schedule_id = ?',$data['whatsapp_message_schedule_id']);
            }

            $WhatsAppMessageSchedule->whatsapp_campaign_id = $data['whatsapp_campaign_id'];
            $WhatsAppMessageSchedule->whatsapp_message_per_campaign_id = $data['whatsapp_message_per_campaign_id'];
            $WhatsAppMessageSchedule->whatsapp_list_per_user_ids = self::getUnformattedLists($data['campaign']['lists']);
            $WhatsAppMessageSchedule->name = $data['name'];
            $WhatsAppMessageSchedule->description = $data['description'];
            $WhatsAppMessageSchedule->schedule_date = $data['schedule_date'] ? strtotime($data['schedule_date']) : self::INMEDIATE;
            $WhatsAppMessageSchedule->status = self::FOR_SEND;
            $WhatsAppMessageSchedule->send_date = 0;
            $WhatsAppMessageSchedule->create_date = time();

            if($WhatsAppMessageSchedule->save())
            {
                return $WhatsAppMessageSchedule->getId();
            }
        }

        return false;
    }

    public function get(int $whatsapp_message_schedule_id = null) 
    {
        if(isset($whatsapp_message_schedule_id) === true)
        {
            if($schedule = $this->_get($whatsapp_message_schedule_id))
            {
                $WhatsAppListPerUser = new WhatsAppListPerUser;

                if($schedule['whatsapp_list_per_user_ids'])
                {
                    $schedule['whatsapp_list_per_user_ids'] = json_decode($schedule['whatsapp_list_per_user_ids'],true);
                }
                
                return $schedule;
            }
        }

        return false;
    }

    public function _get(int $whatsapp_message_schedule_id = null) 
    {
        if(isset($whatsapp_message_schedule_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.name,
                        {$this->tblName}.schedule_date,
                        {$this->tblName}.description,
                        {$this->tblName}.whatsapp_campaign_id,
                        {$this->tblName}.whatsapp_message_per_campaign_id,
                        {$this->tblName}.whatsapp_list_per_user_ids,
                        {$this->tblName}.create_date,
                        {$this->tblName}.status
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_message_schedule_id = '{$whatsapp_message_schedule_id}'
                    ";
                    
            return $this->connection()->row($sql);
        }

        return false;
    }

    public static function pushMessagesInSchedule(array $data = null)
    {
        return WhatsAppMessageSendPerContact::pushMessages($data);
    }
}