<?php 
	require_once ("header.php");

	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");

	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);
?>

<!DOCTYPE html>
<html>
	<head>
		<title>SnapLion Facebook Wizard</title>
		<link href="css/style.css" rel="stylesheet">
		<link href="css/checkbox.css" rel="stylesheet">
		<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>
	</head>

	<body style="margin: 0 !important;">
		<div class="starting-container">
			<div class="loader-bg-main" id="loadingCircle">
				<div class="loader-bg"><img src="img/loader.GIF" width="40"></div>
				<span style="position: absolute;color: #fff;top: 50%;left: 50%;margin-left: -138px;margin-top: 58px;font-family: sans-serif;font-size: 16px;">Please be patient, this may take a minute.</span>
			</div>

			<div class="starting-lower">
				<h4>Mobile App Builder</h4>
				<h3>Build an iPhone & Android Mobile App</h3>
				<h5>	From Your Facebook Page<br>
					In <span>Minutes</span></h5>
					<a href="#" class="btn-orange" id="checkPerms">GET STARTED </a>

					<br><br><br><br>
					<a href = "javascript:void(0)" 
						onclick = "document.getElementById('light').style.display='block';document.getElementById('fade').style.display='block'"
						style="font-size: 14px;font-family: 'dinlight';font-weight: 200;width: 326px;line-height: 19px;margin-top: 100px;color: #fff;">Pricing ?</a>	
			</div>
			<div class="slider-container">
				<div id="slideshow">
					<div>
						<img src="img/photo-1.png">
					</div>
					<div>
						<img src="img/photo-2.png">
					</div>
					<div>
						<img src="img/photo-3.png">
					</div>
					<div>
						<img src="img/photo-4.png">
					</div>
				</div>
			</div>
		</div>
		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>
		<script type='text/javascript' src='js/fbscript.js'></script>





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





	</body>
	<script>
		// var iframeElem = window.parent.document.getElementsByTagName('iframe');
		// iframeElem.css('width', '850px');

		$("#slideshow > div:gt(0)").hide();
		setInterval(function() { 
		  $('#slideshow > div:first')
		    .fadeOut(1000)
		    .next()
		    .fadeIn(1000)
		    .end()
		    .appendTo('#slideshow');
		},  3000);

		$(document).on('click', '#checkPerms', function(event){
			event.preventDefault();

			$('#loadingCircle').show();
			checkPermissions();
		});
		$('#loadingCircle').hide();
	</script>
</html>