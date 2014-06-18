<?php
include_once 'hashing.php';
include_once 'db_connect.php';
include_once 'loginhandler.php';
include_once 'secure_session.php';

if(isset($_POST['loginSuccessUrl']) && isset($_POST['loginFailUrl']))
{
	$login_success = 'Location: ' . $_POST['loginSuccessUrl']; //'Location: ../gokken.php';
	$login_failed = 'Location: ' . $_POST['loginFailUrl']; //'Location: ../index.php?error=1';

	sec_session_start(); // Our custom secure way of starting a PHP session.


	if (isset($_POST['inputUser'], $_POST['p'])) {
	    $user = $_POST['inputUser'];
	    $password = $_POST['p']; // The hashed password.
	
	    if (login($user, $password, $mysqli) == true) {
	        // Login success 
	        header($login_success);
	        //echo "login succes";
	    } else {
	        // Login failed 
	        //header($login_failed);
	        echo "login failed";
	    }
	} else {
	    // The correct POST variables were not sent to this page. 
	    echo 'Invalid Request';
	}
}
?>