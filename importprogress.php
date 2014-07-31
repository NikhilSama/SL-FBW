<?php 
	require_once ("header.php");

	if(!isset($_SESSION[APPID."_accessToken"]))
	header("location:index.php");

	//Picks Default Configuration
	$fbObject = new FBMethods();
	//Sets Access token got from previous page....
	$fbObject->setAccessToken($_SESSION[APPID."_accessToken"]);
	$pageList = $fbObject->api('me/accounts');
	
	$events = array();
	$posts = array();
	$bio = array();
	$location = array();
	$albums = array();
	$videos = array();

	$db = new db_connect();
	$page_id = $_SESSION['pageid'];
	$fbid = $fbObject->getFBID();
	// var_dump($page_id);
	//getting the value of the mobapp_id
	$apptab_query_string = "select apptab_name, apptab_id from ".APPTAB_ID." where page_id = ".$page_id;
	$apptab_data = $db->execute_query($apptab_query_string);
	
	// var_dump($apptab_data);
	
	$apptabs = array();
	foreach ($apptab_data as $apptab) {	
		$apptabs[$apptab['apptab_name']] = $apptab['apptab_id'];
	}
	
	//getting the mobapp_id of the page
	$mobapp = $db->execute_query("SELECT m_app_id FROM " .PAGE. " WHERE page_id = ".$page_id);
	// var_dump($mobapp);
	$mobapp_id = $mobapp[0]['m_app_id'];


	$keyvalues = array();
	
	include_once('import_functions.php');

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//looping through the array to find out the selected options
		foreach ($_POST as $key => $value) {
			if($value == 1) {
				switch($key) {
					case "pageinfo":
						$keyvalues[] = "About";
						// $pageinfo = $fbObject->api($page_id."?fields=name,description,location,cover");
						$pageinfo = $fbObject->api($page_id."?fields=about,bio,description,phone,website,emails,press_contact,booking_agent,general_manager,cover,location");
						//getting the url of the thumbnail
						$picture_small = $fbObject->api($page_id."?fields=picture.type(square)");
						extract_page_info($pageinfo,$picture_small,$apptabs,$page_id);
					break;

					case "events":
						$event_data = $fbObject->fql("SELECT name,eid, start_time, end_time, location,description,venue ,ticket_uri,timezone,pic,pic_big,pic_cover FROM event WHERE eid IN ( SELECT eid FROM event_member WHERE uid =".$page_id." AND start_time >= '2000-08-24T02:07:43' ) ORDER BY start_time DESC");
						$keyvalues[] = "Events";
						extract_event_data($event_data,$apptabs);
						//print_r($events);
					break;

					case "posts":
						$keyvalues[] = "Fan Wall";
						// $post_data = $fbObject->api($page_id."/feed?fields=picture,place,message,id,source,created_time,story,type");
						$post_data = $fbObject->api($page_id."/feed?fields=picture,message,object_id,source,created_time,type&limit=5000");
						extract_post_data($post_data,$apptabs);
						//print_r($posts);
					break;

					case "photos":
						$keyvalues[] = "Photos";
						$fbalbums = $fbObject->api(array('method' => 'fql.query', 'query' => 'SELECT object_id, aid, name, link, photo_count from album WHERE owner = ' . $page_id . ' LIMIT 100000'));
						$photos = $fbObject->api(array('method' => 'fql.query', 'query' => 'SELECT object_id, src, caption, src_big, album_object_id, created from photo WHERE album_object_id IN (SELECT object_id from album WHERE owner = ' . $page_id . ') LIMIT 100000'));

						$newAlbums = array();
						foreach ($photos as $photo) {
							$newAlbums[$photo['album_object_id']][] = $photo;
						}

						$albumPhotos = array();
						foreach ($fbalbums as $album) {
							$album['photos'] = $newAlbums[$album['object_id']];
							$albumPhotos[] = $album;
						}
						// $album_data = $fbObject->api($page_id."?fields=albums.fields(name,id,photos.fields(source,picture,name,album))");
						extract_album_data($albumPhotos,$apptabs);
					break;

					case "videos":
						$keyvalues[] = "Videos";
						$video_data = $fbObject->api($page_id."?fields=videos.fields(id,description,from,source,icon,picture,created_time)");
						extract_video_data($video_data,$apptabs);
					break;
				} //switch case ends
			}	
		} //loop ends
	}

	$section = array();
	// checking if arrays are empty or not and if not adding them to the section array
	global $section;
	if(!empty($bio) ) {
		$section['Bio'] = $bio;
	}

	if( !empty($location) ) {	
		$section['Location'] = $location;
	}

	if(!empty($events) ) {
		$section['Event'] = $events;
		// $db->execute_query("UPDATE ".APPTAB_ID." set item_count = ".$event_count." where page_id=".$page_id." and apptab_name='Events' ");
		$db->execute_query("UPDATE ".APPTAB_ID." set item_count = ". count($events) ." where page_id=".$page_id." and apptab_name='Events' ");
		// $item_count['events'] = $event_count;
		$item_count['events'] = count($events);
	}

	if( !empty($albums) ) {
		$section['Album'] = $albums;

		$photoCount = 0;
		foreach ($albums as $album) {
			$photoCount += count($album['Photo']);
		}

		// $db->execute_query("UPDATE ".APPTAB_ID." set item_count = ".$album_count.", subitem_count = ".$photo_count." where page_id=".$page_id." and apptab_name='Photos' ");
		$db->execute_query("UPDATE ".APPTAB_ID." set item_count = ".count($albums).", subitem_count = ".$photoCount." where page_id=".$page_id." and apptab_name='Photos' ");
		// $item_count['album'] = $album_count;
		// $item_count['photo'] = $photo_count;

		$item_count['album'] = count($albums);
		$item_count['photo'] = $photoCount;
	}

	if( !empty($posts) ) {
		$section['Post'] = $posts;
		// $db->execute_query("UPDATE ".APPTAB_ID." set item_count = ".$post_count." where page_id=".$page_id." and apptab_name='Fan Wall' ");
		$db->execute_query("UPDATE ".APPTAB_ID." set item_count = ".count($posts)." where page_id=".$page_id." and apptab_name='Fan Wall' ");
		// $item_count['posts'] = $post_count;
		$item_count['posts'] = count($posts);
	}

	if( !(empty($videos)) ) {
		$section['Video'] = $videos;
		// $db->execute_query("UPDATE ".APPTAB_ID." set item_count = ".$video_count." where page_id=".$page_id." and apptab_name='Videos' ");
		$db->execute_query("UPDATE ".APPTAB_ID." set item_count = ".count($videos)." where page_id=".$page_id." and apptab_name='Videos' ");
		// $item_count['videos'] = $video_count;
		$item_count['videos'] = count($videos);
	}

	$data =  array();
	// $data['mobapp_id'] = $mobapp_id;
	$data['key'] = KEY;
	$data['mobapp_id'] = $mobapp_id;
	$data['section'] = $section;
	
	submitData($data);
	// die();

	//prepare string to set flags = true for data that has been imported
	$update_query = "update ".APPTAB_ID." set flag = 'true', timestamp = NOW() where page_id=".$page_id." and apptab_name in ('".implode("','", $keyvalues)."')";
	// echo $update_query;
	
	// print_r($string);
	
	$db->execute_query($update_query);	
?>