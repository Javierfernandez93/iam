<?php

namespace DummieTrading;

use HCStudio\Orm;

use JFStudio\Constants;

class VCardPerUser extends Orm
{
	protected $tblName = 'vcard_per_user';
	
	const PROYECTS_URL = "apps/vCard/";
    const DEFAULT_VCARD_NAME = "Tarjeta virtual";
    const TEMPLATE_NAME = "template";

	const UNPUBLISHED = 0;
	const PUBLISHED = 1;
	const DELETED = -1;
	public function __construct()
	{
		parent::__construct();
	}

	public static function getViewPathFile(int $vcard_per_user_id = null) 
    {
        return self::TEMPLATE_NAME."-".$vcard_per_user_id;
    }

	public static function getViewPath(int $vcard_per_user_id = null)
	{
		return self::PROYECTS_URL.$vcard_per_user_id."/";
	}

	public function getAll(int $user_login_id = null) 
	{
		if (isset($user_login_id) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.title,
						{$this->tblName}.template_id,
						{$this->tblName}.route,
						{$this->tblName}.create_date,
						{$this->tblName}.update_date,
						{$this->tblName}.status,
                        template.view
					FROM 
						{$this->tblName}
                    LEFT JOIN 
                        template 
                    ON 
                        template.template_id = {$this->tblName}.template_id
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status IN (".self::PUBLISHED.",".self::UNPUBLISHED.")
					";

			return $this->connection()->rows($sql);
		}
	}
	
	public function existRoute(string $route = null,int $vcard_per_user_id = null) : bool
	{
		if (isset($route) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.route = '{$route}'
					AND 
						{$this->tblName}.vcard_per_user_id != '{$vcard_per_user_id}'
					AND 
						{$this->tblName}.status IN (".self::PUBLISHED.",".self::UNPUBLISHED.")
					";

			return $this->connection()->field($sql) ? true : false;
		}

		return false;
	}
	
	public function getRoute(int $vcard_per_user_id = null)
	{
		if (isset($vcard_per_user_id) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.route
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.vcard_per_user_id = '{$vcard_per_user_id}'
					AND 
						{$this->tblName}.status IN (".self::PUBLISHED.",".self::UNPUBLISHED.")
					";

			return $this->connection()->field($sql);
		}

		return false;
	}

	public function getVcardByRoute(string $route = null)
	{
		if (isset($route) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.route
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.route = '{$route}'
					AND 
						{$this->tblName}.status IN (".self::PUBLISHED.",".self::UNPUBLISHED.")
					";

			return $this->connection()->row($sql);
		}

		return false;
	}

	public function getVcardById(int $vcard_per_user_id = null)
	{
		if (isset($vcard_per_user_id) === true) 
        {
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.route
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.vcard_per_user_id = '{$vcard_per_user_id}'
					AND 
						{$this->tblName}.status IN (".self::PUBLISHED.",".self::UNPUBLISHED.")
					";

			return $this->connection()->row($sql);
		}

		return false;
	}
	
	public function getCount(int $user_login_id = null) 
	{
		if (isset($user_login_id) === true) 
        {
			$sql = "SELECT 
						COUNT({$this->tblName}.{$this->tblName}_id) AS c
					FROM 
						{$this->tblName}
					WHERE 
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status = '".Constants::AVIABLE."'
					";

			return $this->connection()->field($sql);
		}
	}
	 
    public function getTemplateId($vcard_per_user_id = null) 
    {
        if(isset($vcard_per_user_id) === true)
        {
            $sql = "SELECT 
                        {$this->tblName}.template_id
                    FROM 
                        {$this->tblName}
                    WHERE 
                        {$this->tblName}.vcard_per_user_id = '{$vcard_per_user_id}'
                    AND 
                        {$this->tblName}.status = '1'
                    ";

            return $this->connection()->field($sql);
        }

        return false;
    }
}
