<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Token;

class MailingPerProspect extends Orm {
    protected $tblName  = 'mailing_per_prospect';
    public static $UUID_LENGHT = 32;
    public static $SENT  = 0;
    public static $RECEIVED  = 1;
    public static $READ  = 2;
    public static $ERROR  = -1;
    public static $NO_IMAGE = "no-image.png";
    public function __construct() {
        parent::__construct();
    }
    public static function makeUUID() {
        return Token::__randomKey(self::$UUID_LENGHT);
    }
    public function getAll(int $sheet_per_proyect_id = null) {

        if(isset($sheet_per_proyect_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.campaign_per_sheet_id,
                        {$this->tblName}.message_id,
                        {$this->tblName}.status,
                        campaign_per_sheet.campaign_name,
                        proyect.proyect,
                        sheet_per_proyect.title,
                        sheet_per_proyect.proyect_id,
                        prospect_data.names,
                        prospect_data.email,
                        prospect.ip,
                        prospect.system,
                        prospect.prospect_unique_id,
                        prospect.prospect_id
                    FROM 
                        {$this->tblName}
                    LEFT JOIN
                        prospect
                    ON
                        prospect.prospect_id = {$this->tblName}.prospect_id
                    LEFT JOIN
                        prospect_data
                    ON
                        prospect_data.prospect_id = prospect.prospect_id
                    LEFT JOIN 
                        campaign_per_sheet
                    ON
                        campaign_per_sheet.campaign_per_sheet_id = {$this->tblName}.campaign_per_sheet_id
                    LEFT JOIN
                        sheet_per_proyect
                    ON
                        sheet_per_proyect.sheet_per_proyect_id = campaign_per_sheet.sheet_per_proyect_id
                    LEFT JOIN 
                        proyect
                    ON
                        proyect.proyect_id = sheet_per_proyect.proyect_id
                    WHERE
                        campaign_per_sheet.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
                    GROUP BY 
                        prospect.prospect_id
                    ";

            return $this->connection()->rows($sql);
        }
        return false;
    }

    public function _getAll(int $campaign_per_sheet_id = null) {

        if(isset($campaign_per_sheet_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.campaign_per_sheet_id,
                        {$this->tblName}.message_id,
                        {$this->tblName}.status,
                        prospect_data.names,
                        prospect_data.email,
                        prospect.ip,
                        prospect.system,
                        prospect.prospect_unique_id,
                        prospect.prospect_id
                    FROM 
                        {$this->tblName}
                    LEFT JOIN
                        prospect
                    ON
                        prospect.prospect_id = {$this->tblName}.prospect_id
                    LEFT JOIN
                        prospect_data
                    ON
                        prospect_data.prospect_id = prospect.prospect_id
                    WHERE
                        {$this->tblName}.campaign_per_sheet_id = '{$campaign_per_sheet_id}'
                    ";
                    
            return $this->connection()->rows($sql);
        }
        return false;
    }

    public function count(int $campaign_per_sheet_id = null) {

        if(isset($campaign_per_sheet_id) === true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.campaign_per_sheet_id = '{$campaign_per_sheet_id}'
                    ";
                    
            return $this->connection()->field($sql);
        }
        return false;
    }
}