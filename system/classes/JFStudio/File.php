<?php

namespace JFStudio;

use JFStudio\ModelFileErrors;
use Exception;

class File {
    private static $instance;
    private $file_name = null;
    private $content;
    private $path;
    private $file;
    public static $MAIN_PATH = '';
    public static $EXTENSION = 'view.php';
    public static $MODE_W = 'w';
    public static $MODE_A = 'a';
    public static $MODE_WB = 'wb';
    public static $MODE_R = 'r';
    public function __construct()
    {
    }
    
    public static function getInstance()
    {
        if(!self::$instance instanceof self)
          self::$instance = new self;

        return self::$instance;
    }

    public function getFullPath()
    {
        return self::$MAIN_PATH.$this->getPath().$this->getFileName().".".self::$EXTENSION;
    }
    public function existFile()
    {
        return file_exists($this->getFullPath());
    }

    public function makeFile($make_file = false)
    {
        if($this->getFileName())  
        {
            if($this->existFile() === true || $make_file === true)  
            {
                if($this->getContent())
                {
                    // echo "full_path : ".$this->getFullPath();
                    // echo "<br>";

                    $this->setFile(fopen($this->getFullPath(), self::$MODE_WB));
                    fwrite($this->getFile(), $this->getContent());
                    fclose($this->getFile());

                    return true;
                } else {
                    throw new Exception(ModelFileErrors::$NOT_FILE_CONTENT);
                }
            } else {
                throw new Exception(ModelFileErrors::$NOT_FILE_EXIST);
            }
        } else {
            throw new Exception(ModelFileErrors::$NOT_FILE_NAME);
        }
    }

    public function readFile()
    {
        if($this->getFileName())  
        {
            if($this->existFile() === true)  
            {
                $this->setFile(fopen($this->getFullPath(), self::$MODE_R));
                $content = stream_get_contents($this->getFile());

                fclose($this->getFile());
                return $content;
            } else {
                throw new Exception(ModelFileErrors::$NOT_FILE_EXIST);
            }
        } else {
            throw new Exception(ModelFileErrors::$NOT_FILE_NAME);
        }
    }

    public function setFileName($file_name = null)
    {
        if(isset($file_name) === true) {
            $this->file_name = $file_name;
        }
    }

    public function _getFileName()
    {
        return self::$MAIN_PATH.$this->getPath().$this->getFileName().".".self::$EXTENSION;
    }
    public function getFileName()
    {
        return $this->file_name;
    }

    public function setFile($file = null)
    {
        if(isset($file) === true) {
            $this->file = $file;
        }
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setContent($content = null)
    {
        if(isset($content) === true) {
            $this->content = $content;
        }
    }

    public function getContent()
    {
        return $this->content;
    }   

    public function setPath($path = null)
    {
        if(isset($path) === true) {
            $this->path = $path;
        }
    }

    public function getPath()
    {
        return $this->path;
    }   
}