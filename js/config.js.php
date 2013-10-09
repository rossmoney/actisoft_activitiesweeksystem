<?php 
	header("Content-type: text/javascript"); 
	include_once("../includes/config.php");
	include_once("../includes/utilities.php");
?>
// JavaScript Document
var weekdays = [<?php 
for($i = 0; $i < $weekduration; $i++) 
{ 
	print "'".strtolower($weekdays[$i])."'"; 
	if($i != ($weekduration - 1)) 
	{ 
		print ", ";
	}
} 
?>];
var adminpanes = [<?php 
for($i = 0; $i < count($adminpanes); $i++) 
{ 
	print "'".$adminpanes[$i]."'"; 
	if($i != (count($adminpanes) - 1)) 
	{ 
		print ", ";
	}
} 
?>];
var reports = [<?php 
for($i = 0; $i < count($reports); $i++) 
{ 
	print "'".stripspaces_andlower($reports[$i])."'"; 
	if($i != (count($reports) - 1)) 
	{ 
		print ", ";
	}
} 
?>];
var session_timer_length = <?php print ($session_time * 60); ?>;
//var queue_polling_timeout = <?php print $queue_polling_timeout; ?>;

var currenttime = '<?php print date("F d, Y H:i:s", strtotime("+8 hour")); ?>'; //March 05, 2012 20:49:27