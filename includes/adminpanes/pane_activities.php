<?php
$inputwidth = 200;
$selectwidth = $inputwidth;
$textareawidth = 300;
require("includes/config.php");
$activityinfo = get_activity_info(FALSE, FALSE, TRUE);
$activities = $_SESSION['activitydetails'];
?>
<u>Manage Activities</u><br />
<div id="activitylist" style="float:left;">
<input class="sysbutton" style="margin-top: 10px; margin-bottom: 20px;" onclick="addActivity();" type="button" value="Add New Activity">
<?php
	mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
	mysql_select_db($mysql_db) or die("Could not select $site_title_text database!");
	foreach($activities as $activity)
	{
		?>
		<div id="activity_<?php print $activity->id; ?>">
		<input style="width: 300px;" onclick="beginActivityEdit('<?php print $activity->id; ?>');" type="button" value="<?php print $activity->name; ?>">
		<a id="deletebtn" href="javascript:deleteActivity('<?php print $activity->id; ?>');"></a>
		<div style="display: inline-block;"><?php print $activity->placestaken; ?>/<?php print $activity->maxstudents; ?> Taken</div>
		<br />
		</div>
		<?php
	}
?>
</div>
<div id="contentloader" style="display: none;"></div>
<form name="activityedit" action="formsubmit.php" method="post">
<div id="adminform" class="activityform" style="width: 50%; float:right; display: none;">
<label for="name">Name:</label><input style="width: <?php print $inputwidth; ?>px;" id="name" name="name" type="text" maxlength="100" /><br />
<label for="desc">Description:</label><textarea style="width: <?php print $textareawidth; ?>px; height: 200px;" id="desc" name="desc" maxlength="1500"></textarea><br />
<label for="additionalinfo">Special Requirements:<br />(optional)</label><textarea style="width: <?php print $textareawidth; ?>px; height: 60px;" id="additionalinfo" name="additionalinfo" maxlength="500"></textarea><br />
<label for="teacher">Teacher:</label><input style="width: <?php print $inputwidth; ?>px;" id="teacher" name="teacher" type="text" maxlength="50" /><br />
<label for="maxstudents">Number of Places:</label><input style="width: <?php print $inputwidth; ?>px;" id="maxstudents" name="maxstudents" type="text" maxlength="3" /><br />
<label for="cost">Cost (in &pound;):</label><input style="width: <?php print $inputwidth; ?>px;" id="cost" name="cost" type="text" maxlength="10" /><br />
<label for="yearsavailable">Years Available To:<br />(press and hold ctrl to select)</label>
<select style="width: <?php print $selectwidth; ?>px;" id="yearsavailable" name="yearsavailable[]" size="7" multiple="">
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
</select><br />
<label for="onsite">On Site:</label><input id="onsite" name="onsite" type="checkbox" value="onsite" checked="" /><br />
<?php 
	$paperwork_result = mysql_query("SELECT * FROM `paperwork`");
	if(mysql_num_rows($paperwork_result) > 0)
	{
?>
<label for="formsneeded">Forms Needed: (optional)</label><select style="width: <?php print $selectwidth; ?>px;" id="formsneeded" name="formsneeded[]" size="<?php print mysql_num_rows($paperwork_result); ?>" multiple="">
<?php 
		$paperwork_result = mysql_query("SELECT * FROM `paperwork`");
		while($paperwork = mysql_fetch_object($paperwork_result))
		{
?>
			<option value="<?php print $paperwork->id; ?>"><?php print $paperwork->name; ?></option>
<?php
		}
?>
</select><br />
<?php } ?>
<label for="daysavailable">Choose Days Available:<br />(press and hold ctrl to select)</label>
<select style="width: <?php print $selectwidth; ?>px;" id="daysavailable" name="daysavailable[]" size="<?php print $weekduration; ?>" multiple="">
<?php
	for($i = 0; $i < $weekduration; $i++)
	{
		print "<option value=\"$i\">$weekdays[$i]</option>";
	}
?>
</select><br />
<input id="actid" name="activityid" type="hidden" value="">
<input class="sysbutton" id="activitysubmit" onclick="return validateActivityForm();" name="submit" type="submit">
</div>
</form>

<div style="clear:both;"></div>