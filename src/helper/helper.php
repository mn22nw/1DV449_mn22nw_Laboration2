<?php
  namespace helper;

  class Helper {
    private static $sessionAlert = "sessionAlert";
	private static $ceatedUsername = "ceatedUsername";    
	
	function sec_session_start() {
	
        $session_name = 'sec_session_id'; // Set a custom session name
        $secure = false; // Set to true if using https.
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        $httponly = true; // This stops javascript being able to access the session id.
        session_set_cookie_params(3600, $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        session_regenerate_id(true); // regenerated the session, delete the old one.            
}

    /**
      * Get an alert from the session alert system
      * if there are any messages and the deletes it
      * from the session.
      *
      * @return string - The message
      */
    public function getAlert() {
      if (isset($_SESSION[self::$sessionAlert])) {
        $ret = $_SESSION[self::$sessionAlert];
        unset($_SESSION[self::$sessionAlert]);
      } else {
        $ret = "";
      }
      return $ret;
    }

    /**
      * Set an alert to the session alert system
      *
      * @param string $string - The message to save
      * @return boolval
      */
    public function setAlert($string) {
      $_SESSION[self::$sessionAlert] = $string;
      return true;
    }

	public function getCreatedUsername() {
      if (isset($_SESSION[self::$ceatedUsername])) {
        $ret = $_SESSION[self::$ceatedUsername];
        unset($_SESSION[self::$ceatedUsername]);
      } else {
        $ret = "";
      }

      return $ret;
    }


	 public function setCreatedUsername($string) {
      $_SESSION[self::$ceatedUsername] = $string;
      return true;
    }


    /**
      * Makes the param safe from html and stuff...
      *
      * @param string $var - The dirty string
      * @return string - The cleaned up string
      */
    public function makeSafe($var) {
      $var = trim($var);
      $var = stripslashes($var);
      $var = htmlentities($var);
      $var = strip_tags($var);

      return $var;
    }

    /**
      * Generate a unique-ish identifier
      *
      * @return string - The identifier encoded in sha1
      */
    public function setUniqueID() {
      return sha1($_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"]);
    }

    /**
      * Encrypts a given string
      *
      * @return string - The identifier encoded in sha1
      */
    public function encryptString($var) {
      return sha1($var);
    }
  }
