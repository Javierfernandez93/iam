<?php

namespace JFStudio;

use JFStudio\ModelBlockChainErrors;
use HCStudio\Token;
use HCStudio\Util;
use JFStudio\Routing;
use EWallet\Wallet;
use EWallet\Transaction;
use Exception;

class BlockChain {
    private static $instance;
    private static $KEY_LENGHT = 32; 
    private static $HASH_LENGHT = 120;
    private static $ROUTE_LENGHT = 512;
    private static $WALLET_ACTIVE = 1;
    private static $DEFAULT_AMOUNT = 1;
    private static $FIRST_TRANSACTION = 0;
    private static $TRANSACTIONS_CONFIRMED = 5;
    private $ip;
    private $amount;
    private $to;
    private $money_type;
    private $hash;
    private $last_route;
    private $last_hash;
    private $route;
    private $_token_key;
    private $_token;
    private $chain_structure;
    private $Token = null;
    private $Routing = null;
    private $last_block = null;
    private $File = null;
    private $Transaction = null;
    private $_transactions = null;
    private $Wallet = null;
    public static $GENESIS_BLOCK = "GENESIS";
    public static function getInstance()
    {
        if(!self::$instance instanceof self)
          self::$instance = new self;

        return self::$instance;
    }
    public function __construct()
    {
        $this->setToken(new Token);
        $this->getToken()->KEY_LENGHT = (int)(self::$KEY_LENGHT/2);
        $this->setTransaction(new Transaction);
        $this->setWallet(Wallet::getInstance());
        $this->setRouting(Routing::getInstance());
    }
    public function makeEwallet($user_login_id = null)
    {
        if($this->getWallet()->makeEwallet($user_login_id) === true)
        {
            return true;
        }

        return false;
    }
    public function loadWalletByCompanyId($user_login_id = null)
    {
        if(isset($user_login_id) === true)
        {
            if($this->getWallet()->cargarDonde("user_login_id = ?",$user_login_id))
            {
                if($this->getWallet()->status === self::$WALLET_ACTIVE)
                {
                    return true;
                } else {
                    throw new Exception(ModelBlockChainErrors::$INACTIVE_WALLET);
                }
            } else {
                throw new Exception(ModelBlockChainErrors::$NOT_WALLET_FOUND);
            }
        } else {
            throw new Exception(ModelBlockChainErrors::$NOT_WALLET_ID);
        }
    }
    public function loadWalletById($wallet_id = null)
    {
        if(isset($wallet_id) === true)
        {
            if($this->getWallet()->cargarDonde("wallet_id = ?",$wallet_id))
            {
                if($this->getWallet()->status === self::$WALLET_ACTIVE)
                {
                    return true;
                } else {
                    throw new Exception(ModelBlockChainErrors::$INACTIVE_WALLET);
                }
            } else {
                throw new Exception(ModelBlockChainErrors::$NOT_WALLET_FOUND);
            }
        } else {
            throw new Exception(ModelBlockChainErrors::$NOT_WALLET_ID);
        }
    }
    public function loadWalletByAddress()
    {

    }
    public function getRouting()
    {
        return $this->Routing;
    }
    public function setRouting($Routing = null)
    {
        if(isset($Routing) === true)
        {
            if(isset($this->Routing) === false)
            {
                $this->Routing = $Routing;
            }
        }
    }
    public function _getToken()
    {
        return $this->_token;
    }
    public function _setToken($_token = null)
    {
        if(isset($_token) === true)
        {
            $this->_token = str_replace(" ","+",$_token);
        }
    }  
    public function getLastBlock()
    {
        return $this->last_block;
    }
    public function setLastBlock($last_block = null)
    {
        if(isset($last_block) === true)
        {
            $this->last_block = $last_block;
        }
    } 
    public function _getTokenKey()
    {
        return $this->_token_key;
    }
    public function _setTokenKey($_token_key = null)
    {
        if(isset($_token_key) === true)
        {
            $this->_token_key = $_token_key;
        }
    }
    public function getToken()
    {
        return $this->Token;
    }
    public function setToken($Token = null)
    {
        if(isset($Token) === true)
        {
            if(isset($this->Token) === false)
            {
                $this->Token = $Token;
            }
        }
    }
    public function getTo()
    {
        return $this->to;
    }
    public function setTo($to = null)
    {
        if(isset($to) === true)
        {
            $this->to = $to;
        }
    }
    public function getMoneyType()
    {
        return $this->money_type;
    }
    public function setMoneyType($money_type = null)
    {
        if(isset($money_type) === true)
        {
            $this->money_type = $money_type;
        }
    }
    public function getChainStructure()
    {
        return $this->chain_structure;
    }
    public function setChainStructure($chain_structure = null)
    {
        if(isset($chain_structure) === true)
        {
            $this->chain_structure = $chain_structure;
        }
    }
    public function getFile()
    {
        return $this->File;
    }
    public function setFile($File = null)
    {
        if(isset($File) === true)
        {
            if(isset($this->File) === false)
            {
                $this->File = File::getInstance();
            }
        }
    }
    public function getTransaction()
    {
        return $this->Transaction;
    }
    public function setTransaction($Transaction = null)
    {
        if(isset($Transaction) === true)
        {
            $this->Transaction = $Transaction;
        }
    }
    public function _getTransactions()
    {
        return $this->_transactions;
    }
    public function _setTransactions($_transactions = null)
    {
        if(isset($_transactions) === true)
        {
            $this->_transactions = $_transactions;
        }
    }
    public function getWallet()
    {
        return $this->Wallet;
    }
    public function setWallet($Wallet = null)
    {
        if(isset($Wallet) === true)
        {
            $this->Wallet = $Wallet;
        }
    }
    public function setIp($ip = null)
    {
        if(isset($ip) === true)
        {
            $this->ip = $ip;
        }
    }
    public function getIp()
    {
        return $this->ip;
    }
    public function setAmount($amount = null)
    {
        if(isset($amount) === true)
        {
            $this->amount = $amount;
        }
    }
    public function getAmount()
    {
        return $this->amount;
    }
    public function setHash($hash = null)
    {
        if(isset($hash) === true)
        {
            $this->hash = $hash;
        }
    }
    public function getHash()
    {
        return $this->hash;
    }
    public function setLastHash($last_hash = null)
    {
        if(isset($last_hash) === true)
        {
            $this->last_hash = $last_hash;
        }
    }
    public function getLastHash()
    {
        return $this->last_hash;
    } 
    public function setRoute($route = null)
    {
        if(isset($route) === true)
        {
            $this->route = $route;
        }
    }
    public function getRoute()
    {
        return $this->route;
    }
    public function setLastRoute($last_route = null)
    {
        if(isset($last_route) === true)
        {
            $this->last_route = $last_route;
        }
    }
    public function getLastRoute()
    {
        return $this->last_route;
    }
    public function makeHash()
    {
        return $this->getToken()->randomKey(self::$HASH_LENGHT);
    }
    public function makeRoute()
    {
        return $this->getToken()->randomKey(self::$ROUTE_LENGHT);
    }
    public function setChain($chain = null)
    {
        if(isset($chain) === true)
        {
            $this->chain = $chain;
        }
    }
    public function getChain() {
        return $this->chain;
    }
    public function decode(&$str = null)
    {
        if(isset($str) === true)
        {
            if(is_string($str) === true)
            {
                $str = base64_decode($str);   
                $str = json_decode($str,true);
            }
        }
    }
    public function encode(&$str = null)
    {
        if(isset($str) === true)
        {
            if(is_array($str) === true)
            {
                $str = json_encode($str);
                $str = base64_encode($str);   
            }
        }
    }
    public function hasTokenKeyVar() 
    {
        if($token_key = Util::getVarFromPGS('token_key'))   
        {
            return true;
        }
        
        return false;
    }
    public function hasToken() 
    {
        if($token = Util::getVarFromPGS('token'))   
        {
            return true;
        }

        return false;
    }
    public function isValidBlock($transaction_id = 0)
    {
        if($transaction_id === self::$TRANSACTIONS_CONFIRMED)
        {
            return true;
        } 
        
        if($transaction_id === self::$FIRST_TRANSACTION) 
        {
            if($this->hasTokenKeyVar() === true)
            {
                if($this->hasToken() === true)
                {
                    $this->_setToken(Util::getVarFromPGS('token'));
                    $this->_setTokenKey(Util::getVarFromPGS('token_key'));
                } else {
                    throw new Exception(ModelBlockChainErrors::$NOT_HAS_FILE_NAME);
                }
            } else {
                throw new Exception(ModelBlockChainErrors::$NOT_HAS_FOLDER_NAME);
            }
        }
        
        if($this->_isValidBlock() === true)
        {
            $transaction_id++;
            return $this->isValidBlock($transaction_id);
        } 
    }

    public function _isValidBlock()
    {
        if($this->_getToken())
        {   
            if($this->_getTokenKey())
            {
                $this->setChain([
                    'token' => $this->_getToken(),
                    'key' => $this->_getTokenKey()
                ]);

                if($this->getToken()->checkToken($this->getChain()) === true)
                {
                    $this->_setTokenKey($this->getToken()->params['last_hash']);
                    $this->_setToken($this->getTransaction()->getTokenByTokenKey($this->_getTokenKey()));

                    return true;
                }
            }
        }
    }
    public function isValidTransaction($token = null,$token_key = null)
    {
        if(isset($token) === true)
        {
            $_POST['token'] = $token;
        }
        if(isset($token_key) === true)
        {
            $_POST['token_key'] = $token_key;
        }

        if($this->hasTokenKeyVar() === true)
        {
            $this->_setTokenKey(Util::getVarFromPGS("token_key"));

            if($this->hasToken() === true)
            {
                $this->_setToken(Util::getVarFromPGS("token"));

                if($this->getTransaction()->isValidTransaction($this->_getToken(),$this->_getTokenKey()) === true)
                {
                    $this->setChain([
                        'token' => $this->_getToken(),
                        'key' => $this->_getTokenKey(),
                    ]);

                    if($this->getToken()->checkToken($this->getChain()) === true)
                    {
                        $this->setChainStructure($this->getToken()->params);

                        return true;
                    }
                } else {
                    throw new Exception(ModelBlockChainErrors::$INVALID_BLOCK);
                }
            }
        }
    }
    public function getWalletUserInfo($wallet_per_user_id = null)
    {
        return $this->getWallet()->getWalletUserInfo($wallet_per_user_id);
    }
    public function isValidAddress($address = null)
    {
        if(isset($address) == true)
        {
            $this->setTo($address);
        }

        if($this->getTo())
        {
            if($this->getWallet()->hasValidLenghtAddress($this->getTo()))
            {
                if($wallet_per_user_id = $this->getWallet()->existAddress($this->getTo()))
                {
                    return $wallet_per_user_id;
                } else {
                    throw new Exception(ModelBlockChainErrors::$INVALID_ADDRESS_NOT_FOUND);
                }
            } else {
                throw new Exception(ModelBlockChainErrors::$INVALID_ADDRESS_WALLET_LENGHT);
            }
        } else {
            throw new Exception(ModelBlockChainErrors::$INVALID_ADDRESS);
        }

        return false;
    }

    public function makeTransaction()
    {
        for ($i = 0; $i < $this->getAmount(); $i++) { 
            $token = $this->getTransaction()->token;

            $this->setTransaction(new Transaction);

            if($i === self::$FIRST_TRANSACTION)
            {
                $this->setLastHash(self::$GENESIS_BLOCK);
                $this->setHash($this->makeHash());   
                $this->_makeTransaction();
            } else {
                $this->setLastHash($this->getChain()['key']);
                $this->setHash($this->makeHash());   
                $this->_makeTransaction();
            }

        }
    }

    private function _makeTransaction()
    {
        if($this->getIp())
        {
            if($this->getLastHash())
            {
                if($this->isValidAddress())
                {   
                    $this->setChainStructure([
                        'last_hash' => $this->getLastHash(),
                        'to' => $this->getTo(),
                        'hash' => $this->getHash(),
                        'money_type' => $this->getMoneyType(),
                        'token' => $this->_getToken(),
                        'unixtime' => time(),
                        'amount' => self::$DEFAULT_AMOUNT,
                        'ip' => $this->getIp()
                    ]);

                    $this->setChain($this->getToken()->getToken($this->getChainStructure(),true));

                    $this->getTransaction()->token = $this->getChain()['token'];
                    $this->getTransaction()->wallet_id = $this->getWallet()->getWalletId($this->getTo());
                    $this->getTransaction()->token_key = $this->getChain()['key'];

                    if($this->getTransaction()->save() === true)
                    {
                        $this->setLastBlock($this->getChainStructure());
                        return $this->getChainStructure();
                    } else {
                        throw new Exception(ModelBlockChainErrors::$NOT_SAVE_TRANSACTION);
                    }    
                } else {
                    throw new Exception(ModelBlockChainErrors::$INVALID_ADDRESS);
                }
            } else {
                throw new Exception(ModelBlockChainErrors::$NOT_LAST_HASH);
            }
        } else {
            throw new Exception(ModelBlockChainErrors::$NOT_IP);
        }
    }


    public function decodeChain($chain = null) 
    {
        if(isset($chain) && is_array($chain))
        {
            $this->_setToken($chain['token']);
            $this->_setTokenKey($chain['token_key']);

            if($this->getTransaction()->isValidTransaction($this->_getToken(),$this->_getTokenKey()) === true)
            {
                $this->setChain([
                    'token' => $this->_getToken(),
                    'key' => $this->_getTokenKey(),
                ]);

                if($this->getToken()->checkToken($this->getChain()) === true)
                {
                    $this->setChainStructure($this->getToken()->params);

                    return true;
                }
            } else {
                throw new Exception(ModelBlockChainErrors::$ModelBlockChainErrors);
            }
        }
    }
    public function getMD5Transaction($transaction = null) 
    {
        if(isset($transaction) === true)
        {
            return md5($transaction['token'].":".$transaction['token_key']);
        }
    }
    public function getTransactions() 
    {
        if($this->isValidAddress($this->getWallet()->address))
        {
            if($transactions = $this->getTransaction()->getTransactions($this->getWallet()->getId())) {
                return $transactions;
            }
        } else {
            throw new Exception(ModelBlockChainErrors::$INVALID_ADDRESS);
        }

        return false;
    }
    public function addAmount($amount = null) 
    {
        if(isset($amount) === true)
        {
            if(is_integer($amount) === true)
            {
                $this->setAmount($this->getAmount()+$amount);
            }
        }
    }
    public function getFounds() 
    {
        if($transactions = $this->getTransaction()->getTransactions($this->getWallet()->getId()))
        {
            $this->_setTransactions($transactions);
            $this->setAmount(0);

            foreach ($this->_getTransactions() as $transaction) {
                if($this->decodeChain($transaction) === true)
                {
                    $this->addAmount($this->getChainStructure()['amount']);
                }
            }
        }

        return $this->getAmount();
    }
    public function assingBlock($address = null,$token_key = null) 
    {
        if(isset($address) === true)
        { 
            if(isset($token_key) === true) 
            {
                if($this->getTransaction()->cargarDonde("token_key = ? AND status = ?",[$token_key,Transaction::$AVAILABLE_TO_MINE]))
                {
                    if($this->getTransaction()->setTransactionAsSent($this->getTransaction()->getId()) === true)
                    {
                        $BlockChain = new BlockChain;
                        $BlockChain->setIp(Util::getIP());
                        $BlockChain->setTo($address);
                        $BlockChain->setLastHash($this->getTransaction()->token_key);
                        $BlockChain->setHash($this->makeHash());   

                        if($BlockChain->_makeTransaction())
                        {
                            return true;
                        }  else {
                            throw new Exception(ModelBlockChainErrors::$NOT_TRANSACTION_MADE);
                        }
                    } else {
                        throw new Exception(ModelBlockChainErrors::$NOT_TRANSACTION_SENT);    
                    }
                } else {
                    throw new Exception(ModelBlockChainErrors::$INVALID_TOKEN);
                }
            } else {
                throw new Exception(ModelBlockChainErrors::$INVALID_ADDRESS);
            }
        } else {
            throw new Exception(ModelBlockChainErrors::$INVALID_ADDRESS);
        }
    }
    private function _sendMoney($address = null,$amount = null) 
    {
        $done = 0;

        for($i = 0;$i < $amount;$i++)
        {
            $transaction = $this->_getTransactions()[$i];

            if(isset($transaction) === true)
            {
                if($this->decodeChain($transaction) === true)
                {
                    if($this->getTransaction()->setTransactionAsSent($transaction['transaction_id']) === true)
                    {
                        $BlockChain = new BlockChain;
                        $BlockChain->setIp(Util::getIP());
                        $BlockChain->setTo($address);
                        $BlockChain->setLastHash($transaction['token_key']);
                        $BlockChain->setHash($this->makeHash());   

                        if($BlockChain->_makeTransaction())
                        {
                            $done++; 
                        } 
                    }
                }
            }
        }


        return $amount === $done;
    }
    public function sendMoney($address = null,$amount = null) 
    {
        if(isset($amount) === true)
        {
            if(is_integer($amount))
            {
                if($amount > 0)
                {
                    if($this->isValidAddress($address))
                    {
                        if($this->getWallet()->address !== $address)
                        {
                            if($this->getFounds() > 0 )
                            {
                                if($this->getFounds() >= $amount)
                                {
                                    if($this->_sendMoney($address,$amount) === true)
                                    {
                                        return true;
                                    } else {
                                        throw new Exception(ModelBlockChainErrors::$ERROR_SENDING_MONEY);    
                                    }
                                }  else {
                                    throw new Exception(ModelBlockChainErrors::$NOT_ENOUGH_FOUNDS);    
                                }
                            } else {
                                throw new Exception(ModelBlockChainErrors::$NOT_WALLET_FOUNDS);
                            }
                        } else {
                            throw new Exception(ModelBlockChainErrors::$INVALID_ADDRESS_SAME_AS_SENDER);
                        }
                    } else {
                        throw new Exception(ModelBlockChainErrors::$INVALID_ADDRESS);
                    }
                } else {
                    throw new Exception(ModelBlockChainErrors::$INVALID_AMOUNT_ZERO);
                }
            } else {
                throw new Exception(ModelBlockChainErrors::$INVALID_AMOUNT_NOT_INT);
            }
        } else {
            throw new Exception(ModelBlockChainErrors::$INVALID_AMOUNT);
        }
    }
}