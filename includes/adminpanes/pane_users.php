<?php
	$inputwidth = 200;
	$selectwidth = $inputwidth;
	$textareawidth = 300;
	require("includes/config.php");
	mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
	mysql_select_db($mysql_db) or die("Could not select $site_title_text database!");
	$use_moodle_accounts = FALSE;
?>
<div id="contentloader" style="display:none;"></div>
<!--
<div id="useroptions">
<?php
/*
<div id="adminform" class="userautoform">
	<label for="syncusers">Use Moodle Users:</label><input id="syncusers" type="checkbox" <?php if($use_moodle_accounts) print "checked=\"\""; ?> onchange="setUserSync();" />
</div>*/
?>
 
<div id="year_recoptions">
	<?php if($use_moodle_accounts) { display_year_recognition(); } ?>
</div>
</div>
-->

<div id="adminform" class="usermanagerform" style="width: 50%; float: left; display: inline-block;">

<form action="formsubmit.php" method="POST" enctype="multipart/form-data">
	<div style="margin-bottom: 10px;"><u>Add Users With CSV Upload</u></div>
	<label for="csvfile">Choose CSV File:</label><input style="width: 200px; margin-left: 5px;" id="csvfile" name="csvfile" type="file"/>
	<input class="sysbutton" id="csvsubmit" onclick="return validateUserCSVForm();" name="submit" type="submit" value="Upload Users"><br />
	<div style="margin-bottom: 10px;">Files must be delimited with a ',' and must have not more than 7 columns to a line (username,password,firstname,lastname,email,year,admin). Admin must be 1 for Y, 0 for N. E-Mail may be blank to use your organisations domain, setup in admin Settings panel.
</div>
</form>

<div id="adminform" class="usermanagerform">
	<br />
	<div style="margin-bottom: 10px;"><u>Select User To Edit</u></div>
	<label for="year">Select Year:</label>
	<select name="year" id="year" size="1" style="width: 200px;" onchange="updateGroupList('getUserEditor');">
		<option selected="" disabled="">Select...</option>
		<?php
		$years = mysql_query("SELECT `year` FROM `users_years`");
		while($year = mysql_fetch_object($years))
		{
			print "<option value=\"".$year->year."\">".$year->year."</option>\n";
		}
		?>
	</select><br />
	<div id="groupselect"></div>
	<div id="userselect"></div>
	or <input style="margin-left: 70px;" class="sysbutton" id="addsingleuser" type="button" onclick="getUserEditor('true');" value="Add Single User" />
	<div id="edituserscreen" style="display: none;"></div>
</div>

</div>

<div id="adminform" style="float: right; width: 50%">
	<div style="margin-bottom: 10px;"><u>Manage Years & Group Associations</u></div>
	<input id="newyear" name="newyear" type="text" maxlength="2" style="width: 30px;">
	<input class="sysbutton" type="button" value="Add New Year" onclick="addYear();">
	<div style="margin-bottom: 10px;"><u>Edit Years (delete year / assign groups)</u></div>
	<?php
	$years = mysql_query("SELECT `year` FROM `users_years`");
	while($year = mysql_fetch_object($years))
	{
		?>
		<div id="mod_year_<?php print $year->year; ?>">
			<input style="width: 50px;" onclick="beginYearEdit('<?php print $year->year; ?>');" type="button" value="<?php print $year->year; ?>">
			<a id="deletebtn" href="javascript:deleteYear('<?php print $year->year; ?>');"></a>
			<br />
		</div>
		<?php
	}
	?>
	<div style="margin-bottom: 10px;"><u>Manage Groups</u></div>
	<?php
	$groups = mysql_query("SELECT * FROM `users_groups`");
	while($group = mysql_fetch_object($groups))
	{
		?>
		<div id="mod_group_<?php print $group->id; ?>">
			<div style="display:inline-block;"><?php print $group->name; ?></div>
			<a id="deletebtn" href="javascript:deleteGroup('<?php print $group->id; ?>');"></a>
			<br />
		</div>
		<?php
	}
	?>
	<div style="margin-top: 10px;">
		<input id="newgroup" name="newgroup" type="text" maxlength="20" /><input class="sysbutton" onclick="addGroup();" type="button" value="Add New Group" />
	</div>
</div>

<div id="blockeduserlist" style="float: left; width: 50%;">
	<u>Manage Blocked Users</u><br />
	<label for="name" style="width: 80px; display: inline-block; margin-top: 10px;">Username:</label><input style="width: 180px;" id="name" name="name" type="text" maxlength="50" /><br />
	<input class="sysbutton" style="margin-left: 80px;" onclick="addBlockedUser();" type="button" value="Add New Blocked User">
	<div>You can also wildcard block by year, just add * followed by the year number.</div><br />
<?php
	$blocked_user_result = mysql_query("SELECT * FROM `users_blocked` ORDER BY `username` ASC");
	while($blocked = mysql_fetch_object($blocked_user_result))
	{
		if($blocked->username[0] == "*")
		{
			$user_details->id = "wildcard".$blocked->username[1];
			$user_details->username = $blocked->username;
			$user_details->lastname = "Year ".substr($blocked->username, 1,strlen($blocked->username) - 1);
			$user_details->firstname = "Wildcard: ";
		} else {
			$user_details = mysql_query("SELECT * FROM `users` WHERE `username` = '$blocked->username' LIMIT 1");
			$user_details = mysql_fetch_object($user_details);
			if(!$user_details )
			{
				mysql_query("DELETE FROM `users_blocked` WHERE `username` = '$blocked->username' LIMIT 1");
			}
		}
		if($user_details )
		{
		?>
		<div id="blocked_user_<?php print $user_details->id; ?>">
			<div style="display: inline-block;"><?php print $user_details->lastname; ?> <?php print $user_details->firstname; ?></div>
			<div style="display: inline-block;">(<?php print $user_details->username; ?>)</div>
			<a id="deletebtn" href="javascript:deleteBlockedUser('<?php print $user_details->username; ?>','<?php print $user_details->id; ?>');"></a>
			<br />
		</div>
		<?php
		} else {
			?>
			<div id="tmpmessage" style="display: none;">
			<script type="text/javascript">
				$('#messagebox').load('messagegenerator.php?message=7');
				$('#tmpmessage').html('');
			</script>
			</div>
			<?php
		}
	}
?>

</div>

<div id="adminform" style="float:right; width: 50%">
	<div id="yeareditor"></div>
</div>

<div style="clear:both;"></div>
