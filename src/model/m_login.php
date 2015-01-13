<?php
  namespace model;

  require_once('base/Repository.php');
  require_once("src/helper/helper.php");
  require_once('m_MessageRepository.php');
  
class Login {
    private $helper;
	private $MessageRepository;
    private static $uniqueID = "Login::UniqueID";
    private static $username = "Login::Username";
	private static $password = "Login::Password";

    public function __construct() {
	  $this->messageRepository = new \model\MessageRepository();
      $this->helper = new \helper\helper();
    }

 	/**
      * Check if user is logged in with session
	  *   
	  * @return boolval - Either the user is logged in or not
	  */
    public function userIsLoggedIn() {
    	
	 if (isset($_SESSION[self::$uniqueID])) {
        // Check if session is valid and that it is the same User Agent and remote adress
        if ($_SESSION[self::$uniqueID] === $this->helper->setUniqueID()) {
         
			 // check if user values in session is ok with database 
			
			 if (isset($_SESSION[self::$username]) && isset($_SESSION[self::$password])) {
			 	 $un = $_SESSION[self::$username];
				 $pw = $_SESSION[self::$password]; 
			
				$this->isUser($un, $pw); 
		 }
		  return true;
        }
	
      return false;
     }
 }
	
	/*
	 * Checks if user is valid!
	 * @return userid
	 * */
	public function isUser($un, $pw) {
	
		$db = connectDB();

		$q = "SELECT id FROM users WHERE username = '$un' AND password = '$pw'";
	
		$result;
		$stm;
		try {
			$stm = $db->prepare($q);
			$stm->execute();
			$result = $stm->fetchAll();
			if(!$result) {
				throw new \Exception("Could not find the user. <br />Wrong username or password.");
			}
		}
		catch(PDOException $e) {
			throw new \Exception("Error creating query: " .$e->getMessage());
			return false;
		}
		return $result;
		
	}
	/*
	 * Gets the salt for a user from the database
	 */
	function getSalt($username){
		$db = connectDB();
		
		$q = "SELECT salt FROM users WHERE username = '$username'";
	
		$result;
		$stm;
		try {
			$stm = $db->prepare($q);
			$stm->execute();
			$result = $stm->fetchAll();
		}
		catch(PDOException $e) {
			throw new Exception("Error creating query: " .$e->getMessage());
			return false;
		}
	
		return $result;
	}
	

    /**
      * Log in the user
      *
      * @param string $postUsername
      * @param string $postPassword
      * @return boolval
      */
    public function logIn($postUsername, $postPassword) {
   
        // Make the inputs safe to use in the code
     	$un = $this->helper->makeSafe($postUsername);     // TODO - already safe in view !remove?
    	$pw =  $this->helper->makeSafe($postPassword);

	  // If the provided username/password is empty 
      if (empty($postUsername)) {
        $this->helper->setAlert("Användarnamn saknas");
        return false;
      } else if (empty($postPassword)) {
        $this->helper->setAlert("Lösenord saknas");
        return false;
      }
	  
      // Check against SQLITE database if the correct username and password is provided
      try {
      	
		$salt = $this->getSalt($un);
		$password = hash('SHA256', $salt . $pw);
		
		$this->isUser($un, $password); 
	  }
	  catch (\Exception $e){
	  	$this->helper->setAlert($e->getMessage());  
	  	return false;
	  }
		
		//sets session for the user
        $_SESSION[self::$uniqueID] = $this->helper->setUniqueID();
        $_SESSION[self::$username] = $un;
		$_SESSION[self::$password] = $password;


	   return true;
    }
    /**
      * Log out the user
      *
      * @return boolval
      */
    public function logOut() {
    	
		 
      // Check if you really are logged in
      if (isset($_SESSION[self::$uniqueID])) {
        unset($_SESSION[self::$uniqueID]);
		 $_SESSION = array();
		session_destroy(); 
		
        // Set alert message
        $this->helper->setAlert("Du har nu loggat ut.");

        return true;
      }
    }
  }
