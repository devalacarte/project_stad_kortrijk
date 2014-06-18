<?php
include_once 'includes/db_connect.php';
include_once 'includes/loginhandler.php';

sec_session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Secure Login: Protected Page</title>
    <link rel="stylesheet" href="css/Main.css"/>
    <link rel="stylesheet" href="css/button.css"/>
</head>
<body>
<button class="visible-xs btn btn-default btnVotes" type="button"><span>Stem</span></button>
<button class="hidden-xs center-vertical btn btn-default btnVotes disabled" type="button"><span>Stem</span></button>

</body>
</html>