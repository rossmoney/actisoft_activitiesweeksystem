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
				<td style="width: 20%;"><div class="heading">Student</div></td>
				<td style="width: 4%;"><div class="heading">Year</div></td>
				<td style="width: 19%; border-right: 1px solid #000;"><div class="heading">Group</div></td>
				<td style="width: 35%;"><div class="heading">Activity</div></td>
				<td style="width: 15%;"><div class="heading">Pending</div></td>
				<td style="width: 15%;"><div class="heading">Recieved</div></td>
			</tr>
		<?php
		} else {
			print "Last Name,First Name,Year,Group,Activity,Pending,Recieved\r\n";
		}
		$pending_grandtotal = 0;
		$recieved_grandtotal = 0;
		while($student = mysql_fetch_object($users))
		{
			if($student->firstname != "" && $student->lastname != "")
			{
				$activitycosts = "";
				$recieved_total = 0;
				$pending_total = 0;
				$pending_payments = array();
				$recieved_payments = array();
				$colorswitch = TRUE;
				
				$query = "SELECT `name` FROM `users_groups` WHERE `id` = $student->group_id LIMIT 1;";
				$group_details = mysql_query($query);
				$group_details = @mysql_fetch_object($group_details);
				
				$booking_activities = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` = $student->id LIMIT 1");
				$booking_activities = @mysql_fetch_object($booking_activities);
				$user_bookings = array_filter(explode("|", $booking_activities->bookings), "blanks");
				
				if(count($user_bookings) > 0)
				{ 
					$payment_activities = mysql_query("SELECT * FROM `payment_bookings` WHERE `user_id` = $student->id LIMIT 1");
					$payment_activities = @mysql_fetch_object($payment_activities);
					$payment_bookings = array_filter(explode("|" , $payment_activities->activities_remaining), "blanks");
						
					$user_bookings = array_values($user_bookings);
					$payment_bookings = array_values($payment_bookings);
					
					if(!$payment_activities)
					{
						foreach($user_bookings as $booking)
						{
							$activity = $activitydetails[$booking];
							if($activity && $activity->cost > 0)
							{
								$pending_total += $activity->cost;
								$pending_payments[$booking] = $activity->cost;
								$recieved_payments[$booking] = 0;
							}
						}
					} else {
						
						$pending_total = $payment_activities->money_remaining;
						$cost_remaining = $pending_total;
						
						foreach($payment_bookings as $booking)
						{
							$activity = $activitydetails[$booking];
							if(($cost_remaining - $activity->cost) > 0)
							{
								$pending_payments[$booking] = (int)$activity->cost;
								$recieved_payments[$booking] = 0;
							} else {
								$pending_payments[$booking] = $cost_remaining;
								$recieved_payments[$booking] = abs($cost_remaining - $activity->cost);
								$recieved_total += $recieved_payments[$booking];
							}
							$cost_remaining -= $activity->cost;
						}
						
						$full_recieved_payments = array_diff($user_bookings, $payment_bookings);
						
						if(count($full_recieved_payments) > 0)
						{
							foreach($full_recieved_payments as $booking)
							{
								$activity = $activitydetails[$booking];
								if($activity && $activity->cost > 0)
								{
									$recieved_total += $activity->cost;
									$recieved_payments[$booking] = $activity->cost;
									$pending_payments[$booking] = 0;
								}
							}
						}

					}

					$pending_grandtotal += $pending_total;
					$recieved_grandtotal += $recieved_total;
					$activitycosts = "";
					$activitycount = 0;
					for($i = 0; $i < count($user_bookings); $i++)
					{
						$activity = $activitydetails[$user_bookings[$i]];
						if($activity)
						{
							if($activity->cost > 0)
							{
								$activitycount++;
								if($reporttype != "csv") 
								{
									$colorswitch = !$colorswitch;
									if($activitycount > 1)
									{
										$activitycosts .= "<tr><td colspan=\"3\" style=\"border-right: 1px solid black;\"></td>";
									}
									$activitycosts = $activitycosts."<td class=\"";
									if($colorswitch) { $activitycosts .= "row_odd"; } else { $activitycosts .= "row_even"; }
									$activitycosts .= "\">".$activity->name."</td>";
									if(@array_key_exists($activity->id, $pending_payments))
									{
										$activitycosts .= "<td class=\"";
										if($colorswitch) { $activitycosts .= "row_odd"; } else { $activitycosts = $activitycosts."row_even"; }
										$activitycosts .= "\">&pound;";
										
										$activitycosts .= $pending_payments[$activity->id];
	
										$activitycosts = $activitycosts . "</td>";
									} 
									if(@array_key_exists($activity->id, $recieved_payments)) {
										
										$activitycosts .= "<td class=\"";
										if($colorswitch) { $activitycosts .= "row_odd"; } else { $activitycosts .= "row_even"; }
										$activitycosts .= "\">&pound;";
										
										$activitycosts .= $recieved_payments[$activity->id];
										
										$activitycosts .=  "</td>";
									} 
									$activitycosts .=  "</tr>";
								} else {
									$activitycosts = $activitycosts.$student->lastname.",".$student->firstname.",".$student->year.",";
									if($group_details) {
										$activitycosts .= $group_details->name;
									}
									$activitycosts .=  ",".str_replace(",", ";", $activity->name);
									
									if(@array_key_exists($activity->id, $pending_payments))
									{
										$activitycosts .= ",£".$pending_payments[$activity->id];
									} 
									if(@array_key_exists($activity->id, $recieved_payments))
									{
										$activitycosts .= ",£".$recieved_payments[$activity->id];
									} 
									$activitycosts .= "\r\n";
								}
							}
							$cost_remaining = $cost_remaining - $activity->cost;
						}
					}
				}
				if($activitycosts != "")
				{
					if($reporttype != "csv") 
					{
					?>
						<tr class="studentrow">
							<td><?php print $student->lastname." ".$student->firstname; ?></td>
							<td><?php print $student->year; ?></td>
							<td style="border-right: 1px solid black;" >
							<?php 
								if($group_details) {
									print $group_details->name;
								}
							 ?>
							</td>
					<?php
						
					}
					print $activitycosts;
				}
				$colorswitch = !$colorswitch;
				if($pending_total > 0 || $recieved_total > 0)
				{
					if($reporttype != "csv") 
					{
						?>
						<tr>
						<td colspan="3" style="border-right: 1px solid black;"></td>
						<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">Total</td>
						<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">&pound;<?php print $pending_total; ?></td>
						<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">&pound;<?php print $recieved_total; ?></td>
						</tr>
						<?php
					} else {
						print $student->lastname.",".$student->firstname.",".$student->year.",";
						if($group_details) {
							print $group_details->name;
						}
						print ",Total,£".$pending_total.",£".$recieved_total."\r\n";
					}
				}
			}
		}
		if($reporttype != "csv") 
		{
			$colorswitch = FALSE;
		?>
			<tr>
				<td colspan="5">-</td>
			</tr>
			<tr>
				<td></td><td></td><td></td>
				<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">Grand Total:</td>
				<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">&pound;<?php print $pending_grandtotal; ?></td>
				<td class="<?php if($colorswitch) { print "row_odd"; } else { print "row_even"; } ?>">&pound;<?php print $recieved_grandtotal; ?></td>
			</tr>
			</table>
		<?php
		} else {
			print "\r\n,,,,Grand Total:,£". $pending_grandtotal.",". $recieved_grandtotal ."\r\n";
		}
	}
?>