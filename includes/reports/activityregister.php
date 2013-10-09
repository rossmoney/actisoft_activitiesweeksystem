<?php
	$orientation = "portrait";
	$footerstring = "Generated on ".date("d F Y")." at ".date("H:i")." by ".$user_details->firstname." ".$user_details->lastname;
	$activities = mysql_query("SELECT * FROM `activities` ORDER BY `name` ASC");
	if(mysql_num_rows($activities) == 0 || $usernum == 0)
	{
		$string = "You either have no activities or no students registered in the system!";
		if($reporttype != "csv") { print "$string</br></br>"; }
		if($reporttype == "csv") { print "$string\r\n"; }
	} else {
		if($reporttype == "csv")
		{
            print ",Last Name,First Name,Year,Group,";
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
		while($activity = mysql_fetch_object($activities))
		{
            $duration = $activity->duration;
			$startson = $activity->starts;
			switch($reporttype)
			{
			case "html":
			?>
			<table width="100%" style="border: 1px solid #D7D7D7; margin-bottom: 10px;" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><div style="border-bottom: 1px solid #000;" class="heading"><?php print $activity->name; ?></div></td>
				<td style="width: 50px;"><div class="heading">Year</div></td>
				<td style="width: 120px;"><div style="border-right: 1px solid #000;" class="heading">Group</div></td>
                                <?php
					for($i = 0; $i < $weekduration; $i++)
					{
						?>
						<td style="width: 100px;"><div class="heading" style="text-align: center;">
							<?php print $weekdays[$i]; ?></div>
						</td>
						<?php
					}
				?>
			</tr>
			<?php
			break;
			case "pdf":
			break;
			}
			$colorswitch = TRUE;
			mysql_data_seek($users, 0);
			while($student = mysql_fetch_object($users))
			{
				if($student->firstname != "" && $student->lastname != "")
				{
				$student_activities = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` =$student->id LIMIT 1");
				if(@mysql_num_rows($student_activities) > 0)
				{	
					$student_activities  = mysql_fetch_object($student_activities);
					$student_activities->bookings = explode("|", $student_activities->bookings);
					$count = 0;
					$found = FALSE;
  					while($count < $weekduration)
					{
						$activity_id = returnActivityBookedOnDay($count, $student_activities->bookings);
						if($activity_id == $activity->id) $found = TRUE;
						$count++;
					}
					if($found) 
					{
						$colorswitch = !$colorswitch;
						$query = "SELECT `name` FROM `users_groups` WHERE `id` = $student->group_id LIMIT 1;";
						$group_details = mysql_query($query);
						$group_details = @mysql_fetch_object($group_details);
                        if($reporttype != "csv") {
					?>
					<tr>
					<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>"><?php print $student->lastname." ".$student->firstname; ?></td>
					<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>"><?php print $student->year; ?></td>
					<td style="border-right: 1px solid #000;" class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">
						<?php 
							if($group_details) {
								print $group_details->name;
							}
						 ?>
					</td>
                     <?php
                        } else {
							 print str_replace(",", ";", $activity->name).",".$student->lastname.",".$student->firstname.",".$student->year.",";
							 if($group_details) {
									print $group_details->name;
							 }
							 print ",";
                        }
                        for($i = 0; $i < $weekduration; $i++)
						{
							if($i >= ($activity->starts - 1) && $i < (($activity->starts - 1) + $activity->duration))
							{
								if($reporttype != "csv") {
                                                                ?>
								<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">		
									<div class="reg_tickbox" style="margin: 0 auto;"></div>
								</td>
								<?php
                            	} else {
                                	 print "_______,";
                           		}
							} else {
								if($reporttype != "csv") { ?>
									<td style="background-color: #FFBABA;">
									</td>
								<?php
                            	} else {
                                 print "X,";
                            	}
							}
						}
						if($reporttype != "csv") {
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
    	if($reporttype != "csv") {
			?>
			</table>
			<div id="footer"><?php print $footerstring; ?></div>
			<div style="page-break-before: always;"></div>
			<?php
   	 	}
	}
    if($reporttype == "csv") {
        print "\r\n".$footerstring;
    }
}
?>