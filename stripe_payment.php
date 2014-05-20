<?php
	include_once 'Stripe.php';
	Stripe::setApiKey('sk_test_5v6RJiqdRaGXxb6LL6PS62Hx');

	try {
		$customer = Stripe_Customer::create(array(
			  	"card" => $_POST['id'],
			  	"plan" => "FBW",
			  	"email" => $_POST['email']
		  	)
		);

		echo "<pre>";
		print_r($customer);
	} catch (Exception $e) {
		echo "<pre>";
		print_r($e->getMessage());
	}
?>