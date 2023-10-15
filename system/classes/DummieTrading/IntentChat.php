<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\CatalogTagIntentChat;
use DummieTrading\ReplyPerCatalogTagIntentChat;

class IntentChat extends Orm {
    protected $tblName  = 'intent_chat';
    public function __construct() {
        parent::__construct();
    }

    public static function add(array $data = null,int $user_login_id = null) : bool
    {
        if($data['tag'])
        {
            if($catalog_tag_intent_chat_id = CatalogTagIntentChat::add($data['tag']))
            {
                if(self::saveIntents($data['words'],$user_login_id,$catalog_tag_intent_chat_id))
                {
                    if(ReplyPerCatalogTagIntentChat::saveReplys($data['replys'],$catalog_tag_intent_chat_id))
                    {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function saveIntents(array $intents = null, int $user_login_id = null,int $catalog_tag_intent_chat_id = null) : bool {
        if(isset($intents) === true)
        {
            foreach($intents as $intent) 
            {
                $IntentChat = new IntentChat;
                $IntentChat->catalog_intent_type_id = 1;
                $IntentChat->user_login_id = $user_login_id;
                $IntentChat->catalog_tag_intent_chat_id = $catalog_tag_intent_chat_id;
                $IntentChat->words = $intent['value'];
                $IntentChat->create_date = time();

                $IntentChat->save();
            }

            return true;
        }

        return false;
    }

    public function getAll($sheet_per_proyect_id = null) 
    {
        if(isset($sheet_per_proyect_id) === true)
        {
        	$sql = "SELECT 
        				{$this->tblName}.{$this->tblName}_id,
        				{$this->tblName}.words,
        				catalog_tag_intent_chat.tag
        			FROM 
        				{$this->tblName}
        			LEFT JOIN 
        				catalog_tag_intent_chat
        			ON
        				catalog_tag_intent_chat.catalog_tag_intent_chat_id = {$this->tblName}.catalog_tag_intent_chat_id
                    WHERE 
                        {$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
                    AND 
                        {$this->tblName}.status = '1'
        			";
                    
        	return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getAllGroup(int $user_login_id = null) 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.status,
                        {$this->tblName}.words,
                        catalog_tag_intent_chat.catalog_tag_intent_chat_id,
                        catalog_tag_intent_chat.tag
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        catalog_tag_intent_chat
                    ON
                        catalog_tag_intent_chat.catalog_tag_intent_chat_id = {$this->tblName}.catalog_tag_intent_chat_id
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    GROUP BY 
                        catalog_tag_intent_chat.catalog_tag_intent_chat_id
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getCount($catalog_tag_intent_chat_id = null) 
    {
        if(isset($catalog_tag_intent_chat_id) === true)
        {
            $sql = "SELECT 
                        COUNT({$this->tblName}.{$this->tblName}_id) as c
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.catalog_tag_intent_chat_id = '{$catalog_tag_intent_chat_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";
                    
            return $this->connection()->field($sql);
        }

        return false;
    }

    public function getAllWords($catalog_tag_intent_chat_id = null) 
    {
        if(isset($catalog_tag_intent_chat_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.words
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.catalog_tag_intent_chat_id = '{$catalog_tag_intent_chat_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";
                    
            return $this->connection()->column($sql);
        }

        return false;
    }

    public function getAllLike($sheet_per_proyect_id = null,$words = null) 
    {
        if(isset($sheet_per_proyect_id,$words) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.words,
                        catalog_tag_intent_chat.tag,
                        MATCH(`words`) AGAINST ('{$words}' IN BOOLEAN MODE) as rel1
                    FROM 
                        {$this->tblName}  
                    LEFT JOIN 
                        catalog_tag_intent_chat
                    ON
                        catalog_tag_intent_chat.catalog_tag_intent_chat_id = {$this->tblName}.catalog_tag_intent_chat_id
                    WHERE 
                    MATCH 
                        (words) 
                    AGAINST 
                        ('{$words}' IN BOOLEAN MODE)
                    ORDER BY 
                        rel1
                    DESC
                    ";
                    
            return $this->connection()->rows($sql);
        }

        return false;
    }
}