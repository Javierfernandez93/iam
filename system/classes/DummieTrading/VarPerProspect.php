<?php

namespace DummieTrading;

use HCStudio\Orm;

class VarPerProspect extends Orm {
    protected $tblName  = 'var_per_prospect';
    public function __construct() {
        parent::__construct();
    }
    public function getAll($prospect_id = null) 
    {
        $sql = "SELECT 
                    {$this->tblName}.value,
                    var_per_proyect.name,
                    catalog_var.create_date,
                    catalog_var.type,
                    catalog_var.name as var_name
                FROM 
                    {$this->tblName}
                LEFT JOIN 
                    var_per_proyect
                ON 
                    var_per_proyect.var_per_proyect_id = {$this->tblName}.var_per_proyect_id
                LEFT JOIN 
                    catalog_var
                ON 
                    catalog_var.catalog_var_id = var_per_proyect.catalog_var_id
                WHERE 
                    {$this->tblName}.prospect_id = '{$prospect_id}'
                AND 
                    {$this->tblName}.status = '1'
                ";

        return $this->connection()->rows($sql);
    }
}