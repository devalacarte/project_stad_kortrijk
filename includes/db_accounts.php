<?php
include 'db_connect.php';


/* functie om userName op te halen adhv ingevoerde username, bestaat de username? */
function getUserNameById($uid, $table = "accounts")
{
    $sql = "SELECT userName from $table where userName LIKE ? ";
    $result = queryDatabase($sql, "s", $uid);
    return $result;
}

function getUserIdByName($user, $table = "accounts")
{
    $sql = "SELECT id FROM $table WHERE userName LIKE ? ";
    $result = queryDatabase($sql, "s", $user);
    return $result;
}

/* functie voor het ophalen van een paswoord adhv een gebruikersnaam*/
function getPasswordByUserId($uid)
{
    $sql = "SELECT password from accounts where userName = ? ";
    $result = queryDatabase($sql, "s", $uid);
    return $result;
}

/* functie voor het ophalen van waardes uit een database met 1 param */
function queryDatabase($sql, $soortparm, $parm)
{
    global $mysqli;
    $result;
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($soortparm, $parm);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    return $result;
}

?>