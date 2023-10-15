<?php

namespace DummieTrading;

use HCStudio\Orm;

class SymbolParserPerBroker extends Orm {
	
	protected $tblName = 'symbol_parser_per_broker';

	public function __construct() {
		parent::__construct();
	}

	public static function fixSymbol(string $symbol = null) : string
    {
        if(isset($symbol))
        {
            return substr($symbol, 0, 6); 
        }

        return '';
    }

	public static function sanitize(string $broker = null,string $symbol = null) 
    {
        if(isset($broker))
        {
            $symbol = substr($symbol, 0, 6); 

            if($parser = (new self)->getParser($broker))
            {
                return "{$symbol}{$parser}";
            } 
        }

        return $symbol;
    }

	public function getParser(string $broker = null) : string
	{
        if(isset($broker))
        {
            $sql = "SELECT 
                        {$this->tblName}.parser
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.broker = '{$broker}'
                    AND
                        {$this->tblName}.status = '1'
                    ";
            
            return $this->connection()->field($sql);
        }

        return $broker;
	}
}