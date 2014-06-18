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