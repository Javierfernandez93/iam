<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class CatalogBanner extends Orm {
    protected $tblName  = 'catalog_banner';

    const TOP_LEFT = 1;
    const TOP_RIGHT = 2;
    const BOTTOM_LEFT = 3;
    const WHATSAPP_LEFT = 4;
    const WHATSAPP_RIGHT = 5;

    public function __construct() {
        parent::__construct();
    }

    public function getAll() 
    {
        $sql = "SELECT
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.name,
                    {$this->tblName}.position,
                    {$this->tblName}.width,
                    {$this->tblName}.height
                FROM 
                    {$this->tblName}
                WHERE 
                    {$this->tblName}.status = '".Constants::AVIABLE."'
                ";

        return $this->connection()->rows($sql);
    }
}