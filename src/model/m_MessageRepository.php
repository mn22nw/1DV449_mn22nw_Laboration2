<?php

namespace model;

require_once('base/Repository.php');

class MessageRepository {
	

public function getMessages() {
		//connect to db	
		$db = null;
		$db = connectDB();
		
		//Select messages
		
		$endtime = time() + 20;
                        $lasttime = $this->fetch('lastTime');

                        $curtime = null;

                        while(time() <= $endtime){
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
 
                                if($result){
									for ($i=0;  $i <count($result); $i++)
									{
	                                        $curtime = $result[$i]['insertDate'];
	                                }
									
								}		
															
								if($curtime !== $lasttime) {
										
	                                		return $result;
	                                	}	
								
                                else{
                                        sleep(1);
                                }
                
							}
	}
/**
* Called from AJAX to add stuff to DB
*/
public function addMessageToDB($message, $user) {
		$db = null;
		
		$db = connectDB();
		
		$q = "INSERT INTO messages (message, name, insertDate) VALUES('$message', '$user',  CURRENT_TIMESTAMP)";
		
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

 protected function fetch($name){
 				 $val = isset($_POST[$name]) ? $_POST[$name] : null;
                        return $val;
                }
 
}
