<?php

namespace DummieTrading;

use HCStudio\Orm;

class VarConfiguration extends Orm {
    protected $tblName  = 'var_configuration';
    public function __construct() {
        parent::__construct();
    }

    public function getAll() 
    {
        $sql = "SELECT
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.name,
                    {$this->tblName}.description,
                    {$this->tblName}.element_type,
                    {$this->tblName}.helper,
                    {$this->tblName}.format,
                    {$this->tblName}.min_value,
                    {$this->tblName}.max_value,
                    {$this->tblName}.default_value
                FROM 
                    {$this->tblName}
                WHERE 
                    {$this->tblName}.status = '1'
                ";

        return $this->connection()->rows($sql);
    }

    public function get(int $var_configuration_id = null) 
    {
        if(isset($var_configuration_id) === true)
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.name,
                        {$this->tblName}.description
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.var_configuration_id = '{$var_configuration_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";
    
            return $this->connection()->row($sql);
        }

        return false;
    }
}