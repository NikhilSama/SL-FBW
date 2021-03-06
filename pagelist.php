<?php 
	require_once ("header.php");
	require_once ("functions.php");
	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");

	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);

	$pageOwner = $fbObject->api('me');
	$pageList = $fbObject->api('me/accounts');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="utf-8">
		<title>SnapLion FBW</title>
		<link href="css/style.css" rel="stylesheet">
		<link href="css/checkbox.css" rel="stylesheet">

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script src="js/jquery.flexisel.js"></script>
	</head>

	<body style="margin: 0 !important;">
		<div class="fb_maincontainer-cma">
			<div class="loader-bg-main" id="loadingCircle">
				<div class="loader-bg"><img src="img/loader.GIF" width="40"></div>
				<span style="position: absolute;color: #fff;top: 50%;left: 50%;margin-left: -138px;margin-top: 58px;font-family: sans-serif;font-size: 16px;">Please be patient, this may take a minute.</span>
			</div>

			<?php
				//if user is not the admin of any page
				if(!isset($pageOwner['email'])) {
			?>
					<div class="loader-bg-main" id="retryPermissionsId">
						<div class="loader-bg-msg">
							<span class="receivedMessage">
								We need your email to create your mobile app account. 
								<br/>Please click OK when Facebook asks for permission.
								<br/>
								<a href="#" class="btn-orange mt-10" id="retryPermissions">Retry</a>
								<a href="#" class="btn-orange mt-10" id="cancelPermissions">Cancel</a>
							</span>
						</div>
					</div>
			<?php
				} elseif(empty($pageList['data'])) {
			?>
					<div class="loader-bg-main" id="retryPagePermissionsId">
						<div class="loader-bg-msg">
							<span class="receivedMessage">
								<!-- <img src="img/oops.png" width="150"><br/> -->
								We need this permission to create a Mobile App from your Facebook page.
								<br/>Please click on OK when Facebook asks for permission.
								<br/>
								<a href="#" class="btn-orange mt-10" id="retryPagePermissions">Retry</a>
								<a href="#" class="btn-orange mt-10" id="exitPageTab">Cancel</a>
							</span>
						</div>
					</div>
			<?php
				} else {
					//to register user at snaplion
					//function to send the post request to register user
					$db = new db_connect();
					$access_token = $_SESSION[APPID."_accessToken"];
					$fbid = $fbObject->getFBID();
					$email_data = $fbObject->api('me?fields=email');
					$email = $email_data['email'];
					$name = $fbObject->getName();
					$userProfilePic = $fbObject->api("me/picture?redirect=0&height=200&type=normal&width=200");
					$db->execute_query("SELECT * from ".USERS." where fbid=".$fbid);
					$login_flag = 0;

					if( !mysql_affected_rows() ) {
						$data=array("key"=>KEY,"email"=>$email,"cname" => $pageOwner['name'],'profile_pic' => $userProfilePic['data']['url']);

						$url = REGISTER_URL;
						$result = curlreq($data,$url);
						
						//decoding the json received after the registeration process
						$result_array = json_decode($result,true);

						//if user is already registered and entry is not present in database
						if( !$result_array['result']['status'] ) {

							$data=array("key"=>KEY,"email"=>$email);
							$url = CHK_USER;
							$result = curlreq($data,$url);
							
							//decoding the json received after the registeration process
							$resultArr = json_decode($result,true);
							
							if($resultArr['result']['status'] ) {
								$db->execute_query("INSERT into ".USERS."(fbid,name,email,access_token,message_flag, snaplion_id) values('{$fbid}','{$name}','{$email}','{$access_token}',1,". $resultArr['result']['user_id'] .") ");
								// $msg = "Your email ".$email." is aready registered. You can also login at 'snaplion.com'";
								$login_flag = 1;
							}
						}
					} else {
						$flag_data = $db->execute_query("SELECT message_flag, snaplion_id from ".USERS." where fbid=".$fbid);
						$flag = $flag_data[0]['message_flag'];

						//if the snaplion_id of the user doesn't exist in the database and the user is an registered user
						if( !$flag_data[0]['snaplion_id'] ) {
							$login_flag = 1;
							$msg = "Your email ".$email." is aready registered. You can login at 'snaplion.com'";
							$db->execute_query("Update ".USERS." set message_flag = 1 where fbid=".$fbid);
						}
						//if no flag means the message has been shown to the user that the user is registered
						// if( !$flag )
						// {
						// 	$msg = "Your email ".$email." is aready registered. You can login at 'snaplion.com'";
						// 	$db->execute_query("Update ".USERS." set message_flag = 1 where fbid=".$fbid);
						// }
						//if already registered
					}

					//checks if the user is already registered or not
					if($result_array['result']['status']) {	
						//this is the case when a user has registered via the facebook app

						//mobapp_id is snaplion id received after user has registered
						$mobapp_id = $result_array['result']['data']['mobapp_id'];
						
						$name = $fbObject->getName();
						$snaplion_id = $result_array['result']['data']['user_id'];
						$ingredient_id = $result_array['result']['data']['ingredient_id'];
						$sql = "insert into ".USERS."(mobapp_id,snaplion_id,ingredient_id,fbid,name,email,access_token) values('{$mobapp_id}','{$snaplion_id}','{$ingredient_id}','{$fbid}','{$name}','{$email}','{$access_token}')";
						
						//setting the value of the session variable to the value of snaplion mobapp_id
						$_SESSION['mobapp_id'] = $result_array['result']['data']['mobapp_id'];

						$query_string = "insert into ".APPTAB_ID."(fb_id,mobapp_id,apptab_name,apptab_id,flag) values" ;

						//creating a string to enter apptab_id and name in the database only if new user is created
						$apptab_array = $result_array['result']['data']['apptabs'];
						$k = 0;
						foreach ($apptab_array as $reg_array) {	
							//getting apptab_id and apptab_name
							$apptab_id = $reg_array['Apptab']['id'];
							$tab_name = $reg_array['Tab']['tabName'];
							
							if($k == 0) {
								$query_string = $query_string."('{$fbid}','{$mobapp_id}','{$tab_name}','{$apptab_id}','false')";
								$k++;
							} else {
								$query_string = $query_string.",('{$fbid}','{$mobapp_id}','{$tab_name}','{$apptab_id}','false')";
							}
						}
						
						$db->execute_query($sql);
						$db->execute_query($query_string);
					}

					//checks if the user is already registered or not
					if($result_array['result']['status']) {	
						//if new user
						$msg = "Your Account has been successfully created. <br>You can Login at www.snaplion.com with following access details: <br>User ID: ".$email."<br>Password: snaplion123";
					}

					if($msg) {
				?>		
						<div class="loader-bg-main closeMessageOverlay">
							<div class="loader-bg-msg">
								<span class="receivedMessage">
									<?php echo $msg; ?>
									<?php
										if(!$login_flag) {
									?>
											<br/><a href="#" class="closeMessage btn-orange mt-10">Continue</a>
									<?php
										}
									?>
								</span>
								<?php 
									// if($login_flag) {	
								?>
										<!-- <div id="snaplionLogin">
											<h4>To proceed please enter your password for snaplion.com</h4>
											<div class="row-fluid">
												<span class="span3">Email :</span>
												<span class="span3"><input type="text" class="email" name="email" value="<?php echo $email; ?>" disabled="disabled"></span>
											</div>
											<div class="row-fluid">
												<span class="span3">Password :</span>
												<span class="span3"><input style="height:26px;" type="password" placeholder="PASSWORD" name="password" class="password"></span>
											</div>
											<div class="slogin row-fluid">
												<button class="snaplionLogin btn btn-primary">LOGIN</button>
											</div>
											
											<div class="errorMessage">The password entered by you is not correct</div>
										</div> -->
								<?php 
									// }
								?>
							</div>
						</div>
				<?php 
					} 
				?>

					<div class="installedApps" style="display: none;width:100%;">
						<div class="logo"><img src="img/snaplion-logo.png"></div>
						<div class="heading-group mt-20">
							<h2>Congratulations!</h2>
							<div class="mb-20 text-center" style="width:100%;">
							<h4 class="m0 mt-5">Mobile App Wizard is now installed on your page, Lets get started !! </h4></div>
						</div>
					</div>

					<div class="uninstalledApps" style="display: none;width:100%;">
						<div class="heading-group">
							<h4>Choose the Facebook Page for which you want to </h4>
							<h2>Create a Mobile App</h2>
						</div>
						<div class="username" style="margin:20px 0 0 80px;">
							<div class="snap_odr-n"><?php echo $pageOwner['name']; ?></div>
							<div class="tri" style="height:42px;"><img src="img/tri.png"></div>
						</div>
					</div>

					<input type="hidden" id="installedAppId" value="<?php echo INSTALLED_APP_ID; ?>">
					<div id="hiddenInstalled" style="display: none;">
						<div class="blue-strip">
							<div class="green-strip-x"><img src="img/tick-big.png"><br>Installed</div>
							<div class="blue-data installedAppPage" data-id="">
								<img src="img/user-image.png" class="installedAppImage" style="height:64px !important; width:64px !important;">
								<div class="blue-data-text  pointer" data-id="">
									<h3 class="pageName">New Delhi Eat Out</h3>
									<h5 class="pageCategory">Restaurants and Bars</h5>
									<h6 class="cata-likes pageLikes">10,451 likes</h6>
								</div>
							</div>
							<a href="#" class="btn-orange buildapp appLinkDiv" data-id="" style="margin-top: 17px;">BUILD APP</a>
						</div>	
					</div>

					<div id="hiddenUninstalled" style="display: none;">
						<div class="fb_left-cma m0 uninstalledAppPage">
							<div class="strip-gray">
								<div class="radio-box">
									<!-- <span class="toggleradio inactiveRadio" data-id="" data-name="" ></span> -->
									<input type="radio" name="radiog_dark" class="css-checkbox newAppRadio" data-id="" data-name="" id="radio">
									<label for="radio" class="css-label radioalign"></label>
								</div>
								<div class="rightpart">
									<div class="user-image pagePicture">
										<img src="img/user-image.png" class="uninstalledAppImage" style="height:64px !important; width:64px !important;">
									</div>
									<div class="strip-hgroup unistalledPageName">
										<h4 class="pagename pageName">New Delhi Eat Out</h4>
										<h5 class="cata-name pageCategory">Restaurants and Bars</h5>
										<h6 class="cata-likes pageLikes pageLikesUn">10,451 likes</h6>
									</div>
									<a href="#" class="btn-orange buildapp selectedAppInstall">INSTALL</a>
								</div>
							</div>
						</div>
					</div>

					<div id="hiddenUninstalledNew" style="display: none;">
						<div class="fb_left-cma">
							<div class="strip-gray">
								<div class="radio-box">
									<input type="radio" name="radiog_dark" id="radio" class="css-checkbox newAppRadio" data-id="" data-name="">
									<label for="radio" class="css-label radioalign"></label>
								</div>
								<div class="rightpart">
									<div class="user-image">
										<img src="img/user-image.png" class="uninstalledAppImage" style="height:64px !important; width:64px !important;">
									</div>
									<div class="strip-hgroup">
										<h4 class="pagename pageName">New Delhi Eat Out</h4>
										<h5 class="cata-name pageCategory pageLikesUn">Restaurants and Bars</h5>
									</div>
									<div class="likes pageLikes">
										10,451 likes
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="main-strip-pack installedApps" style="display: none;" id="installedAppPages">
						<div class="main-con" id="uninstalledAppPages" style="max-height: 250px;overflow-y: scroll;overflow-x: hidden; margin-bottom:20px;min-height:0 !important;">
						</div>
					</div>

					<div class="main-strip-pack uninstalledApps" style="display: none;max-height: 312px;overflow-y: scroll;overflow-x: hidden;width: 750px; margin-bottom:20px;" id="uninstalledNewAppPages">
						
					</div>

					<!-- <div style="clear:left;"></div>
					<hr class="hr-gray"> -->
					<div class="uninstalledApps uninstalledAppsNext" style="display: none;width: 100%;">
						<div class="hgroupnext">
							<h2>Snaplion's Facebook Wizard </h2>
							<h4>will be installed on your selected Facebook Page</h4>
						</div>
						<div class="button-lower">
							<a href="#" class="btn-orange selectedAppInstallFirst">Next</a>
						</div>
					</div>
				
					<script>
						$(document).ready(function(){
				    		var obj = new Object();
				    		$('#loadingCircle').show();
				    		sendAjaxRequest('AjaxPagelist.php',obj,'html','getPageList');
				    	});
					</script>
			<?php
				}
			?>
		</div>
		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>
		<script type='text/javascript' src='js/new_script.js'></script>
		<script type='text/javascript' src='js/fbscript.js'></script>
		<script type="text/javascript">
			$(document).ready(function() {
				if($('#retryPermissionsId').length || $('#retryPagePermissionsId').length) {
					$('#loadingCircle').hide();	
				}

	    		$(document).on('click', '#retryPermissions', function(event){
	    			event.preventDefault();

	    			$('#loadingCircle').show();
	    			checkPermissions();

	    			$('#retryPermissionsId').hide();
	    		});

	    		$(document).on('click', '#retryPagePermissions', function(event){
	    			event.preventDefault();

	    			$('#loadingCircle').show();
	    			var permsNeeded = ['manage_pages'];
	    			promptForPerms(permsNeeded);

	    			$('#retryPagePermissionsId').hide();
	    		});	

	    		$(document).on('click', '#cancelPermissions', function(event){
	    			event.preventDefault();
	    			
	    			window.top.location.href = 'https://www.facebook.com/<?php echo PAGENAMESPACE; ?>';
	    		});

	    		$(document).on('click', '#exitPageTab', function(event){
	    			event.preventDefault();
	    			
	    			window.top.location.href = 'https://www.facebook.com/<?php echo PAGENAMESPACE; ?>';
	    		});

	    		$(document).on('click', ".closeMessage", function() {
	    			$('#loadingCircle').show();
					$(".closeMessageOverlay").css("display","none");
				});
	    	});
		</script>
	</body>
</html>