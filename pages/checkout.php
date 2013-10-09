<?php
		include_once("includes/config.php");
		include_once("includes/sessionhandler.php");
		include_once("includes/sysfunc.php");
		$userid = $_SESSION['loginuserid'];
		$activities  = $_SESSION['activitydetails'];
        $postbookingcheckout = false;
        if($_SESSION['bookcheckout'] == "true")
        {
            unset($_SESSION['bookcheckout']);
            $postbookingcheckout = true;
        }
        if(!$postbookingcheckout)
        {
            $user_details = $_SESSION['userdetails'];
            if($user_details->admin == 1)
            {
				ob_end_clean();
				header("Location: admin");
				exit;
            }
            if(!isset($user_bookings))
            {
				ob_end_clean();
				$_SESSION['message'] = 10;
				header("Location: browse");
            }
	if($GLOBALS['parentpay_Disable'] != "Yes")
	{
?>
    <h1>Checkout</h1>
<?php } else { ?>
	<h1>Summary</h1>
<?php } ?>
	<div style="text-align:left; margin-bottom: 10px; color: #cccccc;"><?php 
	print $checkouttext;
	?></div>
<?php 	} else { ?>

		<h1>Review</h1>
<?php } ?>
<table style="font-size: 30px;" width="100%" border="0" cellspacing="0" cellpadding="0">
  <?php
  mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
  mysql_select_db($mysql_db) or die("Could not select $site_title_text database!");
  $count = 0;
  $laststart = 0;
  $totalcost = 0;
  if($postbookingcheckout)
  {
      $activity_ids = $_SESSION['cur_bookings'];
  } else {
  	  $activity_ids = $user_bookings->bookings;
  }
  for($count = 0; $count < $weekduration; $count++)
  {
		 $place = TRUE;
		 $activity_id = (int)$activity_ids[$count];
		 $activity_details = $activities[$activity_id];
		 $paymentstatus = mysql_query("SELECT * FROM `payment_bookings` WHERE `user_id` = " . $userid . "LIMIT 1");
		 if(@mysql_num_rows($paymentstatus))
		 {
			 $paymentstatus = mysql_fetch_object($paymentstatus);
			 $payment_activitiesremaining = explode("|", $paymentstatus->activities_remaining);
			 $pending = FALSE;
			 for($i = 0; $i < count($payment_activitiesremaining); $i++)
			 {
				if($activity_id == $payment_activitiesremaining) $pending = TRUE;
			 }
			 if(!$pending) $place = FALSE;
		 }
		 if($activity_id != NULL && $activity_id != "0")
		 {
			 $placed = FALSE;
			 $payacts[] = $activity_id;
		 }
		 if($place) {
			  ?>
			  <tr>
				<td style="color: #999999;"><?php print $weekdays[$count]; ?></td>
				<td style="color: #CCCCCC;"><?php print $activity_details->name; ?></td>
				<?php if(!$placed) { ?>
					<td style="color: #999999; vertical-align:top; text-align:right; width: 150px;" <?php if($activity_details->duration > 1) print "rowspan=\"$activity_details->duration\""; ?>>&pound;<?php print $activity_details->cost; 
					?></td>
					<?php if(!$postbookingcheckout) { ?>
						<td><?php if($activity_details->cost > 0 && $GLOBALS['parentpay_Disable'] != "Yes") { ?><input class="sysbutton" style="width: 120px; margin-left: 10px;" type="button" value="Pay" onclick="activatePayScreen('<?php print $activity_id; ?>');" /><?php } ?></td>
				<?php } ?>
				<!-- <td class="checkout_content" <?php if($activity_details->duration > 1) print "rowspan=\"$activity_details->duration\""; ?>><?php print $activity_details->additionalinfo; ?></td>
				<td class="checkout_content" <?php if($activity_details->duration > 1) print "rowspan=\"$activity_details->duration\""; ?>>
				<?php
				if(!empty($activity_details->formsneeded))
				{
					$formsneeded = explode("|", $activity_details->formsneeded);
					for($i = 0; $i < count($formsneeded); $i++)
					{
						$form = mysql_query("SELECT * FROM `paperwork` WHERE `id`=".$formsneeded[$i]." LIMIT 1");
						$form = mysql_fetch_object($form);
						if(substr($form->url, 0, 4) != "http")
						{
							$form->url = "forms/".$form->url;
						}
						?>
							<a href="<?php print $form->url; ?>" target="_blank"><?php print $form->name; ?></a><br />
						<?php
					}
				} else {
					print "None";
				}
				?>
				</td> -->
			  </tr>
			  <?php
			 	$totalcost = $totalcost + $activity_details->cost;
			}
  	 }
	 $placed = TRUE; 
  }
  ?>
  <tr style="color: #ABDC28; text-align:right;">
	<td colspan="2">Total Cost</td>
	<td>&pound;<?php print $totalcost; ?></td>
	<?php if(!$postbookingcheckout && $GLOBALS['parentpay_Disable'] != "Yes") { ?>
	<td><input class="sysbutton" style="width: 120px; margin-left: 10px; float:left;" type="button" value="Pay All" onclick="activatePayScreen('<?php print implode(",", $payacts); ?>', 'full');" /></td>
	<?php } ?>
  </tr>
</table>
<?php
if(!$postbookingcheckout)
{
	
?>
    <br /><br />
	
<?php } else {  ?>
    <br /><br />
    <input id="bookprevbtn" type="button" onclick="reeditBooking();" value="Back"/>
    <input id="booknextbtn" type="button" value="Book" onclick="saveBooking();"/>
	<div style="clear:both;"></div>
    <div id="cur_bookings" style="display: none;"><?php print json_encode($activity_ids); ?></div>
<?php } ?>
