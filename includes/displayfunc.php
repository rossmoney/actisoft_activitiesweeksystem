<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once("includes/utilities.php");
include_once("includes/config.php");
include_once("includes/sysfunc.php");
include_once("includes/sessionhandler.php");
$userid = $_SESSION['loginuserid'];
mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
mysql_select_db($mysql_db) or die("Could not select awsome database!");

function add_global_messagebox($message, $val = FALSE)
{
	if($message != "")
 	{
		print get_message($message, $val);
 	}
}

function add_meta($pagetitle = false)
{
	require("includes/config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr" >
<head>
  <meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
  <meta http-equiv="Accept-Encoding" content="gzip, deflate" />
  <meta name="keywords" content="booking,activity,week,events,<?php print $site_title_text; ?>" />
  <meta name="description" content="A booking system for activity week events." />
  <meta name="generator" content="<?php print $site_title_text; ?>" />
<title>
<?php
	print $site_title_text;
	if($pagetitle != "") print " - ".$pagetitle;
?>
</title>
<link rel="icon" href="./images/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="./images/favicon.ico" type="image/x-icon" />
<script type="text/javascript" src="js/config.js.php"></script>
<?php 
	if($_SERVER["HTTP_HOST"] == "localhost")
	{
?>
	<link href="css/<?php print stripspaces_andlower($site_title_text); ?>.css" rel="stylesheet" type="text/css" />
	<link href="css/jquery-ui-1.8.17.custom.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/json2.js"></script>
	<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.17.custom.min.js"></script>
	<script type="text/javascript" src="js/<?php print stripspaces_andlower($site_title_text); ?>.js"></script>
	<script type="text/javascript" src="js/admin.js"></script> 
<?php 
	} else {
?>
	<link href="min/g=awsome_css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="min/g=awsome_js"></script>
<?php }
if($pagetitle == "Browse") { ?>
<script type="text/javascript">
$(document).ready(function(){
	$( "#day_tabs" ).tabs();
});
</script>
<?php } ?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19314151-12']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body onload="checkJavaScriptValidity();">
<?php
}

function createActivityFields()
{
           require("includes/config.php");
           for($i = 0; $i < $weekduration; $i++)
           {
		?>
		<div id="<?php print strtolower($weekdays[$i]); ?>_block">
			<div id="<?php print strtolower($weekdays[$i]); ?>" style="float:left; width: 530px;">
						<div class="activityselector">
						<label id="lbl_<?php print strtolower($weekdays[$i]); ?>" for="<?php print strtolower($weekdays[$i]); ?>_select"><?php print $weekdays[$i]; ?></label>
						<select name="<?php print strtolower($weekdays[$i]); ?>" id="<?php print strtolower($weekdays[$i]); ?>_select" onchange="<?php
								print "updateActivities('". $i ."');";
							  ?>">
						</select>
						</div>
			</div>
			<div id="<?php print strtolower($weekdays[$i]); ?>_details" style="float:right;"></div>
			<div id="<?php print strtolower($weekdays[$i]); ?>_available" style="float:right;"></div>
			<div style="clear: both;"></div>
		</div>
		<?php

            }
}

function loadDoneScreen()
{
	?>
	<div style="width: 500px; margin: 0 auto;">
		<h1>You're Done!</h1>
		<?php
		if($GLOBALS['parentpay_Disable'] != "Yes")
			{
			?>
			<input id="paybutton" style="float: left;" type="button" value="Pay Now!" onclick="window.location = 'pay';" />
			<input id="checkoutbutton" style="float: right;" type="button" value="Pay Later" onclick="window.location = 'checkout';" />
			<?php
			}
			else
				{
				?>
				<p align="center">
				<input id="checkoutbutton" type="button" value="Summary" onclick="window.location = 'checkout';" />
				</p>
				<?php
				}
			?>

		<div style="clear: both;"></div>
	</div>
	<?php
}

function display_admin_home_text() 
{
?>
	<div style="text-align:center; margin: 80px 0px;">
		Welcome to the <?php print $site_title_text; ?> admin section. Click a tab to the left to begin.
	</div>
<?php
}
?>
