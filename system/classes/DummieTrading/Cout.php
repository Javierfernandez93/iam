<?php

namespace DummieTrading;

use HCStudio\Orm;

class Cout extends Orm {
	protected $tblName = 'cout';

    const ACTIVE = 1;
	public function __construct() {
		parent::__construct();
	}
	
	public function getFilter(string $filter = null) 
    {
        $sql = "SELECT
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.start_date,
                    {$this->tblName}.end_date
                FROM 
                    {$this->tblName}
                    {$filter}
                ";

        return $this->connection()->row($sql);
    }

	public function getActual(bool $unix = false) 
	{
        if($date = $this->getFilter(" WHERE {$this->tblName}.status = '".self::ACTIVE."'"))
        {
            if($unix === true)
            {
                $date['start_date'] = strtotime($date['start_date']);
                $date['end_date'] = strtotime($date['end_date']);
            }
            
            return $date;
        }

        return false;
	}
}