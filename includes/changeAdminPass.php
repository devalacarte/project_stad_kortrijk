<?php 
ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once 'loginhandler.php';
include_once 'registerHandler.php';

if(isset($_POST["oldPass"]) && isset($_POST["newPass"]))
{
    $oldPass = $_POST["oldPass"];
    $newPass = $_POST["newPass"];

    if(trim($newPass) == "")
        echo json_decode(array("message"=>"Gelieve een nieuw wachtwoord in te stellen."));
    else 
    {
        //Try to log in with the old pass first using loginhandler.php
        $loginResult = login("managementAdmin", $oldPass, $mysqli, "adminAccounts");
        if($loginResult == -1) //Brute force lock
            echo json_encode(array("message"=>"Te veel foutieve pogingen. Probeer opnieuw in vijf minuten."));
        else if($loginResult == 0) //Wrong old password
            echo json_encode(array("message"=>"Foutief huidig wachtwoord."));
        else //Correct old password, carry on
        {
            //Salt the new pass
            $arr = salt_my_pass($newPass);
            $pass = $arr['Hash'];
            $s1 = $arr['Salt1'];
            $s2 = $arr['Salt2'];
            include_once 'db_accounts.php';
            $id = getUserIdByName("managementAdmin", "adminAccounts");

            //Update the password
            $prep_stmt = "UPDATE adminAccounts SET password = ?, salt1 = ?, salt2 = ? WHERE id = ? LIMIT 1";
            $stmt = $mysqli->prepare($prep_stmt);
            $stmt->bind_param('siii', $pass, $s1, $s2, $id);
            $stmt->execute();
            $stmt->close();

            //Alert the page
            echo json_encode(array("message"=>"Wachtwoord veranderd."));
        }
    }
}

?>

