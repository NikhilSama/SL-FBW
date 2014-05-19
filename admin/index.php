<?php 

	session_start();
	//checking if the user is logged in or not, if logged in, user is directed to admin panel
	if(isset($_SESSION["loggedIn"]))
	{
		if($_SESSION["loggedIn"]=="Yes")
	 	header("location:admin_panel.php");
	}
?>

<!DOCTYPE html>
<html lang="en">

	<head>
	    <meta charset="utf-8">
	    <title>Admin Login</title>
	    <link rel="stylesheet" href="css/style.css">
	    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
	    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/css/bootstrap.min.css">
	    <link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
	</head>

	<body>
		<div class="loginContainer">
			<div id="logoImage">
				<img src="images/logoTheme.png" alt="">
				<h2 class="loginText">ADMIN PANEL LOGIN</h2>
			</div>
			
			<div class="top">

				<div class="input-group extraPadding">
			 		 <input type="text" placeholder="USERNAME" class="form-control name required" placeholder="Enter Name">
				</div>
				<div class="input-group">
			 		 <input type="password" placeholder="PASSWORD" class="form-control pass required" placeholder="Enter Password">
				</div>
				<div class="lower">
					<button type="button" class="btn btn-default" id="admin_login">LOGIN</button>
				</div>
			</div>
		</div>

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="https://netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/myscript.js"></script>
	</body>

</html>