<?php 


require_once ("header.php");
//this is the case when the response arrives from snaplion side

if(isset($_GET) && $_GET['status'] == 1 && isset( $_GET['mobapp_id'] ) )
{
	$mobapp_id = $_GET['mobapp_id'];
	$db = new db_connect();
	$db->execute_query("UPDATE ".PAGE." set payment_flag = 1 where m_app_id=".$mobapp_id);
	$page = $db->single_query("SELECT page_id FROM ".PAGE." where m_app_id=".$mobapp_id);
	$page_id = $page['page_id'];
	header("location:https://www.facebook.com/$page_id/?id=$page_id&sk=app_".APPID);



}






 ?>