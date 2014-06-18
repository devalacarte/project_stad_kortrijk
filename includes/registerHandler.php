<?php
include 'hashing.php';
include 'loginhandler.php';
include 'db_connect.php';

/*
 * Checken of er degelijk waardes zijn
 * paswoord hashen adhv 2 salt waardes (functie in hashing.php)
 * paswoord in
 *
 */

if (isset($_POST['inputUser'], $_POST['p'])) {
    $username = filter_input(INPUT_POST, 'inputUser', FILTER_SANITIZE_STRING);

    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);

    //Define what accounts table needs to be used
    $accountsTable = "accounts";
    if(isset($_POST["isAdmin"]) && $_POST["isAdmin"] == 1)
        $accountsTable = "adminAccounts";

    //Check whether or not to redirect after log in (default = redirect)
    $noRedirect = FALSE;
    if(isset($_POST["noRedirect"]) && $_POST["noRedirect"] == 1)
        $noRedirect = TRUE;

    /*
    if (strlen($password) != 128) {
        // hashed passwords moeten 128 characters hebben
        return false;
    }
    */

    $arr = salt_my_pass($password);
    $pass = $arr['Hash'];
    $s1 = $arr['Salt1'];
    $s2 = $arr['Salt2'];
    include_once 'db_accounts.php';
    $id = getUserIdByName($username, $accountsTable);


    //dit nog db_accounts.php steken
    //function setPassForUserId($pass, $s1, $s2, $id);
    $prep_stmt = "UPDATE $accountsTable SET password = ?, salt1 = ?, salt2 = ? WHERE id = ? LIMIT 1";
    if ($stmt = $mysqli->prepare($prep_stmt)) {
        $stmt->bind_param('siii', $pass, $s1, $s2, $id);
        $stmt->execute();
        $stmt->close();

        if(!$noRedirect) 
        {
            if (login($username, $password, $mysqli) == true) {
            // Login success
            header("Location: ../gokken.php");
            //echo "login succes";
            } else {
                // Login failed
                //header($login_failed);
                echo "login failed";
            }
        }
    }
}
?>