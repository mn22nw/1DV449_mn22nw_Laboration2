<?php

namespace model;

require_once('base/Repository.php');

class MessageRepository {
	

public function getMessages() {
		$db = null;
	
		$db = connectDB();
		
		$q = "SELECT * FROM messages";
		
		$result;
		$stm;	
		try {
			$stm = $db->prepare($q);
			$stm->execute();
			$result = $stm->fetchAll();
		}
		catch(\PDOException $e) {
			echo("Error creating query: " .$e->getMessage());
			return false;
		}
		
		if($result)
			return $result;
		else
		 	return false;
	}

/**
* Called from AJAX to add stuff to DB
*/
public function addMessageToDB($message, $user) {
		$db = null;
		
		$db = connectDB();
		
		$q = "INSERT INTO messages (message, name) VALUES('$message', '$user')";
		
		try {
			$addStm = $db->prepare($q);
			$addStm->execute();
			return $message;
		}
		catch(\PDOException $e) {}
		
		$q = "SELECT * FROM users WHERE username = '" .$user ."'";
		$result;
		$stm;
		try {
			$stm = $db->prepare($q);
			$stm->execute();
			$result = $stm->fetchAll();
			if(!$result) {
				return "Could not find the user";
			}
		}
		catch(\PDOException $e) {
			echo("Error creating query: " .$e->getMessage());
			return false;
		}
		// Send the message back to the client
		
		
		echo "Message saved by user: " .json_encode($result);
		
	}
}
