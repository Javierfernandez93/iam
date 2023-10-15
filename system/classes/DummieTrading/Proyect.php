<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Token;

use OwnBoss\CatalogProyect;

class Proyect extends Orm {
    public static $proyects_url = "apps/proyects/";
    public static $template_name = "template";
    public static $MAIN_URL = 'naispages.com/';
    public static $UNIQUE_ID_LENGHT = 5;

    protected $tblName = 'proyect';
    public function __construct() 
    {
        parent::__construct();
    }

    public function isProyectAviableForStore($catalog_proyect_id = null) 
    {
        if(isset($catalog_proyect_id) === true)
        {
            return in_array($catalog_proyect_id,CatalogProyect::getCatalogProyectAviableForStore());
        }
    }

    public static function getUniqueID() 
    {
        return Token::randomNumber(self::$UNIQUE_ID_LENGHT);    
    }
    public static function getViewPath(int $proyect_id = null, int $sheet_per_proyect_id = null) 
    {
        return self::$proyects_url.$proyect_id."/".$sheet_per_proyect_id."/";
    }

    public static function getViewPathFile(int $sheet_per_proyect_id = null) 
    {
        return self::$template_name."-".$sheet_per_proyect_id;
    }
    
    public function constructFullSheetLink($sheet_url = null) 
    {
        return self::$MAIN_URL.$this->url."/".$sheet_url;

    }

    public function getAll(int $user_login_id = null) {

        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.proyect_id,
                        {$this->tblName}.unique_id,
                        {$this->tblName}.keywords,
                        {$this->tblName}.create_date,
                        {$this->tblName}.catalog_proyect_id,
                        {$this->tblName}.update_date,
                        {$this->tblName}.proyect,
                        catalog_proyect.proyect as proyect_kind
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_proyect
                    ON
                        catalog_proyect.catalog_proyect_id = {$this->tblName}.catalog_proyect_id
                    WHERE
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    AND 
                        {$this->tblName}.status = '1'
                        ";

            return $this->connection()->rows($sql);
        }
        return false;
    }
}