<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogPlatform extends Orm {
    protected $tblName  = 'catalog_platform';
    public function __construct() {
        parent::__construct();
    }

    public function getType(int $catalog_platform_id = null) : string|bool
    {
        if (isset($catalog_platform_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.type
                    FROM
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.catalog_platform_id = '{$catalog_platform_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }
}