<?php 
	require_once ("header.php");

	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");

	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);


?>

<html lang="en">
	<head>
	    <meta charset="utf-8">

	    <!-- Bootstrap 2.3.2 -->
	    <link rel="stylesheet" href="css/bootstrap.min.css">

	    <!-- Your CSS -->
	    <link rel="stylesheet" href="css/style.css">

	    <style>


</style>
	</head>
	<body>
	<div id="snaplion_logo">	
		<img id="checkPerms" src="img/button.png" alt="">
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
	</div>
		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>
		<script type='text/javascript' src='./js/fbscript.js'></script>
		<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>
	<script>
		/*document.getElementById("login").onclick = function() {
      FB.login(function(response) {
        console.log(response);
      }, {scope: permsNeeded.join(',')});
    };*/

    $(document).ready(function(){
    	$("#floatingCirclesG").hide();	
    });
    document.getElementById('checkPerms').onclick = function() 
    {
    	$("#floatingCirclesG").show();
      checkPermissions();
    };

    /*document.getElementById('removePerms').onclick = function() {
      removePermissions(['read_stream']);
    };*/
    </script>
	</body>
</html>