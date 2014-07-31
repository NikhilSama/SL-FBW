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

	// $albums = $fbObject->api('/' . $page_id . '/albums?offset=0');
	// $albums = $fbObject->api($page_id."?fields=albums.fields(name,id,photos.fields(source,picture,name,album))");

	$albums = $fbObject->api(array('method' => 'fql.query', 'query' => 'SELECT object_id, aid, name, link, photo_count from album WHERE owner = ' . $page_id . ' LIMIT 100000'));
	// $photos = $fbObject->api(array('method' => 'fql.query', 'query' => 'SELECT object_id, src, caption, src_big from photo WHERE album_object_id IN (SELECT object_id from album WHERE owner = ' . $page_id . ') LIMIT 100000'));
	// echo "<pre>";
	// print_r($albums);
	// print_r($photos);

	$albumCount = count($albums);
	$photoCount = 0;
	foreach ($albums as $album) {
		$photoCount += $album['photo_count'];
	}

	// $events = $fbObject->api('/' . $page_id . '/events?limit=250&&offset=0');
	$events = $fbObject->api(array('method' => 'fql.query', 'query' => 'SELECT eid,description,end_time,host,location,name,pic_big,pic_cover,start_time,ticket_uri,venue from event where creator=' . $page_id));
	$eventCount = count($events);

	// $posts = $fbObject->api($page_id . '/feed');
	//129695797050125/feed?fields=message,full_picture,picture,object_id&until=1322123010&limit=5000
	// $posts = $fbObject->api($page_id."/feed?fields=picture,place,message,id,source,created_time,story,type&limit=500");
	$postCount = 0;
	$posts = $fbObject->api($page_id."/feed?fields=picture,place,message,object_id,source,created_time,type&limit=5000");
	if(!empty($posts['data'])) {
		$postCount += count($posts['data']);
		// feedCount($posts);
	}

	// function feedCount($posts) {
	// 	if(!empty($posts['paging']['next'])) {
	// 		$link = $posts['paging']['next'];
	// 		$link = str_replace("https://graph.facebook.com", "", $link);
	// 		$data = $fbObject->api($link);
	// 		if(!empty($data['data'])) {
	// 			$postCount += count($data['data']);
	// 			feedCount($data);
	// 		}
	// 	}
	// }

	$videos = $fbObject->api('/' . $page_id . '/videos?offset=0');
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

	<body style="margin: 0 !important;">
		<div class="loader-bg-main" id="loadingCircle">
			<div class="loader-bg"><img src="img/loader-x.GIF" width="40"></div>
			<span style="position: absolute;color: #fff;top: 50%;left: 50%;margin-left: -260px;margin-top: 58px;font-family: sans-serif;font-size: 16px;text-align: center;">
				<!-- Please be patient, this may take a minute. -->
				Pls be patient, this step may take upto 5 minutes.<br/> It depends on the amount of data being imported from your Facebook page.
			</span>
		</div>
		<div class="fb_maincontainer">
			<div class="fb_left">
				<div class="snap_odr">
					<div class="longpagename"><?php echo $pageInfo['name']; ?></div>
				</div>
				<div class="fb_left-inner">
					<div class="strip-12 import-radio">
						<div class="strip-green gradient-white fontcolor checkalign">
							<input type="checkbox" class="css-checkbox importSection" id="checkbox1" name="pageinfo" checked />
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
							<input type="checkbox" class="css-checkbox importSection" id="checkbox2" name="posts" checked />
							<label for="checkbox2" name="checkbox1_lbl" class="css-label lite-green-check"></label>
						</div>
						<div class="strip-blue gradient-white">
							<span class="blue-text fontcolor">Posts</span>
							<span class="strip-blue-text fontcolor">Total Posts - <?php echo $postCount; ?></span>
						</div>
						<div class="strip-white dalign">&nbsp;</div>
					</div>

					<div class="strip-12 import-radio">
						<div class="strip-green gradient-white fontcolor checkalign">
							<input type="checkbox" class="css-checkbox importSection" id="checkbox3" name="photos" checked />
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
							<input type="checkbox" class="css-checkbox importSection" id="checkbox4" name="events" checked />
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
							<input type="checkbox" class="css-checkbox importSection" id="checkbox5" name="videos" checked />
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
					<a href="#" class="btn-orange" style="width:360px;" id="submitList">IMPORT TO APP</a>
				</div>
				<div><hr class="hr-gray"></div>
				<div class="button-box">
					<a href="pagelist.php" class="btn-gray">Build Apps for More Pages?</a>

					<br><br>
					<a href="javascript:void(0)" onclick = "document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block';" class="btn-gray">Pricing ?</a>
					<!-- <a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block';"
						style="font-size: 14px;font-family: 'dinlight';font-weight: 200;width: 326px;line-height: 19px;margin-top: 100px;color: #fff;">Pricing ?</a> -->
				</div>
			</div>
		</div>


		<div id="light" class="white_content">
	        <a href = "javascript:void(0)" onclick = "document.getElementById('light').style. display='none';document.getElementById('fade').style.display='none'">
	        <img src="img/close-button.png" class="closebutton"></a>
	        <h1>Convert your Facebook Page to a Mobile App</h1>
	        <h6>Your fans are all mobile. So should you !!</h6>
	        <p>We will convert your Facebook page into a slick and interactive mobile app in minutes.<br>
	        These apps will be available to download from the Apple and Google Play app stores.</p> 

	        <!-- <p class="mb-20">You will be able to engage your fans / customers through your own custom branded mobile app !!</p> -->

	        <div class="pop-lower">
	            <div class="pop-lower-left">
	                <h2 class="doller-pop"><span class="doller-price">$20</span><span class="doller-month">/mo</span></h1>
	            </div>
	            <div class="pop-lower-right">
	                All inclusive offer that gives you:
	                <ul>
	                    
	                    <li>
	                        <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">One Native iPhone App and One Native Android App
	                            <div class="tool-tip slideIn top">
	                                <!-- <span style="float:left;margin-left:50px;">One Android App  </span>   <span style="float:right;margin-right:50px;">  One iPhone app</span><br> -->

	                                <p>In addition to basic features, the app supports the ability to alert your fans and customers through Pushed Notifications. This is an incredible advantage of Native Mobile apps.</p>
	                            </div>
	                        </a>
	                    </li> 
	                    <li> 
	                        <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">App contains 6 auto-    updated Sections from your Facebook page
	                            <div class="tool-tip slideIn top">
	                                <p><b>About, Photos, Fan Wall, Videos, Events, Locations</b>

	                                Once you set it up, all changes made to your Facebook page automatically reflect in the app</p>
	                            </div>
	                        </a> 
	                    </li> 
	                    <li> 
	                        <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Free submission to App stores 
	                            <div class="tool-tip slideIn top">
	                                <p>We take care of the dirty work of submitting your apps to the Apple and Google play stores. We notify you once the apps are live.</p>
	                            </div>
	                        </a>
	                    </li> 
	                    <li>  <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Unlimited Push notifications </a>

	                    </li> 
	                    <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Unlimited app downloads
	                        <div class="tool-tip slideIn top">
	                                <p>No matter how many fans / customers download your app, your  cost remains the same. 
	                                </p>
	                            </div>
	                        </a> 
	                    </li> 

	                    <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Unlimited access to App Content Management System  on www.snaplion.com and our Mobile App Builder on Facebook. 
	                        <!-- <div class="tool-tip slideIn top">
	                                <p>on <a href="http://www.snaplion.com/" target="_blank">www.snaplion.com</a> 
	                                </p>
	                            </div> -->
	                        </a> 
	                    </li> 

	                    <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Unlimited email support
	                        
	                        </a> 
	                    </li> 

	                    <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Ability to manage mobile apps for all your Facebook pages through one simple control panel
	                        <!-- <div class="tool-tip slideIn top">
	                                <p>No matter how many fans / customers download your app, your  cost remains the same. 
	                                </p>
	                            </div> -->
	                        </a> 
	                    </li> 

	                     <li> <a href="#" class="btn btn2 btn-primary  btn-large" style="position:relative;">Ability to upgrade to pro plan (with many more awesome features) at any time
	                        <!-- <div class="tool-tip slideIn top">
	                                <p>No matter how many fans / customers download your app, your  cost remains the same. 
	                                </p>
	                            </div> -->
	                        </a> 
	                    </li> 
	                </ul>
	            </div>
	        </div>
	    </div>
	    
	    <div id="fade" class="black_overlay"></div>

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