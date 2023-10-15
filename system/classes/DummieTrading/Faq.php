<?php

namespace DummieTrading;

use HCStudio\Orm;

class Faq extends Orm {
  protected $tblName  = 'faq';
  public function __construct() {
    parent::__construct();
  }

  public function getAll() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.title,
              {$this->tblName}.description,
              {$this->tblName}.create_date,
              catalog_subtopic.name as subtopic,
              catalog_topic.name as topic
            FROM 
              {$this->tblName}
            LEFT JOIN 
                catalog_subtopic
            ON 
                catalog_subtopic.catalog_subtopic_id = {$this->tblName}.catalog_subtopic_id
            LEFT JOIN 
                catalog_topic
            ON 
                catalog_topic.catalog_topic_id = catalog_subtopic.catalog_topic_id
            WHERE 
              {$this->tblName}.status = '1'
            ";
            
    return $this->connection()->rows($sql);
  }
}