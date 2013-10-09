<?php
$orientation = "portrait";
if($reporttype != "csv") 
	{
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
			<td style="width: 40%;" class="heading"></td>
			<td style="width: 20%; border-right: 1px solid #000;" class="heading">Duration</td>
			<td style="width: 20%;" class="heading">Student</td>
            <td style="width: 8%;" class="heading">Year</td>
			<td style="width: 12%;" class="heading">Group</td>
		</tr>
		<?php
		} else {
			print "Activity,Duration,Last Name,First Name,Year,Group\r\n";
		}
		foreach($activitydetails as $activity)
		{
			mysql_data_seek($users, 0);
			while($student = mysql_fetch_object($users))
			{
				if($student->firstname != "" && $student->lastname != "")
			{
				$student_activities = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` =$student->id LIMIT 1");
				if(mysql_num_rows($student_activities) > 0)
				{	
					$student_activities  = mysql_fetch_object($student_activities);
					$student_activities->bookings = explode("|", $student_activities->bookings);
					$count = 0;
					$query = "SELECT `name` FROM `users_groups` WHERE `id` = $student->group_id LIMIT 1;";
					$group_details = mysql_query($query);
					$group_details = @mysql_fetch_object($group_details);
  					while($count < $weekduration)
					{
						$activity_id = returnActivityBookedOnDay($count, $student_activities->bookings);
						if($activity_id == $activity->id)
						{
							$colorswitch = !$colorswitch;
							$starts = $weekdays[$activity->starts - 1];
							$ends = $weekdays[($activity->starts - 1) + ($activity->duration - 1)];
							if($ends == $starts)
							{
								$ends = "";
							} else {
								$ends = " - ".$ends;
							}
							if($reporttype != "csv") 
							{
							?>
							<tr>
							<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">
								<?php print $activity->name; ?>
							</td>
							<td style="border-right: 1px solid #000;" class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">
								<?php print $starts.$ends; ?>
							</td>
							<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">		
								<?php print $student->lastname." ".$student->firstname; ?>
							</td>
                            <td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">
								<?php print $student->year; ?>
							</td>
							<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">
							<?php 
								if($group_details) {
									print $group_details->name;
								}
							 ?>
							</td>
							</tr>
							<?php
							} else {
								print str_replace(",", ";", $activity->name).",".$starts.$ends.",".$student->lastname.",".$student->firstname.",".$student->year;
								if($group_details) {
									print ",".$group_details->name;
								}
								print "\r\n";
							}
							$count=$count+(int)$activity->duration;
						} else {
							$count++;
						}
					}
					}
				}
			}
		}
	}
	if($reporttype != "csv") 
	{
?>
	</table>
<?php
	}
?>