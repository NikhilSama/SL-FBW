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
	$sql = "select apptab_name from ".APPTAB_ID." where page_id=".$page_id." and flag='true' ";
	$imported_data = $db->execute_query($sql);
	
	$imports = array(); 
	foreach ($imported_data as $data) {	
		//importing all items in the array
		$imports[] = $data['apptab_name'];
	}

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
			<div class="fb_left">
				<?php
					echo "Album Count : " . $albumCount . "<br/>";
					echo "Photo Count : " . $photoCount . "<br/>";
					echo "Event Count : " . $eventCount . "<br/>";
					echo "Post Count : " . $postCount . "<br/>";
					echo "Video Count : " . $videoCount . "<br/>";
				?>
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
					<a href="#" class="btn-orange" style="width:360px;" id="submitList">IMPORT TO SNAPLION</a>
				</div>
				<div><hr class="hr-gray"></div>
				<div class="button-box"><a href="#" class="btn-gray">Build Apps for More Pages?</a></div>
			</div>
		</div>






		<div class="importList">
			<div id="pageinfo" class="row-fluid importItems">
			<!-- checking if the data has already been imported and assigning classes accordingly -->
				<div class="<?php if(in_array('About',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick "; }?>" data-name="pageinfo"></div>
				<div class="itemNameInfo">	
					<h4 class="normalFont">About Us</h4>
					<h5 class="italics normalFont itemCount">About, Location, Mission</h5>
				</div>
			</div> <!-- pageinfo ends -->
				
			<div id="posts" class="row-fluid importItems"    >
				<div class="<?php if(in_array('Fan Wall',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick "; }?>" data-name="posts"></div>
				<div class="itemNameInfo">	
					<h4 class="normalFont">Posts</h4>
				</div>
			</div> <!--posts ends -->

			<div id="albums" class="row-fluid importItems" >
				<div class="<?php if(in_array('Photos',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick "; }?>" data-name="photos"></div>
				<div class="itemNameInfo">	
					<h4 class="normalFont">Photos</h4>
				</div>
			</div> <!--photos ends -->

			<div id="events" class="row-fluid importItems" >
				<div class="<?php if(in_array('Events',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick ";  } ?>" data-name="events"></div>
				<div class="itemNameInfo">	
					<h4 class="normalFont">Events</h4>
				</div>
			</div> <!-- events ends -->
			
			<div id="videos" class="row-fluid importItems">
				<div class="<?php if(in_array('Videos',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick";  } ?>" data-name="videos"></div>
				<div class="itemNameInfo">
					<h4 class="normalFont">Videos</h4>
				</div>
			</div>
		</div>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
		<script src="js/new_script.js"></script>
		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>
	</body>
</html>