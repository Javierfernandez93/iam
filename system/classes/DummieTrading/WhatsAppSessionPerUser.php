<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Token;
use JFStudio\Constants;

class WhatsAppSessionPerUser extends Orm {
    protected $tblName  = 'whatsapp_session_per_user';

    public function __construct() {
        parent::__construct();
    }

    public static function generateApiKeys(int $user_login_id = null) : bool
    {
        $WhatsAppSessionPerUser = new WhatsAppSessionPerUser;
        
        if($WhatsAppSessionPerUser->loadWhere('user_login_id = ?',$user_login_id))
        {
            if(!$WhatsAppSessionPerUser->hasApiKeys($WhatsAppSessionPerUser->getId()))
            {
                $Token = new Token;
                $token = $Token->getToken([
                    'user_login_id' => $WhatsAppSessionPerUser->user_login_id,
                    'session_name' => $WhatsAppSessionPerUser->session_name
                ]);

                $WhatsAppSessionPerUser->client_id = $token['key'];
                $WhatsAppSessionPerUser->client_secret = $token['token'];
                
                return $WhatsAppSessionPerUser->save();
            }
        }

        return false;
    }

    public static function loadByAPIKeys(array $api_data = null) : bool
    {
        if(isset($api_data) === true)
        {
            $WhatsAppSessionPerUser = new WhatsAppSessionPerUser;
            
            if($WhatsAppSessionPerUser->loadWhere('client_id = ? AND client_secret = ?',[
                    'client_id' => $api_data['client_id'],
                    'client_secret' => $api_data['client_secret'],
                ]))
            {
                d(1);
            }
        }

        return false;
    }

    public static function setSavesession(array $data = null) : bool
    {
        if(isset($data) === true)
        {
            $WhatsAppSessionPerUser = new WhatsAppSessionPerUser;
            
            if(!$WhatsAppSessionPerUser->loadWhere('user_login_id = ?',$data['user_login_id']))
            {
                $WhatsAppSessionPerUser->user_login_id = $data['user_login_id'];
                $WhatsAppSessionPerUser->server_address = $data['server_address'];
                $WhatsAppSessionPerUser->create_date = time();
            }
            
            $WhatsAppSessionPerUser->last_login_date = time();
            $WhatsAppSessionPerUser->name = $data['name'] ? $data['name'] : $WhatsAppSessionPerUser->name;
            $WhatsAppSessionPerUser->session_name = $data['session_name'] ? $data['session_name'] : $WhatsAppSessionPerUser->session_name;
            $WhatsAppSessionPerUser->phone = $data['phone'] ? $data['phone'] : $WhatsAppSessionPerUser->phone;

            return $WhatsAppSessionPerUser->save();
        }

        return false;
    }   
    
    public function getUserBySessionName(string $session_name = null)
    {
        if(isset($session_name) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.user_login_id
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.session_name = '{$session_name}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->field($sql);
        }

        return false;
    }   

    public function getVars(int $user_login_id = null)
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.server_name
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
            return $this->connection()->row($sql);
        }

        return false;
    }   

    public function getSessionName(int $user_login_id = null)
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.session_name
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
                    
            return $this->connection()->field($sql);
        }

        return false;
    }   

    public function hasApiKeys(int $whatsapp_session_per_user_id = null) : bool
    {
        if(isset($whatsapp_session_per_user_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.whatsapp_session_per_user_id = '{$whatsapp_session_per_user_id}'
                    AND 
                        {$this->tblName}.client_id != ''
                    AND 
                        {$this->tblName}.client_secret != ''
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
                    
                    
            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }   
}