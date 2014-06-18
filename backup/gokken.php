<!DOCTYPE html>
<?php include 'includes/secure_session.php';
sec_session_start(); ?>
<html>
<head>
    <meta charset="utf-8">
    <title>Gokken</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/Main.css">
</head>

<body class="container">
<div class="col-xs-offset-2 col-xs-10">
    <a class="btn btn-primary" id="logout" href="includes/logouthandler.php">log out</a>
    <!--<button name="logout" type="button" class="btn btn-primary" id="logout">Logout</button>-->
    <p></p>

    <p></p>

    <p></p>

    <p></p>

    <p>session info test</p>

    <p><?php echo "HTTP USER AGENT: " . $_SERVER['HTTP_USER_AGENT']; ?></p>

    <p><?php echo "USER ID: " . $_SESSION['user_id']; ?></p>

    <p><?php echo "USER NAME: " . $_SESSION['username']; ?></p>

    <p><?php echo "LOGIN STRING: " . $_SESSION['login_string']; ?></p>

    <p>des is ofc ma ne test en we gaan vooral ni eerst gaan kijke of da er wel eenwaarde in de sessies of cookies
        zitte</p>

</div>
</body>


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>


</html>