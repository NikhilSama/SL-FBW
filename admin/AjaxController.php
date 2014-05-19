<?php 
require_once("AjaxMethods.php");
$ajax = new AjaxMethods();

if($_SERVER["REQUEST_METHOD"] == "POST")
{

	switch ($_POST["param"]) 
	{
		case 'validateAdmin':
			echo $ajax->validateAdmin($_POST["name"],$_POST["pass"]);
			break;

		case 'logout':
			echo $ajax->logout();
			break;

		case 'removeRow':
			echo $ajax->removeRow($_POST['sid'],$_POST['id'],$_POST['cname']);
			break;

		case 'moveSubcategory':
			echo $ajax->moveSubcategory($_POST['sid'],$_POST['id'],$_POST['cname'],$_POST['cid']);
			break;
		
		case 'addCategory':
			echo $ajax->addCategory($_POST['catname'],$_POST['catId']);
			break;

		case 'addSubcategory':
			echo $ajax->addSubcategory($_POST['id'],$_POST['sname']);
			break;
			
		case 'showPageData':
			echo $ajax->showPageData($_POST['id']);
			break;
	}
}


 ?>