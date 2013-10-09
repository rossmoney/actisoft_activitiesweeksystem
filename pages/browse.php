<?php
	include_once("includes/config.php");
	$user_details = $_SESSION['userdetails'];
	if($user_details->admin == 1)
	{
		ob_end_clean();
		header("Location: admin");
		exit;
	}
	if(isset($user_bookings)) {
		header("Location: checkout");
		exit();
	}
	$activities = $_SESSION['activitydetails'];
	if(count($activities) == 0)
	{
		?>
		<div style="text-align: left; margin-bottom: 10px;">No activities have been added to the system yet.</div>
		<?php
	} else {
?>
<h1><?php print $pagetitle; ?></h1>
Click on the activity names to get more information about an activity.
<div id="day_tabs">
	<ul>
		<?php for($i = 0; $i < $weekduration; $i++) { ?>
		<li><a href="#day_tabs-<?php print ($i + 1); ?>"><?php print $weekdays[$i]; ?></a></li>
		<?php } ?>
	</ul>
	<?php for($i = 0; $i < $weekduration; $i++) { ?>
		<div id="day_tabs-<?php print ($i + 1); ?>">
		<p>
			<div id="accordion-<?php print ($i + 1); ?>" class="accordian">
			<?php
				foreach($activities as $activity)
				{
					if(($activity->starts - 1) == $i ) {
						$places = $activity->maxstudents - $activity->placestaken;
						print "<h3>$activity->name</h3>
						<p>
						$activity->desc";
						if($activity->additionalinfo != "") print "<br /><br />Special Requirements: $activity->additionalinfo";
						print "<br /><br /><span style=\"font-size: 20px;\">Cost: &pound;$activity->cost, Run By: $activity->teacher, Places Left: $places, Duration: $activity->duration Day(s)</span></p>";
					}
				}
			?>
			</div>
		</p>
		</div>
	<?php } ?>
</div>
<script type="text/javascript">
	<?php for($i = 0; $i < count($weekdays); $i++) { ?>
	$("#accordion-<?php print ($i + 1); ?> p").hide();
	$("#accordion-<?php print ($i + 1); ?> h3").click(function(){
		$(this).next("p").slideToggle("slow")
		.siblings("p:visible").slideUp("slow");
		$(this).toggleClass("active");
		$(this).siblings("h3").removeClass("active");
	});
	<?php } ?>
</script>
<div style="text-align:center;">
<?php
	}
	$systemexpired = FALSE;
	if(time() < strtotime($GLOBALS['online_start_time']) || time() > strtotime($GLOBALS['online_end_time'])) 
	{
		$systemexpired = TRUE;
	}
	if(!$systemexpired)
	{
	if(!isset($user_bookings))
      {
?>
	<input id="startbook" name="book" type="button" onclick="window.location = 'book'; checkAndForceLogout('Browse');" value="Start Booking Your Activities" <?php if(count($activities) == 0) print "disabled=\"\""; ?> />
<?php } else {

    ?>
	<input id="viewcheckout" type="button" onclick="window.location = 'checkout';" value="View Checkout" />
<?php } } ?>
</div>