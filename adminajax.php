<?php
	include_once("includes/config.php");
	include_once("includes/displayfunc.php");
	include_once("includes/admindisplayfunc.php");
	include_once("includes/sysfunc.php");
	switch($_REQUEST['function'])
	{
	case "resetsystem":
		resetSystem();
	break;
	case "saveyear":
		saveYear($_REQUEST);
	break;
	case "showyeareditor":
		showYearEditor($_REQUEST['year']);
	break;
	case "showusereditor":
		showUserEditor($_REQUEST['userid'], $_REQUEST['add']);
	break;
	case "deleteuser":
		deleteUser($_REQUEST['user']);
	break;
	case "addyear":
		addYear($_REQUEST['year']);
	break;
	case "deleteyear":
		deleteYear($_REQUEST['year']);
	break;
	case "addgroup":
		addGroup($_REQUEST['group']);
	break;
	case "deletegroup":
		print deleteGroup($_REQUEST['group']);
	break;
	case "deleteblockeduser":
		deleteBlockedUser($_REQUEST['user']);
	break;
	case "addblockeduser":
		addBlockedUser($_REQUEST['user']);
	break;
	case "removependingpayment":
		removePendingPayment($_REQUEST);
	break;
	case "getpendingpayments":
         getPendingPayments($_REQUEST);
	break;
	case "removependingform":
		removePendingForm($_REQUEST);
	break;
	case "getpendingpaperwork":
   		getPendingPaperwork($_REQUEST);
	break;
	case "embedhtmlreport":
		embedReport($_REQUEST);
	break;
	case "loadpane":
		showAdminPane($_REQUEST);
	break;
	case "getactivity":
		getActivityByID($_REQUEST['actid']);
	break;
	case "deleteactivity":
		deleteActivity($_REQUEST['actid']);
	break;
	case "deletebooking":
		deleteBooking($_REQUEST['uid']);
	break;
	case "generateuserlist":
		showUserList($_REQUEST);
	break;
	case "generategrouplist":
		showGroupList($_REQUEST);
	break;
	case "editbooking":
		editBooking($_REQUEST['userid']);
	break;
	case "getpaperwork":
		getPaperworkByID($_REQUEST['papid']);
	break;
	case "deletepaperwork":
		deletePaperwork($_REQUEST['papid']);
	break;
	case "loadyearrecopts":
		display_year_recognition();
	break;
	case "updateuseryearrec":
		updateUserYearRecognition($_REQUEST);
	break;
	case "deductpayment":
		deductPayment($_REQUEST);
	break;
	case "clearpayments":
		clearPayments($_REQUEST);
	break;
	}
?>