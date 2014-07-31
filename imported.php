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

	$albums = $fbObject->api('/' . $page_id . '/albums?limit=250&offset=0');
	$albumCount = count($albums['data']);
	$photoCount = 0;
	foreach ($albums['data'] as $album) {
		$photoCount += $album['count'];
	}

	$events = $fbObject->api('/' . $page_id . '/events?limit=250&&offset=0');
	$eventCount = count($events['data']);

	$posts = $fbObject->api('/' . $page_id . '/posts?limit=250&&offset=0');
	$postCount = count($posts['data']);

	$videos = $fbObject->api('/' . $page_id . '/videos?limit=250&&offset=0');
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

	<body style="margin: 0 !important;">
		<div class="loader-bg-main" id="loadingCircle">
			<div class="loader-bg"><img src="img/loader.GIF" width="40"></div>
			<span style="position: absolute;color: #fff;top: 50%;left: 50%;margin-left: -138px;margin-top: 58px;font-family: sans-serif;font-size: 16px;">
				Please be patient, this may take a minute.
			</span>
		</div>
		<div class="fb_maincontainer">
			<iframe src="<?php echo 'https://fbwsimulator.snaplion.com/#/?app_id='.$mobapp_id; ?>" 
				frameborder="0" style="height: 390px; width: 220px;margin-top: 65px;padding-right: 29px;float: right;">
			</iframe>

			<div class="fb_left">
				<div class="snap_odr">
					<div class="longpagename"><?php echo $pageInfo['name']; ?></div>
				</div>
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
								<span class="blue-text">Posts</span>
								<span class="strip-blue-text">Total Posts - <?php echo $item_count['Fan Wall']; ?></span>
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
								<span class="strip-blue-text fontcolor">Total Post - <?php echo $postCount; ?></span>
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
					<a href="ingredients.php" class="btn-orange appIngredients">App Ingredients</a>
					<?php 
						if(! $payment_flag && $ingredients_flag) { 
					?>
							<script src="https://checkout.stripe.com/checkout.js"></script>
							<!-- <button id="customButton" src="img/makePayment.png"></button> -->
							<a href="#" id="customButton" class="btn-orange">Submit App</a>
							<!-- <a href="payment.php"><img src="img/makePayment.png" alt=""></a> -->

							<script>
							  	var handler = StripeCheckout.configure({
								    // key: 'pk_test_t18WSF7iWl1Ign6jilpRs3n3',
								    key: 'pk_live_d1NrtHiXyRWMfJvvS9fxzxON',
								    image: 'img/snaplion_round_logo.jpg',
								    token: function(token, args) {
								    	console.log(token);
								    	console.log(args);

								    	$('#loadingCircle').show();
										var stripePayment 		= new Object();
										//getting the email id and password
										stripePayment.param 	= 'stripePayment';
										stripePayment.token 	= token.id;
										stripePayment.mobapp_id = "<?php echo $mobapp_id; ?>";
										stripePayment.page_id = "<?php echo $page_id; ?>";

										$.ajax({
									      	url		: 'AjaxMethods.php',
									      	method	: 'POST',
									      	data	: stripePayment,
									      	dataType: 'html',
											success	: function(response) {
												console.log(response);
												var res = JSON.parse(response);
												if(res['result']['status']) {
													alert(res['result']['message']);
													$('#customButton').hide();
												} else {
													alert(res['result']['message']);
												}
												console.log(res);
												$('#loadingCircle').hide();
									  		}
										});
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
						} else {
					?>
							<button class="btn-gray-dis" disable>App Submited</button>	
					<?php
						}
					?>
				</div>
				<div><hr class="hr-gray"></div>
				<div class="button-box">
					<a href="pagelist.php" class="btn-gray">Build Apps for More Pages?</a>

					<br><br>
					<a href="javascript:void(0)" onclick = "document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block';" class="btn-gray">Pricing ?</a>
					<!-- <a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'"
						style="font-size: 14px;font-family: 'dinlight';font-weight: 200;width: 326px;line-height: 19px;margin-top: 100px;color: #fff;">Pricing ?</a> -->
				</div>
			</div>
		</div>

		<div id="light" class="white_content">
	        <a href = "javascript:void(0)" onclick = "document.getElementById('light').style. display='none';document.getElementById('fade').style.display='none'">
	        <img src="img/close-button.png" class="closebutton"></a>
	        <h1>Convert your Facebook Page to a Mobile App</h1>
	        <h6>Your fans are all mobile. So should you !!</h6>
	        <p>We will convert your Facebook page into a slick and interactive mobile app in minutes.<br>
	        These apps will be available to download from the Apple and Google Play app stores.</p> 

	        <!-- <p class="mb-20">You will be able to engage your fans / customers through your own custom branded mobile app !!</p> -->

	        <div class="pop-lower">
	            <div class="pop-lower-left">
	                <h2 class="doller-pop"><span class="doller-price">$20</span><span class="doller-month">/mo</span></h1>
	            </div>
	            <div class="pop-lower-right">
	                All inclusive offer that gives you:
	                <ul>
	                    
	                    <li>
	                        <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">One Native iPhone App and One Native Android App
	                            <div class="tool-tip slideIn top">
	                                <!-- <span style="float:left;margin-left:50px;">One Android App  </span>   <span style="float:right;margin-right:50px;">  One iPhone app</span><br> -->

	                                <p>In addition to basic features, the app supports the ability to alert your fans and customers through Pushed Notifications. This is an incredible advantage of Native Mobile apps.</p>
	                            </div>
	                        </a>
	                    </li> 
	                    <li> 
	                        <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">App contains 6 auto-    updated Sections from your Facebook page
	                            <div class="tool-tip slideIn top">
	                                <p><b>About, Photos, Fan Wall, Videos, Events, Locations</b>

	                                Once you set it up, all changes made to your Facebook page automatically reflect in the app</p>
	                            </div>
	                        </a> 
	                    </li> 
	                    <li> 
	                        <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Free submission to App stores 
	                            <div class="tool-tip slideIn top">
	                                <p>We take care of the dirty work of submitting your apps to the Apple and Google play stores. We notify you once the apps are live.</p>
	                            </div>
	                        </a>
	                    </li> 
	                    <li>  <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Unlimited Push notifications </a>

	                    </li> 
	                    <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Unlimited app downloads
	                        <div class="tool-tip slideIn top">
	                                <p>No matter how many fans / customers download your app, your  cost remains the same. 
	                                </p>
	                            </div>
	                        </a> 
	                    </li> 

	                    <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Unlimited access to App Content Management System  on www.snaplion.com and our Mobile App Builder on Facebook. 
	                        <!-- <div class="tool-tip slideIn top">
	                                <p>on <a href="http://www.snaplion.com/" target="_blank">www.snaplion.com</a> 
	                                </p>
	                            </div> -->
	                        </a> 
	                    </li> 

	                    <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Unlimited email support
	                        
	                        </a> 
	                    </li> 

	                    <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Ability to manage mobile apps for all your Facebook pages through one simple control panel
	                        <!-- <div class="tool-tip slideIn top">
	                                <p>No matter how many fans / customers download your app, your  cost remains the same. 
	                                </p>
	                            </div> -->
	                        </a> 
	                    </li> 

	                     <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Ability to upgrade to pro plan (with many more awesome features) at any time
	                        <!-- <div class="tool-tip slideIn top">
	                                <p>No matter how many fans / customers download your app, your  cost remains the same. 
	                                </p>
	                            </div> -->
	                        </a> 
	                    </li> 
	                </ul>
	            </div>
	        </div>
	    </div>
	    
	    <div id="fade" class="black_overlay"></div>


		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
		<script src="js/new_script.js"></script>
		<script>
		//checking if all the items have been imported and then
			$(document).ready(function(){
				var count = 0;

				$(document).on('click', '#customButtonWithoutPayment', function(event){
					event.preventDefault();

					alert('Your app is not ready to be submitted yet. Please fill up App Ingredients first.');
				});

				$(document).on('click', '.appIngredients', function(event){
					$('#loadingCircle').show();
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

				$('#loadingCircle').hide();
			});
		</script>

		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>
	</body>
</html>