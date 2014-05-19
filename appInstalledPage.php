<?php 
	require_once ("header.php");
	require_once ("functions.php");
	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");

	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);
	$page_id = $_GET['id'];

?>

<!doctype html>
<html lang="en">
	<head>
	    <meta charset="utf-8">

	    <!-- Bootstrap 2.3.2 -->
	    <link rel="stylesheet" href="css/bootstrap.min.css">

	    <!-- Your CSS -->
	    <link rel="stylesheet" href="css/style.css">
	</head>

	<body>
		<div id="appInstalledPage" style="background-image:url(img/cong-bg.png); background-repeat:no-repeat;">

			<div class="installedApp" style="margin-top:15%;">
				<!-- <h2>Congratulation's</h2>
				<h4 class="normalFont">Snaplion's Facebook Wizard is now installed on your page</h4> -->
				<img src="img/cong-msg.png" alt="">
				<div class="uninstalledAppPage">
					<div class="pagePicture">
						<img src="<?= 'https://graph.facebook.com/'.$page_id.'/picture?height=100&width=100' ?>" >
					</div>
					<div class="unistalledPageName">
						<span class="pagename"><?= $_GET['name']; ?></span>
					</div>
				</div>
			</div> <!-- installedApp ends -->
			
			<img class="pointer proceedToWizard" src="img/proceed.png" data-id="<?= $page_id; ?>" alt="">
		</div> <!-- appInstalledPageEnds -->
	</body>



	<?php 
		// Facebook JS
		echo $fbObject->getFBScript();
	?>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type='text/javascript' src='./js/script.js'></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>

</html>