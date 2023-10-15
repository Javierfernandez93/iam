<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;
use HCStudio\Session;

class CatalogTagIntent extends Orm {
    protected $tblName  = 'catalog_tag_intent';
    public function __construct() {
        parent::__construct();
    }

    public static function deleteIntentSession() 
    {
        $Session = new Session('intent');
        $Session->destroy();
    }

    public static function setCatalogTagIntentIdInSession(int $catalog_tag_intent_id = null) : bool
    {
        $Session = new Session('intent');

        if(isset($catalog_tag_intent_id))
        {
            $Session->set('catalog_tag_intent_id',$catalog_tag_intent_id);
        } else {
            $Session->clear('catalog_tag_intent_id');
        }

        return $catalog_tag_intent_id;
    }
   
    public static function getCatalogTagIntentIdInSession() 
    {
        $Session = new Session('intent');

        return $Session->get('catalog_tag_intent_id') ? $Session->get('catalog_tag_intent_id') : null;
    }

    public static function fixTag(string $tag = null) : string 
    {
        return  strtolower(preg_replace('/\s+/', '_', Util::sanitizeString($tag, true)));
    }

    public static function add(array $data = null) : int
    {
        $CatalogTagIntent = new self;
        $CatalogTagIntent->tag = $data['tag'];
        $CatalogTagIntent->main_node = 1;
        $CatalogTagIntent->create_date = time();
        
        if($CatalogTagIntent->save())
        {
            return $CatalogTagIntent->getId();
        }

        return false;
    }

    public function getCatalogTagIntentIdByTag(string $tag = null)
    {
    	if (isset($tag) === true) 
        {
    		$sql = "SELECT 
    					{$this->tblName}.{$this->tblName}_id,
    					{$this->tblName}.attach_catalog_tag_intent_id
    				FROM
    					{$this->tblName}
    				WHERE 
    					{$this->tblName}.tag = '{$tag}'
    				AND 
    					{$this->tblName}.status = '1'
    				";

    		return $this->connection()->row($sql);
    	}
    }

    public function hasResponse(int $catalog_tag_intent_id = null)
    {
        if (isset($catalog_tag_intent_id) === true) 
        {
            $sql = "SELECT 
                        {$this->tblName}.catalog_tag_intent_id
                    FROM
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.catalog_tag_intent_id = '{$catalog_tag_intent_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }
}