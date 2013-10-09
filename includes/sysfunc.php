<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once("includes/utilities.php");
include_once("includes/config.php");
include_once("includes/sessionhandler.php");
include_once("includes/messages.php");
$userid = $_SESSION['loginuserid'];
mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
mysql_select_db($mysql_db) or die("Could not select awsome database!");

function changePassword($vars) 
{
	$_SESSION['formvars'] = $vars;
	require("includes/config.php");
	require("includes/sessionhandler.php");
	$_SESSION['message'] = "";
	if($vars['newpassword'] == "" || $vars['newpasswordretry'] == "")
	{
		$_SESSION['message'] = 11;
		header("Location: index.php?page=passwordreset&token=". $vars['token']);
	}
	else if($vars['newpassword'] != $vars['newpasswordretry'])
	{
		$_SESSION['message'] = 57;
		header("Location: index.php?page=passwordreset&token=". $vars['token']);
	}
	else
	{
		mysql_query("UPDATE `users` SET `password` = MD5( '" .$vars['newpassword']. "' ) WHERE `id` =" . $vars['UserID']);
		mysql_query("DELETE FROM `passwordresets` WHERE `UserID` = ". $vars['UserID']);
		$_SESSION['message'] = 56;
		header("Location: login");
	}
}

function resetPassword($vars)
{
	$_SESSION['formvars'] = $vars;
	$sendemail = FALSE;
	require("includes/config.php");
	require("includes/sessionhandler.php");
	if(!preg_match("/^[^@]*@[^@]*\.[^@]*$/", $vars['pwdreset_email'])) {
		$_SESSION['message'] = 54;
		header("Location: passwordreset");
	}
	if($_SESSION['message'] != 54) {
		if($GLOBALS['emaildomain'] != "")
		{ 
			$username = explode("@", $vars['pwdreset_email']);
			$to = $username[0] . $GLOBALS['emaildomain'];
			$result = mysql_query("SELECT * FROM `users` WHERE `username` = '". $username[0] . "' LIMIT 1");
			if($vars['pwdreset_email'] == $to)
			{
				$sendemail = TRUE;
			} else {
				$_SESSION['message'] = 7;
				header("Location: passwordreset");
			}
		} else {
			$result = mysql_query("SELECT * FROM `users` WHERE `email` = '". $vars['pwdreset_email'] . "' LIMIT 1");
			if(mysql_num_rows($result) == 1)
			{
				$sendemail = TRUE;
				$to = $vars['pwdreset_email'];
			} else {
				$_SESSION['message'] = 7;
				header("Location: passwordreset");
			}
		}
		if($sendemail)
		{
			$subject = "Reset your $site_title_text Password";
			$message = "Click on this link to reset your password or copy it into your browser.\r\n";
			$token = md5(time() . "s4g");
			$curdir = explode("/", $_SERVER['HTTP_REFERER']);
			array_pop($curdir);
			$curdir = implode("/", $curdir);
			$message .= $curdir . "/index.php?page=passwordreset&token=" . $token . "\r\n";
			$headers = 'From: <' . $adminemail . '>' . "\r\n";
			if(mail($to,$subject,$message,$headers))
			{
				$person_data = mysql_fetch_object($result);
				$resets = mysql_query("SELECT * FROM  `passwordresets` WHERE `UserID` = ". $person_data->id);
				if(mysql_num_rows($resets) > 0)
				{
					mysql_query("UPDATE `passwordresets` SET  `token` =  '" .$token. "' WHERE `UserID` =" . $person_data->id);
				} else {
					mysql_query("INSERT INTO `passwordresets` VALUES('" . $person_data->id . "', '". $token . "');");
				}
				unset($_SESSION['formvars']);
				$_SESSION['message'] = 55;
				header("Location: login");
			} else {
				$_SESSION['message'] = 53;
				header("Location: passwordreset");
			}
		}
	}
}

function completePayment($vars)
{
	$user_details = $_SESSION['userdetails'];
	$activityinfo = $_SESSION['activitydetails'];
	for($i = 0; $i < count($_SESSION['booked_activitys']); $i++)
	{
		$booked_activityinfo[] = $activityinfo[$_SESSION['booked_activitys'][$i]];
	}
	$total = 0;
	for($i = 0; $i < count($booked_activityinfo); $i++) { 
		$cur_activity = $booked_activityinfo[$i]; 
		$total = $total + $cur_activity->cost;
	}
	if($total . $_REQUEST['ServiceID'] == $_REQUEST['Amount'] && count($booked_activityinfo) == $_REQUEST['Quantity'] && $total . $_REQUEST['ServiceID'] == $_REQUEST['TotalPaid'] && $_REQUEST['TotalPaid'] == $_REQUEST['TotalPayable'])
	{
		if(isset($_SESSION['fullpayment']))
		{
			$removePaymentVars = array("uid" => $user_details->id);
			clearPayments($removePaymentVars);
			unset($_SESSION['booked_activitys']);
			unset($_SESSION['fullpayment']);
			unset($_SESSION['cur_bookings']);
		} else {
			var_dump($_SESSION['booked_activitys']);
			$activities = $_SESSION['booked_activitys'];
			$removePaymentVars = array("uid" => $user_details->id, "aid" => implode(":", $activities));
			if(isset($_SESSION['booked_activitys']) && isset($_SESSION['userdetails']))
			{
				removePendingPayment($removePaymentVars);
				unset($_SESSION['booked_activitys']);
				$_SESSION['message'] = 52;
				$_SESSION['parentpayreturn'] = $_REQUEST;
			} else {
				$_SESSION['message'] = 51;
				$_SESSION['messageval'] = "FATAL, Activities IDs Not Set/Marked Off!";
			}
		}
	} else {
		$_SESSION['message'] = 51;
		$_SESSION['messageval'] = "FATAL, Returned parameters do not match expected parameters!";
	}
	header("Location: checkout");
}

function setActivitiesForPayment($vars)
{
	if($vars['full'] == "true")
	{
		$_SESSION['fullpayment'] = TRUE;
	} else {	
		unset($_SESSION['fullpayment']);
	} 
	$_SESSION['booked_activitys'] = explode(",", $vars['activities']);
}

function saveSystemSettings($vars)
{
	require("includes/config.php");
	if($vars['checkouttext'] == "" || $vars["online_start_time"] == "" || $vars["online_end_time"] == "" || $vars["session_time"] == "" || $vars["timetokeepreports"] == "")
	{
		$_SESSION['message'] = 11;
	} else {
		 file_put_contents("includes/admin_settings.php", "<?php\r\n" . 
		 '$online_start_time = ' . "\"".$vars["online_start_time"]."\";\r\n" . 
		 '$online_end_time = ' . "\"".$vars["online_end_time"]."\";\r\n" . 
		 '$session_time = '.$vars["session_time"]."; //mins\r\n" . 
		 '$timetokeepreports = '.$vars["timetokeepreports"]."; //in seconds, currently 30 mins\r\n" .
		 '$emaildomain = '. "\"". addslashes($vars["emaildomain"]) ."\"; //email format: username . emaildomain\r\n" .
		 '$weekduration = '. "\"". addslashes($vars["weekduration"]) ."\"; \r\n" .
		 '$checkouttext = '. "\"".addslashes($vars["checkouttext"])."\";\r\n" .
		 '$systembasedir = '. "\"".addslashes($vars["systembasedir"])."\";\r\n" .
		 '$parentpay_Disable = '. "\"".$vars['parentpay_Disable']."\";\r\n" .
		 '$parentpay_OrgId = '. "\"".$vars['parentpay_OrgId']."\";\r\n" .
		 '$parentpay_UserId = '. "\"".$vars['parentpay_UserId']."\";\r\n" .
		 '$parentpay_ServiceId = '. "\"".$vars['parentpay_ServiceId']."\";\r\n" . "?>");
		$_SESSION['message'] = 42;
	}
	header("Location: admin#Settings");
}

function saveBooking($activityids, $edit_userid = FALSE)
{
	include_once("includes/sessionhandler.php");
	require("includes/config.php");
	$userid = $_SESSION['loginuserid'];
	$activity_data = $_SESSION['activitydetails'];
	if($edit_userid) $userid = $edit_userid;
	deleteBooking($userid);
	$activitiesfordb = implode("|", $activityids);
	if(mysql_query("INSERT INTO `activities_bookings` (`user_id` ,`bookings`) VALUES ('$userid', '$activitiesfordb');")) {
		for($i = 0; $i < count($activityids); $i++)
		{
			if($activity_data[$activityids[$i]])
			{
				//allocate a place (adjust counter)
				mysql_query("UPDATE `activities` SET `placestaken` = `placestaken` + 1 WHERE `id` = $activityids[$i] LIMIT 1 ;");
			}
		}
		if($edit_userid)
		{
			$_SESSION['message'] = 2;
		} else {
			//refresh booking id cache in session
			$user_bookings = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` = $userid LIMIT 1;");
			$user_bookings = mysql_fetch_object($user_bookings);
			$user_bookings->bookings = explode("|", $user_bookings->bookings);
			$_SESSION['userbookings'] = $user_bookings;
			$_SESSION['booked_activitys'] = $activityids;
		}
	} else {
		$_SESSION['message'] = 22;
		unset($_SESSION['booked_activitys']);
		//activities weren't stored so clear cache
	}
}

function returnActivityBookedOnDay($dayid, $bookings)
{
	require("includes/config.php");
	include_once("includes/sessionhandler.php");
	$activity_data = $_SESSION['activitydetails'];
	for($i = 0; $i < count($bookings); $i++)
	{
		$curactivity = $activity_data[$bookings[$i]];
		if($dayid >= ($curactivity->starts - 1) && $dayid <= (($curactivity->starts - 1) + ($curactivity->duration - 1)) && $curactivity->id == $bookings[$i])
		{
			return $curactivity->id;
		}
	}
}

function setWindowLocation($page)
{
	print "<div id=\"windowloc\" style=\"display: none;\">$page</div>";
}

function getuserdetails($userid)
{
	require("includes/config.php");
	$user = mysql_query("SELECT * FROM `users` WHERE `id`=$userid LIMIT 1;");
	$user = mysql_fetch_object($user);
	return $user;
}

function deleteBooking($userid)
{
	require("includes/config.php");
	$booking = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` = $userid LIMIT 1;");
	if(mysql_num_rows($booking) > 0)
	{
		$booking = mysql_fetch_object($booking);
		$booking->bookings = explode("|", $booking->bookings);
		for($i = 0; $i < $weekduration; $i++)
		{
			$actid = returnActivityBookedOnDay($i, $booking->bookings);
			if($actid != "0" && $actid != NULL && $actid != 0 && $actid != "")
			{
				mysql_query("UPDATE `activities` SET `placestaken` = `placestaken` - 1 WHERE `id` = $actid LIMIT 1 ;");
				
			}
			$paperwork = mysql_query("SELECT * FROM `paperwork`");
			if(mysql_num_rows($paperwork) > 0) {
				while($form = @mysql_fetch_object($paperwork))
				{
					mysql_query("DELETE FROM `paperwork_bookings` WHERE `user_id` = $userid AND `paperwork_id` = $form->id LIMIT 1;");
				}
			}
		}
		mysql_query("DELETE FROM `activities_bookings` WHERE `user_id` = $userid LIMIT 1");
		@mysql_query("DELETE FROM `payment_bookings` WHERE `user_id` = $userid");
	}
}

function login($vars)
{
	require("includes/config.php");
	$destpage = "login";
	
	if(!empty($vars['username']))
	{	

	$user_details = mysql_query("SELECT * FROM `users` WHERE `username` = '". $vars['username'] . "' LIMIT 1");
	if(mysql_num_rows($user_details) > 0)
	{
		$accountfound = TRUE;
		$user_details = mysql_fetch_object($user_details);
	
	} else {
		
		if($GLOBALS['emaildomain'] != "")
		{
		
			$vars['username'] = explode("@", $vars['username']);
			$vars['username'] = $vars['username'][0];
			$user_details = mysql_query("SELECT * FROM `users` WHERE `username` = '". $vars['username'] . "' LIMIT 1");
			if(mysql_num_rows($user_details) > 0)
			{
				$accountfound = TRUE;
				$user_details = mysql_fetch_object($user_details);
			}
		
		} else {
		
			$user_details = mysql_query("SELECT * FROM `users` WHERE `email` = '". $vars['username'] .  "' LIMIT 1");
			if(mysql_num_rows($user_details) > 0)
			{
				$accountfound = TRUE;
				$user_details = mysql_fetch_object($user_details);
			}
		
		}
	
	}
		
	if($accountfound)
	{
		if($user_details->password == md5($vars['password']))
		{
			if($user_details->year == 0 && !$user_details->admin)
			{
				$loginok = 21;
			} else {
				$blocked = mysql_query("SELECT * FROM `users_blocked` WHERE `username` = '$user_details->username' OR `username` = '*$user_details->year'");
				if(@mysql_num_rows($blocked) > 0)
				{
					$loginok = 30;
				} else {
					$user_bookings = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` =$user_details->id LIMIT 1;");
					$user_bookings = mysql_fetch_object($user_bookings);
					$loginok = 1;
					if($loginok == 1)
					{
						$_SESSION['loginuserid'] = $user_details->id;
						$_SESSION['userdetails'] = $user_details;
						if($user_details->admin)
						{
							get_activity_info(FALSE, FALSE, TRUE);
						} else {
							if(!$user_bookings)
							{
								get_activity_info($userid);
								unset($_SESSION['userbookings']);
							} else {
								get_activity_info($userid, TRUE);
								$user_bookings->bookings = explode("|", $user_bookings->bookings);
								$_SESSION['userbookings'] = $user_bookings;
							}	
						}
						mysql_query("UPDATE `users` SET `lastlogin` = '".date("Y-m-d")." ".date("H:i:s")."' WHERE `id` =$user_details->id LIMIT 1;");
					}
				}
			}
		} else {
			$loginok = 6;
		}
	} else {
		if($loginok == "") $loginok = 7;
    }

	if($loginok == 1)
	{
		$_SESSION['message'] = $loginok;
		if($user_details->admin)
		{
			$destpage = "admin";
		} else {
			$destpage = "browse";
			if(isset($_SESSION['userbookings'])) {
				$destpage = "checkout";
			} else {
				$destpage = "browse";
			}
		}
	} else {
		$destpage = "login";
	}
		$_SESSION['message'] = $loginok;
	
	} else {
		$_SESSION['message'] = 11;
		
	}
	header("Location: $destpage");
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function get_activity_info($userid = FALSE, $viewbooking = FALSE, $admin = FALSE)
{
	require("includes/config.php");
	mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
	mysql_select_db($mysql_db) or die("Could not select awsome database!");
	$activities = mysql_query("SELECT * FROM `activities` ORDER BY `name` ASC");
	if(!$userid)
	{
		$user_details = $_SESSION['userdetails'];
	} else {
		$user_details = mysql_query("SELECT * FROM `users` WHERE `id` =$userid LIMIT 1;");
		$user_details = mysql_fetch_object($user_details);
	}
    $jsonString = "[";

	while($activity = mysql_fetch_object($activities))
	{
		if($admin)
		{
			$displayactivity = TRUE;
		} else {
			$displayactivity = FALSE;
			$years_available_to = explode("|", $activity->yearsavailable);
			for($i = 0; $i < count($years_available_to); $i++)
			{
				if($years_available_to[$i] == $user_details->year)
				{
					if (($activity->maxstudents - $activity->placestaken) != 0 || $viewbooking)
					{
						$displayactivity = TRUE;
						break;
					}
				}
			}
		}
		if($displayactivity)
        {
				$jsonString = $jsonString.json_encode($activity).",";
				$activityinfo[$activity->id] = $activity;
        }
   }
   $jsonString = substr( $jsonString, 0, -1)."]";
   $_SESSION['activitydetails'] = $activityinfo;
   $_SESSION['activitydetails_json'] = $jsonString;
   if(!$userid)
   {
		return $activityinfo;
   } else { 
		return $jsonString;
   }
}

function addUser($vars)
{
	$result = mysql_query("SELECT `id` FROM `users` WHERE `username` = '".$vars['new_username']."' LIMIT 1");
	if(@mysql_num_rows($result) == 0)
	{
		if($vars['new_username'] == "" || $vars['new_password'] == "" || $vars['new_firstname'] == "" || $vars['new_lastname'] == "" || 
		$vars['new_year'] == "" || $vars['new_admin'] == "")
		{
			$_SESSION['message'] = 11;
		} else {
			if($vars['new_email'] != "" && !preg_match("/^[^@]*@[^@]*\.[^@]*$/", $vars['new_email']))
			{
				$_SESSION['message'] = 54;
			} else {
				if($vars['new_admin'] == "Y") $vars['new_admin'] == 1;
				if($vars['new_admin'] == "N" || $vars['new_admin'] == "") $vars['new_admin'] == 0;
				@mysql_query("INSERT INTO `users` (`email`, `username` ,`password` ,`firstname` ,`lastname` ,`year`, `group_id`,`admin`) VALUES ('".$vars['new_email']."', '".$vars['new_username']."', MD5( '".$vars['new_password']."' ) , '".$vars['new_firstname']."', '".$vars['new_lastname']."', '".$vars['new_year']."', '".$vars['new_group']."', '".$vars['new_admin']."');");
				$_SESSION['message'] = 47;
			}
		}
	} else {
		$_SESSION['message'] = 46;
	}
	header("Location: admin#Users");
}

function updateUserDetails($vars)
{
	if($vars['new_username'] == "" || $vars['new_firstname'] == "" || $vars['new_lastname'] == "" || 
		$vars['new_year'] == "" || $vars['new_admin'] == "")
	{
		$_SESSION['message'] = 11;
	} else {
		if($vars['new_email'] != "" && !preg_match("/^[^@]*@[^@]*\.[^@]*$/", $vars['new_email']))
		{
			$_SESSION['message'] = 54;
		} else {
			$userid = $vars['userid'];
			if($vars['new_password'] != "")
			{
				$passwordstring = ", `password` = MD5( '".$vars['new_password']."' )";
			}
			if($vars['new_admin'] == "Y") $vars['new_admin'] = 1;
			if($vars['new_admin'] == "N" || $vars['new_admin'] == "") $vars['new_admin'] = 0;
			if($vars['new_group'] == "all") $vars['new_group'] = 0;
			$query = "UPDATE `users` SET `email` = '".$vars['new_email']."', `username` = '".$vars['new_username']."',`firstname`='".$vars['new_firstname']."',`lastname` = '".$vars['new_lastname']."', `admin` = '".$vars['new_admin']."', `group_id` = '".$vars['new_group']."', `year`='".$vars['new_year']."' $passwordstring WHERE `id` =$userid LIMIT 1 ;";
			//var_dump($query);
			mysql_query($query);
			$_SESSION['message'] = 38;
		}
	}
	header("Location: admin#Users");
}

function uploadUsers($files)
{
	$done = TRUE;
	$failmsg = 18;
	if($files['csvfile']['error'] == UPLOAD_ERR_NO_FILE) {
		$done = FALSE;
	} elseif ($files['csvfile']['error'] == UPLOAD_ERR_FORM_SIZE || $files['csvfile']['error'] == UPLOAD_ERR_INI_SIZE ) {
		$failmsg = 19;
		$done = FALSE;
	} else {
		$filename = "tmp/userupload.csv";
		if (!@move_uploaded_file($files['csvfile']['tmp_name'], $filename)) 
		{
			$done = FALSE;
		} else {
			if (($handle = fopen($filename, "r")) !== FALSE) {
				$line = fgets($handle);
				$commanum = count_chars($line , 1);
				$commanum = $commanum['44'];
				if($commanum != 6)
				{
					$failmsg = 41;
					$done = FALSE;
				} else {
					rewind($handle);
					while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
						$result = mysql_query("SELECT `id` FROM `users` WHERE `username` = '".$data[0]."' LIMIT 1");
						if(@mysql_num_rows($result) == 0)
						{
							@mysql_query("INSERT INTO `users` (`username` ,`password` ,`firstname` ,`lastname` ,`email`, `year` ,`admin`) VALUES ('".$data[0]."', MD5( '".$data[1]."' ) , '".$data[2]."', '".$data[3]."', '".$data[4]."', '".$data[5]."', '".$data[6]."');");
						}
					}
					$done = TRUE;
				}
				fclose($handle);
			}
			@unlink($filename);
		}
	}
	if(!$done)
	{
		$_SESSION['message'] = $failmsg;
	} else {
		$_SESSION['message'] = 40;
	}
	header("Location: admin#Users");
}

function editAddPaperworkForm($files, $vars)
{
	$done = TRUE;
	$function = $vars['submit'];
	if($vars['loc'] == "Embed")
	{
		$failmsg = 18;
		if($files['formfile']['error'] == UPLOAD_ERR_NO_FILE) {
			$done = FALSE;
		} elseif ($files['formfile']['error'] == UPLOAD_ERR_FORM_SIZE || $files['formfile']['error'] == UPLOAD_ERR_INI_SIZE ) {
			$failmsg = 19;
			$done = FALSE;
		} else {
			$filename = "forms/".$files['formfile']['name'];
			if (!@move_uploaded_file($files['formfile']['tmp_name'], $filename)) 
			{
				$done = FALSE;
			} else {
				if($function == "Add Form")
				{
					mysql_query("INSERT INTO `paperwork` (`name`,`url`) VALUES ('".$vars['name']."', '".$files['formfile']['name']."');");
				} else if($function == "Edit Form") {
					$papid = $vars['papid'];
					$result = mysql_query("SELECT * FROM `paperwork` WHERE `id` =$papid LIMIT 1;");
					$form = mysql_fetch_object($result);
					if(substr($form->url, 0, 4) != "http")
					{
						@unlink("forms/".$form->url);
					}
					mysql_query("UPDATE `paperwork` SET `name` = '".$vars['name']."',`url`='".$files['formfile']['name']."' WHERE `id` =$papid LIMIT 1 ;");
				}
			}
		}
	} else {
		if($function == "Add Form")
		{
			mysql_query("INSERT INTO `paperwork` (`name`,`url`) VALUES ('".$vars['name']."', '".$vars['url']."');");
		} else if($function == "Edit Form") {
			$papid = $vars['papid'];
			$result = mysql_query("SELECT * FROM `paperwork` WHERE `id` =$papid LIMIT 1;");
			$form = mysql_fetch_object($result);
			if(substr($form->url, 0, 4) != "http")
			{
				@unlink("forms/".$form->url);
			}
			mysql_query("UPDATE `paperwork` SET `name` = '".$vars['name']."',`url`='".$vars['url']."' WHERE `id` =$papid LIMIT 1 ;");
		}
	}
	if(!$done)
	{
		$_SESSION['message'] = $failmsg;
	} else {
		$_SESSION['message'] = 17;
	}
	header("Location: admin#Paperwork");
}

function editAddActivity($vars)
{
	$onsite = 0;
	$function = $vars['submit'];
	if($vars['onsite'] != "") $onsite = 1;
	$years = implode("|", $vars['yearsavailable']);
	if(!empty($vars['formsneeded'])) $formsneeded = implode("|", $vars['formsneeded']);
	$starts = $vars['daysavailable'][0] + 1;
	$duration = count($vars['daysavailable']);
	$vars['name'] = htmlspecialchars($vars['name']);
	$vars['desc'] = htmlspecialchars($vars['desc']);
	$vars['additionalinfo'] = htmlspecialchars($vars['additionalinfo']);
	$vars['teacher'] = htmlspecialchars($vars['teacher']);
	$vars['name'] = mysql_real_escape_string($vars['name']);
	$vars['desc'] = mysql_real_escape_string($vars['desc']);
	$vars['additionalinfo'] = mysql_real_escape_string($vars['additionalinfo']);
	$vars['teacher'] = mysql_real_escape_string($vars['teacher']);
	if($function == "Add Activity")
	{
		mysql_query("INSERT INTO `activities` (`name`,`desc`,`additionalinfo`,`teacher`,`maxstudents`,`cost`,`yearsavailable`,`onsite`,`formsneeded`,`starts`,`duration`) VALUES ('".$vars['name']."', '".$vars['desc']."', '".$vars['additionalinfo']."', '".$vars['teacher']."', '".$vars['maxstudents']."', '".$vars['cost']."', '$years', '$onsite', '$formsneeded', '$starts', '$duration');");
	} else if($function == "Edit Activity") {
		$actid = $_REQUEST['activityid'];
		$query = "UPDATE `activities` SET `name` = '".$vars['name']."',`desc`='".$vars['desc']."',`additionalinfo` = '".$vars['additionalinfo']."',`teacher`='".$vars['teacher']."',`maxstudents`='".$vars['maxstudents']."',`cost`='".$vars['cost']."',`yearsavailable`='$years',`onsite`='$onsite',`formsneeded`='$formsneeded',`starts`='$starts',`duration`='$duration' WHERE `id` =$actid LIMIT 1 ;";
		mysql_query($query);
	}
	get_activity_info(FALSE, FALSE, TRUE);
	$_SESSION['message'] = 12;
	header("Location: admin#Activities");
}

function editAddBooking($vars)
{
	require("includes/config.php");
	mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
    mysql_select_db($mysql_db) or die("Could not select $site_title_text database!");
	$function = $vars['submit'];
	$bookedup = 0;
	$activitydetails = $_SESSION['activitydetails'];
	for($i = 0; $i < $weekduration; $i++)
	{
		$activityids[] = $vars[strtolower($weekdays[$i])];
		if($activityids[$i] != "")
		{
			$placestaken = mysql_query("SELECT `placestaken` FROM `activities` WHERE `id` = ".$activityids[$i]." LIMIT 1;");
			$placestaken = mysql_fetch_object($placestaken);
			$placestaken = $placestaken->placestaken;
			$activity_data = $activitydetails[$activityids[$i]];
			if(($activity_data->maxstudents - $placestaken) <= 0)
			{
				$bookedup++;
				$activities_bookedup = $activities_bookedup.$activityids[$i]."|";
			} else {
				$activities_bookedup = $activities_bookedup."0|";
			}
		}
	}
	if($bookedup > 0)
	{
		?>
		<div id="temp">
		<input id="activities_bookedup" type="hidden" value="<?php print $activities_bookedup; ?>" />
		</div>
		<?php
		$_SESSION['message'] = 3;
		include_once("messagegenerator.php");
		$_SESSION['message'] = "";
	} else {
		if($function == "Edit Booking")
		{
			saveBooking($activityids, $vars['userid']);
			setWindowLocation("admin#Bookings");
		} else {
			setWindowLocation("confirm");
		}
	}
}

function getPendingPayments($vars)
{
	require("includes/config.php");
	$activitydetails = $_SESSION['activitydetails'];
	?>
	<u>Payments To Return</u><br />
	<?php
	$userid = $vars['uid'];
	$total_cost = 0;
	$user = getuserdetails($userid);
	$user_activities = mysql_query("SELECT * FROM `payment_bookings` WHERE `user_id` = $userid LIMIT 1");
	$user_activities = mysql_fetch_object($user_activities);
	if(!$user_activities) 
	{
		$user_activities = mysql_query("SELECT * FROM `activities_bookings` WHERE `user_id` = $userid LIMIT 1");
		$user_activities = @mysql_fetch_object($user_activities);
		$user_activities->bookings = explode("|", $user_activities->bookings);
		if($user_activities)
		{
			for($i = 0; $i < $weekduration; $i++)
			{
				$activity_id = returnActivityBookedOnDay($i, $user_activities->bookings);
				$activity = $activitydetails[$activity_id];
				if($activity)
				{
					$total_cost = $total_cost + $activity->cost;
					if($activity->cost > 0) $activities[] = $activity_id;	
				}
			}
		}
		if($total_cost > 0)
		{
			mysql_query("INSERT INTO `payment_bookings` (`user_id` ,`activities_remaining` ,`money_remaining`) VALUES ('$userid',  '".implode("|", $activities)."',  '$total_cost');");	
		}
	} else {
		$activities = explode("|" , $user_activities->activities_remaining);
		$total_cost = $user_activities->money_remaining;
	}
	if(count($activities) > 0)
	{
		$cost_remaining = $total_cost;
		for($i = 0; $i < count($activities); $i++)
		{
			$activity = $activitydetails[$activities[$i]];
			if($activity)
			{
				?>
					<div id="subpayment_<?php print $activities[$i]; ?>">
						<input id="subpaymentchk_<?php print $activities[$i]; ?>" type="checkbox" onchange="removePendingPayment('<?php print $activities[$i]; ?>','<?php print $userid; ?>');" />
						<label style="width: 300px;" for="subpaymentchk_<?php print $activities[$i]; ?>">&pound;<?php 
						if($cost_remaining < $activity->cost && $cost_remaining > 0 && $activity->cost > 0)
						{ 
							print $cost_remaining." left of &pound;".$activity->cost;
						} else {
							print $activity->cost; 
						} ?> for <?php print $activity->name; ?>.</label><br />
					</div>
				<?php
				$cost_remaining = $cost_remaining - $activity->cost;
			}
		}
		if($cost_remaining > 0) print "+ &pound;" . "$cost_remaining additional.<br />"; 
	}
	if($total_cost <= 0)
	{
	?>
		<?php print $user->firstname; ?> is not required to make any payments at this time.
	<?php
	} else {
	?>
		<div id="fullpaymentreturn_link" style="margin-top: 10px;">
			<input id="paymentchk" type="checkbox" onchange="removePendingPayment('<?php print implode(":", $activities); ?>','<?php print $userid; ?>', true);" />
			<label style="width: 300px;" for="paymentchk"><?php print $user->firstname; ?> has paid the full amount of &pound;<?php print $total_cost; ?>.</label>
		</div>
		<br />Tick a box or enter an amount:<br /><br />
		<label for="deductamount" style="width: 120px;">Amount Payed:  &pound;</label><input id="deductamount" name="deductamount" type="text" maxlength="3" size="3" />
		<input name="deduct" onclick="deductPayment('<?php print $userid; ?>');" type="button" value="Deduct From Total"/>
		<br />
		<?php
	}
}

function removePendingForm($vars)
{
	$uid = $vars['uid'];
	$actid = $vars['actid'];
	$formid = $vars['formid'];
	$returnedforms = mysql_query("SELECT * FROM `paperwork_bookings` WHERE `user_id` = $uid AND `paperwork_id` = $formid");
	if(mysql_num_rows($returnedforms) > 0)
	{
		$returned = mysql_fetch_object($returnedforms);
		$activities = explode("|", $returned->activities_returned);
		$activities[] = $actid;
		$activities_returned = implode("|", $activities);
		$query = "UPDATE `paperwork_bookings` SET `activities_returned` = '$activities_returned' WHERE `user_id` = $uid AND `paperwork_id` = $formid LIMIT 1;";
		print $query;
		mysql_query($query);
	} else {
		$query = "INSERT INTO `paperwork_bookings` (`user_id` ,`paperwork_id` ,`activities_returned`) VALUES ('$uid',  '$formid', '$actid');";
		print $query;
		mysql_query($query);
	}
}

function deleteBlockedUser($user)
{
	mysql_query("DELETE FROM `users_blocked` WHERE `username` = '$user' LIMIT 1");
}

function addBlockedUser($user)
{
	$usertest = mysql_query("SELECT * FROM `users_blocked` WHERE `username` = '$user'");
	if(mysql_num_rows($usertest) == 0)
	{
		mysql_query("INSERT INTO `users_blocked` (`username`) VALUES ('$user');");
	}
}

function clearPayments($vars)
{
	$userid = $vars['uid'];
	mysql_query("UPDATE `payment_bookings` SET `activities_remaining` = '', `money_remaining` = 0 WHERE `user_id` = $userid LIMIT 1");
}

function removePendingPayment($vars, $adminpanel = FALSE)
{
	$userid = $vars['uid'];
	$activities =  explode(":", $vars['aid']);
	$result = mysql_query("SELECT * FROM  `payment_bookings` WHERE  `user_id` = $userid LIMIT 1");
	$result = @mysql_fetch_object($result);
	$activitydetails = $_SESSION['activitydetails'];
	if($result)
	{
		$money_remaining = (int)$result->money_remaining;
		$activities_remaining = explode("|" , $result->activities_remaining);
		$cum_total = 0;
		for($i = 0; $i < count($activities_remaining); $i++)
		{
			$activity = $activitydetails[$activities_remaining[$i]];
			for($i2 = 0; $i2 < count($activities); $i2++)
			{
				if($activities[$i2] == $activities_remaining[$i])
				{
					if($activity)
					{
						if($adminpanel) print $cum_total;
						$check_partial = $money_remaining - $cum_total;
						if($check_partial < $activity->cost)
						{
							$money_remaining -= $check_partial;
						} else {
							$money_remaining -= $activity->cost;
						}
						unset($activities_remaining[$i]);
					}
				}
			}
			if($activity) $cum_total += $activity->cost;
		}
		$activities_remaining = implode("|", $activities_remaining);
		mysql_query("UPDATE `payment_bookings` SET `activities_remaining` = '$activities_remaining', `money_remaining` = $money_remaining WHERE `user_id` = $userid LIMIT 1");
	}
}

function deductPayment($vars)
{
	$amount = $vars['amount'];
	$userid = $vars['uid'];
	$result = mysql_query("SELECT * FROM  `payment_bookings` WHERE `user_id` = $userid LIMIT 1");
	$result = @mysql_fetch_object($result);
	$activitydetails = $_SESSION['activitydetails'];
	if($result)
	{
		$money_remaining = (int)$result->money_remaining;
		$money_remaining -= $amount;
		if($money_remaining < 0) $money_remaining = 0;
		$activities_remaining = explode("|" , $result->activities_remaining);
		$activities = $activities_remaining;
		$cumtotal = 0;
		$cum_costs[0] = 0;
		for($i = 0; $i < count($activities); $i++)
		{
			$activity = $activitydetails[$activities[$i]];
			$cumtotal += $activity->cost;
			$cum_costs[$i+1] = $cumtotal;
			if($cum_costs[$i] >= $money_remaining && $cum_costs[$i+1] > $money_remaining)
			{
				unset($activities_remaining[$i]);
			}
		}
		$activities_remaining = implode("|", $activities_remaining);
		mysql_query("UPDATE `payment_bookings` SET `activities_remaining` = '$activities_remaining', `money_remaining` = $money_remaining WHERE `user_id` = $userid LIMIT 1");
	}
}

function deletePaperwork($paperworkid)
{
	$papid = $paperworkid;
	$activities = $_SESSION['activitydetails'];
	$formused = FALSE;
	foreach($activities as $activity)
	{
		$formsneeded = explode("|", $activity->formsneeded);
		for($i = 0; $i < count($formsneeded); $i++)
		{
			if($formsneeded[$i] == $papid)
			{
				$formused = TRUE;
				break;
			}
		}
		if($formused) break;
	}
	if($formused)
	{
		print "used";
	} else {
		$result = mysql_query("SELECT * FROM `paperwork` WHERE `id` = $papid LIMIT 1;");
		$form = mysql_fetch_object($result);
		if(substr($form->url, 0, 4) != "http")
		{
			@unlink("forms/".$form->url);
		}
		mysql_query("DELETE FROM `paperwork` WHERE `id` = $papid LIMIT 1");
	}
}

function deleteUser($user)
{
	mysql_query("DELETE FROM `users` WHERE `id` = $user LIMIT 1");
}

function getActivityByID($activityid)
{
	$activity = mysql_query("SELECT * FROM `activities` WHERE `id` = $activityid LIMIT 1");
	$activity = mysql_fetch_array($activity, MYSQL_NUM);
	$activity[1] = htmlspecialchars_decode($activity[1]);
	$activity[2] = htmlspecialchars_decode($activity[2]);
	print implode("#", $activity);
}

function getPaperworkByID($papid)
{
	$paperwork = mysql_query("SELECT * FROM `paperwork` WHERE `id` = ".$papid." LIMIT 1");
	$paperwork = mysql_fetch_array($paperwork, MYSQL_NUM);
	print implode(",", $paperwork);
}

function deleteActivity($activityid)
{
	mysql_query("DELETE FROM `activities` WHERE `id` = $activityid LIMIT 1");
}

function ajaxSaveBooking($bookings_param)
{
	$bookings = stripslashes(urldecode($bookings_param));
	$bookings = str_replace("\"", "", $bookings);
	$bookings = json_decode($bookings);
	saveBooking($bookings);
	print get_message($_SESSION['message']);
}

function printPostSummary($vars)
{
	require("includes/config.php");
	$_SESSION['bookcheckout'] = "true";
	$bookings = array();
	for($i = 0; $i < $weekduration; $i++)
	{
		if($_REQUEST[strtolower($weekdays[$i])] == "")
		{
			$bookings[] = $bookings[count($bookings)];
		} else {
			$bookings[] = $vars[strtolower($weekdays[$i])];
		}
	}
	$_SESSION['cur_bookings'] =  $bookings;
	include_once("pages/checkout.php");
}

function refreshBookingFields($vars)
{
	if($vars['showtaken'] == "yes")
	{
		$showtaken = true; 
	} else {
		$showtaken = false; 
	}
	startover($showtaken, $vars['uid']);
}

function logout()
{
	require("includes/config.php");
    session_destroy();
	header("Location: login");
}

function resetSystem()
{
	require("includes/config.php");
	mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
	mysql_select_db($mysql_db) or die("Could not select awsome database!");
	mysql_query("UPDATE `activities` SET `placestaken` = 0 WHERE `placestaken` != 0");
	mysql_query("TRUNCATE TABLE `activities_bookings`");
	mysql_query("TRUNCATE TABLE `paperwork_bookings`");
	mysql_query("TRUNCATE TABLE `payment_bookings`");
	get_activity_info(FALSE, FALSE, TRUE);
}

function addYear($year)
{
	if($year != "") mysql_query("INSERT INTO `users_years` (`year` ,`groups`) VALUES ('$year', '');");
}

function deleteYear($year)
{
	mysql_query("DELETE FROM `users_years` WHERE `year` = '$year' LIMIT 1");
}

function addGroup($group)
{
	if($group != "") mysql_query("INSERT INTO `users_groups` (`name`) VALUES ('$group');");
}

function deleteGroup($group)
{ 
	$used = FALSE;
	$years = mysql_query("SELECT * FROM `users_years`");
	while($year = mysql_fetch_object($years))
	{
		$groups = explode("|", $year->groups);
		foreach($groups as $chkgroup)
		{
			if($chkgroup == $group)
			{
				$used = TRUE;
				break;
			}
		}
	}
	if($used)
	{
		return "inuse";
	} else {
		mysql_query("DELETE FROM `users_groups` WHERE `id` = '$group' LIMIT 1");
	}
}

function saveYear($vars)
{
	$newgroups = urldecode($vars['newgroups']);
	mysql_query("UPDATE `users_years` SET `year` = '".$vars['newyear']."', `groups` = '$newgroups' WHERE `year` = ".$vars['year']);
}

?>