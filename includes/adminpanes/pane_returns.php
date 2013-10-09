<?php 
	include_once("includes/displayfunc.php"); 
?>
<div id="contentloader" style="display: none;"></div>
<div id="adminform" class="paymentform" style="width: 50%; float: left; display: inline-block;">
	<div style="margin-bottom: 10px;">Choose user who is returning items.</div>
	<label for="year">Select Year:</label>
	<select name="year" id="year" size="1" style="width: 200px;" onchange="updateGroupList('getUserReturns');">
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
<div id="adminform" class="returnsform" style="width: 50%; float: right; display: inline-block;">
    <div id="pendingpayments" style="margin-top: 10px;"></div>
    <div id="pendingpaperwork" style="margin-top: 10px;"></div>
</div>
<div style="clear:both;"></div>
