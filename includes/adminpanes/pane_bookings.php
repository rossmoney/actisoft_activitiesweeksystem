<?php 
	include_once("includes/displayfunc.php"); 
?>
<div id="contentloader" style="display: none;"></div>
<u>Manage Bookings</u><br /><br />
<div style="margin-bottom: 10px;">Choose user to edit their booking.</div>
<div id="adminform" class="bookingeditform" style="width: 30%; float: left;">
<label for="year">Select Year:</label>
<select name="year" id="year" size="1" style="width: 200px;" onchange="updateGroupList('editBooking');">
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
</div>
<div id="editbookingscreen" style="width: 70%; float: right;"></div>
<div style="clear:both;"></div>