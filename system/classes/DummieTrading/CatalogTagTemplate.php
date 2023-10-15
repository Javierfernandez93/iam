<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogTagTemplate extends Orm {
    protected $tblName  = 'catalog_tag_template';
    public function __construct() {
        parent::__construct();
    }

    public function getAll(int $template_id = null) 
    {
        if (isset($template_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.tag,
                        {$this->tblName}.catalog_tag_value_id,
                        {$this->tblName}.description
                    FROM
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.template_id = '{$template_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ORDER BY 
                        {$this->tblName}.order_id 
                    ASC
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }
}