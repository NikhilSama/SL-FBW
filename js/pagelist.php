<?php 
	require_once ("header.php");

	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");

	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);

	echo '<pre>';
	print_r($fbObject->api('me/accounts'));
	echo '</pre>';
	
?>