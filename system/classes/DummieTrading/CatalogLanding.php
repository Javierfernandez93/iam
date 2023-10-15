<?php

namespace DummieTrading;

use HCStudio\Orm;
use JFStudio\Constants;

class CatalogLanding extends Orm {
    protected $tblName  = 'catalog_landing';

    public function __construct() {
        parent::__construct();
    }

    public function getAll() 
    {
        $sql = "SELECT 
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.landing,
                    {$this->tblName}.description,
                    {$this->tblName}.path,
                    {$this->tblName}.image
                FROM 
                    {$this->tblName} 
                WHERE 
                    {$this->tblName}.status = '".Constants::AVIABLE."'
                ";
        return $this->connection()->rows($sql);
    }
}