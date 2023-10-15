<?php

namespace DummieTrading;

use HCStudio\Orm;

class CatalogTradingAccount extends Orm {
    protected $tblName  = 'catalog_trading_account';
    
    const METATRADER = 1;
    const BINANCE = 2;

    public static function decodeId(string $catalog_trading_account_id = null) : string {

        if($catalog_trading_account_id == self::METATRADER) {
            return 'forex';
        } else if($catalog_trading_account_id == self::BINANCE) { 
            return 'crypto';
        }

        return "";
    }

    public function __construct() {
        parent::__construct();
    }
}