<?php
    include_once("includes/config.php");
	$userid = $_SESSION['loginuserid'];
	$user_details = $_SESSION['userdetails'];
	if(time() < strtotime($GLOBALS['online_start_time']) || time() > strtotime($GLOBALS['online_end_time'])) 
	{
		ob_end_clean();
		header("Location: browse");
		exit;
	}
	if($user_details->admin == 1)
	{
		ob_end_clean();
		header("Location: admin");
		exit;
	}
	$user_bookings = $_SESSION['userbookings'];
	if(isset($user_bookings))
	{
		ob_end_clean();
		header("Location: checkout");
		exit;
	}
?>
<div style="width: 700px; margin: 0 auto; margin-bottom: 40px;" >

	<div style="margin-left: 175px; width: 350px; margin-bottom: 20px;">
	
	<div style="float: left;">
	<div id="step1outer" style="width: 85px; height: 85px; border-radius: 100px; border: 5px solid #666666;">
		<div id="step1inner" style="width: 85px; height: 85px; border-radius: 100px; border: 5px solid #CCCCCC; position:relative; z-index: -5; top: -4px; left: -4px;">
			<div id="step1number" style="font-family: HoboStdMedium, Arial; font-size: 50px; 
			color: #999999; text-shadow: #CCCCCC 1px 1px; margin-top: 5px; margin-left: 27px;">1</div>
		</div>
	</div>
	</div>
	<div style="margin-left: 30px; float: left;">
	<div id="step2outer" style="width: 85px; height: 85px; border-radius: 100px; border: 5px solid #666666; opacity: 0.4;">
		<div id="step2inner" style="width: 85px; height: 85px; border-radius: 100px; border: 5px solid #CCCCCC; position:relative; z-index: -5; top: -4px; left: -4px; opacity: 0.4;">
			<div id="step2number" style="font-family: HoboStdMedium, Arial; font-size: 50px; 
			color: #999999; text-shadow: #CCCCCC 1px 1px; margin-top: 5px; margin-left: 27px; opacity: 0.4;">2</div>
		</div>
	</div>
	</div>
	<div style="margin-left: 30px; float: left;">
	<div id="step3outer" style="width: 85px; height: 85px; border-radius: 100px; border: 5px solid #666666; opacity: 0.4;">
		<div id="step3inner" style="width: 85px; height: 85px; border-radius: 100px; border: 5px solid #CCCCCC; position:relative; z-index: -5; top: -4px; left: -4px; opacity: 0.4;">
			<div id="step3number" style="font-family: HoboStdMedium, Arial; font-size: 50px; 
			color: #999999; text-shadow: #CCCCCC 1px 1px; margin-top: 5px; margin-left: 27px; opacity: 0.4;">3</div>
		</div>
	</div>
	</div>
	
	<div style="clear:both;"></div>
	
	</div>


	<form id="bookingform" name="bookingform" action="">
			<div id="activityinfo" style="display: none;">
				<?php print $_SESSION['activitydetails_json']; ?>
			</div>
			<div id="reviewscreen"></div>
			<div id="bookingfields">
				<div id="activityfields">
					<?php print createActivityFields(); ?>
				</div>
				<!-- <input style="width: 100px;" id="startover" name="startover" type="button" onclick="clearSelections();" value="Start Over"/> -->
				<input id="bookprevbtn" type="button" value="Back" onclick="window.location = 'browse';"/>
				<!-- <div id="totalcost"></div> -->
				<input id="booknextbtn" type="button" value="Next" onclick="checkBooking('Book+Now', '<?php print $userid; ?>');"/>
				<div style="clear:both"></div>
			</div>
	</form>
	<div id="yourdone"></div>

</div>

<div id="bookjsonload">
	<script language="JavaScript" type="text/javascript">
		updateActivities('startover');
	</script>
</div>
