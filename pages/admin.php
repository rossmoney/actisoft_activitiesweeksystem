<?php
	require("includes/config.php");
	$userid = $_SESSION['loginuserid'];
	$user_details = $_SESSION['userdetails'];
	$activitydetails = $_SESSION['activitydetails'];
	if($user_details->admin == 0)
	{
		if(isset($user_bookings)) {
			header("Location: checkout");
			exit();
		} else {
			header("Location: browse");
			exit();
		}
	} 
?>
<table id="adminmainmenu" border="0" cellpadding="0" cellspacing="0">
  <tr>
  	<?php
	for($i = 0; $i < count($adminpanes); $i++)
	{
		?>
		<td><div align="center"><a id="<?php print $adminpanes[$i]; ?>" style="background: url(images/icons/icon-<?php print strtolower($adminpanes[$i]); ?>.png) center top no-repeat;" href="#<?php print $adminpanes[$i]; ?>" class="adminmenu_submenu"><?php print $adminpanes[$i]; ?></a></div></td>
		<?php 
	}
	?>
  </tr>
</table>
<div id="admincontent">
	<div id="messagebox" style="margin-bottom: 0px; padding: 5px; padding-top: 0px;">
		<?php 
			add_global_messagebox($_SESSION['message']); 
		?>
	</div>
	<div id="jsDisabled">
		<?php print get_message(34); ?>
	</div>
	<div id="reportmenu" style="margin-top: 20px;"></div>
	
	<div id="adminpanecontent">
		<?php display_admin_home_text(); ?>
	</div>
</div>
<a href="#Settings" style="background: url(images/icons/icon-settings.png) no-repeat; width: 65px; height: 72px; display:block; float: right; margin-bottom: 5px; margin-right: 20px;"></a>
<div style="clear: right;"></div>