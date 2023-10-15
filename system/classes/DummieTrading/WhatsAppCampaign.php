<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

use DummieTrading\WhatsAppListPerUser;
use DummieTrading\WhatsAppMessagePerCampaign;
use DummieTrading\ContactPerWhatsAppList;

class WhatsAppCampaign extends Orm {
    protected $tblName  = 'whatsapp_campaign';

    public function __construct() {
        parent::__construct();
    }

    public function getAll(int $user_login_id = null) 
    {
        if($campaigns = $this->_getAll($user_login_id))
        {
            $WhatsAppListPerUser = new WhatsAppListPerUser;

            return array_map(function($campaign) use($WhatsAppListPerUser) {
                if($campaign['whatsapp_list_per_user_ids'])
                {
                    $campaign['whatsapp_list_per_user_ids'] = json_decode($campaign['whatsapp_list_per_user_ids'],true);

                    foreach($campaign['whatsapp_list_per_user_ids'] as $whatsapp_list_per_user_id)
                    {
                        $campaign['lists'][] = $WhatsAppListPerUser->get($whatsapp_list_per_user_id);
                    }
                }
                return $campaign;
            },$campaigns);
        }

        return false;
    }
    
    public function getAllListCount(int $user_login_id = null) : int
    {
        if($list = $this->getAllSingleList($user_login_id))
        {
            $list_in = implode(',',$list);

            return (new WhatsAppListPerUser)->getCountIn($list_in);
        }

        return 0;
    }
   
    public function getAllMessageCount(int $user_login_id = null) : int
    {
        if($campaigns = $this->getAllSingle($user_login_id))
        {
            $campaigns_in = implode(',',$campaigns);
    
            return (new WhatsAppMessagePerCampaign)->getCountIn($campaigns_in);
        }

        return 0;
    }
   
    public function getAllContactsCount(int $user_login_id = null) : int
    {
        if($list = $this->getAllSingleList($user_login_id))
        {
            $list_in = implode(',',$list);

            return (new ContactPerWhatsAppList)->getCountIn($list_in);
        }

        return 0;
    }
    
    public function _getAll(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.name,
                        {$this->tblName}.send_date,
                        {$this->tblName}.description,
                        {$this->tblName}.whatsapp_list_per_user_ids,
                        {$this->tblName}.create_date,
                        {$this->tblName}.status
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getAllSingleList(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            if($campaigns = $this->_getAllSingleList($user_login_id))
            {
                $_campaigns = [];

                foreach($campaigns as $campaign)
                {
                    $_campaigns = array_merge($_campaigns, json_decode($campaign,true));
                }

                return array_unique($_campaigns);
            }
        }

        return false;
    }

    public function _getAllSingleList(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.whatsapp_list_per_user_ids
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.whatsapp_list_per_user_ids != ''
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->column($sql);
        }

        return false;
    }

    public function getAllSingle(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->column($sql);
        }

        return false;
    }
    
    public function getCount(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->field($sql);
        }

        return false;
    }
    
    public function getAllIds(int $user_login_id = null) 
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
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->column($sql);
        }

        return false;
    }

    public function get(int $whatsapp_campaign_id = null) 
    {
        if(isset($whatsapp_campaign_id) === true)
        {
            if($campaign = $this->_get($whatsapp_campaign_id))
            {
                $ContactPerWhatsAppList = new ContactPerWhatsAppList;
                $WhatsAppListPerUser = new WhatsAppListPerUser;

                if($campaign['whatsapp_list_per_user_ids'])
                {
                    $campaign['whatsapp_list_per_user_ids'] = json_decode($campaign['whatsapp_list_per_user_ids'],true);

                    foreach($campaign['whatsapp_list_per_user_ids'] as $whatsapp_list_per_user_id)
                    {
                        $campaign['lists'][] = [
                            'list' => $WhatsAppListPerUser->get($whatsapp_list_per_user_id),
                            'contacts' => $ContactPerWhatsAppList->getAll($whatsapp_list_per_user_id)
                        ];
                    }
                }
                
                return $campaign;
            }
        }

        return false;
    }

    public function getSingle(int $whatsapp_campaign_id = null) 
    {
        if(isset($whatsapp_campaign_id) === true)
        {
            if($campaign = $this->_get($whatsapp_campaign_id))
            {
                $WhatsAppListPerUser = new WhatsAppListPerUser;

                $campaign['messages'] = (new WhatsAppMessagePerCampaign)->getAllFromCampaing($campaign['whatsapp_campaign_id']);

                if($campaign['whatsapp_list_per_user_ids'])
                {
                    $campaign['whatsapp_list_per_user_ids'] = json_decode($campaign['whatsapp_list_per_user_ids'],true);

                    foreach($campaign['whatsapp_list_per_user_ids'] as $whatsapp_list_per_user_id)
                    {
                        $campaign['lists'][] = $WhatsAppListPerUser->get($whatsapp_list_per_user_id);
                    }
                }
                
                return $campaign;
            }
        }

        return false;
    }

    public function getSessionName(int $whatsapp_campaign_id = null) 
    {
        if(isset($whatsapp_campaign_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.name,
                        {$this->tblName}.send_date,
                        {$this->tblName}.description,
                        {$this->tblName}.whatsapp_list_per_user_ids,
                        {$this->tblName}.create_date,
                        {$this->tblName}.status
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_campaign_id = '{$whatsapp_campaign_id}'
                    ";
                    
            return $this->connection()->row($sql);
        }

        return false;
    }
    
    public function _get(int $whatsapp_campaign_id = null) 
    {
        if(isset($whatsapp_campaign_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.name,
                        {$this->tblName}.send_date,
                        {$this->tblName}.description,
                        {$this->tblName}.whatsapp_list_per_user_ids,
                        {$this->tblName}.create_date,
                        {$this->tblName}.status
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_campaign_id = '{$whatsapp_campaign_id}'
                    ";
                    
            return $this->connection()->row($sql);
        }

        return false;
    }
}