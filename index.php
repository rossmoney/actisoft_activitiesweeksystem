<?php
	error_reporting(E_ALL ^ E_NOTICE);
	$page = $_REQUEST['page'];
	include_once("includes/config.php");
	include_once("includes/displayfunc.php");
	include_once("includes/sysfunc.php");
	$nologinpages = array("404", "login", "passwordreset");
	$validpages = $nologinpages;
	$validpages[] = "browse";
	$validpages[] = "book";
	$validpages[] = "maintenance";
	$validpages[] = "checkout";
	$validpages[] = "admin";
	$validpages[] = "pay";
	$validpages[] = "passwordreset";
	$validpage = TRUE;
	if($maintenancemode)
	{
		$proceed = TRUE;
		$page = "maintenance";
	} else {
		include_once("includes/sessionhandler.php");
		$user_details = $_SESSION['userdetails'];
		$user_bookings = $_SESSION['userbookings'];
		if(!$user_bookings) unset($user_bookings);
		if($page == "" && $_SESSION['loginuserid'] == "") 
		{
			header("Location: login");
			exit();
		}
		if($page == "" && $_SESSION['loginuserid'] != "")
		{
			if($user_details->admin == 1)
			{
				header("Location: admin");
				exit();
			} else {
				if(isset($user_bookings)) {
					header("Location: checkout");
					exit();
				} else {
					header("Location: browse");
					exit();
				}
			}
		}
		$validpage = FALSE;
		for($i = 0; $i < count($validpages); $i++)
		{
			if($validpages[$i] == $page) $validpage = TRUE;
		}
		if($validpage)
		{
			$proceed = TRUE;
			if($_SESSION['loginuserid'] == "")
			{
				$proceed = FALSE;
				for($i = 0; $i < count($nologinpages); $i++)
				{
					if($nologinpages[$i] == $page) $proceed = TRUE;
				}
			}
		}
	}
	if(!$validpage)
	{
		header("Location: 404");
	} else {
		if($proceed)
		{
			ob_start();
			$pagetitle = ucfirst($page);
			if($page == "404")
			{
				$pagetitle = "HTTP Error ".$page;
			}
			add_meta($pagetitle);
			?>
			<div id="header">
				<a id="logo" href="<?php 
					if($_SESSION['loginuserid'] == "")
					{
						print "login";
					} else {
						if($user_details->admin == 1)
						{
							print "admin";
						} else {
							if(isset($user_bookings)) {
								print "checkout";
							} else {
								print "browse";
							}
						}
					}
				?>"></a>
				  <?php
					if(!$report)
					{
					if($_SESSION['loginuserid'] != "")
					{
						?>
						<div id="userstring">logged in as <a href="formsubmit.php?submit=Logout"><?php print $user_details->firstname." ".$user_details->lastname; ?></a></div><?php
					} else {
						?>
						<div id="userstring">not logged in</div>
						<?php
					}
					} else {
						print "<div style=\"font-size: 45px; text-align: center; padding-top: 28px; color: #fff;\">$reportname</div>";
					}
				?>	
				<!-- <div class="versionno">v<?php print $version_num; ?>.<?php print $commit_num; ?></div> -->
			</div>
			<div id="messageloader" style="display: none;"></div>
			<?php
			if($page != "admin")
			{
		?>
			  <div style="margin: 0 auto; width: 800px;">
			  <div id="precontent">
				  <div id="messagebox">
						<?php 
							if(isset($_SESSION['messageval']))
							{
								add_global_messagebox($_SESSION['message'], $_SESSION['messageval']); 
							} else {
								add_global_messagebox($_SESSION['message']); 
							}
						?>
				  </div>
				  <div id="jsDisabled">
						 <?php print get_message(34); ?>
				  </div>
			  </div>
		<?php
			}
			$_SESSION['message'] = "";
			include_once("pages/$page.php");
			if($page != "admin") print "</div>";
			?>
			<div id="footer">
			<span id="servertime"></span>
			<p>
			&copy; 
			<?php
				if($recentupdateyear != 2009) print "2009 - ";
				print $recentupdateyear." ";
				print $copyright;
			?>
			. All rights reserved.
			</p>
			<a target="_blank" href="http://floudy.com" style="background: url(images/floudy.png) no-repeat; width: 116px; height:52px; display:block; float: right; margin-top: -16px;"></a>
			</div>
			</body>
			</html>
			<?php
			$content = ob_get_clean();
			$content = preg_replace('!\s+!smi', ' ', $content);
			print $content; 
		} else {
			header("Location: 404");
		}
	}
?>
