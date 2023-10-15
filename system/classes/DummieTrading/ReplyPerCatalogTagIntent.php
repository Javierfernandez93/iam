<?php

namespace DummieTrading;

use HCStudio\Orm;

class ReplyPerCatalogTagIntent extends Orm {
    protected $tblName  = 'reply_per_catalog_tag_intent';
    public static $DEFAULT_ARRAY = ["Lo siento, no te entendí","¿Puedes intentar preguntando otra cosa?"];
    public function __construct() {
        parent::__construct();
    }
    
    public static function add(array $data = null) : int
    {
        $ReplyPerCatalogTagIntent = new self;
        $ReplyPerCatalogTagIntent->reply = $data['reply'];
        $ReplyPerCatalogTagIntent->catalog_tag_intent_id = $data['catalog_tag_intent_id'];
        $ReplyPerCatalogTagIntent->create_date = time();
        
        if($ReplyPerCatalogTagIntent->save())
        {
            return $ReplyPerCatalogTagIntent->getId();
        }

        return false;
    }

    public static function getDefaultReply() : string {
        return self::_getReplyByArray(self::$DEFAULT_ARRAY);
    }

    public function getReplyByArray(array $replys) : string {
        return sizeof($replys) > 0 ? $replys[rand(0,sizeof($replys)-1)] : '';
    }

    public static function _getReplyByArray(array $replys) : string {
        return $replys[rand(0,sizeof($replys)-1)];
    }

    public static function getReplyRandom(int $catalog_tag_intent_id = null) : string {
        $ReplyPerCatalogTagIntent = new self;
        $ReplyPerCatalogTagIntent->connection()->stmtQuery("SET NAMES utf8mb4");

        $replys = $ReplyPerCatalogTagIntent->getReply($catalog_tag_intent_id);

        $reply = $ReplyPerCatalogTagIntent->getReplyByArray($replys);    

        return $reply;
    }

    public function getReply(int $catalog_tag_intent_id = null) {
        $sql = "SELECT 
                    {$this->tblName}.reply
                FROM 
                    {$this->tblName}
                WHERE
                    {$this->tblName}.catalog_tag_intent_id = '{$catalog_tag_intent_id}'
                AND 
                    {$this->tblName}.status = '1'
                ";
        return $this->connection()->column($sql);
    }
}