<?php
	include_once("includes/displayfunc.php");
	include_once("includes/sysfunc.php");
	include_once("includes/config.php");
	include_once("includes/sessionhandler.php");
	$function = $_REQUEST['function'];
	switch($function)
	{
        case "settok":
			setLoginKey($_REQUEST['tok']);
		break;
		case "setpaymentactivities":
			setActivitiesForPayment($_REQUEST);
		break;
		case "yourdonescreen":
			loadDoneScreen();
		break;
		case "savebooking":   
            ajaxSaveBooking($_REQUEST['bookings']);
        break;
        case "printpostsummary":
			printPostSummary($_REQUEST);
        break;
		case "removelogin":
			removeLogin();
		break;
		case "updatelogintime":
			updateLoginTime();
		break;
        case "checkforcedlogout":
            autoLogoff($_REQUEST);
        break;
		case "getsession_timeelapsed":
			getTimeElapsedInSession();
		break;
		case "authenticate":
			print authenticateLogin($_REQUEST['token']); 
		break;
		case "startover":
			refreshBookingFields($_REQUEST);
		break;
	}
?>