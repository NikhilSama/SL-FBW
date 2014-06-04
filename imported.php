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
	
	$payment_data = $db->execute_query("SELECT payment_flag, ingredients_flag from ".PAGE." where page_id=".$_SESSION['pageid']);
	$payment_flag = $payment_data[0]['payment_flag'];
	$ingredients_flag = $payment_data[0]['ingredients_flag'];

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
		<div class="loader-bg-main" id="loadingCircle">
			<div class="loader-bg"><img src="img/loader.GIF" width="40"></div>
		</div>
		<div class="fb_maincontainer">
			<iframe src="<?php echo 'https://fbwsimulator.snaplion.com/#/?app_id='.$mobapp_id; ?>" 
				frameborder="0" style="height: 390px; width: 220px;margin-top: 65px;padding-right: 27px;float: right;">
			</iframe>

			<div class="fb_left">
				<div class="snap_odr"><?php echo $pageInfo['name']; ?></div>
				<div class="fb_left-inner">
					<!-- About Section -->
					<?php 
						if(in_array('About',$imports,true)) {
					?>
						<div class="strip-12">
							<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
							<div class="strip-blue">
								<span class="blue-text">About Us</span>
								<span class="strip-blue-text">About, Location, Mission</span>
							</div>
							<div class="strip-white">
								<input type="checkbox" class="css-checkbox autoUpdate" id="checkbox1" name="pageinfo" 
								<?php echo ($update_data['About'] == 'true' ) ? 'checked' : ''; ?> data-id=<?php echo $page_id; ?> data-name="About"/>
								<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
							</div>
						</div>
					<?php
						} else {
					?>
						<div class="strip-12 import-radio">
							<div class="strip-green gradient-white fontcolor checkalign">
								<input type="checkbox" class="css-checkbox importSection" id="checkbox1" name="pageinfo" />
								<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check"></label>
							</div>
							<div class="strip-blue gradient-white">
								<span class="blue-text fontcolor">About Us</span>
								<span class="strip-blue-text fontcolor">About, Location, Mission</span>
							</div>
							<div class="strip-white dalign">
								<a href="#" class="btn-orange btn-small laterImport">IMPORT</a>
							</div>
						</div>
					<?php
						}
					?>
					
					<!-- Post Section -->
					<?php 
						if(in_array('Fan Wall', $imports, true)) {
					?>
						<div class="strip-12">
							<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
							<div class="strip-blue">
								<span class="blue-text">Post</span>
								<span class="strip-blue-text">Total Post - <?php echo $postCount; ?></span>
							</div>
							<div class="strip-white">
								<input type="checkbox" class="css-checkbox autoUpdate" id="checkbox2" name="posts"
								<?php echo ($update_data['Fan Wall'] == 'true' ) ? 'checked' : ''; ?> data-id=<?php echo $page_id; ?> data-name="Fan Wall" />
								<label for="checkbox2" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
							</div>
						</div>
					<?php
						} else {
					?>
						<div class="strip-12 import-radio">
							<div class="strip-green gradient-white fontcolor checkalign">
								<input type="checkbox" class="css-checkbox importSection" id="checkbox2" name="posts" />
								<label for="checkbox2" name="checkbox1_lbl" class="css-label lite-green-check"></label>
							</div>
							<div class="strip-blue gradient-white">
								<span class="blue-text fontcolor">Post</span>
								<span class="strip-blue-text fontcolor">Total Post - <?php echo $item_count['Fan Wall']; ?></span>
							</div>
							<div class="strip-white dalign">
								<a href="#" class="btn-orange btn-small laterImport">IMPORT</a>
							</div>
						</div>
					<?php
						}
					?>

					<!-- Photos Section -->
					<?php 
						if(in_array('Photos', $imports, true)) {
					?>
						<div class="strip-12">
							<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
							<div class="strip-blue">
								<span class="blue-text">Photos</span>
								<span class="strip-blue-text"><?php echo $item_count['Photos']; ?> Albums, <?php echo $subitem_count['Photos']; ?> Photos</span>
							</div>
							<div class="strip-white">
								<input type="checkbox" class="css-checkbox autoUpdate" id="checkbox3" name="photos"
								<?php echo ($update_data['Photos'] == 'true' ) ? 'checked' : ''; ?> data-id=<?php echo $page_id; ?> data-name="Photos" />
								<label for="checkbox3" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
							</div>
						</div>
					<?php
						} else {
					?>
						<div class="strip-12 import-radio">
							<div class="strip-green gradient-white fontcolor checkalign">
								<input type="checkbox" class="css-checkbox importSection" id="checkbox3" name="photos" />
								<label for="checkbox3" name="checkbox1_lbl" class="css-label lite-green-check"></label>
							</div>
							<div class="strip-blue gradient-white">
								<span class="blue-text fontcolor">Photos</span>
								<span class="strip-blue-text fontcolor"><?php echo $albumCount; ?> Albums, <?php echo $photoCount; ?> Photos</span>
							</div>
							<div class="strip-white dalign">
								<a href="#" class="btn-orange btn-small laterImport">IMPORT</a>
							</div>
						</div>
					<?php
						}
					?>

					<!-- Events Section -->
					<?php 
						if(in_array('Events', $imports, true)) {
					?>
						<div class="strip-12">
							<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
							<div class="strip-blue">
								<span class="blue-text">Events</span>
								<span class="strip-blue-text">Total Events - <?php echo $item_count['Events']; ?></span>
							</div>
							<div class="strip-white">
								<input type="checkbox" class="css-checkbox autoUpdate" id="checkbox4" name="events"
								<?php echo ($update_data['Events'] == 'true' ) ? 'checked' : ''; ?> data-id=<?php echo $page_id; ?> data-name="Events" />
								<label for="checkbox4" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
							</div>
						</div>
					<?php
						} else {
					?>
						<div class="strip-12 import-radio">
							<div class="strip-green gradient-white fontcolor checkalign">
								<input type="checkbox" class="css-checkbox importSection" id="checkbox4" name="events" />
								<label for="checkbox4" name="checkbox1_lbl" class="css-label lite-green-check"></label>
							</div>
							<div class="strip-blue gradient-white">
								<span class="blue-text fontcolor">Events</span>
								<span class="strip-blue-text fontcolor">Total Events - <?php echo $eventCount; ?></span>
							</div>
							<div class="strip-white dalign">
								<a href="#" class="btn-orange btn-small laterImport">IMPORT</a>
							</div>
						</div>
					<?php
						}
					?>

					<!-- Events Section -->
					<?php 
						if(in_array('Videos', $imports, true)) {
					?>
						<div class="strip-12">
							<div class="strip-green"><img src="img/tick.png"><br>Imported</div>
							<div class="strip-blue">
								<span class="blue-text">Videos</span>
								<span class="strip-blue-text">Total Videos - <?php echo $item_count['Videos']; ?></span>
							</div>
							<div class="strip-white">
								<input type="checkbox" class="css-checkbox autoUpdate" id="checkbox5" name="videos"
								<?php echo ( $update_data['Videos'] == 'true' ) ? 'checked' : ''; ?> data-id=<?php echo $page_id; ?> data-name="Videos" />
								<label for="checkbox5" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
							</div>
						</div>
					<?php
						} else {
					?>
						<div class="strip-12 import-radio">
							<div class="strip-green gradient-white fontcolor checkalign">
								<input type="checkbox" class="css-checkbox importSection" id="checkbox5" name="videos" />
								<label for="checkbox5" name="checkbox1_lbl" class="css-label lite-green-check"></label>
							</div>
							<div class="strip-blue gradient-white">
								<span class="blue-text fontcolor">Videos</span>
								<span class="strip-blue-text fontcolor">Total Videos - <?php echo $videoCount; ?></span>
							</div>
							<div class="strip-white dalign">
								<a href="#" class="btn-orange btn-small laterImport">IMPORT</a>
							</div>
						</div>
					<?php
						}
					?>
				</div>

				<div class="button-group">
					<a href="ingredients.php" class="btn-orange">App Ingredients</a>
					<?php 
						if(! $payment_flag && $ingredients_flag) { 
					?>
							<script src="https://checkout.stripe.com/checkout.js"></script>
							<!-- <button id="customButton" src="img/makePayment.png"></button> -->
							<a href="#" id="customButton" class="btn-orange">Submit App</a>
							<!-- <a href="payment.php"><img src="img/makePayment.png" alt=""></a> -->

							<script>
							  	var handler = StripeCheckout.configure({
								    key: 'pk_test_idc5V67kywOPFOub6f733v6j',
								    image: 'img/snaplion_round_logo.jpg',
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
							      		name: 'SnapLion',
							      		description: 'Mobile App Wizard',
							      		amount: 2000
							    	});
							    	e.preventDefault();
							  	});
							</script>
					<?php 
						} elseif(!$payment_flag) {
					?>
							<a href="#" id="customButtonWithoutPayment" class="btn-orange">Submit App</a>
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

				$(document).on('click', '#customButtonWithoutPayment', function(event){
					event.preventDefault();

					alert('Your app is not ready to be submitted please fill up ingredients first.');
				});

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