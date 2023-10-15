<?php 

namespace CapitalPayments\Sdk;

use CapitalPayments\Sdk\UrlManager;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Exception;

class Sdk {
    private $api_key = 'SET_YOUR_API_KEY';
    private $api_secret = 'SET_YOUR_API_SECRET';
    
    public function __construct(string $api_key = null,string $api_secret = null)
    {
        if(isset($api_key) === true)
        {
            if(isset($api_secret) === true)
            {
                $this->api_key = $api_key;
                $this->api_secret = $api_secret;
            } else {
                throw new Exception('Invalid API Secret');
            }
        } else {
            throw new Exception('Invalid API Key');
        }
    }

    public function login() : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->get(UrlManager::LOGIN);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }

    public function createInvoice(array $data = null) : array
    {
        if(isset($data['amount'])) 
        {
            if(is_numeric($data['amount'])) 
            {
                $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

                $response = $Client->post(UrlManager::CREATE_INVOICE, [
                    RequestOptions::JSON => $data
                ]);

                if($response->getStatusCode() == 200) 
                {
                    return json_decode($response->getBody()->getContents(),true);
                }

                return [];
            } else {
                throw new Exception('Invalid amount format');
            }
        } else {
            throw new Exception('Invalid amount');
        }
    }
    
    public function createInvoices(array $data = null) : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->post(UrlManager::CREATE_INVOICES, [
            RequestOptions::JSON => ['invoices' => $data]
        ]);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }
    
    public function getBalance(array $data = null) : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->post(UrlManager::GET_BALANCE, [
            RequestOptions::JSON => $data
        ]);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }

    public function getInvoiceStatus(array $data = null) : array
    {
        if(isset($data['invoice_id'])) 
        {
            $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

            $response = $Client->post(UrlManager::GET_INVOICE_STATUS, [
                RequestOptions::JSON => $data
            ]);

            if($response->getStatusCode() == 200) 
            {
                return json_decode($response->getBody()->getContents(),true);
            }

            return [];
        } else {
            throw new Exception('Not invoice id');
        }
    }
    
    public function cancelInvoice(array $data = null) : array
    {
        if(isset($data['invoice_id'])) 
        {
            $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

            $response = $Client->post(UrlManager::CANCEL_INVOICE, [
                RequestOptions::JSON => $data
            ]);

            if($response->getStatusCode() == 200) 
            {
                return json_decode($response->getBody()->getContents(),true);
            }

            return [];
        } else {
            throw new Exception('Not invoice id');
        }
    }
    
    public function getAccount() : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->get(UrlManager::GET_ACCOUNT);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }
    
    public function getMainWallet() : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->get(UrlManager::GET_MAIN_WALLET);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }
    
    public function getWallets() : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->get(UrlManager::GET_WALLETS);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }

    public function getEnvironment() : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->get(UrlManager::GET_ENVIRONMENT);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }

    public function createPayout(array $data = null) : array
    {
        if(isset($data['amount'])) 
        {
            if(is_numeric($data['amount'])) 
            {
                if(isset($data['address']))
                {
                    $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);
    
                    $response = $Client->post(UrlManager::CREATE_PAYOUT, [
                        RequestOptions::JSON => $data
                    ]);
    
                    if($response->getStatusCode() == 200) 
                    {
                        return json_decode($response->getBody()->getContents(),true);
                    }
    
                    return [];
                } else {
                    throw new Exception('Invalid address');
                }
            } else {
                throw new Exception('Invalid amount format');
            }
        } else {
            throw new Exception('Invalid amount');
        }
    }
    
    public function createPayouts(array $data = null) : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->post(UrlManager::CREATE_PAYOUTS, [
            RequestOptions::JSON => ['payouts' => $data]
        ]);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }
    
    public function createCustomer(array $data = null) : array
    {
        if(isset($data['name'])) 
        {
            if(isset($data['email'])) 
            {
                if(isset($data['whatsapp']))
                {
                    $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);
    
                    $response = $Client->post(UrlManager::CREATE_CUSTOMER, [
                        RequestOptions::JSON => $data
                    ]);
    
                    d($response->getBody()->getContents());

                    if($response->getStatusCode() == 200) 
                    {
                        return json_decode($response->getBody()->getContents(),true);
                    }
    
                    return [];
                } else {
                    throw new Exception('Invalid whatsapp');
                }
            } else {
                throw new Exception('Invalid email');
            }
        } else {
            throw new Exception('Invalid name');
        }
    }
    
    public function createItem(array $data = null) : array
    {
        if(isset($data['title'])) 
        {
            if(isset($data['description'])) 
            {
                if(isset($data['price']))
                {
                    $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);
    
                    $response = $Client->post(UrlManager::CREATE_ITEM, [
                        RequestOptions::JSON => $data
                    ]);
    
                    if($response->getStatusCode() == 200) 
                    {
                        return json_decode($response->getBody()->getContents(),true);
                    }
    
                    return [];
                } else {
                    throw new Exception('Invalid price');
                }
            } else {
                throw new Exception('Invalid description');
            }
        } else {
            throw new Exception('Invalid title');
        }
    }

    public function getPayoutStatus(array $data = null) : array
    {
        if(isset($data['payout_id'])) 
        {
            $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

            $response = $Client->post(UrlManager::GET_PAYOUT_STATUS, [
                RequestOptions::JSON => $data
            ]);

            if($response->getStatusCode() == 200) 
            {
                return json_decode($response->getBody()->getContents(),true);
            }

            return [];
        } else {
            throw new Exception('Not payout id');
        }
    }

    public function getCustomer(array $data = null) : array
    {
        if(isset($data['customer_id'])) 
        {
            $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

            $response = $Client->post(UrlManager::GET_CUSTOMER, [
                RequestOptions::JSON => $data
            ]);

            if($response->getStatusCode() == 200) 
            {
                return json_decode($response->getBody()->getContents(),true);
            }

            return [];
        } else {
            throw new Exception('Not customer id');
        }
    }

    public function getCustomers(array $data = null) : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->post(UrlManager::GET_CUSTOMERS, [
            RequestOptions::JSON => $data
        ]);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }

    public function getItem(array $data = null) : array
    {
        if(isset($data['item_id'])) 
        {
            $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);
            
            $response = $Client->post(UrlManager::GET_ITEM, [
                RequestOptions::JSON => $data
            ]);
            
            if($response->getStatusCode() == 200) 
            {
                return json_decode($response->getBody()->getContents(),true);
            }

            return [];
        } else {
            throw new Exception('Not item id');
        }
    }

    public function getItems(array $data = null) : array
    {
        $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

        $response = $Client->post(UrlManager::GET_ITEMS, [
            RequestOptions::JSON => $data
        ]);

        if($response->getStatusCode() == 200) 
        {
            return json_decode($response->getBody()->getContents(),true);
        }

        return [];
    }

    public function cancelPayout(array $data = null) : array
    {
        if(isset($data['payout_id'])) 
        {
            $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

            $response = $Client->post(UrlManager::CANCEL_PAYOUT, [
                RequestOptions::JSON => $data
            ]);

            if($response->getStatusCode() == 200) 
            {
                return json_decode($response->getBody()->getContents(),true);
            }

            return [];
        } else {
            throw new Exception('Not payout id');
        }
    }
   
    public function deleteItem(array $data = null) : array
    {
        if(isset($data['item_id'])) 
        {
            $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

            $response = $Client->post(UrlManager::DELETE_ITEM, [
                RequestOptions::JSON => $data
            ]);
            
            if($response->getStatusCode() == 200) 
            {
                return json_decode($response->getBody()->getContents(),true);
            }

            return [];
        } else {
            throw new Exception('Not item id');
        }
    }
    
    public function deleteCustomer(array $data = null) : array
    {
        if(isset($data['customer_id'])) 
        {
            $Client = new Client(['verify' => true, 'auth' => [$this->api_key, $this->api_secret]]);

            $response = $Client->post(UrlManager::DELETE_CUSTOMER, [
                RequestOptions::JSON => $data
            ]);

            if($response->getStatusCode() == 200) 
            {
                return json_decode($response->getBody()->getContents(),true);
            }

            return [];
        } else {
            throw new Exception('Not customer id');
        }
    }
}