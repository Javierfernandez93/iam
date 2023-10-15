<?php

namespace DummieTrading;

use HCStudio\Orm;

class VarPerProyect extends Orm {
    protected $tblName  = 'var_per_proyect';
    public function __construct() {
        parent::__construct();
    }

    public function getAll($proyect_id = null) 
    {
        if (isset($proyect_id) === true) {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.create_date,
                        {$this->tblName}.name,
                        catalog_var.name_en,
                        catalog_var.name as var_name,
                        catalog_var.type
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_var 
                    ON 
                        catalog_var.catalog_var_id = {$this->tblName}.catalog_var_id
                    WHERE 
                        {$this->tblName}.proyect_id = '{$proyect_id}'
                    AND 
                        {$this->tblName}.status = 1
                    ";

            return $this->connection()->rows($sql);
        }
    }

    public function getAllVarsForPixel($proyect_id = null) 
    {
        if (isset($proyect_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        catalog_var.name_en
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_var 
                    ON 
                        catalog_var.catalog_var_id = {$this->tblName}.catalog_var_id
                    WHERE 
                        {$this->tblName}.proyect_id = '{$proyect_id}'
                    AND 
                        {$this->tblName}.status = 1
                    ";

            return $this->connection()->rows($sql);
        }
    }
}