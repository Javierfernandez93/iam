<?php

namespace DummieTrading;

use HCStudio\Orm;

class NotificationPerUser extends Orm {
    const READ = 1;
	const UNREAD = 0;
	protected $tblName = 'notification_per_user';
	public function __construct() {
        parent::__construct();
	}
    
	public function setNotificationsAsRead($notification_per_users = null) 
	{
		if(isset($notification_per_users) === true)
		{
			foreach ($notification_per_users as $notification_per_user) 
			{
				$NotificationPerUser = new NotificationPerUser;

				if($NotificationPerUser->cargarDonde("notification_per_user_id = ?",$notification_per_user['notification_per_user_id']))
				{
					$NotificationPerUser->see = self::READ;
					$NotificationPerUser->save();
				}
			}
		}
	}
	
	public static function push(int $user_login_id = null,string $message = null,int $catalog_notification_id = null,string $link = '') : bool
	{
		if(isset($user_login_id,$message,$catalog_notification_id) === true)
		{
			$NotificationPerUser = new NotificationPerUser;
			$NotificationPerUser->user_login_id = $user_login_id;
			$NotificationPerUser->message = $message;
			$NotificationPerUser->catalog_notification_id = $catalog_notification_id;
			$NotificationPerUser->link = $link;
			$NotificationPerUser->create_date = time();
			
			return $NotificationPerUser->save();
		}

		return false;
	}

	public function getNotifications($user_login_id = null) 
	{
		if(isset($user_login_id) === true)
		{
			$sql = "SELECT 
						{$this->tblName}.{$this->tblName}_id,
						{$this->tblName}.message,
						{$this->tblName}.link,
						{$this->tblName}.see,
						{$this->tblName}.create_date,
                        catalog_notification.kind,
                        catalog_notification.extra
					FROM
						{$this->tblName}
                    LEFT JOIN 
                        catalog_notification 
                    ON 
                        catalog_notification.catalog_notification_id = {$this->tblName}.catalog_notification_id
					WHERE
						{$this->tblName}.user_login_id = '{$user_login_id}'
					AND 
						{$this->tblName}.status = '1'
					ORDER BY 
						{$this->tblName}.create_date
					DESC
						LIMIT 10
					";

			return $this->connection()->rows($sql);
		}

		return false;
	}
}