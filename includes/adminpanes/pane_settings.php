<div id="contentloader" style="display: none;"></div>

<form name="settingsform" action="formsubmit.php" method="post">

<div id="adminform" class="settingsform" style="width: 50%; float: left; display: inline-block;">
	<u>Change System Settings</u><br /><br />
	<label for="online_start_time">Booking Start Date:</label>
	<input id="online_start_time" name="online_start_time" type="text" value="<?php print $GLOBALS['online_start_time']; ?>" /><br />
	<label for="online_end_time">Booking End Date:</label>
	<input id="online_end_time" name="online_end_time" type="text" value="<?php print $GLOBALS['online_end_time'];  ?>" /><br />
	<label for="session_time">Session Time (mins):</label>
	<input name="session_time" type="text" value="<?php print $GLOBALS['session_time']; ?>" /><br />
	<label for="timetokeepreports">Time to Keep Reports (mins):</label>
	<input name="timetokeepreports" type="text" value="<?php print $GLOBALS['timetokeepreports']; ?>" /><br />
	<label for="weekduration">Duration of week (in days, max 7):</label>
	<input name="weekduration" type="text" value="<?php print $GLOBALS['weekduration']; ?>" /><br />
	<label for="emaildomain">EMail Domain for sending mail to students:<br /></label>
	<input name="emaildomain" type="text" value="<?php print $GLOBALS['emaildomain']; ?>" /><br />
	<div>(e.g. @school.sch.uk) (leave blank to use stored user addresses)</div><br />
	<label for="systembasedir">Base HTTP Directory of Install:<br />(e.g. http://rossmoney.co.uk/actisoft)</label>
	<input name="systembasedir" type="text" value="<?php print $GLOBALS['systembasedir']; ?>" /><br />
	<br /><br />
	<u>ParentPay Settings</u><br /><br />
	<label for="parentpay_Disable">Disable ParentPay:</label>
	<input name="parentpay_Disable" type="checkbox" value="Yes" <?php if($GLOBALS['parentpay_Disable'] == "Yes") print "checked=\"checked\""; ?> />
	<br />
	<label for="parentpay_OrgId">Organisation ID:</label>
	<input name="parentpay_OrgId" type="text" value="<?php print $GLOBALS['parentpay_OrgId']; ?>" /><br />
	<label for="parentpay_UserId">User ID:</label>
	<input name="parentpay_UserId" type="text" value="<?php print $GLOBALS['parentpay_UserId']; ?>" /><br />
	<label for="parentpay_ServiceId">Service ID:</label>
	<input name="parentpay_ServiceId" type="text" value="<?php print $GLOBALS['parentpay_ServiceId']; ?>" /><br />
	<br />
	<input class="sysbutton" id="settingssubmit" onclick="return validateSettingsEditForm();" name="submit" type="submit" value="Update Settings">
	<br /><br /><br />
	<input name="resetactivitycounters" onclick="resetSystem()" value="Reset System (Delete Bookings & Reset Places Taken)" type="button" />
</div>

<div id="adminform" class="checkouttextform" style="width: 50%; float: right; display: inline-block;">
<u>Change Checkout Screen Text</u><br /><br />
<label for="checkouttext">Enter Text:</label><textarea style="width: 500px; height: 200px;" id="checkouttext" name="checkouttext" maxlength="1500"><?php print stripslashes($GLOBALS['checkouttext']); ?></textarea><br />
</div>

</form>

<div style="clear:both;"></div>

<script type="text/javascript">
$('#online_start_time').datepicker({ dateFormat: 'MM dd yy' });
$('#online_end_time').datepicker({ dateFormat: 'MM dd yy' });
</script>