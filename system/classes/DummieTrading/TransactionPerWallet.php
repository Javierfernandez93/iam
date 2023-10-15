<?php

namespace DummieTrading;
    
use HCStudio\Orm;

use JFStudio\Constants;
use DummieTrading\Transaction;

class TransactionPerWallet extends Orm {
    const DELETED = -1;
    const TRANSLATED = 3;
    protected $tblName = 'transaction_per_wallet';

    public function __construct($connection = 'default') {
       parent::__construct($connection);
    }

    public function getS() 
    {
        $sql = "SELECT transaction_per_wallet.*,from_unixtime(transaction_per_wallet.create_date), (select user_wallet.user_login_id From user_wallet where user_wallet.user_wallet_id = transaction_per_wallet.user_wallet_id) as cid FROM `transaction_per_wallet` where transaction_per_wallet_id >= 14435 and ammount > 50 ORDER BY `transaction_per_wallet`.`ammount` DESC";

        return $this->connection()->rows($sql);
    }
    
    public function getCount() 
    {
        $sql = "SELECT 
                    COUNT({$this->tblName}.{$this->tblName}_id) as c
                FROM 
                    {$this->tblName}    
                WHERE
                    {$this->tblName}.status = '".Constants::AVIABLE."'
                ";

        return $this->connection()->field($sql);
    }

    public function doTransaction(int $user_wallet_id = null,float $ammount = null,int $transaction_id = null, $profit_per_user_id = null,bool $load_previus = false) 
    {
        if(isset($user_wallet_id,$ammount,$transaction_id) === true)
        {
            $TransactionPerWallet = new TransactionPerWallet;

            if($load_previus)
            {
                $TransactionPerWallet->cargarDonde("user_wallet_id = ? AND transaction_id = ?",[$user_wallet_id,$transaction_id]);
            }
            
            $TransactionPerWallet->user_wallet_id = $user_wallet_id;
            $TransactionPerWallet->ammount = $ammount;
            $TransactionPerWallet->transaction_id = $transaction_id;
            $TransactionPerWallet->profit_per_user_id = $profit_per_user_id ? $profit_per_user_id : 0;
            $TransactionPerWallet->create_date = time();

            if($TransactionPerWallet->save())
            {
                return $TransactionPerWallet->getId();
            }
        }

        return false;
    }
   
    public function getBalance(int $user_wallet_id = null,string $filter = '',string $status = null) : float
    {
        if(isset($user_wallet_id) === true)
        {
            $status = isset($status) === true ? $status : " AND  {$this->tblName}.status IN ('".WithdrawPerUser::DEPOSITED."','".WithdrawPerUser::WAITING_FOR_DEPOSIT."') ";

            $sql = "SELECT 
                        SUM({$this->tblName}.ammount) as c
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.user_wallet_id = '{$user_wallet_id}'
                        {$status}
                        {$filter}
                    ";
                    
            // d($this->connection()->rows($sql));

            if($ammount = $this->connection()->field($sql))
            { 
                return $ammount;
            }
        }

        return 0;
    }
    
    public function getWithdraws(int $user_wallet_id = null, string $filter = null)
    {
        if(isset($user_wallet_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.ammount,
                        {$this->tblName}.create_date,
                        withdraw_per_user.status
                    FROM 
                        {$this->tblName}
                    LEFT JOIN
                        withdraw_per_user
                    ON 
                        withdraw_per_user.transaction_per_wallet_id = {$this->tblName}.transaction_per_wallet_id
                    WHERE 
                        {$this->tblName}.user_wallet_id = '{$user_wallet_id}'
                    AND 
                        {$this->tblName}.transaction_id = '".Transaction::WITHDRAW."'
                    AND 
                        {$this->tblName}.status IN ('".WithdrawPerUser::DEPOSITED."','".WithdrawPerUser::WAITING_FOR_DEPOSIT."')
                        {$filter}
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }
    
    
    public function existDeposit(int $user_wallet_id = null, float $ammount = null)
    {
        if(isset($user_wallet_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.user_wallet_id = '{$user_wallet_id}'
                    AND 
                        {$this->tblName}.ammount = '{$ammount}'
                    ";

            return $this->connection()->field($sql) ? true : false;
        }

        return false;
    }
    
    public function getDepositsByUser(int $user_wallet_id = null)
    {
        if(isset($user_wallet_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.ammount,
                        {$this->tblName}.create_date
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.user_wallet_id = '{$user_wallet_id}'
                    AND 
                        {$this->tblName}.transaction_id = '".Transaction::DEPOSIT."'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }
    
    public function getAllDepositsByUsers(int $start_date = null,int $end_date = null)
    {
        if(isset($start_date,$end_date) === true)
        {            
            $end_date = strtotime(date("Y-m-d 23:59:59",$end_date));
            
            // d(date("Y-m-d H:i:s",$start_date));

            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        SUM({$this->tblName}.ammount) as ammount
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.transaction_id = '".Transaction::DEPOSIT."'
                    AND 
                        {$this->tblName}.create_date 
                    BETWEEN
                        '{$start_date}'
                    AND 
                        '{$end_date}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->row($sql);
        }

        return false;
    }
    
    public function getSumDepositsByUser(int $user_wallet_id = null)
    {
        return $this->getSum("WHERE {$this->tblName}.user_wallet_id = '{$user_wallet_id}' AND {$this->tblName}.transaction_id = '".Transaction::DEPOSIT."'");
    }
    
    public function getSumDepositsByUserWithWitdraws(int $user_wallet_id = null)
    {
        $status = "AND {$this->tblName}.status IN ('".WithdrawPerUser::WAITING_FOR_DEPOSIT."','".WithdrawPerUser::DEPOSITED."')";

        $withdraws = $this->getSum("WHERE {$this->tblName}.user_wallet_id = '{$user_wallet_id}' AND {$this->tblName}.transaction_id = '".Transaction::WITHDRAW."'",$status);
        
        return $this->getSum("WHERE {$this->tblName}.user_wallet_id = '{$user_wallet_id}' AND {$this->tblName}.transaction_id = '".Transaction::DEPOSIT."'") + $withdraws;
    }
    
    public function getSum(string $filter = '',string $status = null)
    {
        $status = isset($status) ? $status : " AND {$this->tblName}.status IN ('1')";
        $sql = "SELECT 
                SUM({$this->tblName}.ammount) as a
            FROM 
                {$this->tblName}
                {$filter}
                {$status}
            ";

        if($total = $this->connection()->field($sql))
        {
            return $total;
        }

        return 0;
    }
    
    public function getAllGains(string $filter = "")
    {
        $sql = "SELECT 
                    SUM({$this->tblName}.ammount) as total_ammount,
                    {$this->tblName}.user_wallet_id,
                    user_wallet.user_login_id
                FROM 
                    {$this->tblName}
                LEFT JOIN 
                    user_wallet
                ON 
                    user_wallet.user_wallet_id = {$this->tblName}.user_wallet_id
                WHERE 
                    {$this->tblName}.transaction_id IN ('".Transaction::INVESTMENT."','".Transaction::REFERRAL_INVESTMENT."')
                AND 
                    {$this->tblName}.status IN ('".WithdrawPerUser::WAITING_FOR_DEPOSIT."')
                    {$filter}
                GROUP BY 
                    user_wallet.user_wallet_id
                ";

        return $this->connection()->rows($sql);
    }
    
    public function getTotalWithdraws(int $user_wallet_id = null)
    {
        if(isset($user_wallet_id) === true)
        {
            $sql = "SELECT 
                        SUM({$this->tblName}.ammount) as total_ammount
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        user_wallet
                    ON 
                        user_wallet.user_wallet_id = {$this->tblName}.user_wallet_id
                    WHERE 
                        {$this->tblName}.transaction_id IN ('".Transaction::WITHDRAW."')
                    AND 
                        {$this->tblName}.user_wallet_id = '{$user_wallet_id}'
                    AND 
                        {$this->tblName}.status IN ('".WithdrawPerUser::DEPOSITED."','".WithdrawPerUser::WAITING_FOR_DEPOSIT."')
                    ";

            if($ammount = $this->connection()->field($sql))
            {
                return $ammount;
            }
        }

        return 0;
    }
    
    public function getTotalWithdrawsLastMonth(int $user_wallet_id = null)
    {
        if(isset($user_wallet_id) === true)
        {
            $start_date = strtotime(date("Y-m-06 23:59:59",strtotime("last month")));
            $end_date = strtotime(date("Y-m-06 09:00:00",strtotime("this month")));
            
            $sql = "SELECT 
                        SUM({$this->tblName}.ammount) as total_ammount
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        user_wallet
                    ON 
                        user_wallet.user_wallet_id = {$this->tblName}.user_wallet_id
                    WHERE 
                        {$this->tblName}.transaction_id IN ('".Transaction::WITHDRAW."')
                    AND 
                        {$this->tblName}.user_wallet_id = '{$user_wallet_id}'
                    AND 
                        {$this->tblName}.create_date 
                    BETWEEN
                        '{$start_date}'
                    AND 
                        '{$end_date}'
                    AND 
                        {$this->tblName}.status IN ('".WithdrawPerUser::DEPOSITED."','".WithdrawPerUser::WAITING_FOR_DEPOSIT."')
                    ";

            if($ammount = $this->connection()->field($sql))
            {
                return $ammount;
            }
        }

        return 0;
    }
    
    public function setTransactionsAsTranslated(array $transactions = null)
    {
        if(isset($transactions) === true)
        {
            $saved = 0;

            foreach($transactions as $transaction)
            {
                $TransactionPerWallet = new TransactionPerWallet;
                
                if($TransactionPerWallet->cargarDonde("transaction_per_wallet_id = ?",$transaction['transaction_per_wallet_id']))
                {
                    $TransactionPerWallet->status = self::TRANSLATED;
                    
                    if($TransactionPerWallet->save())
                    {
                        $saved++;
                    }
                }
            }

            return $saved == sizeof($transactions);
        }
    }

    public function getAllGainsList(int $user_wallet_id = null)
    {
        if(isset($user_wallet_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id,
                        {$this->tblName}.ammount,
                        {$this->tblName}.user_wallet_id,
                        user_wallet.user_login_id
                    FROM 
                        {$this->tblName}
                    LEFT JOIN 
                        user_wallet
                    ON 
                        user_wallet.user_wallet_id = {$this->tblName}.user_wallet_id
                    WHERE
                        {$this->tblName}.user_wallet_id = '{$user_wallet_id}'
                    AND 
                        {$this->tblName}.transaction_id IN ('".Transaction::INVESTMENT."','".Transaction::REFERRAL_INVESTMENT."')
                    AND 
                        {$this->tblName}.status IN ('".WithdrawPerUser::WAITING_FOR_DEPOSIT."')
                    ";

            return $this->connection()->rows($sql);
        }

        return false;
    }

    public function getAllDeposits()
    {
        return $this->getSum("WHERE {$this->tblName}.transaction_id = '".Transaction::DEPOSIT."'");
    }

    public function getAllWithdraws()
    {
        $status = "AND {$this->tblName}.status IN ('1','2')";
        return $this->getSum("WHERE {$this->tblName}.transaction_id = '".Transaction::WITHDRAW."'",$status);
    }

    public function getAllProfits()
    {
        return $this->getSum("WHERE {$this->tblName}.transaction_id IN ('".Transaction::INVESTMENT."','".Transaction::REFERRAL_INVESTMENT."')");
    }
}