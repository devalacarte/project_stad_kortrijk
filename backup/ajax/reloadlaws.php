<?php
// include error reporting
include_once 'includes/header.php';

// include functions
include_once 'includes/functions.php';
$db = new DBFunctions;

// get current laws
$laws = $db->getCurrentLaws();

if ($laws->num_rows <= 0) {
?>
		<p>Er zijn op dit moment geen wetten actief.</p>
<?php
}
?>
			<ol>
<?php
// print current laws
while ($law = $laws->fetch_array()) {
?>
				<li><?php echo $law['description']; ?></li>
<?php
}
?>
			</ol>