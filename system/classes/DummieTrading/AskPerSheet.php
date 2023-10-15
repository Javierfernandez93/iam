<?php

namespace DummieTrading;

use HCStudio\Orm;

class AskPerSheet extends Orm {
    protected $tblName  = 'ask_per_sheet';
    public function __construct() {
        parent::__construct();
    }
    public function getCatalogAsk($sheet_per_proyect_id = null) 
    {
    	if (isset($sheet_per_proyect_id) === true) 
    	{
    		$sql = "SELECT 
                        catalog_ask.catalog_ask_id,
                        catalog_ask.name
    				FROM
    					{$this->tblName}
                    LEFT JOIN 
                        catalog_ask
                    ON
                        catalog_ask.catalog_ask_id = {$this->tblName}.catalog_ask_id
    				WHERE
    					{$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
    				AND 
    					{$this->tblName}.status = '1'
                    GROUP BY 
                        {$this->tblName}.catalog_ask_id
    				";
                    
    		return $this->connection()->rows($sql);
    	}

    	return false;
    }

    public function getAll($catalog_ask_id = null) 
    {
        if (isset($catalog_ask_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.ask
                    FROM
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.catalog_ask_id = '{$catalog_ask_id}'
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