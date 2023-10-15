<?php

namespace DummieTrading;

use HCStudio\Orm;

class ConversationPerChat extends Orm {
    protected $tblName  = 'conversation_per_chat';
    public function __construct() {
        parent::__construct();
    }

    public function getLastMessages($chat_per_sheet_id = null,$filter = "")
    {
        if (isset($chat_per_sheet_id) === true) {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.message,
                        {$this->tblName}.send_from,
                        {$this->tblName}.catalog_hook_id,
                        {$this->tblName}.create_date
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.chat_per_sheet_id = '{$chat_per_sheet_id}'
                    AND
                        {$this->tblName}.status = '1'
                        {$filter}
                        ";
            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function hasConversation($chat_per_sheet_id = null)
    {
    	if (isset($chat_per_sheet_id) === true) {
    		$sql = "SELECT 
    					COUNT({$this->tblName}.{$this->tblName}_id) as c
    				FROM 
    					{$this->tblName}
    				WHERE
    					{$this->tblName}.chat_per_sheet_id = '{$chat_per_sheet_id}'
    				AND
    					{$this->tblName}.status = '1'
    					";
    		return $this->connection()->field($sql) ? true : false;
    	}

    	return false;
    }
}