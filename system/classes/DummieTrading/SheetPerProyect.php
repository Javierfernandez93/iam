<?php

namespace DummieTrading;

use OwnBoss\CampaignPerSheet;
use OwnBoss\ChatPerSheet;
use HCStudio\Orm;
use HCStudio\Util;

class SheetPerProyect extends Orm {
    protected $tblName  = 'sheet_per_proyect';
    public static $MAX_SHEET_PER_PROYECT = 3;
    public function __construct() {
        parent::__construct();
    }

    public function getQuality($sheet_per_proyect_id = null): int {
        $score = 0;

        $SheetPerProyect = new SheetPerProyect;
        $CampaignPerSheet = new CampaignPerSheet;
        $ChatPerSheet = new ChatPerSheet;

        if($CampaignPerSheet->hasCampaing($sheet_per_proyect_id) === true)
        {
            $score ++;
        }

        if($ChatPerSheet->hasChat($sheet_per_proyect_id) === true)
        {
            $score ++;
        } 

        if($SheetPerProyect->hasSheet($sheet_per_proyect_id) === true)
        {
            $score ++;
        }

        return Util::getPercentaje(3,$score);
    }

    public function getOwnerId($sheet_per_proyect_id = null) {
        if(isset($sheet_per_proyect_id) === true)
        {
            $sql = "SELECT 
                        proyect.user_login_id
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        proyect
                    ON
                        proyect.proyect_id = sheet_per_proyect.proyect_id
                    WHERE
                        {$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'";

            return $this->connection()->field($sql);
        }

        return false;
    }
 
    public function getProyectName($sheet_per_proyect_id = null) {
        if(isset($sheet_per_proyect_id) === true)
        {
            $sql = "SELECT 
                        proyect.proyect
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        proyect
                    ON
                        proyect.proyect_id = sheet_per_proyect.proyect_id
                    WHERE
                        {$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'";

            return $this->connection()->field($sql);
        }

        return false;
    }

    public function getAmountOfSheetsPerProyect(int $proyect_id = null) {
        if(isset($proyect_id) === true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as amount
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        proyect
                    ON
                        proyect.proyect_id = sheet_per_proyect.proyect_id
                    WHERE
                        {$this->tblName}.proyect_id = '{$proyect_id}'";

            return $this->connection()->field($sql);
        }

        return self::$MAX_SHEET_PER_PROYECT;
    }
    public function isAviableToAddSheet(int $proyect_id = null) {
        return $this->getAmountOfSheetsPerProyect($proyect_id) < self::$MAX_SHEET_PER_PROYECT;
    }
    public function getAll(int $proyect_id = null) 
    {
        if(isset($proyect_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.title,
                        {$this->tblName}.update_date,
                        {$this->tblName}.url,
                        {$this->tblName}.status,
                        catalog_sheet.name
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        proyect
                    ON
                        proyect.proyect_id = sheet_per_proyect.proyect_id
                    LEFT JOIN 
                        catalog_sheet
                    ON
                        catalog_sheet.catalog_sheet_id = {$this->tblName}.catalog_sheet_id
                    WHERE
                        proyect.proyect_id = '{$proyect_id}'";

            return $this->connection()->rows($sql);
        }
        return false;
    }

    public function hasSheet($sheet_per_proyect_id = null) 
    {
        if(isset($sheet_per_proyect_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }

    public function getOthersSheets($proyect_id = null,$sheet_per_proyect_id = null) {

        if(isset($proyect_id,$sheet_per_proyect_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.title
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.proyect_id = '{$proyect_id}'
                    AND
                        {$this->tblName}.sheet_per_proyect_id != '{$sheet_per_proyect_id}'
                    AND 
                        {$this->tblName}.status = '1'
                        ";
                        
            return $this->connection()->rows($sql);
        }

        return false;
    }
}