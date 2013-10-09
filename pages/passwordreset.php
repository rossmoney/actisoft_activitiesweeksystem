<?php
    include_once("includes/config.php");
    mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
    mysql_select_db($mysql_db) or die("Could not select $site_title_text database!");
?>
<h1>Reset Password</h1>
<?php 
$vars = $_SESSION['formvars'];
$showform = TRUE;
if(isset($_REQUEST['token']))
{
	mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
	mysql_select_db($mysql_db) or die("Could not connect to MySQL server!");
	$person_data = mysql_query("SELECT * FROM `passwordresets` WHERE `token` = '". $_REQUEST['token'] . "'");
	if(mysql_num_rows($person_data) > 0)
	{
		?>
		<div style="text-align:left; margin-bottom: 10px; color: #cccccc;">
		<?php
		$showform = FALSE;
		$person_data = mysql_fetch_object($person_data);
		$person_data = mysql_query("SELECT * FROM `users` WHERE `id` =". $person_data->UserID );
		$person_data = mysql_fetch_object($person_data);
		print $person_data->firstname;
		?>
		, enter your new password below:</div><br />
		<form id="formbody" action="formsubmit.php"  method="post">
			<label for="newpassword">New Password:</label><input name="newpassword" type="password" maxlength="50" tabindex="1" /><?php if($vars['newpassword'] == "") { ?><div class="requiredfield">*</div><?php } ?><br />
			<label for="newpasswordretry">Retype Password:</label><input id="newpasswordretry" name="newpasswordretry" type="password" maxlength="50" tabindex="2" /><?php if($vars['newpasswordretry'] == "") { ?><div class="requiredfield">*</div><?php } ?><br />
			<input name="UserID" type="hidden" value="<?php print $person_data->id; ?>" />
			<input name="token" type="hidden" value="<?php print $_REQUEST['token']; ?>" />
			<div style="text-align: center;"><input id="changepwd_submit" class="sysbutton" name="submit" type="submit" value="Change Password" /></div>
		</form>
		<?php
	} else {
		$showform = TRUE;
	}
} 
if($showform)
{
	$emailvalid = TRUE;
	if($_SESSION['message'] == "54")
	{
		$emailvalid = FALSE;
	}
	?>
	<form id="formbody" action="formsubmit.php"  method="get">
		<label for="pwdreset_email" style="text-align:left; margin-bottom: 10px; color: #cccccc;">Email you used to register:</label> <input class="pwdreset_form_fields" name="pwdreset_email" type="text" value="<?php print $vars['pwdreset_email']; ?>" maxlength="145" tabindex="1" /><?php if(!$emailvalid) { ?><div class="requiredfield">*</div><?php } ?><br />
		<div style="text-align: center;"><input style="width: 300px;" id="resetpwd_submit" name="submit" class="sysbutton" type="submit" value="Reset Password" /></div>
	</form>
	<?php unset($_SESSION['formvars']); unset($_SESSION['fieldsmissing']); 
}
?> 