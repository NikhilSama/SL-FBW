<?php 
	//here we only need to check the events and the photos rest of the updates are handled by the realtime updates
	require_once ("header.php");
	error_reporting(E_ALL);
	set_time_limit(0);
	
	$fbObject = new FBMethods();
	$db = new db_connect();
	$albums = array();
	$events = array();
	$section = array();
	$videos = array();
	$posts = array();
	$location = array();
	$bio = array();

	require_once("functions.php");

	$imported_data  = $db->execute_query("SELECT * from ".APPTAB_ID." where page_id = " . $_REQUEST['page_id'] . " and flag = 'true' and update_flag = 'true' and (apptab_name = 'Events' or apptab_name = 'Photos' or apptab_name = 'Videos' or apptab_name = 'About' or apptab_name = 'Fan Wall')") ;

	//checking wheather the imported data is event or photos
	foreach ($imported_data as $apptab_data) {
		$mobapp_id = $apptab_data['mobapp_id'];
		//checking whether the event that has been imported is photo or event
		$page_id = $apptab_data['page_id'];
		// var_dump($page_id);
		if( $apptab_data['apptab_name'] == 'Photos' ) {	
			//when the change type is images
			//function to update the photos on the snaplion website in case an update takes place
			try {
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
				extractPhotoUpdate($albumPhotos, $apptab_data);
				checkData($page_id);
			} catch (Exception $e) {

			}
		} else if( $apptab_data['apptab_name'] == 'Events' ) {	
			//when changes take place in events
			// $event_data = $fbObject->fql("SELECT name, eid, start_time, end_time, location,description,venue ,ticket_uri,timezone,pic FROM event WHERE eid IN ( SELECT eid FROM event_member WHERE uid =".$page_id." AND start_time >= '2000-08-24T02:07:43' ) ORDER BY start_time DESC");
			try {
				$event_data = $fbObject->fql("SELECT name,eid, start_time, end_time, location,description,venue ,ticket_uri,timezone,pic,pic_big,pic_cover FROM event WHERE eid IN ( SELECT eid FROM event_member WHERE uid =".$page_id." AND start_time >= '2000-08-24T02:07:43' ) ORDER BY start_time DESC");
				
				extractEventUpdate($event_data,$apptab_data);
				checkData($page_id);
			} catch (Exception $e) {

			}
		} else if( $apptab_data['apptab_name'] == 'Videos' ) {	
			//when the change type is video
			try {
				$video_data = $fbObject->api($page_id."?fields=videos.fields(id,description,from,source,icon,picture,created_time)");
				extractVideoUpdate($video_data,$apptab_data);
				checkData($page_id);
			} catch (Exception $e) {

			}
		} else if( $apptab_data['apptab_name'] == 'About' ) {
			// $page_data = $fbObject->api($page_id."?fields=name,description,location,cover");
			try {
				$page_data = $fbObject->api($page_id."?fields=about,bio,description,phone,website,emails,press_contact,booking_agent,general_manager,cover,location");
				
				$picture_small = $fbObject->api($page_id."?fields=picture.type(square)");
				extract_page_info($page_data,$picture_small,$apptab_data);
				checkData($page_id);
			} catch (Exception $e) {

			}
		} else if( $apptab_data['apptab_name'] == 'Fan Wall' ) {
			try {
				$post_data = $fbObject->api($page_id."/feed?fields=picture,message,object_id,source,created_time,type&limit=5000");

				extract_post_data($post_data,$apptab_data);
				checkData($page_id);
			} catch (Exception $e) {

			}
		}
	}
?>