<?php
//function logOut($loc="")
//{
include 'secure_session.php';
sec_session_start();
$_SESSION = array();

// get session parameters
$params = session_get_cookie_params();

// Delete the actual cookie.
setcookie(session_name(),
    '', time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]);

// Destroy session
session_destroy();

$loc = "";
if(isset($_REQUEST["loginUrl"]))
    $loc = $_REQUEST["loginUrl"];

if ($loc == "") {
    header('Location: ../login.php');
} else {
    header('Location: ' . $loc);
    //  }
}?>