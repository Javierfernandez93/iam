<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogTagIntentChat extends Orm {
    protected $tblName  = 'catalog_tag_intent_chat';
    public function __construct() {
        parent::__construct();
    }
    
    public static function add(string $tag = null) {
        $CatalogTagIntentChat = new CatalogTagIntentChat;
        $CatalogTagIntentChat->tag = $tag;
        $CatalogTagIntentChat->create_date = time();

        return $CatalogTagIntentChat->save() ? $CatalogTagIntentChat->getId() : false;
    }
   
    public function isUnique(int $user_login_id = null,string $tag = null) : bool
    {
        if(isset($user_login_id,$tag) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id 
                    FROM 
                        {$this->tblName} 
                    LEFT JOIN
                        intent_chat 
                    ON 
                        intent_chat.catalog_tag_intent_chat_id = {$this->tblName}.catalog_tag_intent_chat_id
                    WHERE 
                        {$this->tblName}.tag = '{$tag}'
                    AND 
                        intent_chat.user_login_id = '{$user_login_id}'
                    AND 
                        intent_chat.status = '1'
                    ";
    
            return ($this->connection()->field($sql)) ? false : true;
        }

        return false;
     }
}