<?php
  namespace controller;

  require_once("src/model/m_login.php");
  require_once("src/view/v_login.php");
  require_once("src/helper/helper.php");
  
  
  
  class Login {
  	
    private $model;
    private $view;
    private $helper;

    public function __construct() {
      $this->model = new \model\Login();
      $this->view = new \view\Login($this->model);

      $this->helper = new \helper\Helper();
    }

    public function viewPage() {
    $messages = "";
	
      // Check if user is logged in with session 
      if ($this->model->userIsLoggedIn()) {

	
        // Check if user pressed log out
        if ($this->view->LogoutAttempt()) {
          // Then log out
          if ($this->model->logOut()) {
            // And then present the login page
            return $this->view->showLogin();
          }
        }

      // Logged in and did not press log out, then show the message page
      return $this->view->showMessages();
      } 
      
      else {
        // Check if the user did press login
        if ($this->view->LoginAttempt()) {  
          
	          //CHECK IN MODEL IF LOGIN IS CORRECT AND SET A SESSION
	         if ($this->model->logIn($this->view->getUsernameInput(), $this->view->getPasswordInput()))
	         {	
	          // Then show the logout page  
	          return $this->view->showMessages();
			 }
		 
	        // Else show the login page
	        return $this->view->showLogin();
      	}
    }
	  return $this->view->showLogin(); 
  }
 }
