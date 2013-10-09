<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once("includes/utilities.php");
include_once("includes/config.php");
include_once("includes/sysfunc.php");
include_once("includes/sessionhandler.php");
$userid = $_SESSION['loginuserid'];
mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
mysql_select_db($mysql_db) or die("Could not select awsome database!");

function getPendingPaperwork($vars)
{
?>
	<u>Forms To Return</u><br /><br />
	<?php
	$userid = $vars['uid'];
	$activitydetails = $_SESSION['activitydetails'];
	$user = getuserdetails($userid);
	
	$formdetails = mysql_query("SELECT * FROM `paperwork`");
	$student_activities = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` = $userid LIMIT 1");
	
	if(mysql_num_rows($student_activities) > 0)
	{	
		$student_activities  = mysql_fetch_object($student_activities);
		$student_activities  = explode("|", $student_activities->bookings);
		$formno = 0;
		ob_start();
		?>
		<div id="forms_placeholder">
		<div class="paperworkreturnform" id="adminform">
		<table border="0" cellspacing="0" cellpadding="0">
		<tbody style="vertical-align: top;">
		<?php
		while($form = mysql_fetch_object($formdetails))
		{
			ob_start();
			?>
			<tr>
			<td style="width: 200px;"><div style="margin-right: 10px;"><?php print  $form->name; ?>:</div></td>
			<td>
			<?php
			$formno2 = 0;
			$user_paperwork = mysql_query("SELECT * FROM `paperwork_bookings` WHERE `user_id` = $userid AND `paperwork_id` = $form->id LIMIT 1");
			$user_paperwork = mysql_fetch_object($user_paperwork);
			$activities_returned = explode("|", $user_paperwork->activities_returned);
			foreach($student_activities as $activity_id)
			{
				$returned = FALSE;
				foreach($activities_returned as $activity_id_returned)
				{
					if($activity_id == $activity_id_returned)
					{
						$returned = TRUE;
					}
				}
				if(!$returned)
				{
					$activity_details = $activitydetails[$activity_id];
					$formsneeded = explode("|", $activity_details->formsneeded);
					foreach($formsneeded as $form_id_needed)
					{
						if($form_id_needed == $form->id) {
					?>
					<input id="form_<?php print $userid; ?>_<?php print $activity_id; ?>_<?php print $form->id; ?>" type="checkbox" onchange="removePendingForm('<?php print $userid; ?>','<?php print $activity_id; ?>', '<?php print $form->id; ?>');" /><label for="form_<?php print $userid; ?>_<?php print $activity_id; ?>_<?php print $form->id; ?>"><?php print $activity_details->name; ?></label>
					<br /><br />
					<?php
							$formno++;
							$formno2++;
						}
					}
				}
			}
			?>
			</td></tr>
			<?php
			$content = ob_get_clean();
			if($formno2 > 0) print $content;
		}
		?>
		</tbody>
		</table>
		</div>
		</div>
	<?php
		$content = ob_get_clean();
		if($formno > 0)
		{
			print $content;
		} else {
			print "<div>$user->firstname has returned all their forms.</div>";
		}
	} else {
		?>
		<div><?php print $user->firstname; ?> has no forms to return.</div>
		<?php
	}
}

function editBooking($userid)
{
	include_once("includes/displayfunc.php");
	
	$booking_details = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` = $userid LIMIT 1;");
	$booking_details = mysql_fetch_object($booking_details);
?>
<div style="margin-bottom: 10px;">Edit booking selections.</div>
<form id="bookingform" name="bookingform" action="">
	<div id="activityinfo" style="display: none;">
		<?php 
			get_activity_info($userid, TRUE);
			print $_SESSION['activitydetails_json'];
		?>
	</div>
	<div id="reviewscreen"></div>
	<div id="bookingfields">
	<div style="margin-bottom: 10px;">Make your selections.</div>
	<div id="activityfields">
		<?php print createActivityFields(); ?>
</div>
<!-- admin extra -->
<div id="userbookingdata" style="display: none;">
	<input id="selectedactivities" type="hidden" value="<?php print $booking_details->bookings; ?>" />
</div>
<!-- end admin extra -->
<div id="totalcost"></div>
	<div id="bookjsonload">
	<script language="JavaScript" type="text/javascript">
		updateActivities('startover');
	</script>
	</div>
<input class="sysbutton" id="startover" name="startover" type="button" onclick="clearSelections();" value="Start Over"/>
<input class="sysbutton" type="button" value="Edit Booking" onclick="checkBooking('Edit+Booking', '<?php print $userid; ?>');" />
<input class="sysbutton" type="button" value="Delete Booking" onclick="deleteBooking('<?php print $userid; ?>');" />
	</div>
</form>
<?php
}

function embedReport($vars)
{
	$reporttype = $vars['reporttype'];
	$refresh = $vars['refresh'];
	?>
	<!-- <a href="reportgen.php?reporttype=pdf&reportid=<?php print $reporttype; ?>">Download as PDF File</a>,-->
	<a class="sysbutton" style="text-decoration:none;" href="reportgen.php?reporttype=csv&reportid=<?php print $reporttype; ?>">Download as CSV File</a>, <input class="sysbutton" type="button" onclick="javascript:printReport();" value="Print Report" />, <input class="sysbutton" type="button" onclick="javascript:showReport('<?php print $reporttype; ?>', 'html', 'yes');" value="Refresh Report" />, <input class="sysbutton" type="button" onclick="javascript:showAdminPane('Reports');" value="New Report" />
	<iframe id="reportout" src="reportgen.php?reporttype=html&reportid=<?php print $reporttype; ?>&refresh=<?php print $refresh; ?>" style="border: 1px solid #000; width: 100%; height: 600px;"/>
	<?php
}

function showAdminPane($vars)
{
	if($vars['pane'] == "adminhome")
	{
		 display_admin_home_text();
	} else {
		$pane = "includes/adminpanes/pane_".stripspaces_andlower($vars['pane']).".php";
		include_once($pane);
	}
}

function showUserEditor($userid, $add = FALSE)
{
	$inputwidth = 200;
	$selectwidth = $inputwidth;
	$textareawidth = 300;
	if(!$add) {
		$user_details = mysql_query("SELECT * FROM `users` WHERE `id` = $userid LIMIT 1");
		$user_details = mysql_fetch_object($user_details);
	}
	$years = mysql_query("SELECT `year` FROM `users_years`");
	?>
	<div style="margin-bottom: 10px;"><u>Edit User Details</u></div>
	<form action="formsubmit.php" method="post">
	<label for="new_username">Username:</label><input style="width: 200px;" id="new_username" name="new_username" type="text" maxlength="50" value="<?php if(!$add) print $user_details->username; ?>"><br>
	<label for="new_password">Password:</label><input style="width: 200px;" id="new_password" name="new_password" type="password" maxlength="50"><br /><?php if(!$add) print "(leave blank to keep current password)<br />"; ?>
	<label for="new_firstname">Firstname:</label><input style="width: 200px;" id="new_firstname" name="new_firstname" type="text" maxlength="50" value="<?php if(!$add) print $user_details->firstname; ?>"><br>
	<label for="new_lastname">Surname:</label><input style="width: 200px;" id="new_lastname" name="new_lastname" type="text" maxlength="50" value="<?php if(!$add) print $user_details->lastname; ?>"><br>
	<label for="new_email">E-Mail:<br />(leave blank for user@emaildomain defined in settings.)</label><input style="width: 200px;" id="new_email" name="new_email" type="text" maxlength="50" value="<?php if(!$add) print $user_details->email; ?>"><br>
	<label for="new_year">Select Year:</label>
	<select name="new_year" id="new_year" size="1" style="width: 200px;" onchange="updateGroupList('newuser');">
		<option <?php if($add == "TRUE") print "selected=\"\""; ?> disabled="">Select...</option>
		<?php
		while($year = mysql_fetch_object($years))
		{
			print "<option ";
			if(!$add && $user_details->year == $year->year) print "selected=\"\" ";
			print "value=\"".$year->year."\">".$year->year."</option>\n";
		}
		?>
	</select><br />
	<div id="newuser_groupselect"><?php 
		if(!$add) {
			$vars['year'] = $user_details->year;
			$vars['group'] =  $user_details->group_id;
			$vars['newuser'] = "Y";
			showGroupList($vars); 
		}
	?></div>
	<label for="new_admin">Admin? Y for YES, N for NO</label><input style="width: 200px;" id="new_admin" name="new_admin" type="text" maxlength="1" <?php if(!$add && $user_details->admin) print "value=\"Y\""; ?> ><br>
	<?php if(!$add) { ?>
	<input name="userid" type="hidden" value="<?php print $user_details->id;  ?>" />
	<input type="submit" name="submit" onclick="return validateUserDetailsForm();" id="userdetailsubmit" value="Update User Details">
	<a id="deletebtn" href="javascript:deleteUser('<?php print $user_details->id; ?>');"></a>
	<?php } else { ?>
	<input class="sysbutton" type="submit" name="submit" onclick="return validateUserDetailsForm();" id="newusersubmit" value="Add New User">
	<?php } ?>
	</form>
	<?php
}

function showGroupList($vars)
{
	$year = $vars['year'];
	$groups = mysql_query("SELECT * FROM `users_years` WHERE `year` = $year LIMIT 1;");
	$groups = mysql_fetch_object($groups);
	$groups = explode("|", $groups->groups);
?>
	<label <?php if($vars['newuser'] != "Y") { print "for=\"group\""; } else { print "for=\"new_group\""; } ?> >Select Group:</label>
	<select <?php if($vars['newuser'] != "Y") { print "name=\"group\" id=\"group\""; } else { print "name=\"new_group\" id=\"new_group\""; } ?> size="1" style="width: 200px;" <?php if($vars['newuser'] != "Y")
	{
		print "onchange=\"updateUserList('".$vars['callbackfunc']."');\""; } ?> >
	<option <?php if(!$user_details) print "selected=\"\""; ?> disabled="">Select...</option>
	<option value="all">All</option>
	<?php
	foreach($groups as $group)
	{
		$group_details = mysql_query("SELECT * FROM `users_groups` WHERE `id` = $group LIMIT 1;");
		$group_details = mysql_fetch_object($group_details);
		if(!empty($group_details->name) && !empty($group))
		{
		?>
		<option <?php if($vars['newuser'] == "Y" && $group == $vars['group']) print "selected=\"\""; ?> value="<?php print $group; ?>"><?php print $group_details->name; ?></option>
		<?php
		}
	}
?>
	</select>
<?php
}

function showUserList($vars)
{
	$year = $vars['year'];
	$group = $vars['group'];
	if($group == "all") 
	{
		$query = "SELECT * FROM `users` WHERE `year` = $year ORDER BY `lastname` ASC;";
	} else {
		$query = "SELECT * FROM `users` WHERE `year` = $year AND `group_id` = $group ORDER BY `lastname` ASC;";
	}
	$users = mysql_query($query);
	?>
	<label for="user">Select Student:</label>
	<select name="user" id="user" size="1" style="width: 200px;" onchange="<?php print $vars['callbackfunc']; ?>();">
	<option selected="" disabled="">Select...</option>
	<?php
	if($users) {
		while($user = mysql_fetch_object($users))
		{
			if(!empty($user->firstname) && !empty($user->lastname))
			{
			?>
			<option value="<?php print $user->id; ?>"><?php print $user->lastname." ".$user->firstname; ?></option>
			<?php
			}
		}
	}
?>
	</select>
<?php
}

function showYearEditor($year)
{
	$groups = mysql_query("SELECT * FROM `users_groups`");
	$selected_groups = mysql_query("SELECT `groups` FROM `users_years` WHERE `year` = $year LIMIT 1");
	$selected_groups = mysql_fetch_object($selected_groups);
	$selected_groups = explode("|", $selected_groups->groups);
?>
	<div style="margin-bottom: 5px;"><u>You are Editing Groups for Year <input id="edit_newyear" type="text" maxlength="2" style="width: 20px;" value="<?php print $year; ?>"></u></div>
	<div style="margin-bottom: 10px;">Select groups for this year by holding down CTRL and clicking the groups you would like.</div>
	<select id="groupselector" style="width: 100px;" size="<?php print mysql_num_rows($groups); ?>" multiple>
	<?php
	while($group = mysql_fetch_object($groups))
	{
		$isselected = FALSE;
		foreach($selected_groups as $selected)
		{
			if($selected == $group->id)
			{
				$isselected = TRUE;
				break;
			}
		}
		?>
		<option value="<?php print $group->id; ?>" <?php if($isselected) print "selected=\"\""; ?> ><?php print $group->name; ?></option>
		<?php
		
	}
	?>
	</select><br />
	<input onclick="saveYear('<?php print $year; ?>')" type="button" value="Save Year" />
<?php
}
?>