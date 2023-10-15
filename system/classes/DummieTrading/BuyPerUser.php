<?php

namespace DummieTrading;

use HCStudio\Util;
use HCStudio\Orm;
use JFStudio\Curl;

use DummieTrading\CatalogPaymentMethod;
use DummieTrading\UserReferral;
use DummieTrading\CommissionPerUser;
use DummieTrading\NotificationPerUser;
use DummieTrading\CatalogCommission;
use DummieTrading\Product;
use DummieTrading\AdviceType;
use DummieTrading\LicencePerUser;
use DummieTrading\Exercise;
use DummieTrading\CatalogCommissionType;
use DummieTrading\CatalogCurrency;
use DummieTrading\CatalogNotification;
use DummieTrading\CreditPerUser;

use BlockChain\Wallet;
use BlockChain\Transaction;

class BuyPerUser extends Orm {
  protected $tblName  = 'buy_per_user';

  /* types */
  const PRODUCT = 'product';
  const PACKAGE = 'package';

  /* status */
  const DELETED = -1;
  const EXPIRED = 0;
  const PENDING = 1;
  const VALIDATED = 2;

  /* buy_days */
  const BUYS_DAYS = 8;

  const URL_INSERT_INTO_ROCKET = 'https://www.qm.iam.com.mx/app/application/insertIntoRocket.php';
  // const URL_INSERT_INTO_ROCKET = 'http://localhost:8888/quickMoney/app/application/insertIntoRocket.php';

  public function __construct() {
    parent::__construct();
  }

  public function unformatData()
  {
    if($this->getId())
    {
      return self::_unformatData($this->data());
    }
  }
	
	public static function unformatItems(array $items = null)
	{
    $_items = [];
    
		foreach ($items as $item)
		{
      $_item = null;

      if($item['type'] == self::PACKAGE)
      {
        $_item = (new Package)->getPackage($item['id']);
      } else if($item['type'] == self::PRODUCT) {
        $_item = (new Product)->getProduct($item['id']);
      }

      if(isset($_item) && !empty($_item))
      {
        $_items[] = array_merge(
          $item,
          $_item
        );
      }
		}

		return $_items;
	}
  
  public static function _unformatData(array $data = null) 
  {
    if(isset($data) === true)
    {
      $data['catalog_payment_method'] = (new CatalogPaymentMethod)->get($data['catalog_payment_method_id']);
      $data['checkout_data'] = isset($data['checkout_data']) ? json_decode($data['checkout_data'] ?? null,true) : [];
      $data['ipn_data'] = isset($data['ipn_data']) ? json_decode($data['ipn_data'] ?? null,true) : [];
      $data['items'] = self::unformatItems(json_decode($data['item'],true));

      return $data;
    }
  }

  public function getAll(int $user_login_id = null,string $filter = null)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.amount,
                {$this->tblName}.catalog_payment_method_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.invoice_id,
                {$this->tblName}.send,
                {$this->tblName}.status,
                {$this->tblName}.item,
                {$this->tblName}.create_date,
                {$this->tblName}.checkout_data
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
                {$filter}
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";

      return $this->connection()->rows($sql);
    }

    return false;
  }
  
  public function getLastOrder(int $user_login_id = null,string $filter = null)
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.amount,
                {$this->tblName}.catalog_payment_method_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.invoice_id,
                {$this->tblName}.status,
                {$this->tblName}.item,
                {$this->tblName}.create_date,
                {$this->tblName}.checkout_data
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
                {$filter}
              ORDER BY 
                {$this->tblName}.create_date 
              DESC 
              ";

      return $this->connection()->row($sql);
    }

    return false;
  }
  
  public function getList(string $filter = null)
  {
    if($buys = $this->_getList($filter))
    {
      return array_map(function($buy){
        $buy['checkout_data'] = json_decode($buy['checkout_data'],true);
        return $buy;
      },$buys);
    }
  }
  
  public function _getList(string $filter = null)
  {
    $sql = "SELECT 
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.amount,
              {$this->tblName}.catalog_payment_method_id,
              {$this->tblName}.invoice_id,
              {$this->tblName}.user_login_id,
              {$this->tblName}.send,
              {$this->tblName}.status,
              {$this->tblName}.item,
              {$this->tblName}.create_date,
              {$this->tblName}.checkout_data,
              catalog_payment_method.payment_method,
              user_data.names
            FROM 
              {$this->tblName}
            LEFT JOIN 
              user_data 
            ON 
              user_data.user_login_id = {$this->tblName}.user_login_id
            LEFT JOIN 
              catalog_payment_method 
            ON 
              catalog_payment_method.catalog_payment_method_id = {$this->tblName}.catalog_payment_method_id
              {$filter}
            ORDER BY 
              {$this->tblName}.create_date 
            DESC 
            ";

    return $this->connection()->rows($sql);
  }

  public function isInvoicePending(string $invoice_id = null)
  {
    if(isset($invoice_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.invoice_id = '{$invoice_id}'
              AND 
                {$this->tblName}.status = '".self::PENDING."'
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }
  
  public function isInvoiceDeletedOrExpired(string $invoice_id = null)
  {
    if(isset($invoice_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.invoice_id = '{$invoice_id}'
              AND 
                {$this->tblName}.status IN ('".self::EXPIRED."','".self::DELETED."')
              ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }

  public static function hasProductVCard(array $items = null) : bool
  {
    $enable_vcard = false;

    foreach ($items as $item)
    {
      if($item['enable_vcard'])
      {
        $enable_vcard = true;
      }
    }

    return $enable_vcard;
  }
  
  public static function hasCommission(array $items = null) : bool
  {
    $has_commission = false;

    foreach ($items as $item)
    {
      if($item['catalog_commission'])
      {
        $has_commission = true;
      }
    }

    return $has_commission;
  }
  
  public static function hasFunds(array $items = null) : bool
  {
    $has_funds = false;

    foreach ($items as $item)
    {
      if($item['sku'] ?? null == Product::EWALLET_SKU)
      {
        $has_funds = true;
      }
    }

    return $has_funds;
  }

  public static function insertIntoRocket(array $items = null,int $user_login_id = null,int $buy_per_user_id = null) 
  {
    if(isset($items,$user_login_id,$buy_per_user_id) === true)
    {
      if($items = implode(",",array_column($items[0]['products'],'product_id')))
      {
        $Curl = new Curl;
        $Curl->setBasicAuthentication(Util::USERNAME,Util::PASSWORD);

        $Curl->post(self::URL_INSERT_INTO_ROCKET,[
          'items' => $items,
          'user_login_id' => $user_login_id,
          'buy_per_user_id' => $buy_per_user_id,
        ]);

        return $Curl->getResponse(true);
      }
    }
  }

  public static function getItemsCardAmount(array $items = null) : int
  {
    $amount = 0;
  
    foreach($items as $item)
    {
      foreach($item['products'] as $product)
      {
        $amount += $product['quantity'];
      }
    }

    return $amount;
  }
  
  public static function deletePayment(int $buy_per_user_id = null) : bool
  {
    if(isset($buy_per_user_id) === true)
    {
      $BuyPerUser = new BuyPerUser;

      if($BuyPerUser->loadWhere('buy_per_user_id = ?',$buy_per_user_id))
      {
        $BuyPerUser->status = self::DELETED;

        return $BuyPerUser->save();
      }
    }
  }

  public static function addComission(array $data = null) : bool
  {
    $UserReferral = new UserReferral;
    
    if($referral = $UserReferral->getInfo($data['user_login_id_from']))
    {
      if($sponsorReferral = $UserReferral->getInfo($referral['referral_id']))
      {
        $commission = round(Util::getPercentaje($data['price'],$sponsorReferral['commission']));
  
        $data = array_merge($data,[
          'user_login_id' => $referral['referral_id'],
          'amount' => $commission,
        ]);
  
        if(CommissionPerUser::addCommission($data))
        {
          $message = "Has recibido una comisión por $ {$commission} USD por la compra del paquete de tu referido {$data['user_login_id_from']} - Pronto la dispersaremos a tu billetera electrónica.";
  
          return NotificationPerUser::push($referral['referral_id'],$message,CatalogNotification::GAINS,"");
        }
      }
    }
    
    return false;
  }

  public static function validateIpnPayment(int $buy_per_user_id = null,array $ipn_data = []) : bool
  {
    $BuyPerUser = new BuyPerUser;

    if($BuyPerUser->loadWhere('buy_per_user_id = ?',$buy_per_user_id))
    {
      if(self::processPayment($buy_per_user_id))
      {
        $BuyPerUser->catalog_validation_method_id = CatalogValidationMethod::INTERNAL_IPN;
        $BuyPerUser->ipn_data = json_encode($ipn_data);
        $BuyPerUser->approved_date = time();
        $BuyPerUser->user_support_id = 0;
        $BuyPerUser->status = self::VALIDATED;
    
        return $BuyPerUser->save();
      }
    }

    return false;
  }

  public static function existTrial(int $user_login_id = null) : bool
  {
    if($package_ids = (new Package)->getTrialPackageIds())
    {
      $buys = (new BuyPerUser)->hasPackagesBuyData($user_login_id,$package_ids);

      if(isset($buys) && !empty($buys))
      {
        return true;
      }
    }

    return false;
  }
  public static function processPayment(int $buy_per_user_id = null,array $ipn_data = []) : bool
  {
    if(isset($buy_per_user_id) === true)
    {
      $BuyPerUser = new BuyPerUser;

      if($BuyPerUser->loadWhere('buy_per_user_id = ?',$buy_per_user_id))
      {
        $data = $BuyPerUser->unformatData();

        if(self::existTrial($BuyPerUser->user_login_id))
        {
          return false;
        }

        if(self::hasLicenceProduct($data['items']))
        {
          Exercise::setExerciseAsByBuy($BuyPerUser->getId(),Exercise::WAITING);
          
          self::addComission([
            'buy_per_user_id' => $BuyPerUser->getId(),
            'user_login_id_from' => $BuyPerUser->user_login_id,
            'price' => $BuyPerUser->amount,
            'catalog_commission_type_id' => CatalogCommissionType::NETWORK_TYPE_ID,
            'catalog_currency_id' => CatalogCurrency::USD, 
            'package_id' => 0
          ]);
        }
        
        if(self::hasFunds($data['items']))
        {
          if($ReceiverWallet = Wallet::getWallet($BuyPerUser->user_login_id))
          {
            $Wallet = Wallet::getWallet(Wallet::MAIN_EWALLET);
            
            if($transaction_per_wallet_id = $Wallet->createTransaction($ReceiverWallet->public_key,$BuyPerUser->amount,Transaction::prepareData(['@sysFund'=>$BuyPerUser->order_id]),true))
            {
              $BuyPerUser->ipn_data = json_encode([
                'transaction_per_wallet_id' => $transaction_per_wallet_id,
                'public_key' => $ReceiverWallet->public_key,
              ]);
              
              $BuyPerUser->save();
            } 
          } 
        }

        return true;
      }
    }

    return false;
  }
  
  public function getCurrency()
  {
    if($this->getId())
    {
      return (new CatalogCurrency)->getCurrency($this->catalog_currency_id);
    }

    return false;
  }

  public function isActive(int $user_login_id = null) : bool
  {
    if(isset($user_login_id) === true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id
              FROM 
                {$this->tblName}
              WHERE 
                {$this->tblName}.user_login_id = '{$user_login_id}'
              AND 
                {$this->tblName}.status = '".self::VALIDATED."'
              ";

      if($this->connection()->rows($sql))
      {
        return true;
      }
    }

    return false;
  }
  
  
  public static function hasPackageOnItems(array $items = null,int $package_id = null) : bool
  {
    $found = false;

    foreach($items as $item)
    {
      if($item['type'] == self::PACKAGE && $item['id'] == $package_id)
      {
        $found = true;
      }
    }

    return $found;
  }
  
  public static function hasCatalogPackageIdOnItems(array $items = null,int $catalog_package_type_id = null) : bool
  {
    $found = false;

    foreach($items as $item)
    {
      if($item['type'] == self::PACKAGE && $item['catalog_package_type_id'] == $catalog_package_type_id)
      {
        $found = true;
      }
    }

    return $found;
  }

  public function getProductsIds(array $items = null) : array
  {
    $product_ids = [];

    foreach($items as $item)
    {
      $product_ids = [...$product_ids,...array_column($item['products'],"product_id")];
    }

    return $product_ids;
  }

  public function hasProductIdActive(int $user_login_id = null,int $product_id = null) : bool
  {
    $found = false;

    if(isset($user_login_id,$product_id) === true)
    {
      if($buys = $this->getAll($user_login_id,"AND buy_per_user.status = '".self::VALIDATED."'"))
      {
        foreach($buys as $buy)
        {
          if($data = self::_unformatData($buy))
          {
            $product_ids = self::getProductsIds($data['items']);
            
            if(in_array($product_id,$product_ids))
            {
              $found = true;
            }
          }
        }
      }
    }

    return $found;
  }

  public function hasPackagesBuy(int $user_login_id = null,array $package_ids = null) : bool
  {
    $found = false;

    if(isset($user_login_id,$package_ids) === true)
    {
      if($buys = $this->getAll($user_login_id,"AND buy_per_user.status = '".self::VALIDATED."'"))
      {
        foreach($buys as $buy)
        {
          if($data = self::_unformatData($buy))
          {
            foreach(array_column($data['items'],'id') as $id)
            {
              if(in_array($id,$package_ids))
              {
                $found = true;
              }
            }
          }
        }
      }
    }

    return $found;
  }
  
  public function hasPackagesBuyData(int $user_login_id = null,array $package_ids = null) : array|bool
  {
    $found = false;

    if(isset($user_login_id,$package_ids) === true)
    {
      if($buys = $this->getAll($user_login_id,"AND buy_per_user.status = '".self::VALIDATED."'"))
      {
        $buys = array_map(function($buy) use ($package_ids){
          $buy['data'] = self::_unformatData($buy);
          
          return $buy;
        },$buys);

        return array_filter($buys,function($buy) use ($package_ids){
          $found = false;

          if($data = self::_unformatData($buy))
          {
            foreach(array_column($data['items'],'id') as $id)
            {
              if(in_array($id,$package_ids))
              {
                $found = true;
              }
            }
          }

          return $found;
        });
      }
    }

    return false;
  }
  
  public function hasPackagesBuyDataSingle(int $user_login_id = null,array $package_ids = null) : array|bool
  {
    $found = false;

    if(isset($user_login_id,$package_ids) === true)
    {
      if($buy = $this->getLastOrder($user_login_id,"AND buy_per_user.status = '".self::VALIDATED."'"))
      {
        $buy['data'] = self::_unformatData($buy);

        if($data = self::_unformatData($buy))
        {
          foreach(array_column($data['items'],'id') as $id)
          {
            if(in_array($id,$package_ids))
            {
              return $buy;
            }
          }
        }
      }
    }

    return false;
  }
  
  public function hasPackagesBuyDataLast(int $user_login_id = null,array $package_ids = null) : array|bool
  {
    $found = false;

    if(isset($user_login_id,$package_ids) === true)
    {
      if($buys = $this->getAll($user_login_id,"AND buy_per_user.status = '".self::VALIDATED."'"))
      {
        $buys = array_map(function($buy) use ($package_ids){
          $buy['data'] = self::_unformatData($buy);
          
          return $buy;
        },$buys);

        return array_filter($buys,function($buy) use ($package_ids){
          $found = false;

          if($data = self::_unformatData($buy))
          {
            foreach(array_column($data['items'],'id') as $id)
            {
              if(in_array($id,$package_ids))
              {
                $found = true;
              }
            }
          }

          return $found;
        });
      }
    }

    return false;
  }

  public function hasPackageBuy(int $user_login_id = null,int $package_id = null) : bool
  {
    $found = false;

    if(isset($user_login_id,$package_id) === true)
    {
      if($buys = $this->getAll($user_login_id,"AND buy_per_user.status = '".self::VALIDATED."'"))
      {
        foreach($buys as $buy)
        {
          if($data = self::_unformatData($buy))
          {
            if(self::hasPackageOnItems($data['items'],$package_id))
            {
              $found = true;
            }
          }
        }
      }
    }

    return $found;
  }

  public function getBuysForAdvices()
  {
    if($buys = $this->_getBuysForAdvices())
    {
      return array_map(function($buy){
        if($data = self::_unformatData($buy))
        {
          $buy['formated_items'] = implode(", ",array_column($data['items'],'title'));
        }
        
        $buy['showed'] = false;
        $buy['advice_type'] = AdviceType::ACTIVATION;
        
        return $buy;
      },$buys);
    }

    return false;
  }
  
  public function _getBuysForAdvices()
  {
    $minBuyDays = strtotime("-".self::BUYS_DAYS." days");

    $sql = "SELECT 
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.user_login_id,
              {$this->tblName}.item,
              user_data.names
            FROM 
              {$this->tblName}
            LEFT JOIN 
              user_data 
            ON 
              user_data.user_login_id = {$this->tblName}.user_login_id
            WHERE 
              {$this->tblName}.approved_date >= '{$minBuyDays}'
            AND 
              {$this->tblName}.status = '".self::VALIDATED."'
            ";

    return $this->connection()->rows($sql);
  }
  
  public static function getFee(int $catalog_payment_method_id = null,float $amount = null) 
  {
    return Util::getPercentaje($amount,(new CatalogPaymentMethod)->getFee($catalog_payment_method_id));
  }
  
  public function getBuysByAmount(float $amount = null)
  {
    if(isset($amount) == true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.item,
                {$this->tblName}.amount,
                user_data.names
              FROM 
                {$this->tblName}
              LEFT JOIN 
                user_data 
              ON 
                user_data.user_login_id = {$this->tblName}.user_login_id
              WHERE 
                {$this->tblName}.amount = '{$amount}'
              AND 
                {$this->tblName}.status = '".self::VALIDATED."'
              ";

      return $this->connection()->rows($sql);
    }

    return false;
  }

  public function _getBuysByIn(string $user_login_id_in = null)
  {
    if(isset($user_login_id_in) == true)
    {
      $sql = "SELECT 
                {$this->tblName}.{$this->tblName}_id,
                {$this->tblName}.invoice_id,
                {$this->tblName}.user_login_id,
                {$this->tblName}.item,
                {$this->tblName}.checkout_data,
                {$this->tblName}.ipn_data,
                {$this->tblName}.status,
                {$this->tblName}.amount,
                LOWER(user_data.names) as names,
                user_login.email
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
              WHERE 
                {$this->tblName}.user_login_id IN ({$user_login_id_in})
              ";

      return $this->connection()->rows($sql);
    }

    return false;
  }

  public function getBuysByIn(string $user_login_id_in = null,int $catalog_package_type_id = null)
  {
    if(isset($user_login_id_in) == true)
    {
      if($buys = $this->_getBuysByIn($user_login_id_in))
      {
        $_buys = [];

        foreach($buys as $buy) {
          if($data = self::_unformatData($buy))
          {
            $buy = array_merge($buy,$data);
            $buy['formated_items'] = implode(", ",array_column($data['items'],'title'));

            if(self::hasCatalogPackageIdOnItems($data['items'],$catalog_package_type_id))
            {
              $_buys[] = $buy;
            }
          }

        }

        return $_buys;
      }
    }

    return false;
  }
  
  public function getReferralPayments(int $user_login_id = null,int $catalog_package_type_id = null)
  {
    if(isset($user_login_id) == true)
    {
      if($user_login_id_in = (new UserReferral)->getReferralsIds($user_login_id))
      {
        $user_login_id_in = implode(',',$user_login_id_in);

        if($buys = $this->getBuysByIn($user_login_id_in,$catalog_package_type_id))
        {
          return $buys;
        }
      }
    }

    return false;
  }
  
  public static function applyLicences(int $user_login_id = null,array $items = null) 
  {
    array_map(function($item) use($user_login_id) {
      array_map(function($product) use($user_login_id) {
        if(Product::hasLicenceSku($product['product']['sku']))
        {
          LicencePerUser::makeLicences($user_login_id,$product['quantity']);
        }
      },$item['products']);
    },$items);
  }

  public static function applyCredits(int $user_login_id = null,array $items = null) 
  {
    array_map(function($item) use($user_login_id) {
      if($item['sku'] == Product::CREDIT_SKU)
      {
        CreditPerUser::addCredits($user_login_id,$item['quantity']);
      }
    },$items);
  }

  public static function hasLicenceProduct(array $items = null) : bool
  {
    $licence_product = false;

    foreach ($items as $item)
    {
      if(isset($item['products']))
      {
        foreach ($item['products'] as $product)
        {
          if(isset($product['product']))
          {
            if(isset($product['product']['sku']))
            {
              if(Product::hasLicenceSku($product['product']['sku']))
              {
                $licence_product = true;
              }
            }
          }
        }
      } else {
        if(Product::hasLicenceSku($item['sku']))
          {
            $licence_product = true;
          }
      }
    }

    return $licence_product;
  }

  public static function hasCreditProduct(array $items = null) : bool
  {
    $credit_product = false;

    foreach ($items as $product)
    {
      if(Product::hasCreditSku($product['sku']))
      {
        $credit_product = true;
      }
    }

    return $credit_product;
  }

  public function getAllBuys(string $filter = null)
  {
    $sql = "SELECT 
              {$this->tblName}.{$this->tblName}_id,
              {$this->tblName}.amount,
              {$this->tblName}.catalog_payment_method_id,
              {$this->tblName}.user_login_id,
              {$this->tblName}.invoice_id,
              {$this->tblName}.status,
              {$this->tblName}.item,
              {$this->tblName}.create_date,
              {$this->tblName}.checkout_data
            FROM 
              {$this->tblName}
              {$filter}
            ORDER BY 
              {$this->tblName}.create_date 
            DESC 
            ";

    return $this->connection()->rows($sql);
  }

  public function getPackageBuys(int $package_id = null) 
  {
    if($buys = $this->getAllBuys("WHERE {$this->tblName}.status = '".self::VALIDATED."'"))
    {
      return array_filter($buys,function($buy) use($package_id) {
        if($data = self::_unformatData($buy))
        {
          return self::hasPackageOnItems($data['items'],$package_id);
        } return false;
      });
    }

    return [];
  }


  public function existBuyByInvoiceId(string $invoice_id = null) : bool
  {
    if(isset($invoice_id))
    {
      $sql = "SELECT 
            {$this->tblName}.{$this->tblName}_id
          FROM 
            {$this->tblName}
          WHERE 
            {$this->tblName}.invoice_id = '{$invoice_id}'
          AND 
            {$this->tblName}.status != '-1'
          ";

      return $this->connection()->field($sql) ? true : false;
    }

    return false;
  }


  public static function getBuyLeftDays(array $data = null) 
  {
    $endSuscription = strtotime("+".$data['days']." days", $data['activation_date']);

    $days = round(($endSuscription- time()) / (60 * 60 * 24));

    return $days > 0 ? $days : 0;
  }

  public static function getBuyInfo(array $buy = null) 
  {
    return [
      'leftDays' => self::getBuyLeftDays([
        'days' => $buy['data']['items'][0]['day'],
        'activation_date' => $buy['create_date']
      ]),
      'create_date' => $buy['create_date'],
      'buy_per_user_id' => $buy['buy_per_user_id'],
      'amount' => $buy['amount'],
      'day' => $buy['data']['items'][0]['day'],
      'title' => $buy['data']['items'][0]['title']
    ];
  }

  public static function setAsSend(int $buy_per_user_id = null)
  {
    if(!isset($buy_per_user_id))
    {
      return false;
    }

    $BuyPerUser = new self;
    
    if(!$BuyPerUser->loadWhere("buy_per_user_id = ?", $buy_per_user_id))
    {
      return false;
    }

    $BuyPerUser->send = 1;
    
    return $BuyPerUser->save();
  }
}