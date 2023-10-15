<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

class ContactPerWhatsAppList extends Orm {
    protected $tblName  = 'contact_per_whatsapp_list';

    public function __construct() {
        parent::__construct();
    }

    public static function saveContact(int $whatsapp_contact_id = null,int $whatsapp_list_per_user_id = null) : bool
    {
        $ContactPerWhatsAppList = new ContactPerWhatsAppList;
        
        if(!$ContactPerWhatsAppList->loadWhere('whatsapp_contact_id = ? AND whatsapp_list_per_user_id = ?',[$whatsapp_contact_id,$whatsapp_list_per_user_id]))
        {
            $ContactPerWhatsAppList->whatsapp_list_per_user_id = $whatsapp_list_per_user_id;
            $ContactPerWhatsAppList->whatsapp_contact_id = $whatsapp_contact_id;
            $ContactPerWhatsAppList->create_date = time();

            return $ContactPerWhatsAppList->save();
        }

        return true;
    }

    public static function saveContacts(array $contacts = null,int $whatsapp_list_per_user_id = null) : bool
    {
        if(isset($contacts) == true)
        {
            $saved = 0;

            foreach($contacts as $whatsapp_contact_id)
            {
                if(self::saveContact($whatsapp_contact_id,$whatsapp_list_per_user_id))
                {
                    $saved++;
                }
            }

            return $saved == sizeof($contacts);
        }

        return false;
    }

    public function getCount(int $whatsapp_list_per_user_id = null) 
    {
        if(isset($whatsapp_list_per_user_id) === true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_list_per_user_id = '{$whatsapp_list_per_user_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->field($sql);
        }

        return false;
    }
    
    
    public function getAll(int $whatsapp_list_per_user_id = null) 
    {
        if(isset($whatsapp_list_per_user_id) === true)
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.status,
                        whatsapp_contact.whatsapp_contact_id,
                        whatsapp_contact.name,
                        whatsapp_contact.phone
                    FROM 
                        {$this->tblName} 
                    LEFT JOIN 
                        whatsapp_contact
                    ON 
                        whatsapp_contact.whatsapp_contact_id =  {$this->tblName}.whatsapp_contact_id
                    WHERE 
                        {$this->tblName}.whatsapp_list_per_user_id = '{$whatsapp_list_per_user_id}'
                    AND 
                        {$this->tblName}.status != '".Constants::DELETE."'
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getAllSimple(int $whatsapp_list_per_user_id = null) 
    {
        if(isset($whatsapp_list_per_user_id) === true)
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id,
                        whatsapp_contact.name,
                        whatsapp_contact.phone
                    FROM 
                        {$this->tblName} 
                    LEFT JOIN 
                        whatsapp_contact
                    ON 
                        whatsapp_contact.whatsapp_contact_id =  {$this->tblName}.whatsapp_contact_id
                    WHERE 
                        {$this->tblName}.whatsapp_list_per_user_id = '{$whatsapp_list_per_user_id}'
                    AND 
                        {$this->tblName}.status != '".Constants::DELETE."'
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }
    
    public function getCountIn(int $whatsapp_list_per_user_id_in = null) 
    {
        if(isset($whatsapp_list_per_user_id_in) === true)
        {
            $sql = "SELECT
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_list_per_user_id IN ('{$whatsapp_list_per_user_id_in}')
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->field($sql);
        }

        return false;
    }
}