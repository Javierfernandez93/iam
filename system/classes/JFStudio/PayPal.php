<?php

namespace JFStudio;

class PayPal {
    // const CLIENT_ID = "AfR2RRNUmTU82_mkmKJvUgCnYYrILn-p3NKjlHAOnLpIHXiNVR0Ag7vpouPxiLuPwG8O8ad65BCJF1oo"; 
    // const CLIENT_SECRET = "ENgAx4PgpYXzBgbiPvL74RaOL3LuHfjpqdwq1Rk3z2xn9sFkxiswVig4IqZfiJjx5XHrFJExMHFYaoDD";

    /* states */
    const APPROVED = 'approved';
    const FAILED = "failed";
    
    const CLIENT_ID = "AeUcNJJBK5T2E9ZmeYzg1iNpeC0tS0gG4GMJyewRvP2vcq0Q_aBOK3TFjaSimg9MbNrie_ZGhe1n3LKW"; 
    const CLIENT_SECRET = "ENSv9ZfStha6x8R5_j3xqEBI4odOUvKxlK_FbIK6M-zDBzTMIB3IQ9ppTR8GsUuOd7yEYLZqKCh6uMGS";

    const RETURN_URL = "https://www.iam.com.mx/apps/paypal/";
    const CANCEL_URL = "https://www.iam.com.mx/apps/backoffice";
    const URL = "https://www.iam.com.mx/apps/admin/subcore/application/validate_buy.php";
    
    const MODE = 'live'; // 'live', 'sandbox'
    
    const TEMPORAL_VALIDATION = true;

    private static $instance;
    
	public static function getInstance()
 	{
    	if(!self::$instance instanceof self)
      		self::$instance = new self;

    	return self::$instance;
 	}
}