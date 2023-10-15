# CapitalPayments
This Api has been made Crypto Payments based on USDT.TRC20
All examples are available into examples/ folder.

# Install with composer 
> composer require capitalpayments/sdk:dev-main

1. Create an account [Create account](capitalpayments.me/apps/signup "Create account")
2. Create api key [here](https://www.capitalpayments.co/apps/api/ "here")
3. Follow next steps to connect your account

(NOTE: Sandbox mode needs test coins request [here](https://www.capitalpayments.co/apps/api/ "here"))

# Login 

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

$response = $Sdk->login();

```

# Get environment 

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the environment : response >= int $sandobox (0 or 1)
$response = $Sdk->getEnvironment();

```

# Get account

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the account data
$response = $Sdk->getAccount();

```

# Get balance

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the balance from the api
$response = $Sdk->getBalance();

```

# Get main wallet

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get main wallet data (private key is included)
$response = $Sdk->getMainWallet();

```

# Get wallets

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# retrives all wallets attached to api 
$response = $Sdk->getWallets();

```

# Get invoice status

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the invoice status
$response = $Sdk->getInvoiceStatus([
    'invoice_id' => 'invoice_id' # string 
]);

```

# Cancel invoice

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the invoice status
$response = $Sdk->cancelInvoice([
    'invoice_id' => 'invoice_id' # string 
]);

```

# Create payout

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the invoice status
$response = $Sdk->createPayout([
    'payout_id' => 'payout_id' # string 
    'amount' => 'amount' # float|int 
]);

```

# Get payout status

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# get the payout status
$response = $Sdk->getPayoutStatus([
    'payout_id' => 'payout_id' # string 
]);

```

# Cancel payout 

```
<?php 

include __DIR__ . "/../vendor/autoload.php";

$Sdk = new CapitalPayments\Sdk\Sdk('api_key','api_secret');

# cancel payout  
$response = $Sdk->cancelPayout([
    'payout_id' => 'PayoutId', # @string
]);

```