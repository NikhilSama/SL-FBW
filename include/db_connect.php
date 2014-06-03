<?php
error_reporting(E_ERROR | E_PARSE);
include_once "constants.php";

/*
*	Class to connect to DB
*	Use as ----
*	$db = new db_connect();
*	
*	Methods -
*	execute_query 		-		Returns a two dimensional array of result set.
*	single_query 		- 		Returns a single dimensional array containing only first row of result set
*	aggregate_query 	- 		Returns only first row first column of the result set.Can be used for aggregate functions.. 
*	execute_nonquery	-		Returns the number of rows affected by a DML query....
*/

class db_connect
{
	private $dbHandler;
	private $connHandler;
	function __construct()
	{

		$this->connHandler=mysql_connect(SERVER,USERNAME,PASSWORD);
		if($this->connHandler) {
			$this->dbHandler = mysql_select_db(DB,$this->connHandler);
			mysql_query ("set character_set_results='utf8'");
			if(!$this->dbHandler)
				echo "Could not connect to DB ".DB;
		} else {
			echo "Could not connect to server.";
		}
		
		mysql_set_charset('utf8');
	}
	//Returns a two dimensional array
	public function execute_query($query)
	{
		$dataHandler = mysql_query($query) or die(mysql_error());
		$dd=array();
		while($row = mysql_fetch_assoc($dataHandler))
		{
			$dd[] = $row;
		}
		return $dd;
	}
	//Returns a one dimensional array only containing the first row of the result set
	public function single_query($query)
	{
		$dataHandler = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_assoc($dataHandler);
		return $row;
	}
	//Returns only the first row first column of the result set. Use for aggregate functions
	public function aggregate_query($query)
	{
		$dataHandler = mysql_query($query) or die(mysql_error());
		$row = mysql_fetch_array($dataHandler);
		return $row[0];
	}
	//Used to run DML queries. It returns the number of rows affected..
	public function execute_nonquery($query) 
   	{
       mysql_query($query) or die(mysql_error());
       return mysql_affected_rows();
   	}
}
?>