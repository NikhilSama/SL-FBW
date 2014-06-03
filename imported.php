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
	$events = $fbObject->api('/' . $page_id . '/events?limit=500&&offset=0');
	$posts = $fbObject->api('/' . $page_id . '/posts?limit=500&&offset=0');
	$videos = $fbObject->api('/' . $page_id . '/videos?limit=500&&offset=0');
?>

<!DOCTYPE html>
<html>
<head>
<title>fb</title>
<link href="css/style.css" rel="stylesheet">
<link href="css/checkbox.css" rel="stylesheet">
</head>

<body>

	<?php
		echo "<pre>";
		print_r($albums);
		print_r($events);
		print_r($posts);
		print_r($videos);
	?>
		
	<div class="fb_maincontainer">
		<iframe class="appDraw" id="appPreview" src="<?php echo 'https://fbwsimulator.snaplion.com/#/?app_id='.$mobapp_id; ?>" frameborder="0">
		</iframe>

		<div class="fb_left">
			<div class="snap_odr">Snaplion Order App Page</div>
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
						<span class="strip-blue-text">Total Post - 28</span>
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
						<span class="strip-blue-text">1 Albums, 1 Photos</span>
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
						<span class="strip-blue-text">Total Events - 00</span>
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
						<span class="strip-blue-text">Total Videos - 00</span>
					</div>
					<div class="strip-white">
						<input type="checkbox" class="css-checkbox" id="checkbox5"  checked=""/>
						<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check">Auto update</label>
					</div>
				</div>	
			</div>
			<div class="button-group">
				<a href="#" class="btn-orange">App Ingredients</a>
				<a href="#" class="btn-orange">make payment</a>
			</div>
			<div><hr class="hr-gray"></div>
			<div class="button-box"><a href="#" class="btn-gray">Build Apps for More Pages?</a></div>
			
		</div>
		
		<div class="fb_left">
			<div class="snap_odr">Snaplion Order App Page</div>
			<div class="fb_left-inner">

				<div class="strip-12 import-radio">
					<div class="strip-green gradient-white fontcolor checkalign">
						<input type="checkbox" class="css-checkbox" id="checkbox4"  checked=""/>
						<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check"></label>
					</div>
					<div class="strip-blue gradient-white">
						<span class="blue-text fontcolor">About Us</span>
						<span class="strip-blue-text fontcolor">About, Location, Mission</span>
					</div>
					<div class="strip-white dalign">&nbsp;</div>
				</div>

				<div class="strip-12 import-radio">
					<div class="strip-green gradient-white fontcolor checkalign">
						<input type="checkbox" class="css-checkbox" id="checkbox4"  checked=""/>
						<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check"></label>
					</div>
					<div class="strip-blue gradient-white">
						<span class="blue-text fontcolor">Post</span>
						<span class="strip-blue-text fontcolor">Total Post - 28</span>
					</div>
					<div class="strip-white dalign">&nbsp;</div>
				</div>

				<div class="strip-12 import-radio">
					<div class="strip-green gradient-white fontcolor checkalign">
						<input type="checkbox" class="css-checkbox" id="checkbox4"  checked=""/>
						<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check"></label>
					</div>
					<div class="strip-blue gradient-white">
						<span class="blue-text fontcolor">Photos</span>
						<span class="strip-blue-text fontcolor">1 Albums, 1 Photos</span>
					</div>
					<div class="strip-white dalign">&nbsp;</div>
				</div>

				<div class="strip-12 import-radio">
					<div class="strip-green gradient-white fontcolor checkalign">
						<input type="checkbox" class="css-checkbox" id="checkbox4"  checked=""/>
						<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check"></label>
					</div>
					<div class="strip-blue gradient-white">
						<span class="blue-text fontcolor">Events</span>
						<span class="strip-blue-text fontcolor">Total Events - 00</span>
					</div>
					<div class="strip-white dalign">&nbsp;</div>
				</div>

				<div class="strip-12 import-radio">
					<div class="strip-green gradient-white fontcolor checkalign">
						<input type="checkbox" class="css-checkbox" id="checkbox4"  checked=""/>
						<label for="checkbox1" name="checkbox1_lbl" class="css-label lite-green-check"></label>
					</div>
					<div class="strip-blue gradient-white">
						<span class="blue-text fontcolor">Videos</span>
						<span class="strip-blue-text fontcolor">Total Videos - 00</span>
					</div>
					<div class="strip-white dalign">&nbsp;</div>
				</div>

					
			</div>
			<div class="button-group">
				<a href="#" class="btn-orange" style="width:360px;">IMPORT TO SNAPLION</a>
			</div>
			<div><hr class="hr-gray"></div>
			<div class="button-box"><a href="#" class="btn-gray">Build Apps for More Pages?</a></div>
			
		</div>
	</div>
</body>
</html>





<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="css/slider.css">
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
		<title>Document</title>
	</head>
	<body>
	
	
	<div id="importedPage" class="container-fluid">
		<div class="importList">

			<div id="pageinfo" class="row-fluid importItems">
			<!-- checking if the data has already been imported and assigning classes accordingly -->
				<div class="<?php if(in_array('About',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick "; }?>" data-name="pageinfo"></div>
				<div class="itemNameInfo">
					<span class="<?php if($update_data['About'] == 'true' ) { echo 'span5 autoUpdate right'; } else { echo 'span5 unUpdate right'; } ?> " data-id=<?php echo $page_id; ?> data-name="About">Auto Update</span>	
					<h4 class="normalFont">About Us</h4>
					<h5 class="itemCount normalFont italics">About, Location, Mission</h5>
				</div>
			</div> <!-- pageinfo ends -->
				
			<div id="posts"  class="row-fluid importItems">
				<div class="<?php if(in_array('Fan Wall',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick "; }?>" data-name="posts"></div>
				<div class="itemNameInfo">
					<span class="<?php if($update_data['Fan Wall'] == 'true' ) { echo 'span5 autoUpdate right'; } else { echo 'span5 unUpdate right'; } ?> " data-id=<?php echo $page_id; ?> data-name="Fan Wall">Auto Update</span>	
					<h4 class="normalFont">Posts</h4>
					<?php  { ?>
						<h5 class="<?php if(in_array('Fan Wall',$imports,true)){ echo 'itemCount normalFont displayBlock'; } else { echo 'itemCount normalFont displayNone';} ?>"><?php echo "Total Posts - ".$item_count['Fan Wall']; ?></h5>
						<?php } ?>

				</div>
			</div> <!--posts ends -->

			<div id="albums" class="row-fluid importItems" >
				<div class="<?php if(in_array('Photos',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick "; }?>" data-name="photos"></div>
				<div class="itemNameInfo">
					<span class="<?php if($update_data['Photos'] == 'true' ) { echo 'span5 autoUpdate right'; } else { echo 'span5 unUpdate right'; } ?> " data-id=<?php echo $page_id; ?> data-name="Photos">Auto Update</span>	
					<h4 class="normalFont">Photos</h4>
					
					<h5 class="<?php if(in_array('Photos',$imports,true)){ echo 'itemCount normalFont displayBlock'; } else { echo 'itemCount normalFont displayNone';} ?>"><?php echo $item_count['Photos']." Albums ".$subitem_count['Photos']." Photos" ?></h5>

				</div>
			</div> <!--photos ends -->

			<div id="events" class="row-fluid importItems" >
				<div class="<?php if(in_array('Events',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick ";  } ?>" data-name="events"></div>
				<div class="itemNameInfo">
					<span class="<?php if($update_data['Events'] == 'true' ) { echo 'span5 autoUpdate right'; } else { echo 'span5 unUpdate right'; } ?> " data-id=<?php echo $page_id; ?> data-name="Events">Auto Update</span>	
					<h4 class="normalFont">Events</h4>
					
					<h5 class="<?php if(in_array('Events',$imports,true)){ echo 'itemCount normalFont displayBlock'; } else { echo 'itemCount normalFont displayNone';}?>"><?php echo "Total Events - ".$item_count['Events']; ?></h5>
						

				</div>
			</div> <!-- events ends -->
			
			<div id="videos" class="row-fluid importItems">
				<div class="<?php if(in_array('Videos',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick";  } ?>" data-name="videos"></div>
				<div class="itemNameInfo">
					<span class="<?php if( in_array('Videos',$imports,true) ) { echo 'span5 autoUpdate right'; } else { echo 'span5 unUpdate right'; } ?> " data-id=<?php echo $page_id; ?> data-name="Videos">Auto Update</span>
					<h4 class="normalFont">Videos</h4>
					<h5 class="<?php if(in_array('Videos',$imports,true)){ echo 'itemCount normalFont displayBlock'; } else { echo 'itemCount normalFont displayNone';} ?>"><?php echo "Total Videos -".$item_count['Videos']; ?></h5>
						

				</div>
			</div> <!-- videos ends  -->
		</div> <!-- importList ends -->
		
		<div id="submit-button" class="span5">
			<!-- <button class="btn btn-primary" id="submitList" type="button">Submit</button> -->
			<!-- <p style="margin-top: 2%;font-size: 15px;" class="normalFont">Using more data and info mean a much better experience to your customers on the app</p> -->
			<img class="pointer" id="submitList" src="img/import.png" alt="">
			<a href="ingredients.php"><img src="img/appIng.png" class="appIngredients" alt=""></a>

			<?php 
				if(! $payment_flag ) { 
			?>
					<script src="https://checkout.stripe.com/checkout.js"></script>
					<!-- <button id="customButton" src="img/makePayment.png"></button> -->
					<a href="#" id="customButton"><img src="img/makePayment.png" alt=""></a>
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
		
		<div id="progressbar"></div>
		<div id="responsetext"></div>

		<div id="suggestionInstall">
			
			<?php include_once('imported-section.php'); ?>
		</div>
	</div> <!-- container-fluid ends -->

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
	<script src="js/script.js"></script>
	<script src="js/jquery.flexisel.js"></script>
	<script>
	//checking if all the items have been imported and then
		$(document).ready(function(){
			var count = 0;
			$(".importItems").each(function(){
				if(  $(this).children(".checkboxes").hasClass("alreadyImported") )
				{
					count++;
				}
			});
			if( count == $(".importItems").length )
			{
				$("#submitList").css("display","none");
			}

			var uninstalledAppPage = $(".uninstalledAppPage");
			if( uninstalledAppPage.length > 3 )
			{
				if($(".slider").length)
				{
					$(".slider").flexisel({visibleItems:3,clone:true});
					$(".nextStepImg").css("margin-left","-2%");
				}
			} else
			{
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

