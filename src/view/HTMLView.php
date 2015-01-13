<?php
  namespace view;

class HTMLView {

    /**
      * Creates a HTML page. I blame the indentation
      * on webbrowser and PHP.
      *
      * @param string $title - The page title
      * @param string $body - The middle part of the page
      * @return string - The whole page
      */
    public function echoHTML($title = "Login", $css = null, $body, $scripts = null) {
      if ($body === NULL) {
        throw new \Exception("HTMLView::echoHTML does not allow body to be null.");
      }

      echo "<!doctype html>
<html lang='sv'>
	<head>
	  <meta charset='utf-8'>
	    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
	    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
	 	 <title>".$title."</title>
	 	 ". $css ."
	</head>
	<body>
	<div id='page'>
	  ".$body.
	  "</div>"
	  . $scripts ."
	</body>
</html>";
    }
  }