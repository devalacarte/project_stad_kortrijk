<?php
    include_once '../includes/secure_session.php';
    include_once '../includes/process_login.php';
    include_once 'ajaxCRUDInitializer.php';

    sec_session_start();
?>

<!DOCTYPE html>

<html lang="nl">
    <head>
        <meta charset="utf-8" />
        <title>Bar Management - Cocktail Party</title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/button.css">
        <link rel="stylesheet" type="text/css" href="../css/fonts.css">
        <link rel="stylesheet" type="text/css" href="../css/common.css">
        <link rel="stylesheet" type="text/css" href="../css/managementCommon.css">
        <link rel='stylesheet' href='css/index.css' />
    </head>
    <body>
        <?php 
            //Check if session is active
            if(!login_check($mysqliConn, "adminAccounts"))
                header("Location: login.php");
        ?>

        <div id='wrapper'>
            <div id='content'>
                <!--Header image-->
                <header>
                    <img alt="Cocktail party" src="../images/logo.png" />
                </header>
       
                <!--Log out button-->
                <form action='../includes/logouthandler.php'>
                    <input type='submit' class="btnLogOut btn btn-default " type="button" id="btnLogOut" value='log out' />
                    <input type='hidden' value='../BetManagementSite/login.php' name='loginUrl' />
                </form>

                <div class='contentFadeIn'></div>
                <div class='contentContainer'>
                    <h2>Gokkers beheren</h2>

                    <!--Ajax CRUD table-->
                    <div id=divAccountsTable>
                        <?php $tblAccounts->showTable(); ?>
                    </div>
                </div>
                <div class='contentFadeOut'></div>

            </div>
            <!--Footer-->
            <div id="footer">
                <img src="../images/footer.png" alt="footer">
                <span>Copyright &#169; 2014 - Cocktail Party</span>
            </div>
        </div>
    </body>
</html>
