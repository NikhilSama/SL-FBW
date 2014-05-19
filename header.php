<?php 
session_start(); 
ob_start();
header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header("Content-Type: text/html; charset=UTF-8");
require_once ("include/constants.php");
require_once("include/FBMethods.php");
require_once("include/db_connect.php");

?>