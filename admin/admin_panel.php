<?php 
require_once("../include/db_connect.php");
session_start();
if($_SESSION["loggedIn"]!="Yes")
 	header("location:index.php");

 $db = new db_connect();

//getting all the categories
$category_data = $db->execute_query("SELECT * from ".CATEGORY);


?>

<html>

	<head>
	    <meta charset="utf-8">
	    <title>Admin Panel</title>
	    <link rel="stylesheet" href="css/style.css">
	    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
	    <link rel="stylesheet" href="css/bootstrap.min.css">
	    <link href="https://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
	    <style type="text/css" title="currentStyle">
	    @import "css/demo_table.css";
	</style>
	</head>

	<body>
		<div id="container">
			<div id="overlay"></div>
			<div id="moveSubcategory">
				<?php 
				//looping through the category names
					foreach ($category_data as $cdata) 
					{
						?>
						<div class="row-fluid catName" data-id ="<?php echo $cdata['category_id']; ?>">
							<input type="radio" name="categoryname" value="<?php echo $cdata['category_id']; ?>">
							<span ><?= $cdata['category_name']; ?></span>
						</div>
						<?php
					}


				 ?>
				<div class="row-fluid" style="text-align:center;">
					<button class="changeCategory btn btn-primary">SUBMIT</button>
					<button class="cancelChange btn btn-primary">CANCEL</button>
				</div>
			</div>
			<div class="topHeader">
				<img src="images/logoTheme.png" alt="">
				<span class="adminHeader">ADMIN PANEL</span>
			</div>

			<div class="sideBar">
				<ul id="options">
					<li id="getCategories"  class="selected">
						<span>Categories</span>
					</li>

					<li id="getUserData">
						<a href="userData.php">User Data</a>
					</li>

					<li id="logout">
						<span>Logout</span>
					</li>
				</ul>
			</div>
			
			<!-- hidden div for purpose of creating new category -->
			<div id="hiddenCategory">
				<h3 class="categoryName"></h3>
				<div class="subcategoryData" data-id = "">
					<div class="addsubcategory">
						<input type="text" name="addsubcategory" placeholder="ADD SUBCATEGORY" class="addsub" data-id="">
						<button class="createSubcategory btn btn-primary" data-id="">SUBMIT</button>
					</div>
					<div class="subcategoryItems container row-fluid"  data-id = "">
					</div>
				</div>
			</div>
			
			<!-- hidden div for purpose of creating new sub category -->
			<div id="hiddenSubcategory">
				<div class="row-fluid rowItems" data-id="">
					<span class="span3 subName"></span>
					<button class="moveItem btn btn-primary offset3" data-sub="" data-name="" data-id="">Move</button>
					<button class="removeItem btn btn-primary" data-sub="" data-name="" data-id="">Delete</button>
				</div>
			</div>
			
			<!-- hidden div for purpose of creating new radio item -->
			<div id="hiddenRadio">
				<div class="row-fluid catName" data-id ="">
					<input type="radio" name="categoryname" value="">
					<span ></span>
				</div>
			</div>

			
			<div id="categoryData">
				<div id="accordion">
				<?php 

					//looping through the category data
					foreach ($category_data as $cat_data) 
					{	
						//getting the id of the category
						$cat_id = $cat_data['category_id'];

						//getting the data corresponding to certain category id
						$subcategory_data = $db->execute_query("SELECT category_id,subcategory_name from ".SUBCATEGORY." where category_id=".$cat_id);
						?>
						<!-- the name of the category enlisted between h3 tags -->
						<h3 class="categoryName"><? echo $cat_data['category_name']; ?></h3>
						<div class="subcategoryData" data-id = "<?php echo $cat_id; ?>">
							<div class="addsubcategory">
								<input type="text" name="addsubcategory" placeholder="ADD SUBCATEGORY" data-id="<?php echo $cat_id; ?>" class="addsub">
								<button class="createSubcategory btn btn-primary" data-id="<?php echo $cat_id; ?>">SUBMIT</button>
							</div>

							<div class="subcategoryItems container row-fluid"  data-id = "<?php echo $cat_id; ?>">

							<?php 
							//looping through the subcategory data
								foreach ($subcategory_data as $subcat_data) 
								{	
									$subcat_id = $subcat_data['category_id'];

									//getting the name of the subcategory item
									$sub_name = $subcat_data['subcategory_name'];
							 ?>
									<div class="row-fluid rowItems" data-id="<?php echo $cat_id; ?>" data-sub="<?php echo $subcat_id; ?>">
										<span class="span3 subName"><?php echo $sub_name; ?></span>

										<button class="moveItem btn btn-primary offset3" data-name="<?php echo $sub_name; ?>" data-id="<?php echo $cat_id; ?>"  data-sub="<?php echo $subcat_id; ?>">Move</button>

										<button class="removeItem btn btn-primary" data-name="<?php echo $sub_name; ?>" data-id="<?php echo $cat_id; ?>"  data-sub="<?php echo $subcat_id; ?>">Delete</button>
									</div>
							<?php
								} //loop ends for subcategory data

							 ?>
							</div> 

						</div> <!-- subcategoryData ends -->



						<?php
					} //loop ends for category data

				 ?>
				 	<h3 class="addCategory">Add Category</h3>
				 	<div class="addCategory">
				 		<div class="container row-fluid">
				 			<input type="text" placeholder="ADD CATEGORY" name="addCategory">
				 			<input type="text" placeholder="CATEGORY ID" name="addId">
				 			<button class="createCategory btn btn-primary">SUBMIT</button>
				 		</div>
				 	</div>
				</div> <!-- accordion div ends -->

			</div> <!-- categoryData ends -->

		</div> <!-- Container div ends -->
		
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script type="text/javascript" src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="js/myscript.js"></script>
		
	</body>

</html>