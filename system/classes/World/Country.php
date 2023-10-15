<?php

namespace World;

use HCStudio\Orm;
use HCStudio\Util;

class Country extends Orm {
	protected $tblName = 'country';

	public function __construct() {
		parent::__construct('world');
	}

	public function getCountry($iso2 = null){
		if (isset($iso2)) {
			$sql = "SELECT 
						{$this->tblName}.phone_code,
						{$this->tblName}.iso2
					FROM 
						{$this->tblName}
					WHERE	
						{$this->tblName}.iso2 = '{$iso2}'
					";
			return $this->connection()->row($sql);
		}

		return false;
	}

	public function getPhoneCodeByCountry($country = null){
		if (isset($country)) {
			$sql = "SELECT 
						{$this->tblName}.phone_code
					FROM 
						{$this->tblName}
					WHERE	
						{$this->tblName}.country = '{$country}'
					";
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getPhoneCodeByCountryId(int $country_id = null)
	{
		if (isset($country_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.phone_code
					FROM 
						{$this->tblName}
					WHERE	
						{$this->tblName}.country_id = '{$country_id}'
					";
					
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getCountryName($country_id = null)
	{
		if(isset($country_id) && !empty($country_id))
		{
			$sql = "SELECT 
						{$this->tblName}.country
					FROM 
						{$this->tblName}
					WHERE	
						{$this->tblName}.country_id = '{$country_id}'
					";

			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getCountryNameAndPhoneArea($country_id = null)
	{
		if(isset($country_id) && !empty($country_id))
		{
			$sql = "SELECT 
						{$this->tblName}.country,
						{$this->tblName}.phone_code
					FROM 
						{$this->tblName}
					WHERE	
						{$this->tblName}.country_id = '{$country_id}'
					";

			return $this->connection()->row($sql);
		}

		return false;
	}

	public function getCountryId($country = null)
	{
		if(isset($country) && !empty($country))
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE	
						{$this->tblName}.country LIKE '%{$country}%'
					";

			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.country
				FROM 
					{$this->tblName}
				WHERE
					{$this->tblName}.active = '1'
				";

		return $this->connection()->rows($sql);
	}

	public function getAllByWeb(){
		$sql = "SELECT 
					{$this->tblName}.country as nicename,
					{$this->tblName}.phone_code,
					{$this->tblName}.country_id
				FROM 
					{$this->tblName}
				WHERE
					{$this->tblName}.active = '1'
				ORDER BY 
					{$this->tblName}.country
				ASC
					";

		return $this->connection()->rows($sql);
	}

	public function getSingleByWeb(int $country_id = null)
	{
		if(isset($country_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.country as nicename,
						{$this->tblName}.phone_code,
						{$this->tblName}.country_id
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.active = '1'
					AND
						{$this->tblName}.country_id = '{$country_id}'
						";

			return $this->connection()->row($sql);
		}
	}

	public function getAllById($country){	
		if(is_numeric($country)){
			if($country==0){
				$country=1;
			} 
					$sql = "SELECT 
						{$this->tblName}.country as nicename
					FROM 
						{$this->tblName}
					WHERE
						{$this->tblName}.country_id={$country}
					";	

				return $this->connection()->field($sql);
		}
		return false;
	}

	public function getCountryCode(int $country_id = null)
	{
		if (isset($country_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.country_code
					FROM 
						{$this->tblName}
					WHERE	
						{$this->tblName}.country_id = '{$country_id}'
					";
					
			return $this->connection()->field($sql);
		}

		return false;
	}
}