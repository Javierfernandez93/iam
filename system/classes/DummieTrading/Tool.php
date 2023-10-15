<?php

namespace DummieTrading;

use HCStudio\Orm;

class Tool extends Orm {
  protected $tblName  = 'tool';

  const PUBLISHED = 1;
  const UNPUBLISHED = 0;
  const DELETED = -1;
  public function __construct() {
    parent::__construct();
  }

  public function getAll() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.title,
              {$this->tblName}.description,
              {$this->tblName}.route,
              {$this->tblName}.image,
              {$this->tblName}.create_date,
              catalog_tool.tool,
              user_support.names
            FROM
              {$this->tblName}
            LEFT JOIN 
              catalog_tool
            ON 
              catalog_tool.catalog_tool_id = {$this->tblName}.catalog_tool_id
            LEFT JOIN 
              user_support
            ON 
              user_support.user_support_id = {$this->tblName}.user_support_id
            WHERE
              {$this->tblName}.status = '1'
            ";
            
    return $this->connection()->rows($sql);
  }
  
  public function getAllFullInfo() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.title,
              {$this->tblName}.description,
              {$this->tblName}.route,
              {$this->tblName}.create_date,
              {$this->tblName}.status,
              catalog_tool.tool,
              user_support.names
            FROM
              {$this->tblName}
            LEFT JOIN 
              catalog_tool
            ON 
              catalog_tool.catalog_tool_id = {$this->tblName}.catalog_tool_id
            LEFT JOIN
              user_support
            ON 
              user_support.user_support_id = {$this->tblName}.user_support_id
            WHERE
              {$this->tblName}.status != '".self::DELETED."'
            ";
            
    return $this->connection()->rows($sql);
  }

  public function getTool(int $tool_id = null) 
  {
    if(isset($tool_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.title,
                {$this->tblName}.description,
                {$this->tblName}.catalog_tool_id,
                {$this->tblName}.route,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.tool_id = '{$tool_id}'
              AND 
                {$this->tblName}.status = '1'
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }
}