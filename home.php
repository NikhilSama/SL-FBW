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
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
	</head>

	<body>
		<div class="starting-container">
			<div class="starting-lower">
				<h4>Facebook Wizard</h4>
				<h3>Now Build Your Mobile App </h3>
				<h5>	From Your Facebook Page<br>
					In <span>Minutes</span></h5>
				<a href="#" class="btn-orange" id="checkPerms">GET STARTED </a>		
			</div>
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
		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>
		<script type='text/javascript' src='./js/fbscript.js'></script>
	</body>
	<script>
		$("#slideshow > div:gt(0)").hide();

		setInterval(function() { 
		  $('#slideshow > div:first')
		    .fadeOut(1000)
		    .next()
		    .fadeIn(1000)
		    .end()
		    .appendTo('#slideshow');
		},  3000);

		$(document).ready(function(){
	    	$("#floatingCirclesG").hide();	
	    });
	    document.getElementById('checkPerms').onclick = function() {
	    	$("#floatingCirclesG").show();
	      checkPermissions();
	    };
	</script>
</html>