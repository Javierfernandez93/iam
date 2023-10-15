<?php

namespace JFStudio;

class CoinPayments {
    const PUBLIC_KEY = '83ae7f96878ec233fc10321739f6d716379a6de7bce4be3a4c1ce7415e58174a';
    const PRIVATE_KEY = '0a3B0095cf7505Df22129C901DcE61effdF6BE3C4018F2A3434B28959206976c';
    const MERCHANT_ID = 'd8c1f403c780e4c0acb5e9e013009cf8';
    const IPN_SECRET = 'cb5e9e013009cf8@ZUUMMYIPNSECRET@cb5e9e013009cf8';

    //* status * */
    const COMPLETE = 100;
    const INCOMPLETE = 0;
    const EXPIRED = -1;
}