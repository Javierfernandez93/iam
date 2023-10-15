<?php

namespace Jcart;

use HCStudio\Session;

class Jcart
{
	protected $_session  = false;
	private   $_debug    = false;
	private   $_intance  = false;
	public    $_loaded   = false;

	public $messages = array(
		0 => '<b>Jcart</b>::Started!<br>',
		1 => '<b>Jcart</b>::closed<br>',
		2 => '<b>Jcart</b>::itemAdded<br>',
		3 => '<b>Jcart</b>::itemDeleted<br>',
	);

	function __construct($cart_name = "jcart")
	{
		$this->setJcart($cart_name);
	}

	# public method's
	public function addItem($product_id = 1,$data = false)
	{
		return $this->_addItem($product_id,$data);
	}

	public function getAllItems()
	{
		return $this->_getAllItems();
	}

	public function setDebug($debug = false)
	{
		return $this->_setDebug($debug);
	}

	public function showMessage($id)
	{
		$this->setMessage($id);
		echo $this->getMessage($id);
	}

	public function setMessage($id)
	{
		$this->_session->setFlash($id,$this->messages[$id]);
	}

	public function getMessage($id)
	{
		return $this->_session->getFlash($id,$this->messages[$id]);
	}

	public function getItem($product_id = 1)
	{
		return $this->_getItem($product_id);
	}

	public function deleteItem($product_id = false)
	{
		if($product_id) return $this->_deleteItem($product_id);
		return false;
	}

	public function addReg($reg_name = false,$value = false)
	{
		if($reg_name) return $this->_addReg($reg_name,$value);

		return false;
	}

	public function getCount($field = false)
	{
		return ($this->_getCount($field)) ? $this->_getCount($field) : 0;
	}

	public function deleteCart()
	{
		$this->_deleteCart();
	}

	public function getSum($field = false)
	{
		return $this->_getSum($field);
	}

	# private method's
	private function _deleteCart()
	{
		$this->_session->destroy();
	}

	private function setJcart($cart_name)
	{
		$this->_session = new Session($cart_name);
		$this->_intance = $cart_name;

		if($this->_debug) $this->showMessage(0);
	}

	public function addSum($ammount = 0,$field = false)
	{
		return $this->_addSum($ammount,$field);
	}

	public function getReg($reg_name = false)
	{
		return $this->_getReg($reg_name);
	}

	public function addRest($ammount = 0,$field = false)
	{
		return $this->_addRest($ammount,$field);
	}

	private function _addSum($ammount,$field)
	{

		$ammount += $this->_session->get('_items.'.$field);

		return $this->_session->set('_items.'.$field,$ammount);
	}

	private function _addRest($ammount,$field)
	{
		$total = $this->_session->get('_items.'.$field);

		$total -= $ammount;

		# parsing int prevent 'E' convertion
		return $this->_session->set('_items.'.$field,(int)$total);
	}

	private function _getSum($field)
	{
		return $this->_session->get('_items.'.$field);
	}

	private function _addItem($product_id,$data)
	{
		$this->_deleteItem($product_id);
		if(!$this->getReg("_loaded"))
			$this->addReg("_loaded",true);

		$current_product = $this->getItem($product_id);



		$this->addSum($data['ammount_products'],'total_products');
		$this->addSum($data['price'],'total_price');
		$this->addSum($data['points'],'total_points');
		$this->addSum($data['weight'],'weight');
		
		/*Agregados Para distingir si son productos de Mer*/		
		$this->addSum($data['virtualPoints'],'virtualPoints');
		$this->addSum($data['priceMer'],'priceMer');	
		/*Agregados Para distingir si son productos de Mer*/

		if($current_product)
		{
			$data['ammount_products'] += $current_product['ammount_products'];
			$data['price'] += $current_product['total_price'];
			$data['points'] += $current_product['points'];
			$data['weight'] += $current_product['weight'];

			/*Agregados Para distingir si son productos de Mer*/
			$data['virtualPoints'] += $current_product['virtualPoints'];
			$data['priceMer'] += $current_product['priceMer'];		
			/*Agregados Para distingir si son productos de Mer*/
		}

		foreach ($data as $key => $value)
			$this->_session->set('_items.products.'.$product_id.'.'.$key,$value);

		if($this->_debug) $this->showMessage(2);

		return true;
	}

	private function _addReg($reg_name,$value)
	{
		$this->_session->set('_items.'.$reg_name,$value);

		return true;
	}

	private function _getReg($reg_name)
	{
		return $this->_session->get('_items.'.$reg_name);
	}

	private function _setDebug($debug)
	{
		return $this->_debug = $debug;
	}

	private function _getCount($field)
	{
		$extend = ($field) ? '.' . $field : '';

		return count($this->_session->get('_items.products'.$extend));
	}

	private function _getAllItems()
	{
		return $this->_session->get("_items.products");
	}

	public function _deleteItem($product_id)
	{
		$data = $this->_session->get('_items.products.'.$product_id);

		$this->addRest($data['price'],'total_price');
		$this->addRest($data['points'],'total_points');
		$this->addRest($data['ammount_products'],'total_products');
		$this->addRest($data['weight'],'weight');

		/*Agregados Para distingir si son productos de Mer*/
		$this->addRest($data['virtualPoints'],'virtualPoints');
		$this->addRest($data['priceMer'],'priceMer');
		/*Agregados Para distingir si son productos de Mer*/

	    return $this->_session->clear('_items.products.'.$product_id);
	}

	public function _getItem($product_id)
	{
		return $this->_session->get('_items.products.'.$product_id);
	}

}

?>