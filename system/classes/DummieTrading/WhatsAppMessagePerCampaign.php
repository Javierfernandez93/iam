<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

use DummieTrading\WhatsAppCampaign;

class WhatsAppMessagePerCampaign extends Orm {
    protected $tblName = 'whatsapp_message_per_campaign';

    public function __construct() {
        parent::__construct();
    }

    public function getAll(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            if($whatsapp_campaign_ids = (new WhatsAppCampaign)->getAllIds($user_login_id))
            {
                $whatsapp_campaign_ids = implode(',',$whatsapp_campaign_ids);

                return $this->_getAll($whatsapp_campaign_ids);
            }
        }

        return false;
    }
    
    public function getCountIn(int $campaigns_in = null) 
    {
        if(isset($campaigns_in) === true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_campaign_id IN({$campaigns_in})
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->field($sql);
        }

        return false;
    }

    public function _getAll(string $whatsapp_campaign_ids = null) 
    {
        if(isset($whatsapp_campaign_ids) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.title,
                        {$this->tblName}.image,
                        {$this->tblName}.content,
                        {$this->tblName}.body,
                        {$this->tblName}.footer,
                        {$this->tblName}.button,
                        {$this->tblName}.status,
                        {$this->tblName}.section,
                        {$this->tblName}.create_date,
                        catalog_message_whatsapp_type.catalog_message_whatsapp_type_id,
                        catalog_message_whatsapp_type.name 
                    FROM 
                        {$this->tblName} 
                    LEFT JOIN 
                        catalog_message_whatsapp_type
                    ON 
                        catalog_message_whatsapp_type.catalog_message_whatsapp_type_id = {$this->tblName}.catalog_message_whatsapp_type_id
                    WHERE 
                        {$this->tblName}.whatsapp_campaign_id IN({$whatsapp_campaign_ids})
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getAllFromCampaing(string $whatsapp_campaign_id = null) 
    {
        if(isset($whatsapp_campaign_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.title,
                        {$this->tblName}.create_date,
                        catalog_message_whatsapp_type.catalog_message_whatsapp_type_id,
                        catalog_message_whatsapp_type.name 
                    FROM 
                        {$this->tblName} 
                    LEFT JOIN 
                        catalog_message_whatsapp_type
                    ON 
                        catalog_message_whatsapp_type.catalog_message_whatsapp_type_id = {$this->tblName}.catalog_message_whatsapp_type_id
                    WHERE 
                        {$this->tblName}.whatsapp_campaign_id IN({$whatsapp_campaign_id})
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }
    
    public function _get(string $whatsapp_message_per_campaign_id = null) 
    {
        if(isset($whatsapp_message_per_campaign_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.title,
                        {$this->tblName}.image,
                        {$this->tblName}.content,
                        {$this->tblName}.body,
                        {$this->tblName}.footer,
                        {$this->tblName}.button,
                        {$this->tblName}.status,
                        {$this->tblName}.section,
                        {$this->tblName}.create_date,
                        catalog_message_whatsapp_type.catalog_message_whatsapp_type_id,
                        catalog_message_whatsapp_type.name 
                    FROM 
                        {$this->tblName} 
                    LEFT JOIN 
                        catalog_message_whatsapp_type
                    ON 
                        catalog_message_whatsapp_type.catalog_message_whatsapp_type_id = {$this->tblName}.catalog_message_whatsapp_type_id
                    WHERE 
                        {$this->tblName}.whatsapp_message_per_campaign_id = '{$whatsapp_message_per_campaign_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->row($sql);
        }

        return false;
    }
}