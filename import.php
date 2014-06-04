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
		<div class="loader-bg-main" id="loadingCircle">
			<div class="loader-bg"><img src="img/loader.GIF" width="40"></div>
		</div>
		<div class="fb_maincontainer">
			<div class="fb_left">
				<div class="snap_odr"><?php echo $pageInfo['name']; ?></div>
				<div class="fb_left-inner">
					<div class="strip-12 import-radio">
						<div class="strip-green gradient-white fontcolor checkalign">
							<input type="checkbox" class="css-checkbox importSection" id="checkbox1" name="pageinfo" />
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
							<input type="checkbox" class="css-checkbox importSection" id="checkbox2" name="posts" />
							<label for="checkbox2" name="checkbox1_lbl" class="css-label lite-green-check"></label>
						</div>
						<div class="strip-blue gradient-white">
							<span class="blue-text fontcolor">Post</span>
							<span class="strip-blue-text fontcolor">Total Post - <?php echo $postCount; ?></span>
						</div>
						<div class="strip-white dalign">&nbsp;</div>
					</div>

					<div class="strip-12 import-radio">
						<div class="strip-green gradient-white fontcolor checkalign">
							<input type="checkbox" class="css-checkbox importSection" id="checkbox3" name="photos" />
							<label for="checkbox3" name="checkbox1_lbl" class="css-label lite-green-check"></label>
						</div>
						<div class="strip-blue gradient-white">
							<span class="blue-text fontcolor">Photos</span>
							<span class="strip-blue-text fontcolor"><?php echo $albumCount; ?> Albums, <?php echo $photoCount; ?> Photos</span>
						</div>
						<div class="strip-white dalign">&nbsp;</div>
					</div>

					<div class="strip-12 import-radio">
						<div class="strip-green gradient-white fontcolor checkalign">
							<input type="checkbox" class="css-checkbox importSection" id="checkbox4" name="events" />
							<label for="checkbox4" name="checkbox1_lbl" class="css-label lite-green-check"></label>
						</div>
						<div class="strip-blue gradient-white">
							<span class="blue-text fontcolor">Events</span>
							<span class="strip-blue-text fontcolor">Total Events - <?php echo $eventCount; ?></span>
						</div>
						<div class="strip-white dalign">&nbsp;</div>
					</div>

					<div class="strip-12 import-radio">
						<div class="strip-green gradient-white fontcolor checkalign">
							<input type="checkbox" class="css-checkbox importSection" id="checkbox5" name="videos" />
							<label for="checkbox5" name="checkbox1_lbl" class="css-label lite-green-check"></label>
						</div>
						<div class="strip-blue gradient-white">
							<span class="blue-text fontcolor">Videos</span>
							<span class="strip-blue-text fontcolor">Total Videos - <?php echo $videoCount; ?></span>
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

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
		<script src="js/new_script.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$('#loadingCircle').hide();
			});
		</script>
		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>
	</body>
</html>