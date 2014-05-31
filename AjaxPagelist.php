<?php 
	//this is a page to do background processes and return pages on which app is installed and on which app is not installed

	// session_start();
	require_once ("include/constants.php");
	require_once("include/FBMethods.php");
	require_once("include/db_connect.php");

	$pages = array();
	$installed_page_data = array();
	$uninstalled_page_data = array();
	$fbObject = new FbMethods();
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);
	$pageList = $fbObject->api('me/accounts');
	$installed_page_data = array();
	$uninstalled_page_data = array();

	// Run loop for each page
	foreach ($pageList['data'] as $page_data) {	
		$pageId = $page_data['id'];
		$accessTokens[$pageId] = $page_data['access_token']; // Create array of page IDs and access tokens
		$_SESSION[pageId."_".APPID."_pageTokens"] = $accessTokens; // Save Page Access tokens in Session to be used by AJAX

		//check if app is already installed on the the page as page tab
		//getting the app access token
		$access_token = file_get_contents('https://graph.facebook.com/oauth/access_token?client_id='.INSTALLED_APP_ID.'&client_secret='.INSTALLED_APP_SECRET.'&grant_type=client_credentials');

		//acces token is in the form 'access_token=value'
		$token = explode("=", $access_token);
		$token = $token[1];
		$app_check = json_decode(file_get_contents('https://graph.facebook.com/'.$pageId.'/tabs/'.INSTALLED_APP_ID.'?access_token='.$token));

		$pageInfo = json_decode(file_get_contents('https://graph.facebook.com/'.$pageId.'?access_token='.$token));

		//if app_check is empty then app is installed else not installed
		if(!empty($app_check->data)) {	
			//creating an array for the pages on which the app is installed
			$installed_page_data[] = $pageInfo;
		} else {	
			//creating an array for pages on which the app is not installed
			$uninstalled_page_data[] = $pageInfo;
		}
	} //end of loop
	// print_r($installed_page_data);
	// print_r($uninstalled_page_data);
	// die();
	$pages['installed'] = $installed_page_data;
	$pages['uninstalled'] = $uninstalled_page_data;
	$page_json = json_encode($pages);
	print_r($page_json);
 ?>