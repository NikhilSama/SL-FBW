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

	$pageInfo = $fbObject->api($pageId);
?>

<!DOCTYPE html>
<html>
	<head>
		<title>SnapLion Facebook Wizard</title>
		<link href="css/style.css" rel="stylesheet">
		<link href="css/checkbox.css" rel="stylesheet">
	</head>

	<body>
		<?php
			echo "<pre>";
			print_r($pageInfo);
		?>
		<div class="app-container">
			<div class="head-app">
				<h1>Congtratulations!</h1>
				<h4>Choose the Facebook Page for which you want to </h4>
			</div>

			<div class="pagebox-container">
				<div class="pagebox" style="margin-left: 260px;">
					<div class="upper">
						<img src="<?= 'https://graph.facebook.com/'.$page_id.'/picture?height=100&width=100' ?>" class="icon-img">
					</div>
					<div class="lower">
						<div class="radio-space">
							<input type="radio" name="radiog_dark" id="radio5" class="css-checkbox " checked="checked">
							<label for="radio5" class="css-label radioalign-box"></label>
						</div>
						<div class="radio-space-next">
							<h3><?= $_GET['name']; ?></h3>
							<h4>Likes - <?php echo $pageInfo['likes']; ?></h4>
							<h5><?php echo $pageInfo['category']; ?> </h5>
						</div>
					</div>
				</div>
			</div>

			<div class="proceed-section">
				<div class="h-next" style="margin-top:30px;">
					<a href="#" class="btn-orange proceedToWizard" data-id="<?= $page_id; ?>">PROCEED TO WIZARD</a></div> 
			</div>
		</div>
	</body>

	<?php 
		// Facebook JS
		echo $fbObject->getFBScript();
	?>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type='text/javascript' src='js/new_script.js'></script>
</html>