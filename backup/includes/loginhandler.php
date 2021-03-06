<?php
include_once 'settings.php';
include_once 'hashing.php';
include_once 'secure_session.php';
include_once 'db_connect.php';

function addNewPassword($user, $password1, $mysqli)
{
    if ($password1) {
        $arr = salt_my_pass($password1);

    } else {
        return false;
    }
}

function login($user, $password, $mysqli)
{
    // prepares statemens - SQL injection
    if ($stmt = $mysqli->prepare("SELECT id, userName, password, salt1, salt2  FROM accounts WHERE userName LIKE ? LIMIT 1")) {
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($db_id, $db_username, $db_password, $db_salt1, $db_salt2);
        $stmt->fetch();

        //$password argument hashen met de opgehaalde salts en dan vergelijken met het opgehaalde paswoord, als beide gelijk zijn = inloggen
        $password = hash_salts($db_salt1, $password, $db_salt2);
        if ($stmt->num_rows == 1) {
            // checken of er teveel login pogingen zijn

            //if (checkbrute($user_id, $mysqli) == true) {
            // Account is locked
            //return false;
            //} else {
            // paswoorden vergelijken
            if ($db_password == $password) {
                // Password is correct!
                $user_browser = $_SERVER['HTTP_USER_AGENT'];
                $user_id = preg_replace("/[^0-9]+/", "", $db_id); //xss
                $_SESSION['user_id'] = $user_id;
                $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $db_username); //xss
                $_SESSION['username'] = $username;
                $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                // Login successful.
                return true;
            } else {
                // Password is niet correct - loggen in de database
                //$now = time();
                //$mysqli->query("INSERT INTO login_attempts(user_id, time) VALUES ('$user_id', '$now')");
                return false;
            }
            //}
        } else {
            // No user exists.
            return false;
        }
    }
}


function checkbrute($user_id, $mysqli)
{
    // Get timestamp of current time 
    $now = time();

    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = $now - (2 * 60 * 60);

    if ($stmt = $mysqli->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);

        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();

        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 5) {
            return true;
        } else {
            return false;
        }
    }
}


/*
We do this by checking the "user_id" and the "login_string" SESSION variables. 
The "login_string" SESSION variable has the user's browser information hashed together with the password. 
We use the browser information because it is very unlikely that the user will change their browser mid-session. 
Doing this helps prevent session hijacking
*/
function login_check($mysqli)
{
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];

        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $mysqli->prepare("SELECT password FROM accounts WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute(); // Execute the prepared query.
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);

                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }
    } else {
        // Not logged in 
        return false;
    }
}


?>