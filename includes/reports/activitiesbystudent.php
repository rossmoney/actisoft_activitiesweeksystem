<?php
$orientation = "landscape";
if($reporttype != "csv") { 
?>
	<table width="100%" style="border: 1px solid #D7D7D7;" border="0" cellspacing="0" cellpadding="0">
	<tbody style="vertical-align:middle;">
<?php
	}
	if($usernum == 0)
	{
		$string = "You have no students registered in the system!";
		if($reporttype != "csv") { print "<tr><td>$string</td></tr>"; }
		if($reporttype == "csv") { print "$string\r\n"; }
	} else {
		$colorswitch = TRUE;
		if($reporttype != "csv") 
		{ 
		?>
		<tr>
		<td style="width:12%;" class="heading"></td>
                <td style="width: 4%;" class="heading">Year</td>
				<td style="width:12%; border-right: 1px solid #000;" class="heading">Group</td>
		<?php
		for($i = 0; $i < $weekduration; $i++)
		{
			print "<td style=\"width: ".(76 / $weekduration)."%;\" class=\"heading\">".$weekdays[$i]."</td>";
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
	 					$duration = $activity_details->duration;
						if($reporttype != "csv") {
						?>
						<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>" <?php if($duration > 1 && !$register) print "colspan=\"$duration\""; ?>><?php print $activity_details->name; ?>
						</td>
						<?php
						} else {
							print $activity_details->name;
							for($i = 0; $i < ($duration - 1); $i++)
							{
								print ",". str_replace(",", ";", $activity_details->name);
							}
							if($count != ($weekduration - 1))
							{
								print ",";
							}
						}
						if(!$register)
						{
							$count=$count+$duration;
						} else {
							$count++;
						}
					}
					
					if($reporttype != "csv") 
					{
						?></tr><?php
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
	</tbody>
	</table>
<?php
	}
?>