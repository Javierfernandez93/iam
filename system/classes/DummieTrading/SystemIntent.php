<?php

namespace DummieTrading;

use HCStudio\Orm;

class SystemIntent extends Orm {
    protected $tblName  = 'system_intent';
    public function __construct() {
        parent::__construct();
    }
    public function getAll() {
    	$sql = "SELECT 
    				{$this->tblName}.{$this->tblName}_id,
    				{$this->tblName}.words,
    				catalog_tag_system_intent.tag
    			FROM 
    				{$this->tblName}
    			LEFT JOIN 
    				catalog_tag_system_intent
    			ON
    				catalog_tag_system_intent.catalog_tag_system_intent_id = {$this->tblName}.catalog_tag_system_intent_id
    			ORDER BY 
    				catalog_tag_system_intent.catalog_tag_system_intent_id
    			ASC
    			";

    	return $this->connection()->rows($sql);
    }
}