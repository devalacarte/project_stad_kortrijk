<?php
/*
 * Update-script
 * -> Executed when the countdown-timer is 0 (when round ends)
 *
 */

// include error reporting
include_once 'includes/header.php';

// include functions
include_once 'includes/functions.php';
// create new instance of functions-class
$db = new DBFunctions;

// request parameters
$parameters = $db->getParameters();

// calculate end time of this round
$endTime = $parameters['currentLawStartTime'] + $parameters['lawDurationInMins'];

/*
 * resetCount was added to prevent multiple resets from different browsers.
 * Now, only the first browser who requests this page will trigger the excecution of the code below
 * (it will also increase the resetCount in the database with 1)
 * Other browsers will have the old resetCount and so this code will not be executed by them.
 *
 */

// check if the resetCount in the header (GET) is equal to the resetCount in the database
if ($_GET['resetCount'] == $parameters['resetCount']) {
	// check if the end time has passed
	if (time() > $endTime) {
		// new start time & increase resetcount with 1
		$db->startNewCycle($parameters['resetCount'] + 1);
	}
}
?>