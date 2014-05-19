<?php 

	require_once ("header.php");

	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");

	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);

	$db = new db_connect();
	$mobapp_data = $db->execute_query( "SELECT m_app_id from ".PAGE." where page_id=".$_SESSION['pageid'] );
	$mobapp_id = $mobapp_data[0]['m_app_id'];

	

 ?>
<html>

	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-responsive.min.css">
	</head>

	<body>
		<div id="paymentDetails">

			<div id="formCover">
				<h4 class="normalFont center">Enter Credit Card Information Below to Sign up :</h4>
				<form action='https://www.2checkout.com/checkout/purchase' method='post' class="form payment">

				    <input type='hidden' name='sid' value='1591665' />
				    <input type='hidden' name='product_id' value='35'>
				    <input type='hidden' name='mode' value='2CO' />
				    <input type='hidden' name='demo' value='Y' />
				    <!-- <input type="hidden" name="x_receipt_link_url" value="http://release.snaplion.com/payments/thankYou"> -->
				    <input type="hidden" name="x_receipt_link_url" value="http://www.snaplion.com/orders/processfbworder">
				    <input type="hidden" name="mobapp_id" value="<?php echo $mobapp_id; ?>">


                    <input type='hidden' name='li_0_type' value='product' />
                    <input type='hidden' name='li_0_name' value='SnapLion Mobile App' />
                    <input type='hidden' name='li_0_duration' value='Forever' />
                    <input type='hidden' name='li_0_tangible' value='N' />
                    <input type='hidden' name='purchase_step' value='payment-method' />
                 	<input type="hidden" label='li_0_startup_fee' name='li_0_startup_fee' value=-98 class="input-block-level required"     />
               	 	<input type="hidden" label='li_0_recurrence' name='li_0_recurrence' value="1 Month" class="input-block-level required"     />
                	<input type="hidden" label='li_0_price' name='li_0_price' value=99 class="input-block-level required"     />                

				    <div class="field span9">
				        <label for="card_holder_name">Name on Credit Card</label>
				        <input type="text" label='Billing Name' name='card_holder_name' placeholder='Enter Name *' class="input-block-level required" id="card_holder_name"/>      
				    </div>

				    <div class="field span9">
				        <label for="paymentAddress1">Billing Address</label>
				        <input type="text" name='street_address' id="paymentAddress1" placeholder='Enter Address1 *' class="input-block-level required"/>
				        <input type="text" name='street_address2' placeholder='Enter Address2' class="input-block-level"/>
				    </div>
				    
			        <div class="span3">
			            <label for="paymentCity">City</label>
			            <input type="text" name='city' id="paymentCity" placeholder='Enter City *' class="input-block-level required"/>
			        </div>

			        <div class="span3">
			            <label for="paymentState">State</label>
			            <input type="text" name='state' id="paymentState" placeholder='Enter State *' class="input-block-level required"/>
			        </div>

			        <div class="span3">
			            <label for="paymentCountry">Country</label>
			            <input type="text" name='country' id="paymentCountry" placeholder='Enter Country *' class="input-block-level required"/>
			        </div>
			   
			        <div class="span4">
			            <label for="paymentEmail">Email</label>                                
			            <input type="text" name='email' id="paymentEmail" placeholder='Email *' class="input-block-level required" id="card_holder_email"/>
			        </div>

			        <div class="offset1 span4">
			            <label for="paymentZip">ZIP</label>                              
			            <input type="text" name='zip' id="paymentZip" placeholder='Enter ZIP' class="input-block-level"/>
			        </div>

			        <div class="span4">
			            <label for="paymentPhone">Phone</label>                                
			            <input type="text" id="paymentPhone" name='phone' placeholder='Phone' class="input-block-level"/>
			        </div>

			        <div class="offset1 span4">
			            <label for="paymentPhoneE">Phone EXT</label>                                
			            <input type="text" name='phone_extension' id="paymentPhoneE" placeholder='Phone Ext' class="input-block-level"/>
			        </div>

				    <div class="field span9 center">
				        <input name='submit' type='submit' value='Sign Up' class="orangebut btn submit track" data-objectname="Pay" data-location="Custom Payment Page" />
				    </div>

				</form>
			</div> <!-- formCover ends -->

		</div> <!-- paymentDetails ends -->
		
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
		<script src="js/script.js"></script>
		<!--<script src="https://www.2checkout.com/static/checkout/javascript/direct.min.js"></script>-->

		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>

	</body>

</html>