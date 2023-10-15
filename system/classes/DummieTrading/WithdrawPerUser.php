<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Util;

use BlockChain\Transaction;

class WithdrawPerUser extends Orm
{
    protected $tblName  = 'withdraw_per_user';
    
    const WAITING_FOR_DEPOSIT = 1;
    const DEPOSITED = 2;
    const DELETED = -1;
    
    public function __construct()
    {
        parent::__construct();
    }

    public static function saveWithdraw(int $user_login_id = null,int $withdraw_method_per_user_id = null,float $amount = null,int $transaction_per_wallet_id = null) : bool
    {
        if(isset($user_login_id,$withdraw_method_per_user_id,$amount,$transaction_per_wallet_id) === true)
        {
            $WithdrawPerUser = new WithdrawPerUser;
            $WithdrawPerUser->user_login_id = $user_login_id;
            $WithdrawPerUser->transaction_per_wallet_id = $transaction_per_wallet_id;
            $WithdrawPerUser->withdraw_method_per_user_id = $withdraw_method_per_user_id;
            $WithdrawPerUser->amount = $amount;
            $WithdrawPerUser->status = self::WAITING_FOR_DEPOSIT;
            $WithdrawPerUser->create_date = time();

            return $WithdrawPerUser->save();
        }

        return false;
    }

    public function getLastWithdraws(int $user_login_id = null,string $limit = null)
    {
        if(isset($user_login_id) === true)
        {
            $sql =  "SELECT 
                        {$this->tblName}.withdraw_method_per_user_id,
                        {$this->tblName}.amount,
                        {$this->tblName}.status,
                        {$this->tblName}.result_data,
                        withdraw_method_per_user.wallet,
                        catalog_withdraw_method.method,
                        catalog_currency.currency,
                        {$this->tblName}.apply_date
                    FROM
                        {$this->tblName}
                    LEFT JOIN 
                        withdraw_method_per_user
                    ON 
                        withdraw_method_per_user.withdraw_method_per_user_id = {$this->tblName}.withdraw_method_per_user_id
                    LEFT JOIN 
                        catalog_withdraw_method
                    ON 
                        catalog_withdraw_method.catalog_withdraw_method_id = withdraw_method_per_user.catalog_withdraw_method_id
                    LEFT JOIN 
                        catalog_currency
                    ON 
                        catalog_currency.catalog_currency_id = catalog_withdraw_method.catalog_currency_id
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}' 
                    AND 
                        {$this->tblName}.status != '".self::DELETED."' 
                        {$limit} 
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getAll(string $filter = null)
    {
        $sql =  "SELECT 
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.withdraw_method_per_user_id,
                    {$this->tblName}.transaction_per_wallet_id,
                    {$this->tblName}.apply_date,
                    {$this->tblName}.user_login_id,
                    {$this->tblName}.create_date,
                    {$this->tblName}.amount,
                    {$this->tblName}.status,
                    catalog_currency.currency,
                    catalog_withdraw_method.method,
                    withdraw_method_per_user.wallet,
                    user_login.email,
                    user_data.names
                FROM
                    {$this->tblName}
                LEFT JOIN 
                    user_data
                ON 
                    user_data.user_login_id = {$this->tblName}.user_login_id
                LEFT JOIN 
                    user_login
                ON 
                    user_login.user_login_id = {$this->tblName}.user_login_id
                LEFT JOIN 
                    withdraw_method_per_user
                ON 
                    withdraw_method_per_user.withdraw_method_per_user_id = {$this->tblName}.withdraw_method_per_user_id
                LEFT JOIN 
                    catalog_withdraw_method
                ON 
                    catalog_withdraw_method.catalog_withdraw_method_id = withdraw_method_per_user.catalog_withdraw_method_id
                LEFT JOIN 
                    catalog_currency
                ON 
                    catalog_currency.catalog_currency_id = catalog_withdraw_method.catalog_currency_id
                    {$filter} 
                ";

        return $this->connection()->rows($sql);
    }
}
