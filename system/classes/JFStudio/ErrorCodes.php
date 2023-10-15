<?php

namespace JFStudio;

class ErrorCodes {
    /* meta api */
    const NOT_META_API = 100;
    const INVALID_LOTAGE = 101;
    const NOT_META_API_RESPONSE = 102;
    const ERR_MARKET_CLOSED = 103;

    static function getName(int $error_code = null)
    {
        return match($error_code) {
            self::NOT_META_API => 'NOT_META_API',
            self::INVALID_LOTAGE => 'INVALID_LOTAGE',
            self::NOT_META_API_RESPONSE => 'NOT_META_API_RESPONSE',
            self::ERR_MARKET_CLOSED => 'ERR_MARKET_CLOSED',
            default => 'ERROR_NOT_PROVIED'
        };
    }
}