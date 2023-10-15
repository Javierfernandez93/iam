<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class Landing extends Orm {
  protected $tblName  = 'landing';

  public function __construct() {
    parent::__construct();
  }

  public static function add(array $data = null) : bool
  {
    $Landing = new Landing;
    $Landing->title = $data['title'];
    $Landing->content = $data['content'] ?? '';
    $Landing->video = $data['video'] ?? '';
    $Landing->catalog_landing_action_id = $data['catalog_landing_action_id'] ?? '';
    $Landing->background = $data['background'] ?? '';
    $Landing->action = $data['action'] ?? '';
    $Landing->share_text = $data['share_text'] ?? '';
    $Landing->description = $data['description'] ?? '';
    $Landing->path = strtolower($data['path']) ?? '';
    $Landing->create_date = time();
    $Landing->text = $data['text'] ?? '';

    return $Landing->save();
  }
  
  public function getAllForUser() 
  {
    $sql = "SELECT 
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.catalog_landing_action_id,
              {$this->tblName}.title,
              {$this->tblName}.video,
              {$this->tblName}.share_text,
              {$this->tblName}.description,
              {$this->tblName}.content,
              {$this->tblName}.path,
              {$this->tblName}.background,
              {$this->tblName}.text,
              {$this->tblName}.action,
              {$this->tblName}.status,
              {$this->tblName}.create_date
            FROM 
              {$this->tblName} 
            WHERE
              {$this->tblName}.status IN (".Constants::AVIABLE.")
            ";

    return $this->connection()->rows($sql);
  }
  
  public function getAll() 
  {
    $sql = "SELECT 
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.catalog_landing_action_id,
              {$this->tblName}.title,
              {$this->tblName}.video,
              {$this->tblName}.share_text,
              {$this->tblName}.description,
              {$this->tblName}.content,
              {$this->tblName}.path,
              {$this->tblName}.background,
              {$this->tblName}.text,
              {$this->tblName}.action,
              {$this->tblName}.status,
              {$this->tblName}.create_date
            FROM 
              {$this->tblName} 
            WHERE
              {$this->tblName}.status IN (".Constants::AVIABLE.",".Constants::DISABLED.")
            ";

    return $this->connection()->rows($sql);
  }

  public function existPath(string $path = null) : bool
  {
    if(isset($path) === true) 
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName} 
              WHERE
                {$this->tblName}.path = '{$path}'
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public function getLandingByPath(string $path = null)
  {
    if(isset($path) === true) 
    {
      $sql = "SELECT            
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.catalog_landing_action_id,
                {$this->tblName}.title,
                {$this->tblName}.video,
                {$this->tblName}.description,
                {$this->tblName}.share_text,
                {$this->tblName}.content,
                {$this->tblName}.path,
                {$this->tblName}.background,
                {$this->tblName}.text,
                {$this->tblName}.action,
                {$this->tblName}.status,
                {$this->tblName}.create_date
              FROM 
                {$this->tblName} 
              WHERE
                {$this->tblName}.path = '{$path}'
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getLandingTitleByPath(string $path = null)
  {
    if(isset($path) === true) {
      $sql = "SELECT 
                {$this->tblName}.title
              FROM 
                {$this->tblName} 
              WHERE
                {$this->tblName}.path = '{$path}'
              AND 
                {$this->tblName}.status IN (".Constants::AVIABLE.")
              ";

      return $this->connection()->field($sql);
    }

    return false;
  }

  public static function setLandingAs(int $landing_id = null,int $status = null) : bool
  {
    $Landing = new self;
    
    if($Landing->loadWhere('landing_id = ?', $landing_id))
    { 
      $Landing->status = $status;
      
      return $Landing->save();
    }
    
    return false;
  }
}