<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogVar extends Orm {
    protected $tblName  = 'catalog_var';
    public function __construct() {
        parent::__construct();
    }

    public function getAll() 
    {
        $sql = "SELECT
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.name
                FROM 
                    {$this->tblName}
                WHERE 
                    {$this->tblName}.status = '1'
                ";

        return $this->connection()->rows($sql);
    }

    public function getVarId(string $name = null) 
    {
        if(isset($name))
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.name = '{$name}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }
    
    public function getVarIdByIdentificator(string $identificator = null) 
    {
        if(isset($identificator))
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.identificator = '{$identificator}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }
}