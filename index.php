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
if(!$fbObject->isLiked())
{
	echo "Please like the page to continue";
	// echo "<body style='margin:0px; padding:0px;'>";
	// echo "<img src='img/fangate.png' width='810px'/>";
	// echo "</body>";
	die();
}
?>
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
<div id="floatingCirclesG" style="display:none; margin-top: 42%; margin-left: 0%; float: left;">
	<div class="f_circleG" id="frotateG_01">
	</div>
	<div class="f_circleG" id="frotateG_02">
	</div>
	<div class="f_circleG" id="frotateG_03">
	</div>
	<div class="f_circleG" id="frotateG_04">
	</div>
	<div class="f_circleG" id="frotateG_05">
	</div>
	<div class="f_circleG" id="frotateG_06">
	</div>
	<div class="f_circleG" id="frotateG_07">
	</div>
	<div class="f_circleG" id="frotateG_08">
	</div>
</div>
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
$_SESSION[ 'pageid'] =  $request['page']['id'];

if($_SESSION['pageid'] == PAGEID)
{	
	//checking if the user is already registered on the page and if take user directly to the pagelist instead of home.php
	$fbid = $fbObject->getFBID();

	$db->execute_query("select * from ".USERS." where fbid = ".$fbid);
	if( mysql_affected_rows() )
	{	
		//registered user
		//also checking if user may have revoked the permissions given to the page
		$wantPermissions = "email,manage_pages";
		$permissions = $fbObject->isAuthorized($wantPermissions);
		if($permissions!="true")
		{
			$fbObject->login($permissions);
			die();
		} else
		{
			header("location:pagelist.php");
		}
		
	} else 
	{	
		//unregistered user
		header("location:home.php");
	}
	

} else if($request['page']['admin']) 
{	
	//if user is the admin of the page
	//runs when data is to be imported
	$db->execute_query( "SELECT * FROM ".APPTAB_ID." where flag = 'true' and page_id = ".$_SESSION['pageid'] );
	if(mysql_affected_rows())
	{
		header("location:imported.php");
	} else
	{
		header("location:import.php");
	}
	

} else
{
	//If user is not the admin of the page and neither is he using the app from the snaplion page
	echo "You Are Not Authorised to Use This App";
}


?>

