<?php

namespace DummieTrading;

use HCStudio\Orm;

class ProspectPerMailList extends Orm {
    protected $tblName  = 'prospect_per_mail_list';
    public function __construct() {
        parent::__construct();
    }

    public function getList($mail_list_id = null)
    {
        if(isset($mail_list_id) === true)
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id,
                        prospect_data.prospect_id,
                        prospect_data.names,
                        prospect_data.email
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                    	prospect
                    ON 
                    	prospect.prospect_id = {$this->tblName}.prospect_id
                    LEFT JOIN 
                    	prospect_data
                    ON 
                    	prospect_data.prospect_id = {$this->tblName}.prospect_id
                    WHERE 
                        {$this->tblName}.mail_list_id = '{$mail_list_id}'
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }
}