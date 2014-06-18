<?php
/*
 * Bar PMU
 *
 */

// include error reporting
include_once 'includes/header.php';

// include functions
include_once($_SERVER['DOCUMENT_ROOT']."thomas.van.den.ber1/includes/functions.php");
include_once 'includes/pmuFunctions.php';
// create new instance of functions-class
$db = new DBFunctions;

// get parameters
$parameters = $db->getparameters();
?>

<!doctype html>
<html>
	<head>
		<title>Bar PMU</title>
		<link rel="stylesheet" type="text/css" href="http://student.howest.be/thomas.van.den.ber1/css/fonts.css">
		<link rel="stylesheet" type="text/css" href="css/countdown.css">
		<link rel="stylesheet" type="text/css" href="css/barPMU.css" />
	</head>
	<body>
		<div id="container">
			<div id="header">
				<div id="hdr-logo">
					<a href="<?php echo $_SERVER['PHP_SELF']; ?>"><img src="images/logo.png" alt="Logo - The cocktail party" /></a>
				</div>
				<div id="hdr-countdown">
					<span id="tmr-h1" class="countdown-medium-number">0</span>
					<span id="tmr-h2" class="countdown-medium-number">0</span>
					<span class="countdown-medium-separator">:</span>
					<span id="tmr-m1" class="countdown-medium-number">0</span>
					<span id="tmr-m2" class="countdown-medium-number">0</span>
					<span class="countdown-medium-separator">:</span>
					<span id="tmr-s1" class="countdown-medium-number">0</span>
					<span id="tmr-s2" class="countdown-medium-number">0</span>
				</div>
				<div class="clear"></div>
			</div>
			<div class="contentFadeIn"></div>
			<div id="stats">
				<div id="stats-header"></div>
				<div id="stats-body">
					<div id="stats-left">
						<div id="divCurrentProps">
							<h1 class="h1">Gokstatistieken</h1>
							<div id="divNoCurrentProps">
								Er zijn nog geen wetsvoorstellen.
							</div>
							<table id="tblCurrentProps">
								<thead>
									<tr>
										<th class="col1 border-bottom">&nbsp;</th>
										<th class="col2 border-bottom border-right">&nbsp;</th>
										<th class="col3 border-bottom">Cocktail vanaf</th>
									</tr>
								</thead>
								<tbody id="tblCurrentProps-body">
								</tbody>
							</table>
						</div>
					</div>
					<div id="stats-right">
						<div id="amountOfRewards"></div>
						<div id="divPageNumbers">
							<ul></ul>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div id="stats-footer"></div>
			</div>
			<div class="contentFadeOut"></div>
			<div id="footer">
			</div>
		</div>
		<script src="js/jquery.plugin.js"></script>
		<script src="js/countdown_new.js"></script>
		<script src="js/currentProps.js"></script>
		<script>
			var auto_refresh_countdown = setInterval(function (){
				$('#countdownscript').load('ajax/reloadcountdown.php'); //Refresh timer
				requestUpdate(); //Refresh propositions
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