<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Token;

class MailingVars {
    private static $instance;
    public static $TAG = "%";
    public static function getInstance()
    {
        if(!self::$instance instanceof self)
          self::$instance = new self;

        return self::$instance;
    }

    public function __construct() {
    }

    public function exist($content = null,$var = null) 
    {
        return strpos($content, $var) !== false;
    }

    public function findAndReplace(&$content = null,$var = null, $replacement = null) 
    {
        $var = self::$TAG.$var.self::$TAG;

        if($this->exist($content,$var) === true)
        {
            $content = str_replace($var, $replacement, $content);
        }
    }

    public function getForTranslationVars() : array 
    {
        return [
            0 => ['key' => 'names','helper' => "Nombre de usuario."],
            1 => ['key' => 'email','helper' => "Correo electrónico."]
        ];
    }

    public function translate($user = null,$content = null) 
    {
        if (isset($user,$content) === true) 
        {
            foreach($this->getForTranslationVars() as $var)
            {
                if($user[$var['key']])
                {
                    $this->findAndReplace($content,$var['key'],$user[$var['key']]);    
                }
            }
        }
        
        return $content;
    }
}