<?php
/*
 * AJAX-file
 * -> Calcs the remaining time left till end of round.
 *
 */

// include error reporting
include_once '../includes/header.php';

// include functions
include_once($_SERVER['DOCUMENT_ROOT']."thomas.van.den.ber1/includes/functions.php");
// create new instance of functions-class
$db = new DBFunctions;

// request parameters
$parameters = $db->getParameters();
?>

			<script>
				calcRemainingSeconds(<?php echo $parameters['currentLawStartTime']; ?>, <?php echo $parameters['lawDurationInMins']; ?>, <?php echo time(); ?>);
				setResetcount(<?php echo $parameters['resetCount']; ?>);
			</script>