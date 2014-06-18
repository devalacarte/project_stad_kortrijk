<?php
/*
 * Simple timer example
 *
 */

// include error reporting
include_once 'includes/header.php';

// include functions
include_once 'includes/functions.php';
// create new instance of functions-class
$db = new DBFunctions;

// get parameters
$parameters = $db->getParameters();
?>
<html>
	<head>
		<title>Simple timer example</title>
		<link rel="stylesheet" type="text/css" href="http://student.howest.be/thomas.van.den.ber1/css/fonts.css">
		<link rel="stylesheet" type="text/css" href="css/countdown.css">
	</head>
	<body>
		<h3>Normal</h3>
		<div id="countdown">
			<span id="tmr-h1" class="countdown-number">0</span>
			<span id="tmr-h2" class="countdown-number">0</span>
			<span class="countdown-separator">:</span>
			<span id="tmr-m1" class="countdown-number">0</span>
			<span id="tmr-m2" class="countdown-number">0</span>
			<span class="countdown-separator">:</span>
			<span id="tmr-s1" class="countdown-number">0</span>
			<span id="tmr-s2" class="countdown-number">0</span>
		</div>
		<h3>Small</h3>
		<div id="countdown-small">
			<span id="tmr-h1" class="countdown-small-number">0</span>
			<span id="tmr-h2" class="countdown-small-number">0</span>
			<span class="countdown-small-separator">:</span>
			<span id="tmr-m1" class="countdown-small-number">0</span>
			<span id="tmr-m2" class="countdown-small-number">0</span>
			<span class="countdown-small-separator">:</span>
			<span id="tmr-s1" class="countdown-small-number">0</span>
			<span id="tmr-s2" class="countdown-small-number">0</span>
		</div>
		<script src="js/jquery.plugin.js"></script>
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
	</body>
</html>