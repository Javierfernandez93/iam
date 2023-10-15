<?php

namespace Jcart;

use HCStudio\Session;

use DummieTrading\Package;
use DummieTrading\Product;
use DummieTrading\CatalogPaymentMethod;

use HCStudio\Util;
use HCStudio\Token;

class Cart 
{	
	private static $instances = [];
	private static $instance;
	private static $instance_id;
	private $Session = null;
	private $Sessions = null;

	public $main = false;
	public $charge_shipping = false;

	public $_instance_id = null;
	
	public $items = [];
	public $amount;
	public $discount;

	const DEFAULT_INSTANCE_MODE = true;
	const SESSIONS_SEGMENT = "cart_sessions";
	

	const ONLY_PACKAGE = 1;
	const ONLY_PRODUCT = 2;
	const SPECIFIC_ID = -1;

	const PRODUCT = 'Product';
	const PACKAGE = 'Package';
	const INSTANCE_LENGHT = 20;

	/* states  */
	const FIRST_OCCURRENCE = -1;
	const NEW_INSTANCE = 'NEW_INSTANCE';
	const MAIN_INSTANCE = 'MAIN_INSTANCE';
	const LAST_INSTANCE = 'LAST_INSTANCE';
	const TEMPORAL_INSTANCE = 'TEMPORAL_INSTANCE';
	const FIRST_INSTANCE = 5;

	public function loadSessionInstancesOrder() 
	{
		$this->loadSessionsInstance();
		
		$instances_id = $this->Sessions->getData();

		if(isset($instances_id) === true) 
		{
			return $instances_id;
		}

		return false;
	}

	public function isLoaded() : bool
	{
		return $this->_instance_id ? true : false;
	}

	public function loadSessionInstances() 
	{
		$this->loadSessionsInstance();
		$instances = [];
		
		$instances_ids = $this->Sessions->getData();

		if(isset($instances_ids) === true) 
		{
			foreach ($instances_ids as $instance_id) 
			{
				$instances[] = self::getInstance($instance_id);
			}
		}

		return $instances;
	}

	public function countLoadSessionInstances() {
		$this->loadSessionsInstance();
		
		$instances_id = $this->Sessions->getData();

		return isset($instances) ? count($instances) : 0;
	}

	public static function setInstanceAsLast($_instance_id = null,$reverse = false) 
	{
		if(isset($_instance_id) === true)
		{
			$instances_id = self::getInstancesOrder($Temp);
			
			if (in_array($_instance_id, $instances_id) === true) 
			{
				foreach ($instances_id as $instance_id) 
			    {
					if($instance_id != $_instance_id)
			    	{
						$new_order_instances[] =  $instance_id;
			    	}
			    }
				
			    $new_order_instances[] =  $_instance_id;
			}

			if(empty($new_order_instances) === false)
			{
				if($reverse === true)
				{
					$new_order_instances  =array_reverse($new_order_instances);
				}

				$Temp->Sessions = new Session(self::SESSIONS_SEGMENT);
				
				foreach ($new_order_instances as $key => $instance_id) {
					$Temp->Sessions->set((string)$key,$instance_id);
				}

				return true;
			}
		}

		return false;
	}

	public static function setInstanceAsFirst($_instance_id = null) 
	{
		return self::setInstanceAsLast($_instance_id,true);
	}

	public static function orderInstanceMainAsFirst() 
	{
		return self::setInstanceAsLast(self::getMainInstancId());
	}

	public static function getInstancesOrder() 
	{
		return (new Cart)->loadSessionInstancesOrder();
	}

	public static function formatInstanceName($company_id = null,$active = null,$package_id = null) {
		return "{$company_id}-".($active ? 1 : 0)."-{$package_id}";
	}
	
	public static function getInstances(bool $order_instances = false) 
	{	
		return (new Cart)->loadSessionInstances($order_instances);
 	}

 	public static function getCountInstances() 
	{
		return (new Cart)->countLoadSessionInstances();
 	}

 	public static function hasInstances() 
	{
		return !empty((new Cart)->loadSessionInstances());
 	}
 		
 	public static function hasFilterInstance($instance_id = null)
 	{	
 		return in_array($instance_id,[self::FIRST_INSTANCE,self::LAST_INSTANCE]);
 	}

 	public static function getFilterInstances($instance_id = null)
 	{
 		if($instances = self::getInstances())
 		{
	 		if($instance_id === self::FIRST_INSTANCE)
	 		{
	 			return $instances[0];
	 		} else if($instance_id === self::LAST_INSTANCE) {
	 			return $instances[sizeof($instances)-1];
	 		} 
 		}

 		return false;
 	}

 	public static function getFormatInstance($company_id = false,$package_id = null,$buy_type = null,$extra_param = 0)
	{
		return implode("-",[$company_id,$package_id,$buy_type,$extra_param]);
	}
	
 	public static function getInstanceId()
	{
		return (new Token)->randomKey(self::INSTANCE_LENGHT);
	}

	public static function getInstanceIdByFilter($instance_id = null)
	{
		if($instances = self::getInstancesInStorage())
		{
			if($instance_id === self::FIRST_INSTANCE)
			{
				return $instances[0];
			} else if($instance_id === self::LAST_INSTANCE) {
				return $instances[sizeof($instances)-1];
			}
		}

		return false;
	}

	public static function getLastInstancIdActive($instance_id = null)
	{
		$Session = new Session(self::SESSIONS_SEGMENT);
		
        if ($instances_ids = $Session->getData()) {
        	return $instances_ids[sizeof($instances_ids)-1];
        }

		return false;
	}

	// new_method
 	public static function getMainInstancId()
	{
		$_instance_id = false;

		$Session = new Session(self::SESSIONS_SEGMENT);
		
        if ($instances_ids = $Session->getData()) 
		{
			foreach($instances_ids as $instance_id)
			{
				$SessionTemp = new Session($instance_id);
		
				if($SessionTemp->get("vars")["main"] == true)
				{
					$_instance_id = $instance_id;
				}
			}
        }

		return $_instance_id;
	}

	// new_method
	public static function getVarFromMainInstance($var_name = null)
	{
		$Session = new Session(self::SESSIONS_SEGMENT);
		$value = null;
		
        if ($instances_ids = $Session->getData()) 
		{
			foreach($instances_ids as $instance_id)
			{
				$SessionTemp = new Session($instance_id);
		
				if($vars = $SessionTemp->get("vars"))
				{
					if($vars["main"] == true)
					{
						$value = $vars[$var_name];
					}
				}
			}
        }

		return $value;
	}

	// new_method
	public static function getVarFromInstance($_instance_id = null,$var_name = null)
	{
		$Session = new Session(self::SESSIONS_SEGMENT);
		$value = null;
		
        if ($instances_ids = $Session->getData()) 
		{
			foreach($instances_ids as $instance_id)
			{
				$SessionTemp = new Session($instance_id);
		
				if($_instance_id == $instance_id)
				{
					if($vars = $SessionTemp->get("vars"))
					{
						if(isset($vars[$var_name]) === true)
						{
							$value = $vars[$var_name];
						}
					}
				}
			}
        }

		return $value;
	}

 	public static function getInstance($instance_id = null)
 	{	
 		$instance_id = isset($instance_id) === true ? $instance_id : self::NEW_INSTANCE;
 		
		if(isset($instance_id) === true && is_string($instance_id))
		{
            if (self::hasFilterInstance($instance_id) === true) 
			{
                $instance_id = self::getInstanceIdByFilter($instance_id);
            } else if($instance_id === self::NEW_INSTANCE) {
				$instance_id = self::getInstanceId();
            } else if($instance_id === self::MAIN_INSTANCE) {
				$instance_id = self::getMainInstancId();
			} 

			self::$instance_id = $instance_id;
			
			self::$instances[$instance_id] = self::$instances[$instance_id] ?? null;

			if(!self::$instances[$instance_id] instanceof self) {
				self::$instances[$instance_id] = new self;
			}

			if(self::$instances[$instance_id])
			{
				self::setInstance(self::$instances[$instance_id]);

				self::$instances[$instance_id]->setInstanceId($instance_id);

				return self::$instances[$instance_id];
			}
		}
 	}

 	public function setInstanceId($instance_id = null) 
	{
 		$this->_instance_id = $instance_id;
 	}

 	public static function _getInstance() {
 		return self::$instance;
 	}

 	public function loadSessionInstance() {
 		$this->setSessionInstance();
 	}

 	public function loadSessionsInstance() {
 		if(isset($this->Sessions) == false)
		{
 			$this->setSessionsInstance();
 		}
 	}

	// @TODO DELETE VAR AND METHODS
 	public function getChargeShipping() {
 		return $this->charge_shipping;
 	}

 	// public function getVar("main") {
 	// 	return $this->main;
 	// }

 	public function setMain($main = null) {
 		if (isset($main)) {
 			$this->main = $main;
 		}
 	}

 	public function setChargeShipping($charge_shipping = null) {
 		if (isset($charge_shipping)) {
 			$this->charge_shipping = $charge_shipping;
 		}
 	}

 	public function calculateFee() : float
 	{
		$amount = $this->getTotalAmount(null,null,['fee'=>false]);

		if($fee = (new CatalogPaymentMethod)->getFee($this->getVar('catalog_payment_method_id')))
		{
			return Util::getPercentaje($amount,$fee);
		}
		
		return 0;
 	}

 	public function getSessionInstance() {
 		return $this->Session;
 	}

 	public function setSessionInstance() 
	{
		if(isset($this->Session) === false) {
			$this->Session = new Session(self::$instance_id);
		}
 	}

 	public function setSessionsInstance() 
	{
        if (isset($this->Sessions) === false) 
		{
            $this->Sessions = new Session(self::SESSIONS_SEGMENT);
        }
 	}

 	public static function setInstance($instance = null) 
	{
 		if (isset($instance) === true) {
 			self::$instance = $instance;
 		}
 	}

 	public function exist($Item = null) 
 	{
 		$_key = false;

 		if (isset($Item)) 
 		{
 			if(isset($this->items) && empty($this->items) == false)
 			{
	 			foreach ($this->items as $key => $item) 
	 			{
	 				if(get_class($item['item']) === get_class($Item))
	 				{
	 					if($Item->getId() === $item['item']->getId()) {
	 						if(isset($item['offer']) == false)
	 						{
	 							$_key = $key;
	 						}
	 					}
	 				}
	 			}		
 			}
 		}	

 		return $_key;
 	}

 	public function loadFromSession() 
	{
		$this->loadSessionInstance();
		$this->setItems($this->Session->get("items"));
		$this->setVars($this->Session->get("vars"));
 	}

 	public function delete() {
 		$this->loadSessionInstance();
 		$this->loadSessionsInstance();

 		$this->Sessions->destroy();

 		return $this->Session->destroy();
 	}

 	public function save() : bool
	 {
 		$this->loadSessionsInstance();
 		$this->loadSessionInstance();

		$this->Session->set("items",$this->getItems());
		$this->Session->set("vars",$this->getVars());

		if($this->existSessionId($this->_instance_id) === false)
		{
			$this->appendInstance($this->_instance_id);
		}

		return true;
	}

	public function appendInstance(string $instance_id = null) : bool
	{
		if(isset($instance_id) == true)
		{
			$Session = new Session(self::SESSIONS_SEGMENT);

			$instances = self::getInstancesInStorage();
			$instances[] = $instance_id;

			$Session->set('instances',json_encode($instances));
			
			return true;
		}

		return false;
	}

	public static function getInstancesInStorage()
	{
		$Session = new Session(self::SESSIONS_SEGMENT);

		$instances_ids = $Session->get('instances');

		return $instances_ids ? json_decode($instances_ids,true) : [];
	}

	public function existSessionId($instance_id)
	{
		$instances = $this->getInstancesInStorage();

		return in_array($instance_id,$instances);
	}

	public function getKeyInstancesLastKey()
	{
		$instances_ids = $this->Sessions->getData();

		return sizeof($instances_ids) == 0 ? 1 : sizeof($instances_ids)+1;
	}

 	public function _addItem($Item = null,float $quantity = null,$additional_fields = null,bool $search = true)
 	{
 		$this->loadSessionInstance();

 		$data = ["item"=>$Item,"quantity"=>$quantity];

 		if(isset($additional_fields) === true && is_array($additional_fields))
 		{
 			$data = array_merge($data,$additional_fields);
 		}
 		
		$this->items = is_array($this->items) ? $this->items : [];
		
 		if(($key = $this->exist($Item)) !== false && $search === true)
 		{
 			$this->items[$key] = $data;
 			return true;
 		} else {
 			$this->items[] = $data;

 			return true;
 		}

 		return false;
 	}

 	public function setItems($items = null) {
 		$this->items = $items;
 	}

 	public function count($filter = null,$ids = null,$exclude = false) {
 		$count = 0;
 		$items = $this->getItems($filter,$ids,$exclude);

 		if(empty($items) === false)
 		{
 			foreach ($items as $item) 
 			{
 				$count += $item['quantity'];
 			}

 		}

 		return $count;
 	}

 	public function countPoints($filter = null,$ids = null,$exclude = false) {
 		$points = 0;
 		$items = $this->getItems($filter,$ids,$exclude);
 		if(empty($items) === false)
 		{
 			foreach ($items as $item) 
 			{
 				$points += $item['quantity'] * $item['item']->points;
 			}

 		}

 		return $points;
 	}

 	public function searchItem($kind,$id,$search_only_key,$search_offer = false) {
 		$data = false;

 		if(empty($this->getItems()) === false)
 		{
	 		foreach ($this->getItems() as $key => $item) 
	 		{
	 			if($this->getClassName(get_class($item['item'])) === $kind) 
	 			{
	 				if($id === -1)
	 				{
	 					if($search_only_key === true)
	 					{
	 						$data = $key;
	 					} else {
	 						$data = $item;
	 					}
	 				} else if($id === 0) {
		 				if($key === $id)
		 				{
		 					if($search_only_key === true)
		 					{
		 						$data = $key;
		 					} else {
		 						$data = $item;
		 					}
		 				}
	 				} else {
	 					if($item['item']->getId() === (int)$id)
		 				{
		 					if(($search_offer == false && $item['offer'] == false) || ($search_offer == true && $item['offer'] == true))
		 					{
			 					if($search_only_key === true)
			 					{
			 						$data = $key;
			 					} else {
			 						$data = $item;
			 					}		 				
		 					}
		 				}
	 				}
	 			}
	 		}
 		}
 		
 		return $data;
 	}

 	public function getClassName($CLASS) {
	    return array_pop(explode('\\', $CLASS));
	}

 	public function _getItem($kind,$id,$key = false,$search_offer = false) {
 		if($kind == 'Product') {
 			return $this->searchItem($kind,$id,$key,$search_offer);
 		} else if($kind == 'Package') {
 			return $this->searchItem($kind,$id,$key,$search_offer);
 		}
 	}

 	public function getItem(string $kind = null,int $id = 0,bool $search_offer = false) {
 		return $this->_getItem($kind,$id,false,$search_offer);
 	}

 	public function getItemKey($kind,$id = 0) {
 		return $this->_getItem($kind,$id,true);
 	}

 	public function deleteItems($kind) {
 		$key = $this->getItemKey($kind,-1);

 		if($key !== false)
 		{
	 		$items = $this->getItems();

	 		unset($items[$key]);

	 		$this->setItems(array_values($items));

	 		if(empty($this->getItems()) == false)
	 		{
	 			return $this->deleteItems($kind);
	 		}
 		} 

 		return true;
 	}

 	public function deleteItem($kind,$id) 
	{
 		$key = $this->getItemKey($kind,$id);

 		if($key !== false)
 		{
	 		$items = $this->getItems();

	 		unset($items[$key]);

	 		$this->setItems(array_values($items));
 		} 

 		return true;
 	}

 	public function deleteItemByKey($kind,$key) { 	
 		if($key !== false)
 		{
	 		$items = $this->getItems();

	 		unset($items[$key]);

	 		$this->setItems(array_values($items));
 		} 

 		return true;
 	}

 	public function deleteProducts() {
 		return $this->deleteItems('Product');
 	}

 	public function deletePackages() {
 		return $this->deleteItems('Package');
 	}

 	public function deletePackage($id) {
 		return $this->deleteItem('Package',$id);
 	}

 	public function deleteProduct($id) {
 		return $this->deleteItem('Product',$id);
 	}

 	public function deleteProductByKey($key) {
 		return $this->deleteItemByKey('Product',$key);
 	}

 	public function getProduct($id = 0,$search_offer = false) {
 		return $this->getItem('Product',$id,$search_offer);
 	}
 	public function getPackage($id = 0,$search_offer = false) {
 		return $this->getItem('Package',$id,$search_offer);
 	}

 	public function _getItems($filter = null,$ids = null,$exclude = null) {
 		$items = [];

 		if($this->items)
 		{	
	 		foreach ($this->items as $item) 
	 		{
	 			if(isset($ids) === true)
	 			{
	 				if($this->getClassName(get_class($item['item'])) === $filter) 
		 			{
		 				if(in_array($item['item']->getId(), $ids) === !$exclude) 
			 			{
			 				$items[] = $item;
			 			}
		 			}
	 			} else {
		 			if($this->getClassName(get_class($item['item'])) === $filter) 
		 			{
		 				$items[] = $item;
		 			}
	 			}
	 		}
	 		
	 		return $items;
 		}
 	}

 	public function getItems($filter = null,$ids = null,$exclude = null) 
	{
 		if($filter == self::ONLY_PACKAGE){
 			return $this->_getItems('Package',$ids,$exclude);
 		} else if($filter == self::ONLY_PRODUCT) {
 			return $this->_getItems('Product',$ids,$exclude);
 		} else if($filter == self::SPECIFIC_ID) {
 			return $this->_getItems(null,$ids,$exclude);
 		} else {
 			return $this->items;
 		}
 	}

 	public function addItems($items = null)
 	{
 		if (isset($items) && is_array($items)) {
 			foreach ($items as $Item) {
 				if(is_array($Item))
 				{
 					if(isset($Item['quantity']))
 					{
 						$this->addItem($Item['item'],$Item['quantity'],$Item['description']);
 					}
 				} else {
 					$this->addItem($Item,1);
 				}
 			}
 		}
 	}

 	public function getFormatedItems($filter = null)
 	{
 		if($items = $this->getItems($filter))
 		{
 			if(isset($items) === true)
 			{
	 			foreach ($items as $item) {
 					$products[] = [
 						"type" => strtolower((new \ReflectionClass($item['item']))->getShortName()),
 						"quantity" => $item['quantity'],
 						"id" => $item['item']->getId()
 					];
	 			}

	 			return json_encode($products);
 			}
 		}

 		return false;
 	}

 	public function setVar($var = null,$value = null)
 	{
 		if(isset($var) === true)
 		{
 			if(is_string($var) === true)
 			{
 				$this->_setVar($var,$value);
 			} else if(is_array($var) === true){
 				$this->setVars($var);
 			}
 		}
 	}

 	public function _setVar($name = null,$value = null)
 	{
 		$this->vars[$name] = $value;
 	}

 	public function setVars($vars = null)
 	{
 		if(isset($vars) === true && empty($vars) === false)
 		{
 			$keys = array_keys($vars);
 			$values = array_values($vars);

			foreach ($keys as $key => $name) {
				$this->_setVar($name,$values[$key]);
			}
 		}
 	}

 	public function getVars() {
 		return $this->vars;
 	}

 	public function isPackage($object = null)
	{
		return get_class($object) == 'Zuum\Package';
	}

 	public function getVar($name = null)
 	{
 		if(isset($name) === true) {
 			return isset($this->vars[$name]) === true ? $this->vars[$name] : false;
 		}

 		return false;
 	}

 	public function setAmount($amount = null) {
 		$this->amount = $amount;
 	}


 	public function getTotalWeight($filter = null,$ids = null) 
	{
		$total_weight = 0;
		$items = $this->getItems($filter,$ids);

 		if(isset($items) && (empty($items) == false))
 		{
 			foreach ($items as $item) 
 			{ 		
				$total_weight += $item['item']->weight * $item['quantity'];
 			}

 			$this->total_weight = $total_weight;
 		} 		

 		return $this->total_weight;
	}

 	public function getOptions(array $options = []) 
	{
		return array_merge([
			'fee' => true
		],$options);
	}

 	public function getTotalAmount($filter = null,$ids = null,array $options = []) 
 	{
		$options = $this->getOptions($options);

		$amount = 0;
 		$amount = $this->getSubAmount($filter,$ids);

 		if($this->getVar("charge_shipping") == true) {
			// $this->setAmount($this->getAmount() + $this->getCartModel()->getShippingAmount($this->getVar("shipping_company"),$this->count(Cart::ONLY_PRODUCT)));
		}

		if ($this->getVar("reissue")) {
			$amount += $this->getVar("reissue");
		}

		if ($this->getVar("charges")) {
			$amount += $this->getVar("charges");
		}
		
		if($options['fee'] == true)
		{
			if ($this->getVar("fee")) {
				$amount += $this->getVar("fee");
			}
		}
	 	
	 	$amount += $this->getTaxPaymentAmount();

		$this->setAmount($amount);

 		return round(($this->getAmount() - $this->getDiscount()),3);
 	}

 	public function setDiscount(float $discount = null) {
 		$this->discount = $discount;
 	}
 	
 	public function getDiscount() {
 		return $this->discount;
 	}

 	public function getAmount() : float {
 		return $this->amount;
 	}	

 	public function getSubAmount($filter = null,$ids = null) 
	{
		$amount = 0;
		
 		if($items = $this->getItems($filter,$ids))
 		{
 			foreach ($items as $item) { 		
				$item['price_rest'] = $item['price_rest'] ?? 0;
 				$amount += ($item['item']->amount * $item['quantity']) - $item['price_rest'];
 			}
 		} 		

		$this->setAmount($amount);
 		
 		return $this->getAmount();
 	}

 	public static function getVirtualActivationPackage($user_login_id = null)
	{
		$Session = new Session(self::SESSIONS_SEGMENT);
		$package_id = null;
		
		if ($instances_ids = $Session->getData()) 
		{
			foreach($instances_ids as $instance_id)
			{
				$SessionTemp = new Session($instance_id);
		
				if($vars = $SessionTemp->get("vars"))
				{
					if($vars["company_id"] == $user_login_id && $vars["buy_type"] == self::QUALIFICATION)
					{
						if(in_array($vars["package_id"],Package::getPackagesAviablesForExtra()) == true)
						{
							$package_id = $vars["package_id"];
						}
					}
				}
			}
		}

		return $package_id;
	}	

 	public static function isQualified($Cart = null)
	{
		return $Cart->getSubAmount(Cart::ONLY_PRODUCT) >= $Cart->getVar("min_ammount");
	}

	public static function _isQualified($Cart = null,$ammount = null)
	{
		return ($ammount + $Cart->getSubAmount(Cart::ONLY_PRODUCT)) >= $Cart->getVar("min_ammount");
	}

 	public static function hasVirtuallyQualified($user_login_id = null)
	{
		$virtually_qualified = false;

        if (isset($user_login_id) === true) 
		{
            if ($Instances = Cart::getInstances(true)) 
			{
                foreach ($Instances as $instance) 
				{
					$Cart = Cart::getInstance($instance->_instance_id);

                    $Cart->setCountry(self::getVarFromInstance($instance->_instance_id,"country_id"));
                    $Cart->loadFromSession();
					
					if($Cart->getVar("company_id") == $user_login_id)
					{
						if($Cart->isQualified($Cart) && in_array($Cart->getVar("package_id"),Package::getPackagesAviablesForExtra()))
						{
							$virtually_qualified = true;
						}
					}
                }
            }
        }

		return $virtually_qualified;
	}

 	public static function _getTotalAmount()
 	{
 		$total_instances_ammount = 0;

 		if ($Instances = Cart::getInstances(true)) 
 		{
 			foreach ($Instances as $instance) 
			 {
				$Cart = Cart::getInstance($instance->_instance_id);
				$Cart->loadFromSession();

                $total_instances_ammount += $Cart->getTotalAmount(Cart::ONLY_PRODUCT);
 			}
 		}

 		return $total_instances_ammount < 0 ? 0 : $total_instances_ammount;
 	}

 	public static function _getTotalAmountWitouthFees()
 	{
 		$total_instances_ammount = 0;

 		if($Instances = Cart::getInstances(true)) 
 		{
 			foreach ($Instances as $instance) 
			 {
				$Cart = Cart::getInstance($instance->_instance_id);
				$Cart->loadFromSession();

                $total_instances_ammount += $Cart->getTotalAmountWitouthFees(Cart::ONLY_PRODUCT);
 			}
 		}

 		return $total_instances_ammount < 0 ? 0 : $total_instances_ammount;
 	}

 	public static function existChance($CartInstances = null,$spin_per_user_id = null)
 	{
 		$exist = false;

 		if (isset($CartInstances)) 
 		{
 			foreach ($CartInstances as $instance)
 			{
				$Cart = Cart::getInstance($instance->_instance_id);

				if($Cart->getVar("spin_per_user_id") == $spin_per_user_id)
				{
					$exist = true;
				} 
 			}
 		}

 		return $exist;
 	}
 	
	public static function existChanceBySpinPerUserId($spin_per_user_id = null)
 	{
 		$exist = false;

 		if ($Instances = Cart::getInstances(true)) 
		{
			foreach ($Instances as $instance) 
			{
				$Cart = Cart::getInstance($instance->_instance_id);

				$Cart->setCountry(self::getVarFromInstance($instance->_instance_id,"country_id"));
				$Cart->loadFromSession();

				if($Cart->getVar("spin_per_user_id") == $spin_per_user_id)
				{
					$exist = true;
				} 
 			}
 		}

 		return $exist;
 	}

	public static function getInstancesUnique()
 	{
 		$single_instances = [];

 		if ($Instances = Cart::getInstances(true)) 
		{
			foreach ($Instances as $instance) 
			{
				$Cart = Cart::getInstance($instance->_instance_id);

				$Cart->setCountry(self::getVarFromInstance($instance->_instance_id,"country_id"));
				$Cart->loadFromSession();

				$single_instances[$instance->_instance_id] = $Cart->getVar("company_id");
 			}
 		}

		return sizeof($single_instances) > 0 ? array_flip(array_unique($single_instances)) : $single_instances;
 	}

 	public function getShippingAmount()
 	{
 		// return $this->getCartModel()->getShippingAmount($this->getVar("shipping_company"),$this->count(Cart::ONLY_PRODUCT));
 	}

 	public static function _existInstance($CartInstances = null,$instance_name = null)
 	{
 		$exist = false;

 		if (isset($CartInstances,$instance_name) === true) 
 		{
 			foreach ($CartInstances as $instance) 
 			{
 				if($instance->_instance_id == $instance_name)
 				{
 					$exist = true;
				}
 			}
 		}

 		return $exist;
 	}

 	public static function _getShippingAmount($CartInstances = null)
 	{
 		$shipping_ammount = 0;

 		if (isset($CartInstances)) 
 		{
 			foreach ($CartInstances as $instance) 
 			{
				$Cart = Cart::getInstance($instance->_instance_id);
		 		$Cart->loadFromSession();

		 		if($Cart->getVar("main"))
		 		{
		 			$shipping_ammount = $Cart->getShippingAmount();
		 		} 
 			}
 		}

 		return $shipping_ammount;
 	}

 	public static function _getPaymentMethod($CartInstances = null)
 	{
 		$payment_method = 0;

 		if (isset($CartInstances)) 
 		{
 			foreach ($CartInstances as $instance) 
 			{
 				if($instance->_instance_id != 0)
				{
					$Cart = Cart::getInstance($instance->_instance_id);
			 		$Cart->loadFromSession();

			 		if($Cart->getVar("main"))
			 		{
			 			$payment_method = $Cart->getPaymentMethod();
			 		}
				}
 			}
 		}

 		return $payment_method;
 	}

 	public static function _getShippingAddress($CartInstances = null)
 	{
 		$shipping_address = 0;

 		if (isset($CartInstances)) 
 		{
 			foreach ($CartInstances as $instance) 
 			{
 				if($instance->_instance_id != 0)
				{
					$Cart = Cart::getInstance($instance->_instance_id);
			 		$Cart->loadFromSession();

			 		if($Cart->getVar("main"))
			 		{
			 			$shipping_address = $Cart->getShippingAddress();
			 		}
				}
 			}
 		}

 		return $shipping_address;
 	}

 	public static function _getShippingCompany($CartInstances = null)
 	{
 		$shipping_company = 0;

 		if (isset($CartInstances)) 
 		{
 			foreach ($CartInstances as $instance) 
 			{
 				if($instance->_instance_id != 0)
				{
					$Cart = Cart::getInstance($instance->_instance_id);
			 		$Cart->loadFromSession();

			 		if($Cart->getVar("main"))
			 		{
			 			$shipping_company = $Cart->getShippingCompany();
			 		}
				}
 			}
 		}

 		return $shipping_company;
 	}

 	public static function _getReissue($CartInstances = null)
 	{
 		$reissue = 0;

 		if (isset($CartInstances)) 
 		{
 			foreach ($CartInstances as $instance) 
 			{
 				if($instance->_instance_id != 0)
				{
					$Cart = Cart::getInstance($instance->_instance_id);
			 		$Cart->loadFromSession();

			 		if($Cart->getVar("main"))
			 		{
			 			$reissue = $Cart->getVar("reissue");
			 		}
				}
 			}
 		}

 		return $reissue;
 	}

 	public function getTaxPaymentAmount()
 	{
		return 0;
 	}

 	public function addItem($Item = null,float $quantity = 1,$additional_fields = null,bool $search = true)
 	{
 		if(isset($Item) === true) {	
 			return $this->_addItem($Item,$quantity,$additional_fields,$search);
 		}

 		return false;
 	}

	public function getSave()
	{
		$save = 0;

		if($products = $this->getItems(Cart::ONLY_PRODUCT)) 
		{
			foreach ($products as $key => $product) 
			{
				if(isset($product["old_price"]) === true)
				{
					if($product['item']->price_distributor == 0)
					{
						$save += $product["old_price"] * $product["quantity"];
					} else {
						$save += $product['item']->price_distributor * $product["quantity"];
					}
				}
			}
		}

		return $save;
	}

	public function deleteOffers()
	{
		if($items = $this->getItems())
		{
			foreach ($items as $key => $item) 
			{
				if($item['offer'] == true)
				{
					unset($items[$key]);
				}
			}

	 		$this->setItems(array_values($items));
			$this->setVar("gift_assigned",false);
			$this->save();
		}
		
		$this->loadFromSession();
	}
	
	public function getPaymentMethods()
	{
		return (new CatalogPaymentMethod)->getAll($this->getVar('country_id'));
	}


	public static function deleteCarts()
	{
		$Sessions = new Session(self::SESSIONS_SEGMENT);
		return $Sessions->destroy();
	}

	public static function formatFundsItems(float $amount = null)
	{
		return json_encode([
			0 => [
				'type' => 'product',
				'quantity' => $amount,
				'id' => (new Product)->getProductIdBySku(Product::EWALLET_SKU),
			]
		]);
	}
}