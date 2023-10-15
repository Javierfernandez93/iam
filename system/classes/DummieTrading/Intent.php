<?php

namespace DummieTrading;

use HCStudio\Orm;

class Intent extends Orm {
    protected $tblName  = 'intent';
    public function __construct() {
        parent::__construct();
    }
    
    public static function decodeText(string $response = null) : string
    {   
        $reply = "";

        $response = json_decode($response, true);
    
        $reply = $response['text'];

        if(isset($response['products']))
        {
            foreach($response['products'] as $product)
            {
                $reply .= "\n{$product['title']} ";
            }
        }
        
        return $reply;
    }

    public static function add(array $data = null) : int
    {
        $Intent = new self;
        $Intent->words = $data['words'];
        $Intent->catalog_tag_intent_id = $data['catalog_tag_intent_id'];
        $Intent->create_date = time();
        
        if($Intent->save())
        {
            return $Intent->getId();
        }

        return false;
    }

    public static function getIntents(string $message = null,int $attach_catalog_tag_intent_id = null) 
    {
        $Intent = new self;

        $filter = isset($attach_catalog_tag_intent_id) && $attach_catalog_tag_intent_id != 0 ? " AND catalog_tag_intent.catalog_tag_intent_id = '{$attach_catalog_tag_intent_id}' OR catalog_tag_intent.catalog_tag_intent_id = '12'" :  " AND catalog_tag_intent.main_node = '1'";

        if(!$attach_catalog_tag_intent_id)
        {
            if($intents = $Intent->getAllLikeMatch($message,$filter)) {
                return self::removeKey($intents);
            } else if($intents = $Intent->getAllLike($message,$filter)) {
                return self::removeKey($intents);
            }
        }

        return $Intent->getAll($filter);
    }

    public static function removeKey(array $intents = null) : array
    {
        return array_map(function($intent){
            return [
                'intent_id' => $intent['intent_id'],
                'words' => $intent['words'],
                'tag' => $intent['tag'],
            ];
        },$intents);
    }

    public function getAll($filter = "") {
        $sql = "SELECT 
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.words,
                    catalog_tag_intent.catalog_tag_intent_id,
                    catalog_tag_intent.tag
                FROM 
                    {$this->tblName}
                LEFT JOIN 
                    catalog_tag_intent
                ON
                    catalog_tag_intent.catalog_tag_intent_id = {$this->tblName}.catalog_tag_intent_id
                WHERE
                    {$this->tblName}.status = '1'
                    {$filter}
                ORDER BY 
                    catalog_tag_intent.catalog_tag_intent_id
                ASC
                ";

        return $this->connection()->rows($sql);
    }

    public function getAllLike($words = null,$filter = "") 
    {
        if(isset($words) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.words,
                        catalog_tag_intent.tag
                    FROM 
                        {$this->tblName}  
                    LEFT JOIN 
                        catalog_tag_intent
                    ON
                        catalog_tag_intent.catalog_tag_intent_id = {$this->tblName}.catalog_tag_intent_id
                    WHERE 
                        {$this->tblName}.words LIKE '%{$words}%'
                        {$filter}
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }

    public static function getWordsTokenizer(string $words = null) : string 
    {
        $words_sentence = rtrim(implode("",array_map(function($word) {
            return "{$word}* ";
        },explode(" ",$words))));

        return "($words_sentence) ($words)";
    } 

    public function getAllLikeMatch($words = null,$filter = "") 
    {
        if(isset($words) === true)
        {
            $words = self::getWordsTokenizer($words);
            
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.words,
                        catalog_tag_intent.tag,
                        MATCH({$this->tblName}.words) AGAINST('{$words}' IN BOOLEAN MODE) score
                    FROM 
                        {$this->tblName}  
                    LEFT JOIN 
                        catalog_tag_intent
                    ON
                        catalog_tag_intent.catalog_tag_intent_id = {$this->tblName}.catalog_tag_intent_id
                    WHERE 
                        {$this->tblName}.status = '1'
                        {$filter}
                    ORDER BY 
                        score
                    DESC
                    LIMIT 5
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getAllGroup($filter = "") {
        $sql = "SELECT 
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.words,
                    {$this->tblName}.create_date,
                    {$this->tblName}.catalog_tag_intent_id,
                    catalog_tag_intent.tag
                FROM 
                    {$this->tblName}
                LEFT JOIN 
                    catalog_tag_intent
                ON
                    catalog_tag_intent.catalog_tag_intent_id = {$this->tblName}.catalog_tag_intent_id
                    {$filter}
                GROUP BY 
                    catalog_tag_intent.catalog_tag_intent_id
                ORDER BY 
                    {$this->tblName}.create_date
                DESC 
                ";

        return $this->connection()->rows($sql);
    }

    public function getAllByCatalogTagIntentId(int $catalog_tag_intent_id = null) 
    {
        if(isset($catalog_tag_intent_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.words
                    FROM 
                        {$this->tblName}  
                    WHERE 
                        {$this->tblName}.catalog_tag_intent_id = '{$catalog_tag_intent_id}'
                    ";

            return $this->connection()->column($sql);
        }

        return false;
    }
}