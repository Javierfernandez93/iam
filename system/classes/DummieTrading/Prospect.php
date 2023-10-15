<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Session;
use JFStudio\Cookie;

class Prospect extends Orm {
    protected $tblName  = 'prospect';
    public static $UNIQUE_ID = "UNIQUE_ID_OWB";
    public function __construct() {
        parent::__construct();
    }
    public function getFieldsProspect() 
    {
        $Session = new Session(self::$UNIQUE_ID);
        
        return [
            'unique_id' => $Session->get('unique_id'),
            'sheet_per_proyect_id' => $Session->get('sheet_per_proyect_id'),
        ];
    }
    public function setFieldsProspect($unique_id = null,$sheet_per_proyect_id = null) 
    {
        if(isset($unique_id,$sheet_per_proyect_id) === true)
        {
            $Session = new Session(self::$UNIQUE_ID);
            $Session->set('unique_id',$unique_id);
            $Session->set('sheet_per_proyect_id',$sheet_per_proyect_id);

            return true;
        }

        return false;
    }

    public static function getUniqueID() 
    {
        $temp = Cookie::get(self::$UNIQUE_ID); 

        if(is_array($temp) === true || $temp == false)
        {
            Cookie::set(self::$UNIQUE_ID,uniqid());
        }    

        return Cookie::get(self::$UNIQUE_ID);
    }

    public function getAll(int $user_login_id = null) {

        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.proyect_id,
                        {$this->tblName}.unique_id,
                        {$this->tblName}.proyect
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.user_login_id = '{$user_login_id}'";

            return $this->connection()->rows($sql);
        }
        return false;
    }
}