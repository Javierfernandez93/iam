<?php

use GPBMetadata\Google\Type\Money;
use DummieTrading\Package;

 define("TO_ROOT", "../../");

require_once TO_ROOT. "/system/core.php";

$data = HCStudio\Util::getVarFromPGS();

$UserSupport = new DummieTrading\UserSupport;

if(($data['PHP_AUTH_USER'] == HCStudio\Util::USERNAME && $data['PHP_AUTH_PW'] == HCStudio\Util::PASSWORD) || $UserSupport->logged === true)
{
    $BuyPerUser = new DummieTrading\BuyPerUser;
    
    $activations = $BuyPerUser->getPackageBuys(1); // activations
    $suscriptions = $BuyPerUser->getPackageBuys(5); // monthly subscriptions
    $buys = array_merge($activations, $suscriptions);
    
    if($buys)
    {
        $UserReferral = new DummieTrading\UserReferral;
        $ServicePerClient = new DummieTrading\ServicePerClient;
        
        foreach($buys as $buy)
        {
            echo "ID {$buy['user_login_id']} ";

            if($referrals = $UserReferral->getReferralsIds($buy['user_login_id']))
            {
                echo " con Red <br>";

                foreach($referrals as $user_login_id)
                {
                    echo " - invitado {$user_login_id} ";

                    if($services = $ServicePerClient->getAllServicesSold($user_login_id))
                    {
                        echo " con servicios";

                        foreach($services as $service)
                        {
                            DummieTrading\CommissionPerUser::addCreditCommission([
                                'user_login_id' => $buy['user_login_id'],
                                'buy_per_user_id' => $buy['buy_per_user_id'],
                                'catalog_commission_type_id' => DummieTrading\CatalogCommissionType::NETWORK_TYPE_ID,
                                'service_per_client_id' => $service['service_per_client_id'],
                                'user_login_id_from' => $user_login_id,
                                'amount' => 0.5,
                                'catalog_currency_id' => DummieTrading\CatalogCurrency::USD,
                                'package_id' => 0,
                            ]);
                        }
                    } else {
                        echo " sin servicios";
                    }
                    echo " <br>";
                }

            } else {
                echo " Sin Red";
            }
            
            echo " <br>";
        }
    }
} else {
    $data['s'] = 0;
    $data['r'] = "INVALID_CREDENTIALS";
}

echo json_encode(HCStudio\Util::compressDataForPhone($data)); 