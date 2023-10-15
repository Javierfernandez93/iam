<?php

namespace DummieTrading;

use HCStudio\Orm;

class ProspectPerSheet extends Orm {
    protected $tblName  = 'prospect_per_sheet';
    public function __construct() {
        parent::__construct();
    }
    public function getAll(int $sheet_per_proyect_id = null) {

        if(isset($sheet_per_proyect_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.prospect_id,
                        {$this->tblName}.sheet_per_proyect_id,
                        {$this->tblName}.create_date,
                        prospect.ip,
                        prospect.prospect_unique_id,
                        prospect.system
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        prospect
                    ON
                        prospect.prospect_id = {$this->tblName}.prospect_id
                    WHERE
                        {$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
                    ORDER BY 
                        {$this->tblName}.create_date 
                    DESC

                    ";

            return $this->connection()->rows($sql);
        }
        return false;
    }
}