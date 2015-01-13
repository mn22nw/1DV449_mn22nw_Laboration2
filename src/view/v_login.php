<?php
  namespace view;

  require_once("src/controller/c_login.php");
  require_once("src/helper/helper.php");
  require_once("src/helper/nocsrf.php");
  
  class Login {
    private $model;
    private $helper;

    private static $getLogin  = "login";
	private static $uniqueID  = "Login::UniqueID";
	private static $loginBtn  = "Login:loginBtn";
	private static $username = "Login::Username";
	private static $password = "Login::Password";


    public function __construct(\model\Login $model) {
      $this->model = $model;
      $this->helper = new \helper\Helper();
    }

    /**
      * A view for users not logged in
      *
      * @return string - The page log in page
      */
    public function showLogin() {
	  $username =  $this->helper->getCreatedUsername();
	 
	  if (empty($username))
	    $username = empty($_POST[self::$username]) ? '' : $_POST[self::$username];

      $ret = " <div class='container'><h2> Labby Message - Logga In</h2>

      <h3>Ej inloggad.</h3>";
      $ret .= "<div class='errorMessages'>" . $this->helper->getAlert() . "</div>";
		
	// Generate CSRF token to use in form hidden field
	 generateToken();
		
      $ret .= "
	  <form action='?" . self::$getLogin . "' method='post'>
	  	<input type='hidden' name='csrf_token' value='" .  $_SESSION["token"] ."'>
	    <input type='text' name='". self::$username . "' placeholder='Användarnamn' value='".$username."' maxlength='30' class='form-control'>
	    <input type='password' name='". self::$password. "' placeholder='Lösenord' value='' maxlength='30' class='form-control' >
	    <input type='submit' value='Logga in' name='". self::$loginBtn. "' class='btn btn-lg btn-primary btn-block'>

	  </form></div>";

      return $ret;
    }

    /**
      * A view for users logged in
      *
      * @return string - The page log out page
      */
    public function showMessages() {   
     // Generate CSRF token to use in form hidden field
     generateToken();
	 
	 $ret = "<div id='container'>
        	<div id='logo'><img src='pic/logo.png' alt='logo'/></div>
            
            <div id='messageboard'>
                <a href='?logout=true&csrf_token=" . $_SESSION["token"] .  "' id='buttonLogout'>Logga ut</a>
                
                <div id='messagearea'></div>
                
                <p id='numberOfMess'>Antal meddelanden: <span id='nrOfMessages'>0</span></p>
                <p class='label'>Name:</p><br /> <input id='inputName' type='text' name='name' class='nameInput'/><br />
                 <p class='label'>Message:</p><br />
                <textarea name='mess' id='inputText' cols='55' rows='6'></textarea>
                <div id='sendBtnDiv'> <input class='btn btn-primary' type='button' id='buttonSend' value='Write your message' /></div>
                <span class='clear'>&nbsp;</span>
            </div>
        </div>
		<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
		<script src='js/MessageBoard.js'></script>
		<script src='js/bootstrap.js'></script>
		<script src='js/Message.js'></script>
        
        <!-- This script is running to get the messages -->
			<script>
				$(document).ready(function() {
					//do long polling before this
					var url = '../db.db';
					MessageBoard.longPolling(url);
				});
			</script> ";
	   return $ret;
 
    }

    /**
      * Checks if user submitted the form
      *
      * @return boolval
      */
     
    public function LoginAttempt() {
    	
      if (isset($_GET[self::$getLogin]) && isset($_POST[self::$loginBtn])) {
        			
		       if (isset($_POST['csrf_token']) && $_POST['csrf_token'] == $_SESSION["token"])
		       {
		       	 // Token is fine! 
		      	 return true;
		       } 
      }
      return false;	
    }

    public function LogoutAttempt() {
    	 
      if (isset($_GET['logout'])) {
      		
      	 if (isset($_GET["csrf_token"]) && $_GET["csrf_token"] == $_SESSION["token"]) {
			         // Token is fine!
			        return true;
   				 }	 		
      }
      return false;
    }
	
	public function getUsernameInput(){
		if($this->LoginAttempt()) {		
					//makes input safe to use in the code
			return $this->helper->makeSafe($_POST[self::$username]);
		}
	}
	
	public function getPasswordInput(){
		if($this->LoginAttempt()) {
			return $this->helper->makeSafe($_POST[self::$password]);
		}
	}		

  }
