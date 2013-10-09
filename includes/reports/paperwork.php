<?php
	function blanks($key)
	{
		if($key == "")
		{
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	$activitydetails = $_SESSION['activitydetails'];
	$orientation = "portrait";
	if($usernum == 0)
	{
		$string = "You have no students registered in the system!";
		if($reporttype != "csv") 
		{
			print $string."<br />";
		} else {
			print $string."\r\n";
		}
	} else {
		if($reporttype != "csv") 
		{
		?>
			<table width="100%" style="border: 1px solid #D7D7D7;" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td style="width: 16%;"><div class="heading">Student</div></td>
				<td style="width: 4%;"><div class="heading">Year</div></td>
				<td style="width: 12%;"><div class="heading">Group</div></td>
				<td style="width: 30%;"><div class="heading">Form Name</div></td>
				<td style="width: 17%;"><div class="heading">Pending</div></td>
				<td style="width: 17%;"><div class="heading">Returned</div></td>
			</tr>
		<?php
		} else {
			print "Last Name,First Name,Year,Group,Form Name,Pending,Returned\r\n";
		}
		$formdetails = mysql_query("SELECT * FROM `paperwork`");
		while($student = mysql_fetch_object($users))
		{
			if($student->firstname != "" && $student->lastname != "")
			{
				$rowcontent = "";
				$rowcount = 0;
				$query = "SELECT `name` FROM `users_groups` WHERE `id` = $student->group_id LIMIT 1;";
				$group_details = mysql_query($query);
				$group_details = @mysql_fetch_object($group_details);
				$query = "SELECT * FROM `activities_bookings` WHERE `user_id` = $student->id LIMIT 1";
				$user_activities = mysql_query($query);
				$user_activities = mysql_fetch_object($user_activities);
				$user_activities = array_filter(explode("|" , $user_activities->bookings), "blanks");
				mysql_data_seek($formdetails, 0);
				while($form = mysql_fetch_object($formdetails))
				{
					$returned_activities = array();
					$pending_activities = array();
					$colorswitch = !$colorswitch;
					$user_paperwork = mysql_query("SELECT * FROM `paperwork_bookings` WHERE `user_id` = $student->id AND `paperwork_id` = $form->id LIMIT 1");
					$user_paperwork = mysql_fetch_object($user_paperwork);
					$activities_returned = array_filter(explode("|" , $user_paperwork->activities_returned), "blanks");
					for($i = 0; $i < count($user_activities); $i++)
					{
						$returned = FALSE;
						$activity_details = $activitydetails[$user_activities[$i]];
						foreach($activities_returned as $activity_id_returned)
						{
							if($user_activities[$i] == $activity_id_returned)
							{
								$returned = TRUE;
								$returned_activities[] = $activity_details->name; 
							}
						}
						if(!$returned && $activity_details->formsneeded != 0)
						{
							$formsneeded = explode("|", $activity_details->formsneeded);
							foreach($formsneeded as $form_id_needed)
							{
								if($form_id_needed == $form->id) {
									$pending_activities[] = $activity_details->name;
								}
							}
						}
					}
					if(count($pending_activities) > 0 || count($returned_activities) > 0)
					{
						$rowcount++;
						if($reporttype != "csv") {
							if($rowcount != 1)
							{
								$rowcontent .= "<tr style=\"vertical-align:top;\"><td colspan=\"3\" style=\"border-right: 1px solid #000;\"></td>";
							}
							$rowcontent .= "<td class=\"";
							if($colorswitch) { $rowcontent .= "row_odd"; } else { $rowcontent .=  "row_even"; }
							$rowcontent .=  "\">".$form->name."</td><td class=\"";
							if($colorswitch) { $rowcontent .= "row_odd"; } else { $rowcontent .= "row_even"; } 
							$rowcontent .= "\">";
							foreach($pending_activities as $activity)
							{
								$rowcontent .= "<p>".$activity."</p>";
							}
							$rowcontent .= "</td><td class=\"";
							if($colorswitch) { $rowcontent .= "row_odd"; } else { $rowcontent .= "row_even"; } 
							$rowcontent .= "\">";
							foreach($returned_activities as $activity)
							{
								$rowcontent .= "<p>".$activity."</p>";
							}
							$rowcontent .= "</td></tr>";
						} else {
							$rowcontent .= ",,,,".$form->name.",";
							foreach($pending_activities as $activity)
							{
								$rowcontent .= str_replace(",", "", $activity)."; ";
							}
							$rowcontent .= ",";
							foreach($returned_activities as $activity)
							{
								$rowcontent .= str_replace(",", "", $activity)."; ";
							}
							$rowcontent .= "\r\n";
						}
					}
				}
				if($rowcontent != "")
				{
					if($reporttype != "csv") 
					{	
					?>
						<tr class="studentrow" style="vertical-align:top;">
							<td><?php print $student->lastname." ".$student->firstname; ?></td>
							<td><?php print $student->year; ?></td>
							<td style="border-right: 1px solid #000;">
							<?php 
								if($group_details) {
									print $group_details->name;
								}
							 ?>
							</td>
					<?php
						print $rowcontent;
					} else {
						print $student->lastname.",".$student->firstname.",".$student->year;
						if($group_details) {
							print ",".$group_details->name;
						}
						print ",,,\r\n";
						print $rowcontent;
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
	}
?>