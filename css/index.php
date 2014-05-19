<?php 
require_once ("header.php");
// file_put_contents("log.txt",json_encode($_REQUEST));

//Use if you want to configure things yourself... Or configuration will be automatically picked from ./include/constants.php file...
//$config = array("appId"=>APPID,"appSecret"=>APPSECRET,"appDir"=>APPDIR,"appNamespace"=>APPNAMESPACE,"pageId"=>PAGEID,"pageNamespace"=>PAGENAMESPACE,"appAccessToken"=>APPACCESSTOKEN);
//$fbObject = new FBMethods($config);

//All the app configurations will be picked from "./include/constants.php" file....
$fbObject = new FBMethods();

//What to do if the app is not opened from page...
if(!$fbObject->isOpenedFromPage())
{
	//echo "Please open the app from the page";
	$url = $fbObject->getPageUrl();
	echo "<script>window.open('{$url}','_parent')</script>";
	die();
}
//What to do if the page on which the app exists is not liked... 
//Page namespace is given through constants.php
//This parameter will only be available if the app has been opened from page...
//Or else it will also be false...
// if(!$fbObject->isLiked())
// {
// 	echo "Please like the page to continue";
// 	//echo "<img src='img/fangate.jpg' width='790px'/>";
// 	die();
// }

//Specify Permissions you want....
//Do not specify the default permissions.
//Blank itself means basic permissions....
//GIVE PERMISSION NAMES IN COMMA SEPERATED FORM...
//MAKE SURE YOU DO NOT PUT ANY SPACE AFTER OR BEFORE COMMA... 
$wantPermissions = "";
$permissions = $fbObject->isAuthorized($wantPermissions);
if($permissions!="true")
{
	$fbObject->login($permissions);
	die();
}
//GEtting long lived token which is valid for 2 months....
$fbObject->setLongLivedToken();
$_SESSION[APPID."_accessToken"]=$fbObject->getAccessToken();

//Redirect to first page
if($fbObject->request['app_data'])
	header("location:vote.php?open=".$fbObject->request['app_data']);
else
	header("location:vote.php");

?>

