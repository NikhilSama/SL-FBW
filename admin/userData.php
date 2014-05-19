<?php  
require_once("../include/db_connect.php");
session_start();
if($_SESSION["loggedIn"]!="Yes")
{
 	header("location:index.php");
}
$db = new db_connect();

$user_data = $db->execute_query("SELECT name,email,timestamp,fbid from ".USERS);
$visit_data = $db->execute_query("SELECT visit from ".VISIT." limit 1");
$visit_number = $visit_data[0]['visit'];
?>

<html>
	<head>
	    <meta charset="utf-8">
	    <title>Admin Panel</title>
	    
	    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
	    <!-- <link rel="stylesheet" href="css/demo_table_jui.css"> -->
	    <!-- <link rel="stylesheet" href="css/TableTools_JUI.css"> -->
	    <link rel="stylesheet" href="css/jquery.dataTables_themeroller.css">
	    <link rel="stylesheet" href="css/bootstrap.min.css">
	    <link rel="stylesheet" href="css/style.css">
	    <style type="text/css" title="currentStyle">

	    /*@import "css/demo_table.css";*/
	</style>
	</head>
	
	<body>
		<div id="container">

			<div class="topHeader">
				<img src="images/logoTheme.png" alt="">
				<span class="adminHeader">ADMIN PANEL</span>
			</div> <!-- topheader ends -->

			<div class="sideBar">
				<ul id="options">
					<li id="getCategories">
						<a href="admin_panel.php">Categories</a>
					</li>
					
					<li id="getUserData"  class="selected">
						<span>User Data</span>
					</li>

					<li id="logout">
						<span>Logout</span>
					</li>
				</ul>
			</div> <!-- Sidebar ends -->
			

			<div id="pageData">
				
			</div>

			<div id="mainContent">
				<div id="visitNumber">
					<div class="helvetica visitTitle" class="visitTitle">NUMBER OF VISITS</div>
					<div class="helvetica visitnumber"><?php echo $visit_number; ?></div>
				</div> <!-- visitNumber ends -->
				
				<div id="tableHeading">
					<h3 style="margin-left: 20%,float: left;">Application Installs</h3>
				
					<table id="userData">
						<thead>
							<tr>
								<th>NAME</th>
								<th>FACEBOOK ID</th>
								<th>EMAIL</th>
								<th>REG. DATE</th>
								<th>NO. OF INSTALLS</th>
							</tr>
						</thead>

						<tbody>
							<?php 
							//looping through the user data
							foreach ($user_data as $user_info) 
							{	
								$fbid = $user_info['fbid'];
								$db->execute_query("SELECT * from ".PAGE." where fbid=".$fbid);
								$install_count = mysql_affected_rows();
								if($install_count);
								$time = date("m/j/Y" ,strtotime($user_info['timestamp']));
								echo "<tr data-id=$fbid>";
								?>
								<td><?php echo $user_info['name']; ?></td>
								<td><?php echo $user_info['fbid']; ?></td>
								<td><?php echo $user_info['email']; ?></td>
								<td><?php echo $time; ?></td>
								<td data-id=<?php echo $fbid; ?> class="installCount"><?php echo $install_count ?></td>
								<?php
								echo "</tr>";
							}
							 ?>
						</tbody>
					</table>
				</div>
			</div> <!-- Main Content ends -->

			

		</div> <!-- Container div ends -->
		
		

		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="js/myscript.js"></script>
		<script src="js/bootstrap.min.js"></script>

	</body>
</html>