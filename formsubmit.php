<?php
	include_once("includes/sysfunc.php");
	switch($_REQUEST['submit'])
	{
	case "Go!":
		login($_REQUEST);
	break;
	case "Change Password":
		changePassword($_REQUEST);
	break;
	case "Reset Password":
		resetPassword($_REQUEST);
	break;
	case "pay-return-success":
	case "pay-callback-success":
		completePayment($_REQUEST);
	break;
	case "pay-return-failure":
	case "pay-callback-failure":
		$_SESSION['message'] = 51;
		$_SESSION['messageval'] = $_REQUEST['ErrorMsg'];
		header("Location: pay");
	break;
	case "Update Settings":
		saveSystemSettings($_REQUEST);
	break;
	case "Add New User":
		addUser($_REQUEST);
	break;
	case "Upload Users":
		uploadUsers($_FILES);
	break;
	case "Update User Details":
		updateUserDetails($_REQUEST);
	break;
	case "Edit Form":
	case "Add Form":
		editAddPaperworkForm($_FILES, $_REQUEST);
	break;
	case "Edit Activity":
	case "Add Activity":
		editAddActivity($_REQUEST);
	break;
	case "Book Now":
	case "Edit Booking":
		editAddBooking($_REQUEST);
	break;
	case "Logout":
		logout();
	break;
	}
?>