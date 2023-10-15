<?php

namespace JFStudio;

class Binance {
    const API_KEY = "BuMqiOW2SHLAAHvmQBcR114SF51tapcw9twBZd8WZLqzpG77cQR7ryd5shfJMS5u"; 
    const SECRET_KEY = "qIYLUDKo1z9ugOx4MmX2R3Tkmz8LsqiYlYAaSwKgIrM01y6ZCmTgvnI5Fg8qpLIn";

    private static $instance;
    
	public static function getInstance()
 	{
    	if(!self::$instance instanceof self)
      		self::$instance = new self;

    	return self::$instance;
 	}
}


// BuMqiOW2SHLAAHvmQBcR114SF51tapcw9twBZd8WZLqzpG77cQR7ryd5shfJMS5u
// Secret Key
// qIYLUDKo1z9ugOx4MmX2R3Tkmz8LsqiYlYAaSwKgIrM01y6ZCmTgvnI5Fg8qpLIn