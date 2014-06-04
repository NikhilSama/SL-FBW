<?php 
	require_once ("header.php");

	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");
	
	require_once("ingredient-functions.php");
	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);

	$db = new db_connect();
	$snap_data = $db->execute_query( "SELECT ingredient_id, m_app_id from ".PAGE." where page_id=".$_SESSION['pageid'] );
	//function to get ingredients data
	$ingredient_data = getIngredientData($snap_data);
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/style_old.css">
		<!-- <link rel="stylesheet" href="css/ingredients.css"> -->
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
		<link rel="stylesheet" href="css/jquery.Jcrop.css">
		<title>SnapLion Facebook Wizard</title>
	</head>

	<body>
		<div id="ingredientsPage" class="container-fluid main_content" style="position: relative;">
			<div class="loader-bg-main" id="loadingCircle">
				<div class="loader-bg"><img src="img/loader.GIF" width="40"></div>
			</div>

			<div id="crop_modal" class="modal fade hide" tabindex="-1" role="dialog" aria-labelledby="crop_modal_header" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3 id="crop_modal_header">Crop Image</h3>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
					<button class="btn btn-primary btn-disabled"  disabled="true">Loading image...</button>
				</div>
			</div>

			<div class="ingredientsForm ingredients row-fluid">
				<form class="form" id="IngredientChangeForm" method="post">
					<h4 class="app-ing">App ingredients</h4>
					<div id="images" class="span-6">
						<div class="span11 offset1 image_container">
							<div class="upload_image_box app_icon">
			                    <div class="text">
			                    	<div class="app-icon-data">
				                        <h4 class="box_header">
				                            App <span>Icon</span>
				                            <img src="img/help.png" class="helpText2"  rel="tooltip" title="Key active" id="blah">
				                            <br>
				                            <small>(JPEG/JPG, 1024PX * 1024PX)</small>
				                        </h4>

				                        <div class="upload_button_wrapper">
				                            <a href="#" class="iconUpload" data-input-field-id="app_icon"  data-cropping="true" data-cropping-min-width="1024" data-cropping-min-height="1024" data-preview-id="app_icon_preview"><img src="img/upload.png" alt=""></a>									
				                            <span class="has-tooltip" data-toggle="tooltip" title="App icon is the small icon image that shows up on your phone along with app name.  When you tap on this icon image, your app will load on the phone."><!-- <i class="icon-question-sign"> --></i></span>
				                        </div>
			                        </div>
			                        <div class="app-icon-show">
			                    		<img src="<?php if($ingredient_data['app_icon']) {echo $ingredient_data['app_icon']; } else echo 'img/dummy_image.jpg'; ?>" data-value="<?php if($ingredient_data['app_icon']){echo 1;} else {echo 0;} ?>" style="width:102px;" class="iconImage">
			                    	</div>
			                    </div>
			                </div>
			                <div class="row field">
	                        	<div class="span10 offset1 ml-22">
	                        		<label for="IngredientName">App Icon Name</label>
	                        		<input required id="appName" value="<?php if($ingredient_data['name']) {echo $ingredient_data['name'];} ?>" placeholder="App Name" class="input-block-level span10 required" maxlength="12" minlength="3" type="text" id="IngredientName"/>
	                        	</div>

	                        	<div class="tooltip_wrapper">
	                        		<img src="img/help.png" class="helpText"  rel="tooltip" title="Key active" id="blah">
	                            	<span data-tooltip class="has-tip" data-width="200px" title="This is the name of your mobile app that appears below the app icon on the phone screen. Maximum 12 Characters."><!-- <i class="icon-question-sign"></i> --></span>   
	                        	</div>
	                   		</div>

		                    <div class="row field">
		                        <div class="span10 offset1 ml-22">
		                        	<label for="IngredientTitle">App Store Title</label>
		                        	<input id="appTitle" required  value="<?php if($ingredient_data['title']) {echo $ingredient_data['title'];} ?>"  placeholder="App Title" class="input-block-level span10 required" maxlength="100" minlength="5" type="text" id="IngredientTitle"/>
		                        </div>

		                        <div class="tooltip_wrapper">
		                        	<img src="img/help.png" class="helpText"  rel="tooltip" title="Key active" id="blah">
		                            <span data-tooltip class="has-tip" data-width="200px" title="This is the title of your mobile app that appears on iTunes/Android store. Maximum 100 Characters"><!-- <i class="icon-question-sign"></i> --></span> 
		                        </div>
		                    </div>
			                    
		                    <div class="row field">
		                        <div class="span10 offset1 ml-22">
		                        	<label for="IngredientDescription">App Description</label>
		                        	<textarea id="appDescription"  placeholder="App Description" required class="input-block-level span10 required" maxlength="4000" minlength="10" cols="30" rows="3" ><?php if($ingredient_data['description']) {echo $ingredient_data['description'];} ?></textarea>
		                        </div>
		                    	<div class="tooltip_wrapper">
		                    		<img src="img/help.png" class="helpText"  rel="tooltip" title="Key active" id="blah">
		                            <span data-tooltip class="has-tip" data-width="200px" title="This is the description of your mobile app that appears on iTunes/Android store. Maximum 1000 Characters"><!-- <i class="icon-question-sign"> --></i></span>  
		                    	</div>
		                    </div>
				                    
		                    <div class="row field">
		                        <div class="span10 offset1 ml-22">
		                        	<label for="IngredientAppOfficialWebsite">Website URL</label>
		                        	<input id="appUrl" required  value="<?php if($ingredient_data['appOfficialWebsite']) {echo $ingredient_data['appOfficialWebsite'];} else echo "http://" ?>" type="url" placeholder="App Website URL" class="input-block-level span10 url url_validate" id=""/>
		                    	</div>

		                    	<div class="tooltip_wrapper">
		                    		<img src="img/help.png" class="helpText"  rel="tooltip" title="Key active" id="blah">
		                        	<span data-tooltip class="has-tip" data-width="200px" title="This is the app's website URL"><!-- <i class="icon-question-sign"> --></i></span>
		                    	</div>
		                	</div>
				                    
		                    <div class="row field">
		                        <div class="span10 offset1 ml-22">
		                        	<label for="IngredientAppKeywords">App Keywords</label>
		                        	<input id="keyWords" required  value="<?php if($ingredient_data['appKeywords']) {echo $ingredient_data['appKeywords'];} ?>"  placeholder="App Keywords" class="input-block-level span10 required" maxlength="255" type="text" value="" id="IngredientAppKeywords"/>
		                        </div>

		                        <div class="tooltip_wrapper">
		                        	<img src="img/help.png" class="helpText"  rel="tooltip" title="Key active" id="blah">
		                            <span data-tooltip class="has-tip" data-width="200px" title="This list of keywords will enable people to search and find your app on the iTunes and Android store.  Enter a list of all the search terms that you feel users might search for when looking for your app"><!-- <i class="icon-question-sign"> --></i></span>
		                        </div>  
		                    </div>
		                </div>
					</div> <!-- ingredients images ends -->
					
					<div id="fields"  class="span-6-right ingredients_form">
						<div class="span12 image_container">
							<div style="display:none;">
								<input type="hidden" name="_method" value="PUT"/>
							</div>

							<fieldset>
								<div class="upload_image_box splash_screen splashscreen-size mb-0">
				                    <div class="text2" >
				                    	<div class="left_section_text">
				                    		<h4 class="box_header">
					                            loading <span>Screen</span>
					                            
					                        		<img src="img/help.png" class="helpText2"  rel="tooltip" title="Key active" id="blah">
					                            <br>
					                            <small>(JPEG/JPG, 640PX * 1136PX)</small>
				                        	</h4>

				                    	</div>
				                    	<div class="right_section_text">
				                    		<div class="upload_button_wrapper">
					                            <a href="#" class="splash-upload" data-input-field-id="appSplashImage" data-cropping="true" data-cropping-min-width="640" data-cropping-min-height="1136" data-preview-id="splash_preview"><img class="splashImage" src="img/upload.png" alt=""></a>
					                           <!--  <img src="img/help.png" class="imageHelpText" alt=""> -->
												
					                            <span class="has-tooltip" data-toggle="tooltip" title="Splash Screen (also called Loading Image) is the first screen that opens up (albeit for a few seconds) at the time of app launch.  This screen stays open for a few seconds only while your app loads, then dissapears as your app takes over the phone screen."></span>
					                            
					                        </div>
				                    	</div>
				                        
				                    </div>

				                    <div class="preview" id="splash_preview">
				                        <div class="dummy_image_wrapper">
				                            <img src="<?php if($ingredient_data['appSplashImage']){ echo $ingredient_data['appSplashImage']; } else echo 'img/dummy_image.jpg'; ?>" <?php if($ingredient_data['appSplashImage']){ echo "style='width: 100%; width:216px; height:383px;margin-top: 0px;margin-left: 0px;top: 0px; left:0px;'"; } ?>data-value="<?php if($ingredient_data['appSplashImage']){echo 1;} else {echo 0;} ?>" alt="" class="dummy_image">
				                        </div>
				                    </div>
			                	</div>

		                    </fieldset>
						</div>
					</div>  <!-- ingredients form ends -->
					<div class="span12 text-center mb-10">
						<button type="button" class="btn btn-large btn-primary ingredientFinish">Save</button>
					</div>
				</form>
			</div>
		</div> <!-- container-fluid ends -->

		<script type="text/javascript" src="//api.filepicker.io/v1/filepicker.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
		<script src="js/new_script.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function() {
				var count = 0;
				$(".required").each(function() {
					if( $(this).val().trim() != "" ) {	
						count++;
					}
				});
				
				if( count == $(".required").length ) {
					$("div.paymentLink").css("display","block");
				}

				filepicker.setKey("AY8AuvW64QIqfiOKzeK5yz");
				
				//functions to upload the image of icon and splash image and then sending ajax request to check the dimensions of the image
				$(".iconUpload").click(function() {
					filepicker.pick(function(InkBlob) {
						filepicker.stat(InkBlob, {width: true, height: true},
						 function(metadata){
						   console.log(JSON.stringify(metadata));

						   if(metadata.width >= 1024 && metadata.height >= 1024) {
							 //   	iconUpload = new Object();
								// iconUpload.url = InkBlob.url;
								// iconUpload.action = "uploadImage";
								// iconUpload.param = "iconUpload";
								// sendAjaxRequest(pathToController,iconUpload,'html','sizeCheck');

								// iconImage.attr("src",iconUpload.url);
								// iconImage.css({"width":"120px","height":"120px"});
								// iconImage.data("value","1");
							 	// console.log(InkBlob.url);

							  	var iconObject = new Object();
								iconObject.min_width = 1024;
								iconObject.true_width = metadata.width;
								iconObject.min_height = 1024;
								iconObject.true_height = metadata.height;
								iconObject.target_element = 'iconImage';
								iconObject.input_field = '';
					  			crop(InkBlob,iconObject);
						  	} else {
						  		alert("The App Icon dimesnions are not correct. Minimum size required 1024x1024 PX");
						  	}
						});
					});

					$("#filepicker_dialog_container").css('top','100px');
				});

				$(".splash-upload").click(function() {
					filepicker.pick(function(InkBlob){
						filepicker.stat(InkBlob, {width: true, height: true},
						function(metadata){
						   // console.log(JSON.stringify(metadata));

						   if(metadata.width >= 640 && metadata.height >= 1136) {
								// splashUpload = new Object();
								// splashUpload.url = InkBlob.url;
								// splashUpload.action = "uploadImage";
								// splashUpload.param = "splashUpload";
								// sendAjaxRequest(pathToController,splashUpload,'html','sizeCheck');

								var splashObject = new Object();
								splashObject.min_width = 640;
								splashObject.true_width = metadata.width;
								splashObject.min_height = 1136;
								splashObject.true_height = metadata.height;
								splashObject.target_element = 'splashScreen';
								splashObject.input_field = '';
					  			crop(InkBlob,splashObject);
					  		} else {
						  		alert("The Splash Screen dimesnions are not correct. Minimum size required 640x1136 PX");
						  	}
						});
					});
					$("#filepicker_dialog_container").css('top','100px');
				});
				
				$('#loadingCircle').hide();
			});
		</script>
		<script src="js/jquery.Jcrop.js"></script>
		<?php 
			// Facebook JS
			echo $fbObject->getFBScript();
		?>
		<script>
			$(document).ready(function(){
			    $("[rel=tooltip]").tooltip({ placement: 'top'});
			});
		</script>
	</body>
</html>