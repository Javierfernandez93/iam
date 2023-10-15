<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogPixel extends Orm {
    protected $tblName  = 'catalog_pixel';
    public static $VAR = 1;
    public static $CHANGE_SHEET = 2;
    public static $REDIRECTION = 3;
    public static $VIDEO = 4;
    public function __construct() {
        parent::__construct();
    }

    public function getAll() 
    {
        $sql = "SELECT
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.pixel
                FROM 
                    {$this->tblName}
                WHERE 
                    {$this->tblName}.status = '1'
                ";

        return $this->connection()->rows($sql);
    }
    public static function transcode($type = null) 
    {
        if (isset($type) === true) 
        {
            $tpye = strtolower($type);

            if($type === "int")
            {
                return "Entero";
            } else if($type === "text") {
                return "Texto";
            }
        }
    }
}