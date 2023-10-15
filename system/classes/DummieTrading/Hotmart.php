<?php

namespace DummieTrading;

use HCStudio\Orm;

class Hotmart extends Orm {
  protected $tblName  = 'web_hook';

  /* EVENTS */ 
  const PURCHASE_REFUNDED = 'PURCHASE_REFUNDED';
  const PURCHASE_DELAYED = 'PURCHASE_DELAYED';
  const PURCHASE_CANCELED = 'PURCHASE_CANCELED';
  const PURCHASE_COMPLETE = 'PURCHASE_COMPLETE';
  const SUBSCRIPTION_CANCELLATION = 'SUBSCRIPTION_CANCELLATION';
  const PURCHASE_BILLET_PRINTED = 'PURCHASE_BILLET_PRINTED';
  const PURCHASE_CHARGEBACK = 'PURCHASE_CHARGEBACK';
  const PURCHASE_OUT_OF_SHOPPING_CART = 'PURCHASE_OUT_OF_SHOPPING_CART';
  const PURCHASE_PROTEST = 'PURCHASE_PROTEST';
  const PURCHASE_EXPIRED = 'PURCHASE_EXPIRED';
  const PURCHASE_APPROVED = 'PURCHASE_APPROVED';
  const SWITCH_PLAN = 'SWITCH_PLAN';

  public function __construct() {
    parent::__construct();
  }
  
  public static function saveData(array $data = null) : bool
  {
    $WebHook = new self;
    $WebHook->data = json_encode($data);
    $WebHook->create_date = time();

    return $WebHook->save();
  }
}