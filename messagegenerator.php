<?php
	if($_REQUEST['message'] != "")
	{
		$message = $_REQUEST['message'];
	} else {
		$message = $_SESSION['message'];
	}
	include_once("includes/messages.php");
	if($message == "")
	{
		print "<p class='cross'>No Message</p>";
	} else {
		if(isset($_REQUEST['msgboxval']))
		{
			print get_message($message, $_REQUEST['msgboxval']);
		}  else {
			print get_message($message);
		}
	}
?>
