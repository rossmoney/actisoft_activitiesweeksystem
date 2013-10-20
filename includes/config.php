<?php
require("admin_settings.php");
$maintenancemode = false;

$site_title_text = "awsome";
$version_num = 2;
$copyright = "by <a href=\"http://floudy.co.uk\">Floudy</a>";
$recentupdateyear = 2012; //the year I last did any updates to the site, used in footer copyright notice
$adminemail = "no-reply@awsomebook.com";

if(!$maintenancemode)
{
	$weekdays = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	$reports = array("Activity Register", "Activities by Student", "Students by Activity", "Students by Location", "Paperwork", "Payments", "Students With No Booking");
	$adminpanes = array("Activities", "Bookings", "Paperwork", "Returns", "Reports", "Users");

	$session_name = "key"; //the name of the session (displayed in address bar)
	$session_save_path = "sessions"; //where to save sessions on the server
	
	$login_token_timeout = 30; //seconds
		$mysql_host = "localhost";
		$mysql_db = "actisoft";
}
?>
