<?php
	$orientation = "portrait";
	if($reporttype != "csv") { 
?>
	<table width="100%" style="border: 1px solid #D7D7D7;" border="0" cellspacing="0" cellpadding="0">
<?php
	}
	if(count($activitydetails) == 0 || $usernum == 0)
	{
		$string = "You either have no activities or no students registered in the system!";
		if($reporttype != "csv")
		{
		?>
			<tr><td class="row_odd"><?php print $string; ?></td></tr>
		<?php
		} else {
			print $string."\r\n";
		}
	} else {
		$colorswitch = TRUE;
		if($reporttype != "csv") 
		{ 
		?>
		<tr>
		<td style="width: 15%;" class="heading"></td>
		<td style="width: 4%;" class="heading">Year</td>
		<td style="width: 11%; border-right: 1px solid #000;" class="heading">Group</td>
		<?php
		for($i = 0; $i < $weekduration; $i++)
		{
			print "<td style=\"width: ".(70 / $weekduration)."%;\" class=\"heading\">".$weekdays[$i]."</td>";
		}
		?>
		</tr>
		<?php
		} else {
			print "Last Name,First Name,Year,Group,";
			for($i = 0; $i < $weekduration; $i++)
			{
				print $weekdays[$i];
				if($i != ($weekduration - 1))
				{
					print ",";
				}
			}
			print "\r\n";
		}
		while($student = mysql_fetch_object($users))
		{
			if($student->firstname != "" && $student->lastname != "")
			{
				$student_activities = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` =$student->id LIMIT 1");
				if(mysql_num_rows($student_activities) > 0)
				{
					$colorswitch = !$colorswitch;
					$query = "SELECT `name` FROM `users_groups` WHERE `id` = $student->group_id LIMIT 1;";
					$group_details = mysql_query($query);
					$group_details = @mysql_fetch_object($group_details);
					if($reporttype != "csv") 
					{
					?>
						<tr>
    					<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">
							<?php print $student->lastname." ".$student->firstname; ?>
						</td>
						<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">
							<?php print $student->year; ?>
						</td>
						<td style="border-right: 1px solid #000;" class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">
							<?php 
								if($group_details) {
									print $group_details->name;
								}
							 ?>
						</td>
					<?php
					} else {
						print $student->lastname.",".$student->firstname.",".$student->year.",";
						if($group_details) {
							print $group_details->name;
						}
						print ",";
					}
				
					$student_activities = mysql_fetch_object($student_activities);
					$student_activities->bookings = explode("|", $student_activities->bookings);
					$count = 0;
  					while($count < $weekduration)
					{
						$activity_id = returnActivityBookedOnDay($count, $student_activities->bookings);
						$activity_details = $activitydetails[$activity_id];
						if($activity_details->onsite == 1)
						{
							$site = "On Site";
							$onsite[$count]++;
						} else {
							$site = "Off Site";
							$offsite[$count]++;
						}
						if($reporttype != "csv") {
						?>
						<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">
							<?php print $site; ?>
						</td>
						<?php
						} else {
							print $site;
							if($count != ($weekduration - 1))
							{
								print ",";
							}
						}
						$count++;
					}
					
					if($reporttype != "csv") 
					{
						?>
						</tr>
						<?php
					} else {
						print "\r\n";
					}
				}
			}
		}
	}
	if($reporttype != "csv") 
	{
		?>
		</table>
		<table width="100%" style="border: 1px solid #D7D7D7; margin-top: 10px;" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td colspan="2" style="vertical-align:top; width: 30%; border-right: 1px solid #000;">Totals:</td>
			<?php
				for($i = 0; $i < $weekduration; $i++)
				{
					if($onsite[$i] == "") $onsite[$i] = 0;
					if($offsite[$i] == "") $offsite[$i] = 0;
					print "<td style=\"width: ".(70 / $weekduration)."%;\">$onsite[$i] On Site<br />$offsite[$i] Off Site</td>";
				}
			?>
		</tr>
		</table>
		<?php
	} else {
		print "\r\nTotals:,,,";
		for($i = 0; $i < $weekduration; $i++)
		{
			if($onsite[$i] == "") $onsite[$i] = 0;
			print ",$onsite[$i] On Site";
		}
		print "\r\n,,,";
		for($i = 0; $i < $weekduration; $i++)
		{
			if($offsite[$i] == "") $offsite[$i] = 0;
			print ",$offsite[$i] Off Site";
		}
		print "\r\n";
	}
?>