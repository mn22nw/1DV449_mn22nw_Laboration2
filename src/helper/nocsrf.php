<?php 

 if(isset($_GET['function']) && $_GET['function'] == 'generateToken') {
 	generateToken();
	$token = $_SESSION["token"];
	echo $token;
 }


function generateToken() {
	$_SESSION["token"] = md5(uniqid(mt_rand(), true));
}

