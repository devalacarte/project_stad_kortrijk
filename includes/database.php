<?php
include 'db_propositions.php';
include 'db_accounts.php';
?>



<?php
//vote functies
if (isset($_POST['selectedwet'])) {
    $wet = $_POST['selectedwet'];
    setcookie("WET[$wet]", true,time()+60*60*25);
    updateVotes($wet);
}
?>



<?php
//login functies
if (isset($_POST['postUser'])) {
    $user = $_POST['postUser'];

    /*
        kijken of een user bestaat 0 = false, 1 = true
        0 niks returnen
        1 kijken of gebruiker al een paswoord heeft
    */
    $blnUser = checkUserExists($user);
    if ($blnUser == 0) {
        echo 2;
        return;
    }

    /* kijken of er een paswoord bestaat */
    $blnPass = checkPasswordExists($user);
    echo $blnPass;
}


/* kijken of een user bestaat 0 = false, 1 = true */
function checkUserExists($uid)
{
    $user = getUserNameById($uid);
    $exists = checkExists($user);
    return $exists;
}


/* kijken of een paswoord bestaat 0 = false, 1 = true */
function checkPasswordExists($uid)
{
    $pass = getPasswordByUserId($uid);
    $exists = checkExists($pass);
    return $exists;
}


/*Kijken of een waarde bestaat*/
function checkExists($arg)
{
    if ($arg == "" || $arg == NULL) {
        return false;
    } else {
        return true;
    }
}

?>