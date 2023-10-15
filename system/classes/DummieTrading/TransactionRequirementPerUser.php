<?php

namespace DummieTrading;

use HCStudio\Orm;

class TransactionRequirementPerUser extends Orm {
    const PENDING = 1;
    const DELETED = -1;
    const EXPIRED = 0;
    const VALIDATED = 2;
    
    const CRONJOB = 1;
    const ADMIN = 2;
    protected $tblName = 'transaction_requirement_per_user';

    public function __construct() {
        parent::__construct();
    }

    public function getTransactionsPending() 
    {
        return $this->getTransactions(" WHERE {$this->tblName}.status = '".self::PENDING."'");
    }
    
    public function getTransactions(string $filter = null) 
    {
        $sql = "SELECT
                    {$this->tblName}.{$this->tblName}_id,
                    {$this->tblName}.ammount,
                    {$this->tblName}.user_login_id,
                    {$this->tblName}.item_number,
                    {$this->tblName}.txn_id,
                    {$this->tblName}.create_date,
                    {$this->tblName}.checkout_data,
                    {$this->tblName}.validate_date,
                    {$this->tblName}.status,
                    user_login.email,
                    user_account.image,
                    user_data.names
                FROM 
                    {$this->tblName}
                LEFT JOIN 
                    user_data 
                ON 
                    user_data.user_login_id = {$this->tblName}.user_login_id
                LEFT JOIN 
                    user_account 
                ON 
                    user_account.user_login_id = {$this->tblName}.user_login_id
                LEFT JOIN 
                    user_login 
                ON 
                    user_login.user_login_id = {$this->tblName}.user_login_id
                    {$filter}
                GROUP BY 
                    {$this->tblName}.transaction_requirement_per_user_id 
                ";

        return $this->connection()->rows($sql);
    }

    public function getLastTransactions(int $user_login_id = null,string $limit = '') 
    {
        if(isset($user_login_id) === true)
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.ammount,
                        {$this->tblName}.item_number,
                        {$this->tblName}.txn_id,
                        {$this->tblName}.create_date,
                        {$this->tblName}.validate_date,
                        {$this->tblName}.status
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.user_login_id = '{$user_login_id}'
                    ORDER BY 
                        {$this->tblName}.create_date
                    DESC 
                        {$limit}
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }
    
    public function checkTransactionStatus(int $transaction_requirement_per_user_id = null,string $filter = '') 
    {
        if(isset($transaction_requirement_per_user_id) === true)
        {
            $sql = "SELECT
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.transaction_requirement_per_user_id = '{$transaction_requirement_per_user_id}'
                        {$filter}
                    ";

            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }

    public function isPending(int $transaction_requirement_per_user_id = null) : bool
    {
        return $this->checkTransactionStatus($transaction_requirement_per_user_id," AND {$this->tblName}.status = '".self::PENDING."'");
    }
    
    public function isAviableToReactive(int $transaction_requirement_per_user_id = null) : bool
    {
        return $this->checkTransactionStatus($transaction_requirement_per_user_id," AND {$this->tblName}.status IN ('".self::DELETED."','".self::EXPIRED."')");
    }
}