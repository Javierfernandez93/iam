<?php

namespace DummieTrading;

use HCStudio\Orm;

class CampaignPerSheet extends Orm {
    protected $tblName  = 'campaign_per_sheet';
    public function __construct() {
        parent::__construct();
    }
    public function getAll($filter = "") 
    {
    	$sql = "SELECT 
    				{$this->tblName}.{$this->tblName}_id,
    				{$this->tblName}.campaign_name,
    				{$this->tblName}.sheet_per_proyect_id,
    				{$this->tblName}.programated_date,
                    {$this->tblName}.create_date,
                    {$this->tblName}.mail_list_id,
    				{$this->tblName}.mail_template_id,
                    {$this->tblName}.title,
                    proyect.proyect,
    				mail_template.template,
    				mail_list.list_name
    			FROM 
    				{$this->tblName}
    			LEFT JOIN 
    				mail_list
    			ON 
    				mail_list.mail_list_id = {$this->tblName}.mail_list_id
                LEFT JOIN 
    				sheet_per_proyect
    			ON 
                    sheet_per_proyect.sheet_per_proyect_id = {$this->tblName}.sheet_per_proyect_id
                LEFT JOIN 
    				proyect
    			ON 
                    proyect.proyect_id = sheet_per_proyect.proyect_id
    			LEFT JOIN 
    				mail_template
    			ON 
    				mail_template.mail_template_id = {$this->tblName}.mail_template_id
				WHERE 
    				{$this->tblName}.status = '1'
                    {$filter}
				";

		return $this->connection()->rows($sql);
    }
    public function hasCampaing($sheet_per_proyect_id = null) 
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