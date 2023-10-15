<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class CatalogPackageType extends Orm {
	protected $tblName = 'catalog_package_type';

	public static $DEPOSIT = 1;
	public static $CASH = 2;
	public function __construct() {
		parent::__construct();
	}

	public function getIdbyPackageType(string $package_type = null)
	{
        if(isset($package_type))
        {
            $sql = "SELECT 
                        {$this->tblName}.{$this->tblName}_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.package_type = '{$package_type}'
                    AND 
                        {$this->tblName}.status = '".Constants::AVIABLE."'
                    ";
            
            return $this->connection()->field($sql);
        }
	}

	public function get($catalog_payment_method_id = null)
	{
		if(isset($catalog_payment_method_id) == true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.permission,
						{$this->tblName}.description
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->rows($sql);
		}

		return false;
	}
}