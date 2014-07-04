<?php 
	require_once ("header.php");
	$fbObject = new FBMethods();
	$db = new db_connect();
	require_once("functions.php");
	
	$posts = array();
	$bio = array();
	$location = array();
	$verify_token = "someverificationcode";


 	// get request is for verification of real time updates of an app
	if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['hub_mode'])
    && $_GET['hub_mode'] == 'subscribe' && isset($_GET['hub_verify_token'])
    && $_GET['hub_verify_token'] == $verify_token) 
	{	
		echo $_GET['hub_challenge'];
		//echoing back the hub_challenge that came along with the get request, which tells faceboook that the verification has been completed
    	

    } else if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {	
    	
	    $post_body = file_get_contents('php://input');
	    $obj = json_decode($post_body, true);
	    file_put_contents("changed.txt", $post_body."\r\n");
	    // $obj will contain the json in relation to data that has changed

	    $entry = $obj['entry'];

	    //looping throught the page entries
	    foreach ($entry as $entries)
	    {	
	    	
	    	//getting the id of the page on which the change has taken place
	    	$page_id = $entries['id'];


	    	$mobapp = $db->execute_query("SELECT m_app_id FROM " .PAGE. " WHERE page_id = ".$page_id." limit 1");
			$mobapp_id = $mobapp[0]['m_app_id'];


	    	//for importing only those apptabs which have been imported
	    	$apptab_query_string = "select apptab_name from ".APPTAB_ID." where page_id = ".$page_id." and flag = 'true' and update_flag = 'true' ";
			$apptab_data = $db->execute_query($apptab_query_string);

			//$imported data contains those apptab_names for which data has been imported
			$imported_data = array();
			foreach ($apptab_data as $data ) 
			{
				$imported_data[] = $data['apptab_name']; 
			}
			

			//get_apptabs function returns the apptabs 
	    	$apptabs = get_apptabs($page_id);

	    	//looping through particular page's changes
	    	foreach ($entries['changes'] as $change) 
	    	{	
	    		//key tells what field has changed for a particular page
	    		$key = $change['field'];

	    		switch($key)
		    	{
		    		
		    		case 'feed':

		    			//feeds can be further classified into status and posts, posts can be timeline photos etc
		    			//also making sure that data is added after checking the values of $change['value']['verb'] 
		    			if($change['value']['item'] == 'status' && $change['value']['verb'] == 'add' && in_array('Fan Wall', $imported_data)  )
		    			{	
		    				
		    				$post_data = $fbObject->api($page_id."/feed?fields=picture,place,message,id,source,created_time,story,type");
							extract_post_data($post_data,$apptabs);

		    			} 
	
		    		break;

		    		//the code below will run whenever there is change in description or location of change
		    		case 'description':
		    		// case 'picture':
		    		case 'location':
		    			//when description of a page changes
		    			if( in_array('About', $imported_data )  )
		    			{	

		    				// $pageinfo = $fbObject->api($page_id."?fields=description,location,picture.type(large)");
		    				$pageinfo = $fbObject->api($page_id);
							//getting the url of the thumbnail
							$picture_small = $fbObject->api($page_id."?fields=picture.type(square)");
							// file_put_contents("data.txt", json_encode($pageinfo));
							extract_page_info($pageinfo,$picture_small,$apptabs,$page_id);

		    			}
		    		break;
		    	}//switch case ends

	    	}	//loop ends for changes relating to particular page
	    	
	    }	//loop ends for changes relating to all pages


	    $section = array();
		// checking if arrays are empty or not and if not adding them to the section array
		global $section;
		if(!empty($bio) )
		{	
			$section['Bio'] = $bio;
		}
		if( !empty($location) )
		{	
			$section['Location'] = $location;
		}
		
		if( !empty($posts) )
		{	
			$section['Post'] = $posts;
			$db->execute_query("UPDATE ".APPTAB_ID." set item_count = ".$post_count." where page_id=".$page_id." and apptab_name='Fan Wall' ");
		}
		$data =  array();
		// $data['mobapp_id'] = $mobapp_id;
		$data['key'] = KEY;
		$data['mobapp_id'] = $mobapp_id;
		$data['section'] = $section;


		submitData($data);

	}


