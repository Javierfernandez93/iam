<?php

namespace DummieTrading;

use JFStudio\Curl;

class ApiDisruptive extends Curl {
   const URL = 'https://my.disruptivepayments.io/api/';
   const EMAIL = 'leqjl93@hotmail.com';
   const PASSWORD = 'Paula060393';
   
   const GET = 'GET';
   const POST = 'POST';

   public static function getBarrer() : string
   {
      return 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2NDNlMTE3NGUzYWNiNGVmOTBmN2U5YjIiLCJpYXQiOjE2ODIwOTk3OTR9._Hzau-Zvgbs76e0Fb0d7sjJpm9Yvg_H1Ts2M-nLpxY4';
      return 'Bearer '.base64_encode(self::EMAIL.':'.self::PASSWORD);
   }

   public static function getURLByRequest(string $path = null) : string
   {
      return match($path) {
         'status' => self::URL.'system/status',
         'getMe' => self::URL.'user/me',
         'register' => self::URL.'user/register',
         'login' => self::URL.'user/login',
         'pinGenerate' => self::URL.'user/pin-generate',
         'wallets' => self::URL.'account/wallets',
         'generateSeed' => self::URL.'account/generate-seed',
         'credentialSend' => self::URL.'account/credentials-send',
         'getRegisterWallets' => self::URL.'account/register-wallet',
         'singlePayment' => self::URL.'wallets/single-pay',
         default => self::URL.'user/me'
      };
   }

   public function login() 
   {
      return $this->dispatcher('login',[
         'email' => self::EMAIL,
         'password' => self::PASSWORD,
      ],self::POST);
   }
   
   public function pinGenerate() 
   {
      return $this->dispatcher('pinGenerate',[
         'email' => self::EMAIL,
         'password' => self::PASSWORD,
      ],self::POST);
   }
   
   public function register() 
   {
      return $this->dispatcher('register',[
         'password' => self::PASSWORD,
      ]);
   }
   
   public function getMe() 
   {
      return $this->dispatcher('getMe');
   }
   
   public function wallets() 
   {
      return $this->dispatcher('wallets');
   }
   
   public function status() 
   {
      return $this->dispatcher('status');
   }
   
   public function credentialSend() 
   {
      return $this->dispatcher('credentialSend',[],self::POST);
   }
  
   public function singlePayment() 
   {
      return $this->dispatcher('singlePayment',[
         'network' => 'BSC',
         'fundsGoal' => '0',
         'smartContractAddress' => '1',
      ],self::POST);
   }
  
   public function getRegisterWallets() 
   {
      return $this->dispatcher('getRegisterWallets');
   }

   public function generateSeed() 
   {
      return $this->dispatcher('generateSeed',[],self::POST);
   }

   public function dispatcher(string $request = null,array $data = [],string $metod = 'GET') 
   {
      $this->setHeader('Authorization',self::getBarrer());

      if($metod == self::GET)
      {
         $this->get(self::getURLByRequest($request),$data);
      } else if($metod == self::POST) {
         $this->post(self::getURLByRequest($request),$data);
      }

      d($this);

      return $this->getResponse(true);
   }
}
