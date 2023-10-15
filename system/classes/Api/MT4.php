<?php

namespace Api;

use JFStudio\Curl;

class MT4 
{
	const API_KEY = 'eyJhbGciOiJSUzUxMiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI5NzBiYTljZDFlYzhlMjUwNmFhZTM5Y2I1ZDNjNjIyNSIsInBlcm1pc3Npb25zIjpbXSwidG9rZW5JZCI6IjIwMjEwMjEzIiwiaW1wZXJzb25hdGVkIjpmYWxzZSwicmVhbFVzZXJJZCI6Ijk3MGJhOWNkMWVjOGUyNTA2YWFlMzljYjVkM2M2MjI1IiwiaWF0IjoxNjg4NjAzNTIyfQ.QdpD19lO3DkHJWPqEdTxddovaZcyJgHtSuxECvUygT-xAQ7O76uRVrzYCfn9--VbZQeTAqPgkazP8j2Xhlk-NR5S0FQliOcWSqdw-RystKBhwRmsxb8KnKNwtie71mERE2VLV9OJWdhPwIftaMexUJWGFXO7vPU4TaNO16XLZuK8yieiEJElRbDmTe4AKHqvVF5zwbgDWc3ucJTSaoLrYiQe9fk6L81le1KvcxBSawfsh6e5bEJoPZkBWUBEJcifXUIXgYCKAle_iLmo4mXhWDyOpePIzxIOwBuWdHK8MYnut_hKe6D9Xb56yVUa22Y-z0tCoLr0L43W0M3F-AJhCyVzHmsybBbkHS4KtVrPL48FDhHArT0c7hXt5yLfyuRfFmd4QOIzaxHqp7WpwX-auIZchTtz_FqzszoRvOlJTFC4OkfrNIqobKqD0IFjMo57aA6UIkpD5fbAn2t6BJmhXRUcitTu1f1Mxerd42Yt5bQxidXHgItAr7UFrP56m5Gp_i8cz13TMlKxjJoqMphGTJvHldsy3I4RthjGs7IM6q-ETLVP-z9iR49yUdTE-kd_cO9DBbInEierTygLEnjZIIpNN9ZH8GK5HTzjZ37Twl3mXXUQ5KCFV-Yt-In2rubcLEK0vB0cfY4LgqL8nTJ7Pb009jHHi5ZncTXvwx-1NdU';	
	const URL = 'http://54.85.133.59:3000/';	
	// const URL = 'http://localhost:3000/';	

    const URLS = [
        'getMarketPrice' => 'market/price',
        'createDemoAccount' => 'account/demo/create',
        'getTrackingData' => 'account/risk/tracking',
        'closeOrders' => 'account/order/closeAll',
        'closeOrdersWithLoss' => 'account/order/closeAllWithLoss',
        'closeOrdersWithBenefit' => 'account/order/closeAllWithBenefit',
        'closeOrder' => 'account/order/close',
        'getLastOrder' => 'account/order/last',
        'createEquityListener' => 'account/risk/equity',
        'createRiskFactor' => 'account/risk/create',
        'addSuscriber' => 'account/copy/addSuscriber',
        'createAccount' => 'account/create',
        'getAccount' => 'account/get',
        'createMarketOrder' => 'account/order/market',
        'createOrderOco' => 'account/order/oco',
        'getAllPositions' => 'account/position/all/',
        'getAllOrders' => 'account/order/all',
        'getOrder' => 'account/order/get',
        'calculateMargin' => 'account/margin/calculate',
        'getOrdersHistory' => 'account/orders/history',
        'getPositions' => 'account/positions/all',
    ];

    public static function getURL(string $urlName = null) : string
    {
        return self::URL.self::URLS[$urlName];
    }

    public static function calculateDrawDown(float $balance = null,float $equity = null)
    {
        $amount = $equity-$balance;

        if($amount < 0)
        {
            $amount = abs($amount);
            
            return $amount/$equity * 100;
        }

        return 0;
    }

    /* params */
    /* name => riskname*/
    /* id => accountid */
    /* absoluteDrawdownThreshold => int */
    /* period => day */
    public static function createRiskFactor(array $data = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('createRiskFactor'),$data);

        return $Curl->getResponse(true);
	}
    
    public static function getMarketPrice(array $data = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('getMarketPrice'),$data);

        return $Curl->getResponse(true);
	}
    
    public static function getTrackingData(array $data = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('getTrackingData'),$data);

        return $Curl->getResponse(true);
	}
   
    public static function getLastOrder(string $id = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('getLastOrder'),['id'=>$id]);

        return $Curl->getResponse(true);
	}
    
    public static function closeOrders(string $id = null)
    {
		$Curl = new Curl;

        $Curl->get(self::getURL('closeOrders'),['id'=>$id]);

        return $Curl->getResponse(true);
    }
    
    public static function closeOrdersWithBenefit(string $id = null)
    {
		$Curl = new Curl;

        $Curl->get(self::getURL('closeOrdersWithBenefit'),['id'=>$id]);

        return $Curl->getResponse(true);
    }

    public static function closeOrdersWithLoss(string $id = null)
    {
		$Curl = new Curl;

        $Curl->get(self::getURL('closeOrdersWithLoss'),['id'=>$id]);

        return $Curl->getResponse(true);
    }

    public static function closeOrder(array $data = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('closeOrder'),$data);

        return $Curl->getResponse(true);
	}
    
    public static function createEquityListener(array $data = null)
	{
		$Curl = new Curl;
        $Curl->get(self::getURL('createEquityListener'),$data);

        return $Curl->getResponse(true);
	}

    public static function createDemoAccount(array $data = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('createDemoAccount'),$data);

        return $Curl->getResponse(true);
	}

    public static function addSuscriber(array $data = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('addSuscriber'),$data);

        return $Curl->getResponse(true);
	}

    public static function createAccount(array $data = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('createAccount'),$data);

        return $Curl->getResponse(true);
	}
    
    public static function getAccount(string $id = null) 
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('getAccount'), ['id'=>$id]);

        return $Curl->getResponse(true);
	}

    public static function createMarketOrder(array $data = null) 
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('createMarketOrder'),$data);

        return $Curl->getResponse(true);
	}
    
    public static function createOrderOco(array $data = null) 
	{
		$Curl = new Curl;
        $Curl->get(self::getURL('createOrderOco'),$data);

        return $Curl->getResponse(true);
	}

	public static function getAllPositions() 
	{
		$Curl = new Curl;

        $response = $Curl->get(self::getURL('getAllPositions'));

        // if($response->getStatusCode() == 200) 
        // {
        //     return json_decode($response->getBody()->getContents(),true);
        // }
	}

    public static function getAllOrders() 
	{
		$Curl = new Curl;

        $response = $Curl->get(self::getURL('getAllOrders'));

        // if($response->getStatusCode() == 200) 
        // {
        //     return json_decode($response->getBody()->getContents(),true);
        // }
	}

    public static function getOrder() 
	{
		$Curl = new Curl;

        $response = $Curl->get(self::getURL('getOrder'));

        // if($response->getStatusCode() == 200) 
        // {
        //     return json_decode($response->getBody()->getContents(),true);
        // }
	}

    public static function isValidBroker(string $broker = null,string $platform = null): bool {
        $brokers = self::getAllBrokers();
        
        if($brokersList = $brokers[$platform])
        {
            return in_array($broker,$brokersList);
        }
        
        return false;
    }

    public static function getAllBrokers(): array {
        return json_decode(file_get_contents('../../src/files/brokers/brokers.json'), true);
    }

    public static function getOrdersHistory(string $id = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('getOrdersHistory'),['id'=>$id]);

        return $Curl->getResponse(true);
	}

    public static function getPositions(string $id = null)
	{
		$Curl = new Curl;

        $Curl->get(self::getURL('getPositions'),['id'=>$id]);

        return $Curl->getResponse(true);
	}
}