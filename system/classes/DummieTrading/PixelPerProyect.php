<?php

namespace DummieTrading;

use HCStudio\Orm;

class PixelPerProyect extends Orm {
    protected $tblName  = 'pixel_per_proyect';
    public function __construct() {
        parent::__construct();
    }

    public function getAll($proyect_id = null) 
    {
        if(isset($proyect_id) === true)
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.title,
                        {$this->tblName}.create_date,
                        catalog_pixel.pixel
                    FROM 
                        {$this->tblName}
                    LEFT JOIN
                        catalog_pixel
                    ON 
                        catalog_pixel.catalog_pixel_id = {$this->tblName}.catalog_pixel_id
                    WHERE 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
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