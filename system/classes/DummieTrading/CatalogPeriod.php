<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogPeriod extends Orm {
  protected $tblName  = 'catalog_period';
  public static $WEEK = 1;
  public static $MONTH = 2;
  public function __construct() {
    parent::__construct();
  }
  
  public function getPeriodName($catalog_period_id = null) 
  {
    if($catalog_period_id == self::$WEEK)
    {
      return 'semana';
    } else if($catalog_period_id == self::$MONTH) {
      return 'mes';
    }
  }

  public function getPeriodNamePlural($catalog_period_id = null) 
  {
    if($catalog_period_id == self::$WEEK)
    {
      return 'semanas';
    } else if($catalog_period_id == self::$MONTH) {
      return 'meses';
    }
  }
}