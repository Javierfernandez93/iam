<?php 

require_once('init.php');


\Stripe\Stripe::setApiKey('sk_test_dL2vzQGh2cPGxDOQo6nBs85z');

$intent = \Stripe\PaymentIntent::create([
    'amount' => 1099,
    'currency' => 'usd',
]);

// echo "<pre>";
// print_r($intent);die;


?>
<head>
  <title>Checkout</title>
  <script src="http://localhost:8888/sumateahora/src/js/jquery-3.1.1.js" type="text/javascript"></script>
  <script src="http://js.stripe.com/v3/"></script>
  <script type="text/javascript">
  	$(document).ready(function(){


		// Set your publishable key: remember to change this to your live secret key in production
		// See your keys here: https://dashboard.stripe.com/account/apikeys
		var stripe = Stripe('pk_test_LERB57zSWRQ37sOehm4ADL14');
		var clientSecret = "<?php echo $intent->client_secret; ?>";
		var elements = stripe.elements();
		// Set up Stripe.js and Elements to use in checkout form
		var style = {
		  base: {
		    color: "#32325d",
		  }
		};

		var card = elements.create("card", { style: style });
		card.mount("#card-element");
		card.addEventListener('change', function(event) {
		  var displayError = document.getElementById('card-errors');
		  if (event.error) {
		    displayError.textContent = event.error.message;
		  } else {
		    displayError.textContent = '';
		  }
		});

		var submitButton = document.getElementById('submit');

		submitButton.addEventListener('click', function(ev) {
			$("#submit").attr("disabled",true);
		  stripe.confirmCardPayment(clientSecret, {
		    payment_method: {card: card}
		  }).then(function(result) {
		    console.log(result);
		    if (result.error) {
		      // Show error to your customer (e.g., insufficient funds)
		      console.log(result.error.message);
		    } else {
		      // The payment has been processed!
		      if (result.paymentIntent.status === 'succeeded') {
		        // Show a success message to your customer
		        // There's a risk of the customer closing the window before callback
		        // execution. Set up a webhook or plugin to listen for the
		        // payment_intent.succeeded event that handles any business critical
		        // post-payment actions.
		      }
		    }
		  });
		});
	});
  </script>
</head>

<div style="width: 300px;display: inline-block;">
	<div id="card-element">
	  <!-- Elements will create input elements here -->
	</div>

	<!-- We'll put the error messages in this element -->
	<div id="card-errors" role="alert"></div>

	<button id="submit">Pagar</button>
</div>