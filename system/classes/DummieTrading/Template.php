<?php

namespace DummieTrading;

use HCStudio\Orm;

class Template extends Orm {
    protected $tblName  = 'template';
    public function __construct() {
        parent::__construct();
    }
    public function getAll() 
    {
        $sql = "SELECT 
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.view,
                    {$this->tblName}.title,
                    {$this->tblName}.description,
                    {$this->tblName}.create_date,
                    {$this->tblName}.keywords,
                    catalog_category_template.title as catalog_category_template_title
                FROM 
                    {$this->tblName}
                LEFT JOIN 
                    catalog_category_template
                ON 
                    catalog_category_template.catalog_category_template_id = {$this->tblName}.catalog_category_template_id
                WHERE
                    {$this->tblName}.status = '1'";

        return $this->connection()->rows($sql);
    }

    public function getScripts(int $template_id = null) : array
    {
        if(isset($template_id) === true)
        {
            if($scripts = $this->_getScripts($template_id))
            {
                return json_decode($scripts, true);
            }
        }

        return [];
    }
    
    public function _getScripts(int $template_id = null) 
    {
        if(isset($template_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.script
                    FROM 
                        {$this->tblName}
                    WHERE
                        {$this->tblName}.template_id = '{$template_id}'
                    AND 
                        {$this->tblName}.status = '1'
                        ";

            return $this->connection()->field($sql);
        }

        return false;
    }
}