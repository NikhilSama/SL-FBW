<?php 
	require_once ("header.php");
	include_once ("functions.php");
	include_once ("ingredient-functions.php");
	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);
	$db = new db_connect();

	if(isset($_POST['param']) && $_POST['param'] == 'userLogin' ) {
		//verifying the user login
		$fbid = $fbObject->getFBID();
		$login_data = array( 
			"key"=>KEY, 
			"email" => $_POST['email'], 
			"password"=>$_POST['password'] 
		);

		$url = SID_URL;
		// sending curl request to verify user
		$result = curlreq($login_data, $url);

		// creating an associative array of the json received after sending the post request
		$result_array = json_decode($result,true);
		// var_dump($result_array);
		// die();
		//checking if the given password matched the username listed with the password
		if( $result_array['result']['status'] ) {
			$snaplion_id = $result_array['result']['user_id'];
			//updating the user table with the snaplion id received 
			$db->execute_query("UPDATE ".USERS." set snaplion_id=".$snaplion_id." where fbid=".$fbid);
			echo "success";
		} else {
			echo "error";
		}
	}

	if(isset($_POST['action']) && $_POST['action']=='install') {
		global $db;
		$pageTokens = $_SESSION[pageId."_".APPID."_pageTokens"];
		global $fbObject;
		$fbObject->api($_POST['id'].'/tabs', 'POST', array(
	  		'app_id'=>INSTALLED_APP_ID,
	  		'access_token'=>$pageTokens[$_POST['id']]
		));

		//getting the page id on which the app is to be installed
		$page_id = $_POST['id'];
		
		//the mobapp_id if it exists in session
		$mobapp_id = $_SESSION['mobapp_id'];
		$fbid = $fbObject->getFBID();

		$check_page_data = $db->execute_query("SELECT * from ".APPTAB_ID." where page_id=".$page_id);

		//checking if the app was installed on the page and there is already entry regarding the page in the database
		if( mysql_affected_rows() ) {
			die("This app had already beeen installed on this earlier");
		}

		// getting the snaplion id
		$snaplion_data = $db->execute_query("select snaplion_id, ingredient_id from ".USERS." where fbid=".$fbid." limit 1");
		if($snaplion_data) {
			$snaplion_id = $snaplion_data[0]['snaplion_id'];
			$ingredient_id = $snaplion_data[0]['ingredient_id'];
		}

		//getting the name of the page present in post request
		$pname = $_POST['pname'];
		$pname = addslashes($pname);

		//query to check if the entry exists for the same page id
		$page_data = "select * from ".PAGE." where page_id = ".$page_id." and fbid =".$fbid;
		$db->execute_query($page_data);
		
		
		//this runs for the first time and then the session['mobapp_id'] is set to '';
		if($_SESSION['mobapp_id'] != '') {
			//preparing string to set page_id in the apptab_id table
			$mobapp_data = $db->execute_query("select mobapp_id from ".USERS." where fbid=".$fbid);

			$mobapp_id = $mobapp_data[0]['mobapp_id'];
			$query = "update ".APPTAB_ID. " set page_id =".$page_id. " where mobapp_id =".$mobapp_id;
			
			$db->execute_query($query);
			$_SESSION['mobapp_id'] = '';
			//inserting into page table the pages on which the appp has beeen installed
			$query = "insert into ".PAGE."(sl_id,page_id,ingredient_id,page_name,m_app_id,fbid) values('{$snaplion_id}','{$page_id}','{$ingredient_id}','{$pname}','{$mobapp_id}','{$fbid}')";
			$db->execute_query($query);

		} else if( mysql_affected_rows() == 0) {
			//checking if the entry is already available in the table as user may have installled app and then uninstalled on the page
			$page_category_data = $fbObject->api($page_id."?fields=category,name");
			print_r($page_category_data);
			$page_category = $page_category_data['category'];
			print_r($page_category);
			$page_category = strtolower($page_category);

			//matching the case
			$id_query = "SELECT * FROM ".SUBCATEGORY." where subcategory_name like '{$page_category}'";
			
			$app_id_data = $db->execute_query($id_query);
			print_r($app_id_data);

			if( mysql_affected_rows() ) {
				//checking if there exists an category for the page in the database
				$app_id = $app_id_data[0]['category_id'];
			} else {
				//if there exists no match for the type of page , the basic app is created whose id is 1000
				$app_id = 1000;
			}
			print_r($app_id);
			$pageProfilePic = $fbObject->api($page_id."/picture?redirect=0&height=200&type=normal&width=200");

			$name = $fbObject->getName();
			$new_app_data = array(
								"key"=>KEY,
								"user_id"=>$snaplion_id,
								"category_id"=>$app_id,
								'profile_pic' => $pageProfilePic['data']['url'], 
								'name' => $page_category_data['name']
							);
			$url = ADD_APP_URL;
			//sending the post request to0 create a new app with the existing user
			$result = curlreq($new_app_data,$url);

			// creating an associative array of the json received after sending the post request
			$result_array = json_decode($result,true);
			echo "<pre>";
			var_dump($result_array);
			echo "</pre>";

			//getting the mobapp_id , name and snaplion_id
			$mobapp_id = $result_array['result']['data']['mobapp_id'];
			$ingredient_id = $result_array['result']['data']['ingredient_id'];
			$snaplion_id = $result_array['result']['data']['user_id'];
		
			//insering into apptab table the value of apptab_id , mobapp_id
			$query_string = "insert into ".APPTAB_ID."(fb_id,mobapp_id,page_id,apptab_name,apptab_id,flag) values";
			$apptab_array = $result_array['result']['data']['apptabs'];
			
			$k = 0;
			foreach ($apptab_array as $reg_array) {	
				//getting apptab_id and apptab_name
				$apptab_id = $reg_array['Apptab']['id'];
				$tab_name = $reg_array['Tab']['tabName'];
				// print_r($reg_array['Apptab']['id']."=>".$reg_array['Tab']['tabName']);
				if($k == 0) {
					$query_string = $query_string."('{$fbid}','{$mobapp_id}','{$page_id}','{$tab_name}','{$apptab_id}','false')";
					$k++;
				} else {
					$query_string = $query_string.",('{$fbid}','{$mobapp_id}','{$page_id}','{$tab_name}','{$apptab_id}','false')";
				}
			}
			//adding the apptabs in the table
			$db->execute_query($query_string);
			//inserting into page table the pages on which the appp has beeen installed
			$sql = "insert into ".PAGE."(sl_id,page_id,ingredient_id,page_name,m_app_id,fbid) values('{$snaplion_id}','{$page_id}','{$ingredient_id}','{$pname}','{$mobapp_id}','{$fbid}')";
			$db->execute_query($sql);
		}
	}

	//this code below is used to change the user preference is user wants to change the auto-update option of the item
	if( isset($_POST['param']) && $_POST['param'] == "changePreference" ) {
		if($_POST['action'] == "addUpdate") {
			$flag = 'true';
		} else {
			$flag = 'false';
		}

		$apptab_name = $_POST['name'];
		$page_id = $_POST['id'];
		$db->execute_query("UPDATE ".APPTAB_ID." set update_flag='{$flag}' where page_id='{$page_id}' and apptab_name='{$apptab_name}' ");
	}

	if( isset($_POST['action']) && $_POST['action']=="uploadImage" ) {
		$snap_data = $db->execute_query( "SELECT ingredient_id, m_app_id from ".PAGE." where page_id=".$_SESSION['pageid'] );
		$id = $snap_data[0]['ingredient_id'];
		$mobapp_id = $snap_data[0]['m_app_id'];

		$ingredient_data = array();
		//caling the function to send request to upload data to snaplion
		extractImageUpload( $snap_data,$_POST['url'],$_POST['param'] );
	}

	//this runs when app ingredients are being submitted 
	if( isset($_POST['param']) && $_POST['param'] == "submitIngredients" ) {
		$snap_data = $db->execute_query( "SELECT ingredient_id, m_app_id from ".PAGE." where page_id=".$_SESSION['pageid'] );

		$db->execute_query( "UPDATE ".PAGE." set ingredients_flag=1 where page_id=".$_SESSION['pageid']);
		//creating the array for submitting the data 
		$ingredientsImgData = array();
		if(isset($_POST['appSplashImage'])) {
			$ingredientsImgData['appSplashImage'] = $_POST['appSplashImage'];
		}
		
		if(isset($_POST['app_icon'])) {
			$ingredientsImgData['app_icon'] = $_POST['app_icon'];
		}

		$ingredients_data = array(
			"key"                => KEY,
			"id"                 => $snap_data[0]['ingredient_id'],
			"mobapp_id"          => $snap_data[0]['m_app_id'],
			"name"               => $_POST['name'],
			"title"              => $_POST['title'],
			"description"        => $_POST['description'],
			"appOfficialWebsite" => $_POST['url'],
			"appKeywords"        => $_POST['keywords']
		);

		$ingredients_data = array_merge($ingredients_data, $ingredientsImgData);

		submitIngredientData($ingredients_data);
	}

	//this isthe case when user wants to change preference for app gloss
	if( isset($_POST['param']) && $_POST['param'] == "appGloss" ) {
		$snap_data = $db->execute_query( "SELECT ingredient_id, m_app_id from ".PAGE." where page_id=".$_SESSION['pageid'] );

		$ingredients_data = array(
			"key"                => KEY,
			"id"                 => $snap_data[0]['ingredient_id'],
			"mobapp_id"          => $snap_data[0]['m_app_id'],
			"appIconGlossEffect" => $_POST['value']
		);

		submitIngredientData($ingredients_data);
	}

	if(isset($_POST['param']) && $_POST['param'] == 'stripePayment' ) {
		//verifying the user login
		$paymentData = array( 
			"key"=>KEY, 
			"token" => $_POST['token'], 
			"mobapp_id"=>$_POST['mobapp_id'] 
		);

		$url = STRIPE_PAYMENT;
		// sending curl request to verify user
		$result = curlreq($paymentData, $url);

		// creating an associative array of the json received after sending the post request
		$result_array = json_decode($result,true);
		// var_dump($result_array);
		// die();
		//checking if the given password matched the username listed with the password
		if( $result_array['result']['status'] ) {
			//updating the user table with the snaplion id received
			$db->execute_query("UPDATE " . PAGE . " set payment_flag = 1 where page_id= " . $_POST['page_id']);
		}

		echo $result;
		exit;
	}
?>