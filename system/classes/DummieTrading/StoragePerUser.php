<?php

namespace DummieTrading;

use HCStudio\Orm;
use HCStudio\Token;

class StoragePerUser extends Orm {
	protected $tblName = 'storage_per_user';
	const PATH_LENGHT = 15;
	const PATH = 'src/storage/';
	const EXCLUDE_FOLDERS = ['..','.'];
	const DEFAULT_DISK_SIZE = 104857600; // mb
	const VIDEO_EXTENSIONS = ['mp4','avi'];
	const IMAGE_EXTENSIONS = ['png','jpg','jpeg','svg','gif','ai'];
	const RELATIVE_PATH = '../../';
	public function __construct() {
		parent::__construct();
	}

	public function getStoragePerUserId($user_login_id = null)
	{
		if (isset($user_login_id) === true) 
		{
			$path = $this->hasStorage($user_login_id);

			if($path == false)
			{
				$path = $this->makeStorage($user_login_id);
			}

			return $path;
		}

		return false;
	}

	public function getIcon($path = null)
	{
		if($extension = $this->getFileInfo($path))
		{
			if($extension === "txt")
			{
				return '<i class="fas fa-file-code"></i>';
			} else if(in_array($extension,self::IMAGE_EXTENSIONS) === true) {
				return '<i class="far fa-file-image"></i>';
			} else if(in_array($extension,self::VIDEO_EXTENSIONS) === true) {
				return '<i class="far fa-file-video"></i>';
			} else {
				return '<i class="far fa-file"></i>';
			}
		}
	}
	public function getFileInfo($path = null)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	public function getFolders($path = null)
	{
		$scan = scandir($path);
		
		foreach($scan as $file) 
		{
			$full_path = $path."/".$file;

			if(is_dir($full_path) === true && in_array($file, self::EXCLUDE_FOLDERS) == false) 
			{
				$folders[] = [
					"full_path" => $full_path,
					"file" => $file
				];
			}
		}

		return $folders;
	}

	public function getFiles(string $path = null)
	{
		$scan = scandir($path);
		
		foreach($scan as $file) 
		{
			$full_path = $path."/".$file;
			
		   	if(is_file($full_path) === true) 
		  	{
		    	$files[] = [
					"full_path" => $full_path,
					"file" => $file
				];
			}
		}

		return $files;
	}

	public function getFullData($path = null)
	{
		$scan = scandir($path);
		
		foreach($scan as $file) 
		{
		    $folders[] = $file;
		}

		return $folders;
	}

	public function getDiskSize()
	{
		return self::DEFAULT_DISK_SIZE;
	}

	public function getDiskUsed($path = null)
	{
	    $size = 0;

	    foreach (glob(rtrim($path, '/').'/*', GLOB_NOSORT) as $each) 
	    {
	        $size += is_file($each) === true ? filesize($each) : $this->getDiskUsed($each);
	    }

	    return $size;
	}

	public function getPercentajeDiskUsed($path = null)
	{
	    return ceil(($this->getDiskUsed($path) * 100) / self::DEFAULT_DISK_SIZE);
	}


	public function getStoragePath($path = null,$relative = false)
	{
		if($relative == true)
		{
			return self::RELATIVE_PATH.self::PATH.$path;	
		}
		
		return self::PATH.$path;	
	}

	public function makePathStorage($path = null)
	{
		return mkdir($this->getStoragePath($path,true),0777,true);
	}

	public function makeStorage($user_login_id = null)
	{
		$Token = new Token;
		
		$StoragePerUser = new StoragePerUser;
		$StoragePerUser->path = $Token->randomKey(self::PATH_LENGHT);
		$StoragePerUser->user_login_id = $user_login_id;
		$StoragePerUser->create_date = time();

		if($StoragePerUser->save() === true)
		{
			if($this->makePathStorage($StoragePerUser->path) === true)
			{
				return $StoragePerUser->path;
			}
		}

		return false;
	}

	public function hasStorage($user_login_id = null)
	{
		if (isset($user_login_id) === true) 
		{
			$sql = "SELECT 
						{$this->tblName}.path
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.{$this->tblName}_id,
					{$this->tblName}.brand,
					{$this->tblName}.create_date
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->rows($sql);
	}

	public function _getAll()
	{
		$sql = "SELECT 
					{$this->tblName}.brand
				FROM 
					{$this->tblName}
				WHERE 
					{$this->tblName}.status = '1'
				";
		
		return $this->connection()->column($sql);
	}

	public function getBrand($catalog_brand_id = null)
	{
		if(isset($catalog_brand_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.brand
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.catalog_brand_id = '{$catalog_brand_id}'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getCatalogBrandId($brand = null)
	{
		if(isset($brand) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.brand LIKE '%{$brand}%'
					AND 
						{$this->tblName}.status = '1'
					";
			
			return $this->connection()->field($sql);
		}

		return false;
	}
}