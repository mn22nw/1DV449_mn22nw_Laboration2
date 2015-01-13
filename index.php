<?php
  
  require_once("src/view/HTMLView.php");
  require_once("src/controller/c_login.php");
  require_once("src/helper/helper.php");
    $helper = new \helper\Helper();
	$helper->sec_session_start();
  
 	error_reporting(E_ALL & ~E_NOTICE);
  

  	$LoginController = new \controller\Login();
	$body = $LoginController->viewPage();

  	$view = new \view\HTMLView();
	  
	$css = '<link href="css/bootstrap.css" rel="stylesheet">
    		<link rel="stylesheet" type="text/css" href="css/dyn.css" />';
	$script ='<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
   			  <script src="js/bootstrap.js"></script>';
		  
	$view->echoHTML("Messy Labbage",$css, $body, $script);
