<input type="hidden" id="installedAppId" value="<?php echo INSTALLED_APP_ID; ?>">
<?	
	//declaring the arrays to store the information of the pages that have the app installed and not installed 
	$installed_page_data = array();
	$uninstalled_page_data = array();

	// Run loop for each page
	foreach ($pageList['data'] as $page_data) 
	{	

		$pageId = $page_data['id'];
		$accessTokens[$pageId] = $page_data['access_token']; // Create array of page IDs and access tokens
		$_SESSION[pageId."_".APPID."_pageTokens"] = $accessTokens; // Save Page Access tokens in Session to be used by AJAX
	
		/*<img src="https://graph.facebook.com/<?=$pageId?>/picture" alt="">*/
	

		//check if app is already installed on the the page as page tab
		//getting the app access token
		$access_token = file_get_contents('https://graph.facebook.com/oauth/access_token?client_id='.INSTALLED_APP_ID.'&client_secret='.INSTALLED_APP_SECRET.'&grant_type=client_credentials');

		//acces token is in the form 'access_token=value'
		$token = explode("=", $access_token);
		$token = $token[1];
		$app_check = json_decode(file_get_contents('https://graph.facebook.com/'.$pageId.'/tabs/'.INSTALLED_APP_ID.'?access_token='.$token));
	

		//if app_check is empty then app is installed else not installed
		if(!empty($app_check->data))
		{	
			//creating an array for the pages on which the app is installed
			$installed_page_data[] = $page_data;
			 
		} else
		{	
			//creating an array for pages on which the app is not installed
			$uninstalled_page_data[] = $page_data;
		}
		


	} //end of loop

	
	//checking if there are any pages which do not have the app installed
	if( count($uninstalled_page_data) )
	{
		?>
		<!-- div for pages on which the app is not installed -->
		<div id="uninstalledAppPages">
			<h4 class="normalFont">Build Apps For More Pages</h4>
			<ul class="slider" style="margin-left:0px;">
		<?php 

		foreach ($uninstalled_page_data as $uninstalled_pages) 
		{	
			//getting the id of the page on which the option is there to install the app
			$page_id = $uninstalled_pages['id'];
			$page_name = $uninstalled_pages['name'];
			?>
			<li>
				<div class="uninstalledAppPage">
					<div class="pagePicture">
						<img src="<?= 'https://graph.facebook.com/'.$page_id.'/picture?height=100&width=100' ?>" >
					</div>
					<div class="unistalledPageName">
						<span class="toggleradio inactiveRadio" data-id="<?= $page_id; ?>" data-name="<?= $page_name; ?>" ></span>
						<span class="pagename"><?= $uninstalled_pages['name']; ?></span>
					</div>
				</div>
			</li>
			<?php  
		}//loop ends for pages on which the app is not installed

	 	?>
		</ul>
		</div> <!-- uninstalledAppPages div ends -->

		<div id="nextStep">
			<div class="progressImg">
				<img class="progressImage1" src="img/progress1.png" alt="">
			</div>

			<div>
				<div class="nextStepText">
					<h3 class="normalFont ">Snaplion's Facebook Wizard</h3>
					<h5 style="margin-top:-4%;" class="normalFont inlineDisplay">will be installed on your selected Facebook Page</h5>				
				</div>
				<div class="nextStepImg">
					<img class="pointer nextStep" src="img/nextStep.png" alt="">
				</div>
			</div>
		</div>

		<?php  
	} 
	
	?>