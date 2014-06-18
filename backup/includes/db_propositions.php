<?php
include 'db_connect.php';

/* als er op de voteknop op de stempagina wordt geklikt 1 stem toevoegen */
function updateVotes($arg_1)
{
    global $mysqli;
    $votes = getVoteByWetId($arg_1) + 1;
    $stmt = $mysqli->prepare("UPDATE propositions SET votes = ? WHERE id = ?");
    $stmt->bind_param("ii", $votes, $arg_1);
    $stmt->execute();
    $stmt->close();
    echo getVoteByWetId($arg_1);
}

/* aantal stemmen ophalen voor stempagina adhv het wetsvoorstelID */
function getVoteByWetId($arg_1)
{
    global $mysqli;
    $return;
    $result = $mysqli->prepare("SELECT votes FROM propositions WHERE id = ?");
    $result->bind_param("i", $arg_1);
    $result->execute();
    $result->bind_result($return);
    $result->fetch();
    $result->close();
    return $return;
}

?>