<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogLevel extends Orm {
    protected $tblName  = 'catalog_level';

    const DEFAULT_CATALOG_LEVEL_ID = 1;
    public function __construct() {
        parent::__construct();
    }
    
    public function getAmountOfReferralsById(int $catalog_level_id = null) 
    {
        if(isset($catalog_level_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.amount
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.catalog_level_id = '{$catalog_level_id}'
                    ";

            return $this->connection()->field($sql);
        }
    }

    public function getLevelById(int $catalog_level_id = null) 
    {
        if(isset($catalog_level_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName} 
                    WHERE 
                        {$this->tblName}.catalog_level_id = '{$catalog_level_id}'
                    ";
            return $this->connection()->field($sql);
        }
    }
}