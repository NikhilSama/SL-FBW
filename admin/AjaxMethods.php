<?php 
require_once "../include/db_connect.php";
class AjaxMethods
{
	private $db;
	public function __construct()
	{
		session_start();
		// $this->db = new db_connect();
	}

	public function validateAdmin($name,$pass)
	{
		if($name=="admin" && $pass== "admin123")
		{
			$_SESSION["loggedIn"]="Yes";
			return "1";
		}
		else 
			return "0";
	}
	
	public function logout()
	{
		session_destroy();
	}


	public function removeRow($sid,$id,$cname)
	{
		//$id and $cname represent id of the row and the name of the subcategory to be removed
		$this->db = new db_connect();
		$delete_query = "DELETE from ".SUBCATEGORY." where category_id =".$id." and id = ".$sid." and subcategory_name='{$cname}'";
		$this->db->execute_query($delete_query);

		//checking if the entry has been deleted
		if(mysql_affected_rows())
		{
			return 1;
		} else
		{
			//when there was en error while completing the request
			return "error";
		}
	}

	public function moveSubcategory($sid,$id,$name,$cid)
	{	

		$this->db = new db_connect();
		$update_query = "UPDATE ".SUBCATEGORY." set category_id =".$cid." where category_id=".$id." and id=".$sid." and subcategory_name='{$name}'";
		$this->db->execute_query($update_query);
		
		//checking if the entry has been updated
		if(mysql_affected_rows())
		{
			return 1;
		} else
		{
			//when there was en error while completing the request
			return "error";
		}
	}

	public function addCategory($name,$id)
	{	
		
		$this->db = new db_connect();
		$category_query = "SELECT * from ".CATEGORY." where category_name= '{$name}' " ;
		$this->db->execute_query($category_query);
		
		if( mysql_affected_rows() )
		{	
			//this means there is already a category with similar cartegory name
			return "errorname";
		}

		$category_id_data = $this->db->execute_query("SELECT * from ".CATEGORY." where category_id=".$id);
		if( mysql_affected_rows() )
		{	
			//this means there is already a category with similar cartegory id
			return "errorid";
		}
		
		
		$name = addslashes($name);
		$sql = "INSERT into ".CATEGORY."(category_name,category_id) values('{$name}',$id)";

		$this->db->execute_query($sql);

		//checking if the entry has been created
		if(mysql_affected_rows())
		{
			return $id;
		}
		
	}

	public function addSubcategory($id, $name)
	{	
		$name = addslashes($name);
		$this->db = new db_connect();
		$sql = "INSERT into ".SUBCATEGORY."(category_id,subcategory_name) values($id,'{$name}')";
		$this->db->execute_query($sql);

		//checking if the entry has been created
		if( mysql_affected_rows() )
		{
			return mysql_insert_id();
		} else 
		{
			//when there was en error while completing the request
			return "error";
		}
	}

	public function showPageData($id)
	{
		$this->db = new db_connect();
		$fetchQuery = ("SELECT page_id,page_name from ".PAGE." where fbid=".$id);
		$pageData = $this->db->execute_query($fetchQuery);
		$container = "<div class='container row-fluid'><img class='arrowImg' src='images/arrow.png'><img class='closeImg' src='images/close.png'>";
		foreach ($pageData as $data) 
		{	
			$page_id = $data['page_id'];
			//creating divs for the image of the page and other information such as what items have been imported
			$container .= "<div class='pagedata row-fluid'>";
			$container .= "<div class='span2 pageImg'>";
			$container .= "<img src='http://graph.facebook.com/".$page_id."/picture'></div>";
			$container .= "<div class='span10 pageinfo'>";
			

			$apptab_data = $this->db->execute_query("SELECT apptab_name,flag from ".APPTAB_ID." where page_id=".$data['page_id']);
			$apptab = array();
			foreach ($apptab_data as $apptabs) 
			{
				if( $apptabs['flag'] == 'true' )
				{
					$apptab[$apptabs['apptab_name']] = "imported";
				} else
				{
					$apptab[$apptabs['apptab_name']] = "notImported";
				}
				
			}

			$container .= "<h4>".$data['page_name']."</h4>";
			$container .= "<span class='span2 about ".$apptab["About"]."'></span>";
			$container .= "<span class='span2 photos ".$apptab["Photos"]."'></span>";
			$container .= "<span class='span2 events ".$apptab["Events"]."'></span>";
			$container .= "<span class='span2 posts ".$apptab["Fan Wall"]."'></span>";
			$container .= "<span class='span2 videos ".$apptab["Videos"]."'></span>";
			$container .= "</div></div>";
		}
		return $container;

	}

}	
 ?>
