<?php

require_once("src/model/m_MessageRepository.php");


$messageRepository = new \model\MessageRepository();
/*
* It's here all the ajax calls goes
*/

if(isset($_POST['action']) && !empty($_POST['action'])) {
	
	if ($_POST['action'] == "getMessages")
		  	   
		  	   	echo(json_encode($messageRepository->getMessages()));
		    }


if(isset($_GET['function'])) {

	 
	
	//check if token is alright when adding message!
	
	 if (isset($_GET['csrf_token'])) 
		 {
		 	// TODO - why is $_SESSION["token"] not set??
		   //  if (isset($_SESSION["token"]) && $_GET['csrf_token'] == $_SESSION["token"]) {

				// token is okey! Do functions
		        if($_GET['function'] == 'add') {
				    $name = $_GET["name"];
					$message = $_GET["message"];
					$messageRepository->addMessageToDB($message, $name);
			    }
			// }
     } 
}
