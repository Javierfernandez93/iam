<?php

namespace JFStudio;

use Talento\UserToken;

class Push {
	public $OPEN_BACKOFFICE = 1;
	public $OPEN_NETWORK = 2;
	public $OPEN_CONFERENCES = 3;
	public $OPEN_SHARE = 4;
	public $OPEN_PROFILE = 5;
	public $OPEN_WALLET = 6;
	public $OPEN_LINK = 100;
	private $path_to_firebase_cm = 'https://fcm.googleapis.com/fcm/send';
	private $api_key = 'AIzaSyCJ-sn77tGlnQGGO5iDSipH179K6DQY6CE';
	public $check_token = true;
	public $token = false;
	private static $instance;
	public function __construct(){ }
	public static function getInstance()
 	{
    if(!self::$instance instanceof self)
      self::$instance = new self;

    return self::$instance;
 	}
 	
 	public function getToken()
 	{
 		return $this->token;
 	}

 	public function isAbleToSendPush($token = false)
 	{
 		if(!$this->check_token) {
 			return true;
 		}

 		if($token) {	
 				return (new UserToken)->isAbleToSendPush($token);
 		}

 		return false;
 	}
 	public function setTokens($tokens)
 	{
 		$this->setToken($tokens,false);
 	}
 	public function setToken($token,$check_token = true)
 	{
 		$this->check_token = $check_token;
 		$this->token = $token;
 	}

 	public function sendImage($params = false,$save_push_per_user = false)
	{
		if($params && is_array($params))
		{	
			if($this->check_token) {
				if($params['token']) $this->setToken($params['token']);
			}
			
			if($company_id = $this->isAbleToSendPush($this->getToken())) {

				if($params['title'] && $params['body'] && $params['message'])
				{
					if($token = $this->getToken())
					{
						$fields = [
					  	'to' => $token,
				      'data' => [
				        "title" => $params['title'],
				        'message' => $params['message'],
				        'body' => $params['body'],
				        'icon' => $params['icon'] ? $params['icon'] : 1,
				        'image' => 'https://www.quinielagratis.com/src/img/logo.png',
				        'id' => $params['id'] ? $params['id'] : 1,
				        'image_url' => $params['image_url'] ? $params['image_url'] : "",
				        'sound' => "alarm",
				        'push_per_user_id' => $push_per_user_id ? $push_per_user_id : false,
				        'click_action' => $params['click_action'],
				        'turn_on_light' => $params['turn_on_light'],
				        'alarm_sound' => $params['alarm_sound'],
				        'ajust_sound' => $params['ajust_sound'],
				        'action_call_emergency' => $params['action_call_emergency'],
				        'vibration_pattern' => $params['vibration_pattern'],
				        'company_id' => $params['company_id'] ? $params['company_id'] : 0,
				        'url' => $params['url'] ? $params['url'] : "https://www.youtube.com/watch?v=WoGWPN3DWHk",
				      ],
			    	];

				    $headers = [
				      'Authorization:key=' . $this->api_key,
				      'Content-Type:application/json'
				    ];

						$ch = curl_init();

				    curl_setopt($ch, CURLOPT_URL, $this->path_to_firebase_cm);
				    curl_setopt($ch, CURLOPT_POST, true);
				    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
				    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

				    $result = curl_exec($ch);

				    curl_close($ch);
				    
				    return $result;
					}
				}
			}
		}
		return false;
	}
	
	
	public function sendMasivePush($params = false,$save_push_per_user = false)
	{
		
		if($params && is_array($params))
		{	
			
			if($params['title'] && $params['body'] && $params['message'])
			{
				$fields = [
				  	'registration_ids' => $params["tokens"],
					'data' => [
					"title" => $params['title'],
					'message' => $params['message'],
					'body' => $params['body'],
					'icon' => $params['icon'] ? $params['icon'] : 1,
					'image' => 'https://www.quinielagratis.com/src/img/logo.png',
					'id' => $params['id'] ? $params['id'] : 1,
					'image_url' => $params['image_url'] ? $params['image_url'] : "",
					'sound' => "alarm",
					'push_per_user_id' => $push_per_user_id ? $push_per_user_id : false,
					'click_action' => $params['click_action'],
					'turn_on_light' => $params['turn_on_light'],
					'alarm_sound' => $params['alarm_sound'],
					'ajust_sound' => $params['ajust_sound'],
					'action_call_emergency' => $params['action_call_emergency'],
					'vibration_pattern' => $params['vibration_pattern'],
					'company_id' => $params['company_id'] ? $params['company_id'] : 0,
					'url' => $params['url'] ? $params['url'] : "https://www.youtube.com/watch?v=WoGWPN3DWHk",
			  ],
			  
			];
			
			
			print_r($fields);
			
		    $headers = [
				'Authorization:key=' . $this->api_key,
				'Content-Type:application/json'
		    ];
			
			$ch = curl_init();
			
		    curl_setopt($ch, CURLOPT_URL, $this->path_to_firebase_cm);
		    curl_setopt($ch, CURLOPT_POST, true);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
		    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

		    $result = curl_exec($ch);

		    curl_close($ch);
		    
		    return $result;
		}
	}
		return false;
	}

	public function send($title = false, $body = false, $message = false,$image = 'https://www.quinielagratis.com/src/img/icon-white.png',$actions = false,$token = false,$click_action = "map_fragment")
	{
		if($token) $this->setToken($token);

		if($this->isAbleToSendPush($this->getToken()))
		{
			if($title && $body && $message)
			{
				if($token = $this->getToken())
				{
					$fields = [
				  	'to' => $token,
			     //  	'notification' => [
				    //     'title' => $title,
				    //     'body' => $body,
				    //     'image' => 'https://www.quinielagratisg.com/src/img/icon-white.png',
				    //     'icon' => 'notification_icon_map',
				    //     // 'vibrate'	=> "1",
								// 'sound'		=> "alarm",
			     //  	],
			      'data' => [
			        "title" => $title,
			        'message' => $message,
			        'body' => $body,
			        'icon' => 'notification_icon_map',
			        'image' => 'https://www.quinielagratisg.com/src/img/icon-white.png',
			        'image_url' => 'https://www.quinielagratisg.com/src/img/icon-white.png',
			        'sound' => "alarm",
			        'click_action' => $params['click_action'],
			        'turn_on_light' => $params['turn_on_light'],
			        'alarm_sound' => $params['alarm_sound'],
			        'ajust_sound' => $params['ajust_sound'],
			        'action_call_emergency' => $params['action_call_emergency'],
			        'vibration_pattern' => $params['vibration_pattern'],
			        'company_id' => 0,
			        'click_action' => $click_action
			      ],
		    	];

		    	if($actions)
		    		$fields['data']['actions'] = $actions;

			    $headers = [
			      'Authorization:key=' . $this->api_key,
			      'Content-Type:application/json'
			    ];

					$ch = curl_init();

			    curl_setopt($ch, CURLOPT_URL, $this->path_to_firebase_cm);
			    curl_setopt($ch, CURLOPT_POST, true);
			    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
			    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

			    $result = curl_exec($ch);

			    curl_close($ch);
			    
			    return $result;
				}
			}
		}
		return false;
	}
}