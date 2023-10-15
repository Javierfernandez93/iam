<?php

namespace DummieTrading;

use JFStudio\Curl;

class ApiTron extends Curl {
   // const URL = 'https://api.trongrid.io/wallet/'; // DEV
   const URL = 'https://api.shasta.trongrid.io/';
   
   // const BASE_URL = 'https://api.trongrid.io';
   const BASE_URL = 'https://api.shatsa.trongrid.io'; // SANDBOX

   const API_KEY = 'b2305ba6-4074-48ef-b80c-9d1d0031179f';
   const USDT_TRC20_CONTRACT = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';
   const FEE_LIMIT = 30;
   const GET = 'GET';
   const POST = 'POST';
   const USDT = 'USDT';

   public function getTrasanctionHistory(string $address = null) {
      $url = "https://api.trongrid.io/v1/accounts/{$address}/transactions/trc20";
      $this->get($url);
      
      return $this->getResponse(true);
   }
   
   public function getBalance(string $address = null) {
      $url = "https://api.shasta.trongrid.io/wallet/getAccount";
      $this->setHeader('TRON-PRO-API-KEY',self::API_KEY);
      $this->post($url,json_encode([
         'visible' => true,
         'address' => $address
      ]));
      
      return $this->getResponse(true);
   }
 
   public function getTokens() {
      $url = "https://api.trongrid.io/v1/assets/USDT/list";
      $this->setHeader('TRON-PRO-API-KEY',self::API_KEY);
      $this->get($url);
      
      return $this->getResponse(true);
   }


   public static function parserAmount(float $amount = null,int $decimals = null) : float
   {
      return (float) bcdiv((string)$amount, (string)1e6, 8);
   }

   public static function getURLByRequest(string $path = null,string $append = null) : string
   {
      return match($path) {
         'createTransaction' => self::URL.'wallet/createtransaction',
         'getAccountInfoByAddress' => self::URL."v1/accounts/{$append}",
         default => self::URL.'user/me'
      };
   }

   public function getAccountInfoByAddress(string $address = null) 
   {
      return $this->dispatcher([
         'request' => 'getAccountInfoByAddress',
         'append' => $address
      ]);
   }

   public function createTransaction(array $data = null) 
   {
      return $this->dispatcher([
         'request' => 'createTransaction',
         'data' => array_merge([
            'permission_id' => '',
            'visible' => true,
         ],$data),
         'method' => self::POST
      ]);
   }

   public function dispatcher(array $query = null) 
   {
      $data = isset($query['data']) ? json_encode($query['data']) : null;

      $this->setHeader('TRON-PRO-API-KEY',self::API_KEY);

      if(isset($query['method']) && $query['method'] == self::POST) {
         $this->post(self::getURLByRequest($query['request'],$query['append'] ?? null),$data);
      } else {
         $this->get(self::getURLByRequest($query['request'],$query['append'] ?? null));
      }

      return $this->getResponse(true);
   }
}
