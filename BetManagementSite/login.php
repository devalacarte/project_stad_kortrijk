<?php 
    include_once '../includes/secure_session.php'; 
 ?>

<!DOCTYPE html>
<html lang="nl">
    <head>
        <meta charset="utf-8" />
        <title>Log in - Cocktail Party Bar Management Site</title>
        <script src="../js/jquery.min.js"></script>
        <script src="../js/sha512.js"></script>
        <script src="../js/login.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/button.css">
        <link rel="stylesheet" type="text/css" href="../css/fonts.css">
        <link rel="stylesheet" type="text/css" href="../css/common.css">
        <link rel="stylesheet" type="text/css" href="../css/managementCommon.css">
        <link rel="stylesheet" href="../css/managementLogin.css" />
    </head>
    <body>        
        <div id='wrapper'>
            <div id='content'>
                <!--Header image-->
                <header>
                    <img alt="Cocktail party" src="../images/logo.png" />
                </header>

                <h2>Beheer cocktailbar</h2>

                <?php
                    //Handle wrong pin
                    if(isset($_REQUEST["error"]))
                    {
                        $errorText = "Foute pincode.";
                        if($_REQUEST["error"] == 2) 
                            $errorText = "Te veel foutieve pogingen. Probeer opnieuw in vijf minuten.";
                        ?>
                        <div id="divLoginFail"><?php echo $errorText; ?></div>
                        <!--Hide message after time-->
                        <script>
                            setTimeout(function ()
                            {
                                $("#divLoginFail").hide();
                            }, 5000);
                        </script>
                        <?php
                    }
                ?>

                <form method='post' action='../includes/process_login.php' >
                    <input type='password' name="inputPassword" id="inputPassword" placeholder="PIN" class="form-control"/>
                    <button type='submit' id='submit' class="btn btn-default">Log in</button>
                    <input type='hidden' name="inputUser" id="inputUser" value='barAdmin' />
                    <input type='hidden' name="loginSuccessUrl" value='../BetManagementSite/index.php' />
                    <input type='hidden' name="loginFailUrl" value='../BetManagementSite/login.php?error' />
                    <input type='hidden' name="isAdmin" value="1" />
                </form>
            </div>
            <!--Footer-->
            <div id="footer">
                <img src="../images/footer.png" alt="footer">
                <span>Copyright &#169; 2014 - Cocktail Party</span>
            </div>
        </div>
    </body>
</html>
