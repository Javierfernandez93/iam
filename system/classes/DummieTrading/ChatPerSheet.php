<?php

namespace DummieTrading;

use HCStudio\Orm;

class ChatPerSheet extends Orm {
    protected $tblName  = 'chat_per_sheet';
    public static $MINUTES_FOR_ACTIVE = 1;
    public static $IA = 0;
    public static $HUMAN = 1;
    public function __construct() {
        parent::__construct();
    }
    public function isActive($last_join_date = null) : bool
    {
        return strtotime("+".self::$MINUTES_FOR_ACTIVE." minutes",$last_join_date) > time();
    }
    public function isAtending($chat_per_sheet_id = null) 
    {
        if (isset($chat_per_sheet_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.attend
                    FROM
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.chat_per_sheet_id = '{$chat_per_sheet_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }
    public function getSheetPerProyect($chat_per_sheet_id = null) 
    {
        if (isset($chat_per_sheet_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.sheet_per_proyect_id
                    FROM
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.chat_per_sheet_id = '{$chat_per_sheet_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }
    public function getAll($sheet_per_proyect_id = null) 
    {
    	if (isset($sheet_per_proyect_id) === true) 
    	{
    		$sql = "SELECT 
    					{$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.last_join_date,
                        {$this->tblName}.attend,
                        {$this->tblName}.user_login_id,
                        prospect_data.names,
                        prospect_data.email,
                        CONCAT_WS(' ',user_data.names,user_data.last_name) as names_atendence,
                        {$this->tblName}.prospect_id
    				FROM
    					{$this->tblName}
                    LEFT JOIN 
                        prospect_data
                    ON 
                        prospect_data.prospect_id = {$this->tblName}.prospect_id
                    LEFT JOIN 
                        user_data
                    ON 
                        user_data.user_login_id = {$this->tblName}.user_login_id
    				WHERE
    					{$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
    				AND 
    					{$this->tblName}.status = '1'
    				";

    		return $this->connection()->rows($sql);
    	}

    	return false;
    }

    public function hasChat($sheet_per_proyect_id = null) 
    {
        if(isset($sheet_per_proyect_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }
}