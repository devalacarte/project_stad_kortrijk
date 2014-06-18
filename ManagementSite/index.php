<?php
    // include error reporting
    include_once '../includes/header.php';

    // include functions
    include_once '../includes/functions.php';
    // create new instance of functions-class
    $db = new DBFunctions;

    // get parameters
    $parameters = $db->getParameters();

    include_once '../includes/secure_session.php';
    include_once '../includes/process_login.php';
    include_once 'ajaxCRUDInitializer.php';

    sec_session_start();
?>

<!DOCTYPE html>

<html lang="nl">
    <head>
        <meta charset="utf-8" />
        <title>Cocktail Party Management Site</title>
        <script src="../js/jquery.min.js"></script>
        <script src="../js/sha512.js"></script>
        <script src="../js/countdown.js"></script>
        <script src="barAdminFormFunctions.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/button.css">
        <link rel="stylesheet" type="text/css" href="../css/fonts.css">
        <link rel="stylesheet" type="text/css" href="../css/common.css">
        <link rel="stylesheet" type="text/css" href="../css/managementCommon.css">
        <link rel="stylesheet" type="text/css" href="../css/countdown.css">
        <link rel="stylesheet" href="css/index.css" />
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

                <!--Countdown-->
                <div id="countdown">
                    <span class="countdown-medium-number tmr-h1">0</span>
                    <span class="countdown-medium-number tmr-h2">0</span>
                    <span class="countdown-medium-separator">:</span>
                    <span class="countdown-medium-number tmr-m1">0</span>
                    <span class="countdown-medium-number tmr-m2">0</span>
                    <span class="countdown-medium-separator">:</span>
                    <span class="countdown-medium-number tmr-s1">0</span>
                    <span class="countdown-medium-number tmr-s2">0</span>
                </div>
       
                <!--Log out button-->
                <form action='../includes/logouthandler.php'>
                    <input type='submit' class="btnLogOut btn btn-default " type="button" id="btnLogOut" value='log out' />
                    <input type='hidden' value='../ManagementSite/login.php' name='loginUrl' />
                </form>

                <div class='contentFadeIn'></div>
                <div class='contentContainer'>
                    <!-- Change passwords -->
                    <div id=divChangeBarAdminPassContainer>
                        <h2 id="btnToggleBarAdminPass">Wachtwoorden veranderen <img  class="expandChevron" alt="Expandeer/Verklein"
                             src="images/chevronRight.png" /></h2>
                        <div id="divCollapsePaswordsContainer" style="display: none">
                            <!-- Change bar admin pass -->
                            <div id="divChangeBarPassContainer">
                                <h3>Barbeheerder pincode veranderen</h3>
                                <form method='post' action='../includes/registerHandler.php' id='frmChangeBarAdminPass' >
                                    <!-- PASS -->
                                    <div id="password">
                                        <label for="inputPassword">Nieuwe PIN</label>

                                        <div>
                                            <input name="inputPassword" type="password" class="form-control" id="inputPassword" placeholder="PIN">
                                        </div>
                                    </div>

                                    <!-- CONFIRM -->
                                    <div id="confirm" >
                                        <label for="inputConfirm">Bevestig PIN</label>

                                        <div>
                                            <input name="inputConfirm" type="password" class="form-control" id="inputConfirm" placeholder="Bevestig PIN">
                                        </div>
                                    </div>

                                    <!-- SUBMIT -->
                                    <button name="submit" type="submit" id="submit" class="btn btn-default">Verander PIN</button>

                                    <div id="divBarAdminPassChangeSuccess" style="display: none">
                                        Pincode aangepast.
                                    </div>
                                </form>
                            </div>
                            <div id="divChangeAdminPassContainer">
                                <!-- Change main admin pass -->
                                <h3>Algemeen beheerder wachtwoord veranderen</h3>
                                <form method='post' action='../includes/changeAdminPass.php' id='frmChangeMainAdminPass' >
                                    <!-- Old password -->
                                    <div>
                                        <label for="mainAdminOldPass">Huidig wachtwoord</label>

                                        <div>
                                            <input name="mainAdminOldPass" type="password" class="form-control" id="mainAdminOldPass" 
                                                placeholder="Huidig wacthwoord">
                                        </div>
                                    </div>
                                    <!-- New password -->
                                    <div>
                                        <label for="mainAdminNewPass">Nieuw wachtwoord</label>

                                        <div>
                                            <input name="mainAdminNewPass" type="password" class="form-control" id="mainAdminNewPass" 
                                                placeholder="Nieuw wachtwoord">
                                        </div>
                                    </div>
                                    <!-- New password confirm -->
                                    <div>
                                        <label for="mainAdminNewPassConfirm">Nieuw wachtwoord bevestigen</label>

                                        <div>
                                            <input name="mainAdminNewPassConfirm" type="password" class="form-control" id="mainAdminNewPassConfirm" 
                                                placeholder="Bevestig">
                                        </div>
                                    </div>

                                    <!-- SUBMIT -->
                                    <div>
                                        <div>
                                            <button name="mainAdminNewPassSubmit" type="submit" id="mainAdminNewPassSubmit" 
                                                class="btn btn-default">Verander</button>
                                        </div>
                                    </div>

                                    <div id="divMainAdminPassChangeMessage" style="display: none"></div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!--Ajax CRUD Tables-->
                    <div id="divParamsTable">
                        <h2>Spelparameters</h2>
                        <?php $tblParams->showTable(); ?>
                    </div>
                    <div id="divPropsTable">
                        <h2>Wetsvoorstellen</h2>
                        <?php $tblPropositions->showTable(); ?>
                    </div>
                </div>
                <div class='contentFadeOut'></div>

            </div>
            <!--Footer-->
            <div id="footer">
                <img src="../images/footer.png" alt="footer">
                <span>Copyright &#169; 2014 - Cocktail Party</span>
            </div>
            <script src="js/countdown.js"></script>
            <script>
                var auto_refresh_countdown = setInterval(function (){
                    $('#countdownscript').load('ajax/reloadcountdown.php');
                }, 10 * 1000); // refresh every 10 seconds
            </script>
            <div id="countdownscript">
                <script>
                    calcRemainingSeconds(<?php echo $parameters['currentLawStartTime']; ?>, <?php echo $parameters['lawDurationInMins']; ?>, <?php echo time(); ?>);
                    setResetcount(<?php echo $parameters['resetCount']; ?>);
                    startTimer();
                </script>
            </div>
        </div>
    </body>
</html>
