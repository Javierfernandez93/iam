<?php

namespace JFStudio;

class Routing {
    private static $instance;
    public static $PERMISSIONS = 0777;
    public static $FIRST_DIRECTORY_NAME = "token";
    public static $DIRECTORY_JOINNER = "-";
    public static $SECOND_DIRECTORY_NAME = "key";
    public static function getInstance()
    {
        if(!self::$instance instanceof self)
          self::$instance = new self;

        return self::$instance;
    }
    public function __construct()
    {
        
    }

    public function formatDirectoryUri($directory = null)
    {
        if(isset($directory) === true)
        {
            if(is_array($directory) === true)
            {
                if(isset($directory[self::$FIRST_DIRECTORY_NAME]) === true)
                {
                    if(isset($directory[self::$SECOND_DIRECTORY_NAME]) === true)
                    {
                        return implode(self::$DIRECTORY_JOINNER, $directory);
                    }
                }
            }
        }
    }
    public function makeDirectory($directory = null)
    {
        if(isset($directory) === true)
        {   
            if(mkdir($directory, self::$PERMISSIONS, true) === true) {
                return true;
            } else {
                die('Fallo al crear las carpetas...');
            }
        }
    }
}