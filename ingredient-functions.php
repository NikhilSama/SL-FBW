<?php  
function getIngredientData($snap_data)
{
	$mobapp_id = $snap_data[0]['m_app_id'];
	$ingredient_id = $snap_data[0]['ingredient_id'];

	$submit_data = array(
		"mobapp_id" => $mobapp_id,
		"key" => KEY
		);

	$url = GET_INGREDIENTS_URL;

	// use key 'http' even if you send the request to https://...
	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => http_build_query($submit_data),
	    ),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	$result_data = json_decode($result,true);

	//var_dump($result_data['result']['status']);
	if( $result_data['result']['status'] )
	{	
		return $result_data['result']['data'];
	} else
	{
		return "error";
	}
}


	function extractImageUpload($snap_data,$url,$param)
	{	
		global $ingredient_data;
		global $fbObject;
		global $db;

		//getting the value of ingredient id and mobappp id
		$id = $snap_data[0]['ingredient_id'];
		$mobapp_id = $snap_data[0]['m_app_id'];

		$ingredient_data['key'] = KEY;
		$ingredient_data['id'] = $id;
		$ingredient_data['mobapp_id'] = $mobapp_id;

		//checking the case wheather to upload the icon or splash image or  home image
		switch ($param) 
		{
			case 'iconUpload':
				$ingredient_data['app_icon'] = $url;
				break;

			case 'splashUpload':
				$ingredient_data['appSplashImage'] = $url;
				break;

			case 'homeUpload':
				$ingredient_data['home_screen'] = $url;
				break;

		}

		submitIngredientData($ingredient_data);

	}


	function submitIngredientData($ingredient_data)
	{
		$url = ADD_INGREDIENTS_URL;
		
		// use key 'http' even if you send the request to https://...
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		        'method'  => 'POST',
		        'content' => http_build_query($ingredient_data),
		    ),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		$result_data = json_decode($result,true);

		echo $result_data['result']['status'];

	}