<?php

namespace DummieTrading;

enum UserApiCodes: int {
    case DATA_OK = 200;
    case INVALID_CREDENTIALS = 300;
    case NOT_INVOICE_ID = 301;
    case INVALID_INVOICE = 302;
    case NOT_AMOUNT = 303;
    case INVALID_AMOUNT_FORMAT = 304;
    case ERROR_AT_CREATE_INVOICE = 305;
    case NOT_WALLET_ADDRESS = 306;
    case NOT_WALLET_ATTACHED = 307;
    case NOT_HOOK_URL = 308;
    case DATA_NOT_SAVED = 310;
    case INVOICE_EXIST = 311;
    case PAYOUT_NOT_EXIST = 312;
    case NOT_PAYOUT_ID = 313;
    case INVALID_WALLET_ADDRESS = 314;
    case ERROR_AT_CREATE_PAYOUT = 315;
    case INVALID_PAYOUT = 316;
    case NOT_ACCOUNT = 317;
    case NOT_API_KEY = 318;
    case NOT_MERCHANT_ID = 319;
    case NOT_ITEMS = 320;
    case NOT_ITEM = 321;
    case NOT_PAYOUT = 322;
    case PAYOUT_PAYED = 323;
    case PAYOUT_CANCELED = 324;
    case ERROR_AT_CREATE_INVOICES = 325;
    case NOT_CUSTOMERS = 326;
    case ERROR_AT_CREATE_CUSTOMER = 327;
    case NOT_NAMES = 328;   
    case NOT_WHATSAPP = 329;
    case NOT_EMAIL = 330;
    case NOT_CUSTOMER_ID = 331;
    case ERROR_AT_UPDATE_CUSTOMER = 332;
    case NOT_CUSTOMER = 333;
    case NOT_ITEM_ID = 334;
    case NOT_PRICE = 335;
    case NOT_DESCRIPTION = 336;
    case NOT_TITLE = 337;
    case ERROR_AT_CREATE_ITEM = 338;
    case ERROR_AT_CREATE_PAYOUTS = 339;
    case ERROR_AT_UPDATE_ITEM = 340;
    case NOT_WALLET = 341;
    case INVALID_TRON_WALLET_ADDRESS = 342;
    case INVALID_ENVIROMENT = 343;
    case NOT_PENDING_INVOICE = 344;
    case ERROR_AT_SEND_TEXT_INVOICE_AS_PAYED = 345;
    case ERROR_AT_SET_DEPOSIT_WALLET = 346;
    case ERROR_AT_CREATE_SPLIT = 347;
    case NOT_SPLIT_ID = 348;
    case ERROR_AT_REMOVE_SPLIT = 349;
    case NOT_HOSTS = 350;
    case NOT_HOST = 351;
    case HOST_EXISTS = 352;
    case INVALID_HOST = 353;
    case HOST_NOT_IN_WHITE_LIST = 354;
    case NOT_FIELDS = 355;
    
    public function label(): string {
        return static::getLabel($this);
    }

    public static function getLabel($value): string {
        return match ($value) {
            self::DATA_OK => 'DATA_OK',
            self::INVALID_CREDENTIALS => 'INVALID_CREDENTIALS',
            self::NOT_INVOICE_ID => 'NOT_INVOICE_ID',
            self::INVALID_INVOICE => 'INVALID_INVOICE',
            self::NOT_AMOUNT => 'NOT_AMOUNT',
            self::INVALID_AMOUNT_FORMAT => 'INVALID_AMOUNT_FORMAT',
            self::ERROR_AT_CREATE_INVOICE => 'ERROR_AT_CREATE_INVOICE',
            self::NOT_WALLET_ADDRESS => 'NOT_WALLET_ADDRESS',
            self::NOT_WALLET_ATTACHED => 'NOT_WALLET_ATTACHED',
            self::NOT_HOOK_URL => 'NOT_HOOK_URL',
            self::DATA_NOT_SAVED => 'DATA_NOT_SAVED',
            self::INVOICE_EXIST => 'INVOICE_EXIST',
            self::PAYOUT_NOT_EXIST => 'PAYOUT_NOT_EXIST',
            self::NOT_PAYOUT_ID => 'NOT_PAYOUT_ID',
            self::INVALID_WALLET_ADDRESS => 'INVALID_WALLET_ADDRESS',
            self::ERROR_AT_CREATE_PAYOUT => 'ERROR_AT_CREATE_PAYOUT',
            self::INVALID_PAYOUT => 'INVALID_PAYOUT',
            self::NOT_ACCOUNT => 'NOT_ACCOUNT',
            self::NOT_API_KEY => 'NOT_API_KEY',
            self::NOT_MERCHANT_ID => 'NOT_MERCHANT_ID',
            self::NOT_ITEMS => 'NOT_ITEMS',
            self::NOT_ITEM => 'NOT_ITEM',
            self::NOT_PAYOUT => 'NOT_PAYOUT',
            self::PAYOUT_PAYED => 'PAYOUT_PAYED',
            self::PAYOUT_CANCELED => 'PAYOUT_CANCELED',
            self::ERROR_AT_CREATE_INVOICES => 'ERROR_AT_CREATE_INVOICES',
            self::NOT_CUSTOMERS => 'NOT_CUSTOMERS',
            self::ERROR_AT_CREATE_CUSTOMER => 'ERROR_AT_CREATE_CUSTOMER',
            self::NOT_NAMES => 'NOT_NAMES',
            self::NOT_WHATSAPP => 'NOT_WHATSAPP',
            self::NOT_EMAIL => 'NOT_EMAIL',
            self::NOT_CUSTOMER_ID => 'NOT_CUSTOMER_ID',
            self::ERROR_AT_UPDATE_CUSTOMER => 'ERROR_AT_UPDATE_CUSTOMER',
            self::NOT_CUSTOMER => 'NOT_CUSTOMER',
            self::NOT_ITEM_ID => 'NOT_ITEM_ID',
            self::NOT_PRICE => 'NOT_PRICE',
            self::NOT_DESCRIPTION => 'NOT_DESCRIPTION',
            self::NOT_TITLE => 'NOT_TITLE',
            self::ERROR_AT_CREATE_ITEM => 'ERROR_AT_CREATE_ITEM',
            self::ERROR_AT_CREATE_PAYOUTS => 'ERROR_AT_CREATE_PAYOUTS',
            self::ERROR_AT_UPDATE_ITEM => 'ERROR_AT_UPDATE_ITEM',
            self::NOT_WALLET => 'NOT_WALLET',
            self::INVALID_TRON_WALLET_ADDRESS => 'INVALID_TRON_WALLET_ADDRESS',
            self::INVALID_ENVIROMENT => 'INVALID_ENVIROMENT',
            self::NOT_PENDING_INVOICE => 'NOT_PENDING_INVOICE',
            self::ERROR_AT_SEND_TEXT_INVOICE_AS_PAYED => 'ERROR_AT_SEND_TEXT_INVOICE_AS_PAYED',
            self::ERROR_AT_SET_DEPOSIT_WALLET => 'ERROR_AT_SET_DEPOSIT_WALLET',
            self::ERROR_AT_CREATE_SPLIT => 'ERROR_AT_CREATE_SPLIT',
            self::NOT_SPLIT_ID => 'NOT_SPLIT_ID',
            self::ERROR_AT_REMOVE_SPLIT => 'ERROR_AT_REMOVE_SPLIT',
            self::NOT_HOSTS => 'NOT_HOSTS',
            self::NOT_HOST => 'NOT_HOST',
            self::HOST_EXISTS => 'HOST_EXISTS',
            self::INVALID_HOST => 'INVALID_HOST',
            self::HOST_NOT_IN_WHITE_LIST => 'HOST_NOT_IN_WHITE_LIST',
            self::NOT_FIELDS => 'NOT_FIELDS',
        };
    }
}