<?php

namespace DummieTrading;

use HCStudio\Orm;

class MetaPerSheet extends Orm {
	protected $tblName = 'meta_per_sheet';
	const MIN_META_CONFIGURATED = 3;
	public function __construct() {
		parent::__construct();
	}

	public function get($sheet_per_proyect_id = null,$catalog_meta_id = null)
	{
		if(isset($sheet_per_proyect_id,$catalog_meta_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.content
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
					AND 
						{$this->tblName}.catalog_meta_id = '{$catalog_meta_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}
	}

	public function _get($sheet_per_proyect_id = null,$meta = null)
	{
		if(isset($sheet_per_proyect_id,$meta) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.content
					FROM 
						{$this->tblName}
					LEFT JOIN 
						catalog_meta
					ON 
						catalog_meta.catalog_meta_id = {$this->tblName}.catalog_meta_id
					WHERE 
						{$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
					AND 
						catalog_meta.meta = '{$meta}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}
	}

	public function getAll($sheet_per_proyect_id = null)
	{
		if(isset($sheet_per_proyect_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.content,
						catalog_meta.meta,
						catalog_meta.catalog_meta_id
					FROM 
						{$this->tblName}
					LEFT JOIN 
						catalog_meta
					ON 
						catalog_meta.catalog_meta_id = {$this->tblName}.catalog_meta_id
					WHERE 
						{$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->rows($sql);
		}
	}
	
	public function count($sheet_per_proyect_id = null)
	{
		if(isset($sheet_per_proyect_id) === true)
		{
			$sql = "SELECT 
						COUNT({$this->tblName}.{$this->tblName}_id) as c
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.sheet_per_proyect_id = '{$sheet_per_proyect_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}
	}
}