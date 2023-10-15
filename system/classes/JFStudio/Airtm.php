<?php

namespace JFStudio;

class Airtm {
    const CUSTOMER_EMAIL = 'zuumbusiness';

    private static $instance;
    
	public static function getInstance()
 	{
    	if(!self::$instance instanceof self)
      		self::$instance = new self;

    	return self::$instance;
 	}
}