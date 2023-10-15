<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

class WhatsAppListPerUser extends Orm {
    protected $tblName  = 'whatsapp_list_per_user';

    public function __construct() {
        parent::__construct();
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
                        {$this->tblName}.whatsapp_list_per_user_id IN({$campaigns_in})
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->field($sql);
        }

        return false;
    }

    public function getAll(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.name,
                        {$this->tblName}.description,
                        {$this->tblName}.status,
                        {$this->tblName}.create_date
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status != '".Constants::DELETE."'
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }
   
    public function get(int $whatsapp_list_per_user_id = null) 
    {
        if(isset($whatsapp_list_per_user_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.name,
                        {$this->tblName}.description,
                        {$this->tblName}.create_date
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_list_per_user_id = '{$whatsapp_list_per_user_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->row($sql);
        }

        return false;
    }
}