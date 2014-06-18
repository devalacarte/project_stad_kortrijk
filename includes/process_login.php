<?php
include_once 'hashing.php';
include_once 'db_connect.php';
include_once 'loginhandler.php';
include_once 'secure_session.php';

if(isset($_POST['loginSuccessUrl']) && isset($_POST['loginFailUrl']))
{
	$login_success = 'Location: ' . $_POST['loginSuccessUrl']; //'Location: ../gokken.php';
	$login_error = 'Location: ' . $_POST['loginFailUrl']; //'Location: ../index.php?error=1';
    
    //Default accounts db = accounts; check if the admin accounts table should be used
    $accountsTable = "accounts";
    if(isset($_POST['isAdmin']) && $_POST['isAdmin'] == 1)
        $accountsTable = "adminAccounts";

	sec_session_start(); // Our custom secure way of starting a PHP session.


	if (isset($_POST['inputUser'], $_POST['p'])) {
	    $user = $_POST['inputUser'];
	    $password = $_POST['p']; // The hashed password.
	
        $loginResult = login($user, $password, $mysqli, $accountsTable);
	    if ($loginResult == 1) {
	        // Login success 
	        header($login_success);
	        //echo "login succes";
	    } else if($loginResult == 0) {
	        // Login failed 
            $headerLocation = $login_error . "=1";
	        header($headerLocation);
	    }
        else {
            //Brute force protection
            $headerLocation = $login_error . "=2";
            header($headerLocation);
        }
	} else {
	    // The correct POST variables were not sent to this page. 
	    echo 'Invalid Request';
	}
}
?>