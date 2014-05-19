<?php 
	require_once ("header.php");

	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");

	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);

	$pageList = $fbObject->api('me/accounts');
	
?>

<?php 

	$wantPermissions = "email,manage_pages";
	$permissions = $fbObject->isAuthorized($wantPermissions);
	if($permissions!="true")
	{
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
	foreach ($imported_data as $data) 
	{	
		//importing all items in the array
		$imports[] = $data['apptab_name'];
	}
	
 ?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
		<title>Document</title>
	</head>
	<body>
		<div id="importPage" class="container-fluid">
			<div class="importList">

				<div id="pageinfo" class="row-fluid importItems">
				<!-- checking if the data has already been imported and assigning classes accordingly -->
					<div class="<?php if(in_array('About',$imports,true)) { echo 'checkboxes alreadyImported'; } else { echo "checkboxes untick "; }?>" data-name="pageinfo"></div>
					<div class="itemNameInfo">	
						<h4 class="normalFont">About Us</h4>
						<h5 class="italics normalFont itemCount">About, Location, Mission</h5>
					</div>
				</div> <!-- pageinfo ends -->
					
				<div id="posts"  class="row-fluid importItems"    >
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
			<div id="submit-button" class="row-fluid">
				<!-- <button class="btn btn-primary" id="submitList" type="button">Submit</button> -->
				<img class="pointer" id="submitList" src="img/import.png" alt="">
			</div>
			
			<div id="progressbar"></div>
			<div id="responsetext"></div>

		</div> <!-- container-fluid ends -->

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
		<script src="js/script.js"></script>

		<?php 
		// Facebook JS
		echo $fbObject->getFBScript();
		?>
	</body>
	
</html>

