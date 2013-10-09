<?php 
	if($GLOBALS['parentpay_Disable'] == "Yes")
	{
		header("Location: checkout");
		exit;
	} 
	unset($_SESSION['messageval']);
	if(!isset($_SESSION['booked_activitys']))
	{
		header("Location: checkout");
		exit;
	} 
	$activityinfo = $_SESSION['activitydetails'];
	for($i = 0; $i < count($_SESSION['booked_activitys']); $i++)
	{
		$booked_activityinfo[] = $activityinfo[$_SESSION['booked_activitys'][$i]];
	}
?>
<h1>Pay With ParentPay</h1>
<div style="padding-top: 20px; width: 600px; margin: 0 auto; font-size: 16px; color: #CCCCCC;">
	You are paying for the following items:
	<ul style="margin-bottom: 20px; margin-top: 10px;">
	<?php for($i = 0; $i < count($booked_activityinfo); $i++) { 
	$cur_activity = $booked_activityinfo[$i];
	if($cur_activity != NULL && $cur_activity->cost > 0) { 
	?>
	<li style="margin-left: 30px;"><?php print $cur_activity->name." with ".$cur_activity->teacher." - £".$cur_activity->cost; ?></li>
	<?php } } ?>
	</ul>

<?php $user_details = $_SESSION['userdetails']; ?>
<form action="https://www.parentpay.com/SHOP/Info/Entry.aspx" method="POST">
<div id="formbody">
<input name="OrgId" value="<?php print $parentpay_OrgId; ?>" type="hidden" />
<input name="UserId" value="<?php print $parentpay_UserId; ?>" type="hidden"/>
<input name="Mode" value="ShopMode" type="hidden"/>
<input name="ServiceId" value="<?php print $parentpay_ServiceId; ?>" type="hidden" />
<input name="SuccessReturnURL"
value="<?php print $systembasedir; ?>/formsubmit.php?submit=pay-return-success" type="hidden" />
<input name="FailureReturnURL"
value="<?php print $systembasedir; ?>/formsubmit.php?submit=pay-return-failure" type="hidden" />
<input name="SuccessCallbackURL" value="<?php print $systembasedir; ?>/formsubmit.php?submit=pay-callback-success" type="hidden" />
<input name="FailureCallbackURL" value="<?php print $systembasedir; ?>/formsubmit.php?submit=pay-callback-failure" type="hidden" />
<input type="hidden" name="PayerFirstName" value="<?php print substr($user_details->firstname, 0, 100); ?>" /><br />
<input type="hidden" name="PayerLastName" value="<?php print substr($user_details->lastname, 0, 100); ?>"  /><br />
<input name="PayerYear" value="Year: <?php  print substr($user_details->year, 0, 10); ?>" type="hidden" />
<?php 
$itemcount = 0;
for($i = 0; $i < count($booked_activityinfo); $i++) { 
	$cur_activity = $booked_activityinfo[$i]; 
	if($cur_activity != NULL && $cur_activity->cost > 0) {
	$itemcount++;
?>
<input name="Shop<?php print $itemcount; ?>ItemCode" value="<?php print $cur_activity->id; ?>" type="hidden" />
<input name="Shop<?php print $itemcount; ?>ItemCodeDisplay" value="<?php print substr($cur_activity->name, 0, 50); ?>" type="hidden" />
<input name="Shop<?php print $itemcount; ?>ItemDescription" value="<?php print substr($cur_activity->name, 0, 150); ?>" type="hidden" />
<input name="Shop<?php print $itemcount; ?>ItemQuantity" value="1" type="hidden" />
<input name="Shop<?php print $itemcount; ?>ItemPrice" value="£<?php print $cur_activity->cost; ?>.00" type="hidden" />
<?php } } ?>

<input style="width: 250px; margin-bottom: 30px; float:right;" class="sysbutton" type="submit" value="Confirm Order">
<div style="clear:both;"></div>
</div>
</form>

</div>

