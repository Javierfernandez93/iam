<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserPerProyect extends Orm {
    protected $tblName  = 'user_per_proyect';
    public static $DELETED = -1;
    public static $INACTIVE = 0;
    public static $ACTIVE = 1;
    public function __construct() {
        parent::__construct();
    }
    public function getAll($proyect_id = null) 
    {
        if(isset($proyect_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.status,
                        {$this->tblName}.user_login_id,
                        CONCAT_WS(' ',user_data.names,user_data.last_name) as names,
                        user_login.last_login_date,
                        user_account.skills,
                        user_setting.image
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        user_data
                    ON 
                        user_data.user_login_id = {$this->tblName}.user_login_id
                    LEFT JOIN 
                        user_account
                    ON 
                        user_account.user_login_id = {$this->tblName}.user_login_id
                    LEFT JOIN 
                        user_setting
                    ON 
                        user_setting.user_login_id = {$this->tblName}.user_login_id
                    LEFT JOIN 
                        user_login
                    ON 
                        user_login.user_login_id = {$this->tblName}.user_login_id
                    WHERE
                        {$this->tblName}.proyect_id = '{$proyect_id}'
                    AND
                        {$this->tblName}.status != '".self::$DELETED."'
                        ";

            return $this->connection()->rows($sql);
        }

        return false;
    }
    public function getAllSingle($proyect_id = null) 
    {
        if(isset($proyect_id) === true)
        {
            $sql = "SELECT 
                        CONCAT_WS(' ',user_data.names,user_data.last_name) as names,
                        user_setting.user_login_id,
                        user_setting.image
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        user_data
                    ON 
                        user_data.user_login_id = {$this->tblName}.user_login_id
                    LEFT JOIN 
                        user_setting
                    ON 
                        user_setting.user_login_id = {$this->tblName}.user_login_id
                    WHERE
                        {$this->tblName}.proyect_id = '{$proyect_id}'
                        ";

            return $this->connection()->rows($sql);
        }

        return false;
    }
}