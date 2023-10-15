<?php

namespace DummieTrading;

use HCStudio\Orm;

class ReplyPerCatalogTagIntentChat extends Orm {
    protected $tblName  = 'reply_per_catalog_tag_intent_chat';
    public static $DEFAULT_ARRAY = ["Lo siento, no te entendí","¿Puedes intentar preguntando otra cosa?"];
    public function __construct() {
        parent::__construct();
    }
    
    public static function saveReplys(array $replys = null, int $catalog_tag_intent_chat_id = null) : bool {
        if(isset($replys) === true)
        {
            foreach($replys as $reply) 
            {
                $ReplyPerCatalogTagIntentChat = new ReplyPerCatalogTagIntentChat;
                $ReplyPerCatalogTagIntentChat->catalog_tag_intent_chat_id = $catalog_tag_intent_chat_id;
                $ReplyPerCatalogTagIntentChat->reply = $reply['value'];
                $ReplyPerCatalogTagIntentChat->create_date = time();

                $ReplyPerCatalogTagIntentChat->save();
            }
            
            return true;
        }

        return false;
    }

    public static function getDefaultReply() : string 
    {
        return self::_getReplyByArray(self::$DEFAULT_ARRAY);
    }
    
    public function getReplyByArray(array $replys) : string 
    {
        return $replys[rand(0,sizeof($replys)-1)];
    }
    
    public static function _getReplyByArray(array $replys) : string 
    {
        return $replys[rand(0,sizeof($replys)-1)];
    }

    public function getReplyRandom(int $catalog_tag_intent_id = null) : string 
    {
        $replys = $this->getReply($catalog_tag_intent_id);

        return $this->getReplyByArray($replys);
    }

    public function getReply(int $catalog_tag_intent_chat_id = null) : array 
    {
        $sql = "SELECT 
                    {$this->tblName}.reply
                FROM 
                    {$this->tblName}
                WHERE
                    {$this->tblName}.catalog_tag_intent_chat_id = '{$catalog_tag_intent_chat_id}'
                AND 
                    {$this->tblName}.status = '1'
                ";
                
        return $this->connection()->column($sql);
    }
}