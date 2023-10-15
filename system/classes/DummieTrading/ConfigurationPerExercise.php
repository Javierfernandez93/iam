<?php

namespace DummieTrading;

use HCStudio\Orm;

use DummieTrading\VarConfiguration;

class ConfigurationPerExercise extends Orm {
  protected $tblName  = 'configuration_per_exercise';

  public function __construct() {
    parent::__construct();
  }
  
  public static function parse(array $vars = null) : string 
  {
    return json_encode(array_map(function($var){
        return [
            'var_configuration_id' => $var['var_configuration_id'],
            'value' => $var['default_value']
        ];
    },$vars));
  }

  public static function add(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $ConfigurationPerExercise = new ConfigurationPerExercise;
      $ConfigurationPerExercise->exercise_id = $data['exercise_id'];
      $ConfigurationPerExercise->configuration = self::parse($data['vars']);
      $ConfigurationPerExercise->buy_per_user_id = $data['buy_per_user_id'];
      $ConfigurationPerExercise->create_date = time();
      
      return $ConfigurationPerExercise->save();
    }

    return false;
  }
 
  public static function unParse(array $vars = null) 
  {
    $VarConfiguration = new VarConfiguration;
    return array_map(function($var) use($VarConfiguration) {
        $var = array_merge($var,$VarConfiguration->get($var['var_configuration_id']));
        return $var;
    },$vars);
  }

  public function _getConfiguration(int $exercise_id = null) 
  {
    if(isset($exercise_id) === true)
    {
        if($configuration = $this->getConfiguration($exercise_id))
        {
            return self::unParse(json_decode($configuration['configuration'],true));
        }
    }
  }

  public function getConfiguration(int $exercise_id = null) 
  {
    if(isset($exercise_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.configuration
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.exercise_id = '{$exercise_id}'
              AND
                {$this->tblName}.status = '1'
               ";
      
      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getExerciseId(int $buy_per_user_id = null) 
  {
    if(isset($buy_per_user_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.exercise_id
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.buy_per_user_id = '{$buy_per_user_id}'
              AND
                {$this->tblName}.status = '1'
               ";
      
      return $this->connection()->field($sql);
    }

    return false;
  }
}