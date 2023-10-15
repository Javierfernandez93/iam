<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogLoan extends Orm {
  protected $tblName  = 'catalog_loan';
  public function __construct() {
    parent::__construct();
  }
  
  public function getLoan($catalog_loan_id = null) 
  {
    if (isset($catalog_loan_id) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.loan
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.catalog_loan_id = '{$catalog_loan_id}'
              AND
                {$this->tblName}.status = '1'
              ";
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function _getLoan($catalog_loan_id = null) 
  {
    if (isset($catalog_loan_id) === true) 
    {
      $sql = "SELECT
                {$this->tblName}.payment,
                {$this->tblName}.capital,
                {$this->tblName}.loan
              FROM
                {$this->tblName}
              WHERE 
                {$this->tblName}.catalog_loan_id = '{$catalog_loan_id}'
              AND
                {$this->tblName}.status = '1'
              ";
      return $this->connection()->row($sql);
    }

    return false;
  }

  public function getList() 
  {
    $sql = "SELECT
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.capital,
              {$this->tblName}.payment,
              {$this->tblName}.loan,
              {$this->tblName}.number_of_period,
              catalog_period.catalog_period_id
            FROM
              {$this->tblName}
            LEFT JOIN 
              catalog_period
            ON 
              catalog_period.catalog_period_id = {$this->tblName}.catalog_period_id
            WHERE
              {$this->tblName}.status = '1'
            ";
    return $this->connection()->rows($sql);
  }

  public function getNumberOfPeriod($catalog_loan_id = null) 
  {
    if(isset($catalog_loan_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.number_of_period
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.catalog_loan_id = '{$catalog_loan_id}'
              AND 
                {$this->tblName}.status = '1'
              ";
      return $this->connection()->field($sql);
    }

    return false;
  }

  public function getPayment($catalog_loan_id = null) 
  {
    if(isset($catalog_loan_id) === true)
    {
      $sql = "SELECT
                {$this->tblName}.payment
              FROM
                {$this->tblName}
              WHERE
                {$this->tblName}.catalog_loan_id = '{$catalog_loan_id}'
              AND 
                {$this->tblName}.status = '1'
              ";
      return $this->connection()->field($sql);
    }

    return false;
  }
}