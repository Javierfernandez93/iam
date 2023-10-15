<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogValidationMethod extends Orm {
  protected $tblName  = 'catalog_validation_method';

  /* constants */
  const ADMINISTRATOR = 1;
  const COINPAYMENTS_IPN = 2;
  const EWALLET = 3;
  const PAYPAL_CDN = 4;
  const INTERNAL_USER = 5;
  const INTERNAL_IPN = 6;
  const CAPITALPAYMENTS_IPN = 7;
  const EVOX_IPN = 8;

  public function __construct() {
    parent::__construct();
  }
}