<?php

namespace DummieTrading;

use HCStudio\Orm;

class UserWorkingPerProyect extends Orm {
    protected $tblName  = 'user_working_per_proyect';
    public function __construct() {
        parent::__construct();
    }

    public function countWorkingProyects($user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.proyect_id) AS C
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '1'
                        ";
                        
            return $this->connection()->field($sql);
        }

        return false;
    }
}