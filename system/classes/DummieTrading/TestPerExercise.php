<?php

namespace DummieTrading;

use HCStudio\Orm;

class TestPerExercise extends Orm {
  protected $tblName  = 'test_per_exercise';

  const DEFAULT_TESTS = 1;
  const IN_PROGRESS = 1;
  const DONE = 2;
  public function __construct() {
    parent::__construct();
  }

  public static function addTest(array $data = null) : bool
  {
    $TestPerExercise = new TestPerExercise;
    $TestPerExercise->exercise_id = $data['exercise_id'];
    $TestPerExercise->create_date = time();
    
    return $TestPerExercise->save();
  }

  public static function addTests(array $data = null) : bool
  {
    if(isset($data) === true)
    {
      $saved = 0;

      for($i = 0; $i < $data['test_amount']; $i++)
      {
        if(self::addTest($data))
        {
          $saved++;
        }
      }

      return $saved == $data['test_amount'];
    }

    return false;
  }

  public static function getProgress(array $tests = null) 
  {
    $testsPassed = array_filter($tests,function($test){
      return $test['status'] == self::DONE;
    });

    $testsPassed = sizeof($testsPassed) * 100; 

    return $testsPassed > 0 ? round($testsPassed / sizeof($tests),2) : 0;
  }

  public function getTest(int $exercise_id = null) 
  {
    if(isset($exercise_id) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.start_date,
                {$this->tblName}.end_date,
                {$this->tblName}.create_date,
                {$this->tblName}.score,
                {$this->tblName}.status
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.exercise_id = '{$exercise_id}'
              ";
              
      if($tests = $this->connection()->rows($sql))
      {
        return $tests;
      }
    }

    return null;
  }
}