<?php

namespace Jcart;

use Jcart\ModelShippingMethod;

class ModelShippingMethodCOD
{
	public $shipping_method_cod;
	
	public function __construct()
	{
		$this->setModelShippingMethodCOD();
	}

	public function setModelShippingMethodCOD(){
		$this->shipping_method_cod = [
			ModelShippingMethod::$COD,
			ModelShippingMethod::$ENVIACOLOMBIA,
			ModelShippingMethod::$CONTRAENTREGA_SIN_COBRO,
		];
	}

	public function getModelShippingMethodCOD(){
		return $this->shipping_method_cod;
	}
}