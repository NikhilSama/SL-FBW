<?php
	require_once ("header.php");
	// file_put_contents("log.txt",json_encode($_REQUEST));

	//Use if you want to configure things yourself... Or configuration will be automatically picked from ./include/constants.php file...
	//$config = array("appId"=>APPID,"appSecret"=>APPSECRET,"appDir"=>APPDIR,"appNamespace"=>APPNAMESPACE,"pageId"=>PAGEID,"pageNamespace"=>PAGENAMESPACE,"appAccessToken"=>APPACCESSTOKEN);
	//$fbObject = new FBMethods($config);

	//All the app configurations will be picked from "./include/constants.php" file....
	$fbObject = new FBMethods();

	//What to do if the app is not opened from page...
	// if(!$fbObject->isOpenedFromPage()) {
	// 	//echo "Please open the app from the page";
	// 	$url = $fbObject->getPageUrl();
	// 	echo "<script>window.open('{$url}','_parent')</script>";
	// 	die();
	// }

	//What to do if the page on which the app exists is not liked... 
	//Page namespace is given through constants.php
	//This parameter will only be available if the app has been opened from page...
	//Or else it will also be false...
	// if(!$fbObject->isLiked()) {
	// 	echo "Please like the page to continue";
	// 	// echo "<body style='margin:0px; padding:0px;'>";
	// 	// echo "<img src='img/fangate.png' width='810px'/>";
	// 	// echo "</body>";
	// 	die();
	// }
?>
<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="utf-8">
		<title>SnapLion FBW</title>
		<link href="css/style.css" rel="stylesheet">
		<link href="css/checkbox.css" rel="stylesheet">

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

		<style>
			@-moz-keyframes f_fadeG{
			0%{
			background-color:green}

			100%{
			background-color:#707070}

			}

			@-webkit-keyframes f_fadeG{
			0%{
			background-color:green}

			100%{
			background-color:#707070}

			}

			@-ms-keyframes f_fadeG{
			0%{
			background-color:green}

			100%{
			background-color:#707070}

			}

			@-o-keyframes f_fadeG{
			0%{
			background-color:green}

			100%{
			background-color:#707070}

			}

			@keyframes f_fadeG{
			0%{
			background-color:green}

			100%{
			background-color:#707070}
		</style>
	</head>

	<body>
		<?php
			//Specify Permissions you want....
			//Do not specify the default permissions.
			//Blank itself means basic permissions....
			//GIVE PERMISSION NAMES IN COMMA SEPERATED FORM...
			//MAKE SURE YOU DO NOT PUT ANY SPACE AFTER OR BEFORE COMMA... 

			/*	$wantPermissions = "manage_pages";
				$permissions = $fbObject->isAuthorized($wantPermissions);
				if($permissions!="true")
				{
					$fbObject->login($permissions);
					die();
				}
			*/

			//GEtting long lived token which is valid for 2 months....
			$fbObject->setLongLivedToken();
			$_SESSION[APPID."_accessToken"] = $fbObject->getAccessToken();

			$request = $fbObject->request;
			$db = new db_connect();

			//updating the visit number of the app
			$visit_data = $db->execute_query("SELECT visit from ".VISIT." limit 1");
			$visit_number = $visit_data[0]['visit'];
			$visit_number += 1;
			$db->execute_query("UPDATE ".VISIT." set visit=".$visit_number." where id=1");

			//storing the page id where app is being run
			$_SESSION['pageid'] =  $request['page']['id'];

			if($_SESSION['pageid'] == PAGEID) {
				// //checking if the user is already registered on the page and if take user directly to the pagelist instead of home.php

				echo "<pre>";
				print_r($_SESSION);

				print_r($fbObject->request);

				print_r($_SERVER);
				print_r($_HTTP);

				try {
					$fbid = $fbObject->api('me?fields=id');					
				} catch (Exception $e) {
					print_r($e);
				}

				// $signed_request = $facebook->getSignedRequest();
				exit;

				$fbid = $fbObject->getFBID();

				if(is_numeric($fbid)) {
					$db->execute_query("select * from ".USERS." where fbid = ".$fbid);

					if(mysql_affected_rows()) {
						//registered user
						//also checking if user may have revoked the permissions given to the page
						$wantPermissions = "email,manage_pages";
						$permissions = $fbObject->isAuthorized($wantPermissions);
						if($permissions!="true") {
							$fbObject->login($permissions);
							die();
						} else {
							header("location:pagelist.php");
						}
					} else {
						//unregistered user
						header("location:home.php");
					}
				}

				// $fbid = $fbObject->getFBID();
				// if($fbid == 'A') {
				// 	//unregistered user
				// 	header("location:home.php");
				// } elseif(is_numeric($fbid)) {
				// 	$db->execute_query("select * from ".USERS." where fbid = ".$fbid);
				// 	if(mysql_affected_rows()) {
				// 		//registered user
				// 		//also checking if user may have revoked the permissions given to the page
				// 		$wantPermissions = "email,manage_pages";
				// 		$permissions = $fbObject->isAuthorized($wantPermissions);
				// 		if($permissions!="true") {
				// 			$fbObject->login($permissions);
				// 			die();
				// 		} else {
				// 			header("location:pagelist.php");
				// 		}
				// 	} else {
				// 		//unregistered user
				// 		header("location:home.php");
				// 	}	
				// } else {
				// 	//unregistered user
				// 	header("location:home.php");
				// }

				//checking if the user is already registered on the page and if take user directly to the pagelist instead of home.php
				//also checking if user may have revoked the permissions given to the page
				// $wantPermissions = "email,manage_pages";
				// $permissions = $fbObject->isAuthorized($wantPermissions);
				// if($permissions!="true") {
				// 	$fbObject->login($permissions);
				// 	die();
				// } else {
				// 	$fbObject->setLongLivedToken();
				// 	$_SESSION[APPID."_accessToken"] = $fbObject->getAccessToken();
				// 	$fbid = $fbObject->getFBID();
				// 	//registered user
				// 	$db->execute_query("select * from ".USERS." where fbid = ".$fbid);
				// 	if( mysql_affected_rows() ) { 
				// 		header("location:pagelist.php");
				// 	} else { 
				// 		//unregistered user
				// 		header("location:home.php");
				// 	}
				// }
			} else if($request['page']['admin']) {
				//if user is the admin of the page
				//runs when data is to be imported
				$db->execute_query( "SELECT * FROM ".APPTAB_ID." where flag = 'true' and page_id = ".$_SESSION['pageid'] );
				if(mysql_affected_rows()) {
					header("location:imported.php");
				} else {
					header("location:import.php");
				}
			} else {
				//If user is not the admin of the page and neither is he using the app from the snaplion page
		?>
				<div class="you-r">
		        	<h3 style="text-align:center;"><img src="http://www.snaplion.com/landingpages/logoTheme.png"></h3>
		            <h5 style="text-align:center;font-weight: 200;color: #808080;font-family: 'din';line-height: 26px;font-size: 22px;">You are not authorised to use this App. Get in touch with us at <a href="mailto:contact@snaplion.com" >contact@snaplion.com</a> to create your own App.</h3>
		            <h6 style="text-align:center;margin:0;"><a href="#" class="btn-orange mt-10" id="exitPageTab">Exit</a></h6>
		       	</div>

		       	<script type="text/javascript">
					$(document).ready(function(){
			    		$(document).on('click', '#exitPageTab', function(event){
			    			event.preventDefault();
			    			
			    			window.top.location.href = 'https://www.facebook.com/';
			    		});
			    	});
				</script>
		<?php
			}
		?>
	</body>
</html>