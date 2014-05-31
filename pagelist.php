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
	//if user is not the admin of any page
	if(empty($pageList['data'])) {
?>
		<div id="notAdmin">
			<span class="notAdminMessage">
				<h2>You are not an admin of any page, So you cannot use this app.</h2>
			</span>
		</div>
<?php
		die();
	}
?>





<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="utf-8">
		<title>SnapLion FBW</title>
		<link href="css/style.css" rel="stylesheet">
		<link href="css/checkbox.css" rel="stylesheet">
	</head>

	<body>
		<?php
			echo "<pre>";
			print_r($pageOwner);
			print_r($pageList);
		?>
		<div class="fb_maincontainer-cma">
			<div class="heading-group">
				<h4>Choose the Facebook Page for which you want to </h4>
				<h2>Create the Mobile App on Snaplion</h2>
			</div>
			<div class="username" style="margin:20px 0 0 80px;">
				<div class="snap_odr-n">Deepak Bansal</div>
				<div class="tri" style="height:42px;"><img src="img/tri.png"></div>
			</div>
			<div class="main-strip-pack">
				<div class="fb_left-cma">
					<div class="strip-gray">
						<div class="radio-box">
							<input type="radio" name="radiog_dark" id="radio5" class="css-checkbox " checked="checked">
							<label for="radio5" class="css-label radioalign"></label>
						</div>
						<div class="rightpart">
							<div class="user-image"><img src="img/user-image.png"></div>
							<div class="strip-hgroup">
								<h4 class="pagename">New Delhi Eat Out</h4>
								<h5 class="cata-name">Restaurants and Bars</h5>
							</div>
							<div class="likes">
								10,451 likes
							</div>
						</div>
					</div>
				</div>
				<div class="fb_left-cma">
					<div class="strip-gray">
						<div class="radio-box">
							<input type="radio" name="radiog_dark" id="radio5" class="css-checkbox " checked="checked">
							<label for="radio5" class="css-label radioalign"></label>
						</div>
						<div class="rightpart">
							<div class="user-image"><img src="img/user-image.png"></div>
							<div class="strip-hgroup">
								<h4 class="pagename">New Delhi Eat Out</h4>
								<h5 class="cata-name">Restaurants and Bars</h5>
							</div>
							<div class="likes">
								10,451 likes
							</div>
						</div>
					</div>
				</div>
				<div class="fb_left-cma">
					<div class="strip-gray">
						<div class="radio-box">
							<input type="radio" name="radiog_dark" id="radio5" class="css-checkbox " checked="checked">
							<label for="radio5" class="css-label radioalign"></label>
						</div>
						<div class="rightpart">
							<div class="user-image"><img src="img/user-image.png"></div>
							<div class="strip-hgroup">
								<h4 class="pagename">New Delhi Eat Out</h4>
								<h5 class="cata-name">Restaurants and Bars</h5>
							</div>
							<div class="likes">
								10,451 likes
							</div>
						</div>
					</div>
				</div>
				<div class="fb_left-cma">
					<div class="strip-gray">
						<div class="radio-box">
							<input type="radio" name="radiog_dark" id="radio5" class="css-checkbox " checked="checked">
							<label for="radio5" class="css-label radioalign"></label>
						</div>
						<div class="rightpart">
							<div class="user-image"><img src="img/user-image.png"></div>
							<div class="strip-hgroup">
								<h4 class="pagename">New Delhi Eat Out</h4>
								<h5 class="cata-name">Restaurants and Bars</h5>
							</div>
							<div class="likes">
								10,451 likes
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- <div style="clear:left;"></div>
			<hr class="hr-gray"> -->
			<div class="hgroupnext">
				<h2>Snaplion's Facebook Wizard </h2>
				<h4>will be installed on your selected Facebook Page</h4>
			</div>
			<div class="button-lower">
				<a href="#" class="btn-orange">Next</a>
			</div>
		</div>
	</body>
</html>

















		<div id="floatingCirclesG" style="position:absolute; display;block; margin-top: 42%; margin-left: 46%; float: left;">
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
			//to register user at snaplion
			//function to send the post request to register user
			
			$db = new db_connect();
			$access_token = $_SESSION[APPID."_accessToken"];
			$fbid = $fbObject->getFBID();
			$email_data = $fbObject->api('me?fields=email');
			$email = $email_data['email'];
			$name = $fbObject->getName();
			$db->execute_query("SELECT * from ".USERS." where fbid=".$fbid);
			$login_flag = 0;

			if( !mysql_affected_rows() ) {
				$data=array("key"=>KEY,"email"=>$email);

				$url = REGISTER_URL;
				$result = curlreq($data,$url);
				
				//decoding the json received after the registeration process
				$result_array = json_decode($result,true);

				//if user is already registered and entry is not present in database
				if( !$result_array['result']['status'] )
				{
					$db->execute_query("INSERT into ".USERS."(fbid,name,email,access_token,message_flag) values('{$fbid}','{$name}','{$email}','{$access_token}',1) ");
					$msg = "Your email ".$email." is aready registered. You can also login at 'snaplion.com'";
					$login_flag = 1;
				}

			} else {

				$flag_data = $db->execute_query("SELECT message_flag, snaplion_id from ".USERS." where fbid=".$fbid);
				$flag = $flag_data[0]['message_flag'];

				//if the snaplion_id of the user doesn't exist in the database and the user is an registered user
				if( !$flag_data[0]['snaplion_id'] )
				{
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
				$msg = "Your Account has been successfully created. <br>You can Login at snaplion.com with following access details: <br>User ID: ".$email."<br>Password: snaplion123";
			}

			if($msg) {
		?>		
				<div id="coverUp"></div>
				<div id="confirmMessage">
					<div id="message">
						<span class="receivedMessage"><? echo $msg; ?></span>
						<?php 
							if($login_flag) {	
						?>
								<div id="snaplionLogin">
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
								</div>
						<?php 
							} else {
								echo "<button class='closeMessage btn btn-primary'>OK</button>";
							}
						?>
					</div>
				</div>
		<?php 
			} 
		?>		
		<div id="hiddenInstalled">
			<div class="installedAppPage" data-id=""><!-- data id is page id here -->
				<div class="appLinkDiv pointer" data-id=""><!-- data id is page id here -->
					<img class="installedAppImage" src="" >
					<div class="pageName"><span><!--pageName here--></span></div>
				</div>
			</div><!-- installedAppPage div ends -->
		</div>

		<div id="hiddenUninstalled">
			<li>
				<div class="uninstalledAppPage" >
					<div class="pagePicture">
						<img class="uninstalledAppImage" src="" >
					</div>
					<div class="unistalledPageName">
						<span class="toggleradio inactiveRadio" data-id="" data-name="" ></span> <!-- data-id and data-name are pageid and page name here -->
						<span class="pagename"><!--pageName here--></span>
					</div>
				</div>
			</li>
		</div>

		<div class="pagelistContainer">
			<input type="hidden" id="installedAppId" value="<?php echo INSTALLED_APP_ID; ?>">
			<div id="installedAppPages" style="display:none;">
				<h4 class="normalFont">You Are Already Using The App On These Pages</h4>
			</div>
			
			<div id="uninstalledAppPages">
				<h4 class="newAppMessage normalFont"><!-- message --></h4>
				<ul style="margin-left:0px;" class="slider"></ul>
			</div>
			<?php //include_once('page-section.php'); ?>


			<div id="nextStep" style="display:none;">
				<div class="progressImage">
					<img class="progressImage1" src="img/progress1.png" alt="">
				</div>

				<div>
					<div class="nextStepText">
						<h3 class="normalFont ">Snaplion's Facebook Wizard</h3>
						<h5 style="margin-top:-4%;" class="normalFont inlineDisplay">will be installed on your selected Facebook Page</h5>
					</div>
					<div class="nextStepImg">
						<img class="pointer nextStep" src="img/nextStep.png" alt="">
					</div>
				</div>
			</div>
		</div> <!-- pagelistContainer div ends -->
	
		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type='text/javascript' src='./js/script.js'></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script src="js/jquery.flexisel.js"></script>
		<script>
			$(document).ready(function(){
	    		var obj = new Object();
	    		sendAjaxRequest('AjaxPagelist.php',obj,'html','getPageList');
	    	});
		</script>
	</body>
</html>