<?php 
	require_once ("header.php");

	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");

	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);

	$pageList = $fbObject->api('me/accounts');

	$wantPermissions = "email,manage_pages";
	$permissions = $fbObject->isAuthorized($wantPermissions);
	if($permissions!="true") {
		$fbObject->login($permissions);
		die();
	}

	$page_id = $_SESSION['pageid'];

	$pageInfo = $fbObject->api('/' . $page_id);

	$db = new db_connect();
	$fbid = $fbObject->getFBID();
	//getting apptab ids to get what data has already been extracted
	$sql = "select apptab_name,mobapp_id, update_flag, item_count, subitem_count from ".APPTAB_ID." where page_id=".$page_id." and flag='true' ";
	$imported_data = $db->execute_query($sql);
	$mobapp_id = $imported_data[0]['mobapp_id'];
	
	$imports = array(); 
	$update_data = array();
	foreach ($imported_data as $data) {	
		//importing all items in the array
		$imports[] = $data['apptab_name'];

		//to get wheather items are set to auto upate or not and the count of the items that have been imported, eg count of videos, events etc
		$update_data[$data['apptab_name']] = $data['update_flag'];
		$item_count[ $data['apptab_name'] ] = $data['item_count'];
		$subitem_count[ $data['apptab_name'] ] = $data['subitem_count'];
	}
	
	$payment_data = $db->execute_query("SELECT payment_flag from ".PAGE." where page_id=".$_SESSION['pageid']);
	$payment_flag = $payment_data[0]['payment_flag'];

	$albums = $fbObject->api('/' . $page_id . '/albums?limit=500&&offset=0');
	$albumCount = count($albums['data']);
	$photoCount = 0;
	foreach ($albums['data'] as $album) {
		$photoCount += $album['count'];
	}

	$events = $fbObject->api('/' . $page_id . '/events?limit=500&&offset=0');
	$eventCount = count($events['data']);

	$posts = $fbObject->api('/' . $page_id . '/posts?limit=500&&offset=0');
	$postCount = count($posts['data']);

	$videos = $fbObject->api('/' . $page_id . '/videos?limit=500&&offset=0');
	$videoCount = count($videos['data']);
?>

<!DOCTYPE html>
<html>
	<head>
		<title>SnapLion Facebook Wizard</title>
		<link href="css/style.css" rel="stylesheet">
		<link href="css/checkbox.css" rel="stylesheet">
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
	</head>

	<body>
		<div class="fb_maincontainer">
			<iframe src="<?php echo 'https://fbwsimulator.snaplion.com/#/?app_id='.$mobapp_id; ?>" 
				frameborder="0" style="height: 390px; width: 220px;margin-top: 65px;padding-right: 27px;float: right;">
			</iframe>

			<div class="fb_left">
				<div class="snap_odr"><?php echo $pageInfo['name']; ?></div>
				<div class="fb_left-inner">
					<div class="strip-12">
						<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
						<div class="strip-blue">
							<span class="blue-text">About Us</span>
							<span class="strip-blue-text">About, Location, Mission</span>
						</div>
						<div class="strip-white">
							<input type="checkbox" class="css-checkbox" id="checkbox1"  checked=""/>
							<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
						</div>
					</div>

					<div class="strip-12">
						<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
						<div class="strip-blue">
							<span class="blue-text">Post</span>
							<span class="strip-blue-text">Total Post - <?php echo $postCount; ?></span>
						</div>
						<div class="strip-white">
							<input type="checkbox" class="css-checkbox" id="checkbox2"  checked=""/>
							<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
						</div>
					</div>

					<div class="strip-12">
						<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
						<div class="strip-blue">
							<span class="blue-text">Photos</span>
							<span class="strip-blue-text"><?php echo $albumCount; ?> Albums, <?php echo $photoCount; ?> Photos</span>
						</div>
						<div class="strip-white">
							<input type="checkbox" class="css-checkbox" id="checkbox3"  checked=""/>
							<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
						</div>
					</div>

					<div class="strip-12">
						<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
						<div class="strip-blue">
							<span class="blue-text">Events</span>
							<span class="strip-blue-text">Total Events - <?php echo $eventCount; ?></span>
						</div>
						<div class="strip-white">
							<input type="checkbox" class="css-checkbox" id="checkbox4"  checked=""/>
							<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
						</div>
					</div>

					<div class="strip-12">
						<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
						<div class="strip-blue">
							<span class="blue-text">Videos</span>
							<span class="strip-blue-text">Total Videos - <?php echo $videoCount; ?></span>
						</div>
						<div class="strip-white">
							<input type="checkbox" class="css-checkbox" id="checkbox5"  checked=""/>
							<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
						</div>
					</div>	
				</div>
				<div class="button-group">
					<a href="ingredients.php" class="btn-orange">App Ingredients</a>
					<?php 
						if(! $payment_flag ) { 
					?>
							<script src="https://checkout.stripe.com/checkout.js"></script>
							<!-- <button id="customButton" src="img/makePayment.png"></button> -->
							<a href="#" id="customButton" class="btn-orange">Make Payment</a>
							<!-- <a href="payment.php"><img src="img/makePayment.png" alt=""></a> -->

							<script>
							  	var handler = StripeCheckout.configure({
								    key: 'pk_test_idc5V67kywOPFOub6f733v6j',
								    image: '/square-image.png',
								    token: function(token, args) {
								    	console.log(token);
								    	console.log(args);

								    	$.ajax({
											url: 'stripe_payment.php',
											method:"POST",
											data: token,
											success: function(res) {
											    console.log(res);
											}
										});
								      	// Use the token to create the charge with a server-side script.
								      	// You can access the token ID with `token.id`
								    }
							  	});

							  	document.getElementById('customButton').addEventListener('click', function(e) {
								    // Open Checkout with further options
								    handler.open({
							      		name: 'SnapLion Site',
							      		description: 'FBW',
							      		amount: 2000
							    	});
							    	e.preventDefault();
							  	});
							</script>
					<?php 
						} 
					?>
				</div>
				<div><hr class="hr-gray"></div>
				<div class="button-box"><a href="#" class="btn-gray">Build Apps for More Pages?</a></div>
			</div>
		</div>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
		<script src="js/new_script.js"></script>
		<script>
		//checking if all the items have been imported and then
			$(document).ready(function(){
				var count = 0;
				$(".importItems").each(function(){
					if(  $(this).children(".checkboxes").hasClass("alreadyImported") ) {
						count++;
					}
				});
				if( count == $(".importItems").length ) {
					$("#submitList").css("display","none");
				}

				var uninstalledAppPage = $(".uninstalledAppPage");
				if( uninstalledAppPage.length > 3 ) {
					if($(".slider").length) {
						$(".slider").flexisel({visibleItems:3,clone:true});
						$(".nextStepImg").css("margin-left","-2%");
					}
				} else {
					uninstalledAppPage.css({"width":"25%","display":"inline-block","margin-right": "2%","float":"left"});
					$(".nextStepImg").css("margin-left","-14%");
				}
			});
		</script>

		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>
	</body>
</html>