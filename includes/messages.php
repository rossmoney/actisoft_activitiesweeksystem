<?php
function get_message($id, $val = FALSE)
{
	if($id != "")
	{
	switch($id)
	{
	case 0:
		return "<p class='tick'>You have been logged out.</p>";
	break;
	case 1:
		return  "<p class='tick'>Login was sucessful!</p>";
	break;
	case 2:
		return  "<p class='tick'>Activities have been booked.</p>";
	break;
	case 3:
		return  "<p class='cross'>Some activities are already booked up. Please choose again for ones with crosses.</p>";
	break;
	case 4:
		return "<p class='cross'>Please choose something for each day before you complete your booking.</p>";
	break;
	case 5:
		return "<p class='cross'>Session has timed out! You must log in again.</p>";
	break;
	case 6:
		return "<p class='cross'>You have typed an incorrect password!</p>";
	break;
	case 7:
		return "<p class='cross'>Account was not found!</p>";
	break;
	case 8:
		return "<p class='cross'>Login failed: error unknown!</p>";
	break;
	case 9:
		return "<p class='cross'>You have already finalised your booking!</p>";
	break;
	case 10:
		return "<p class='cross'>You cannot view the checkout as you haven't booked anything yet!</p>";
	break;
	case 11:
		return "<p class='cross'>You haven't filled in all the required details!</p>";
	break;
	case 12:
		return  "<p class='tick'>Activity was stored sucessfully!</p>";
	break;
	case 13:
		return  "<p class='cross'>You must enter a number in the cost and number of places fields!</p>";
	break;
	case 14:
		return  "<p class='tick'>Form was deleted sucessfully!</p>";
	break;
	case 15:
		return  "<p class='tick'>Booking was deleted sucessfully!</p>";
	break;
	case 16:
		return  "<p class='tick'>Activity was deleted sucessfully!</p>";
	break;
	case 17:
		return  "<p class='tick'>Form details were stored sucessfully!</p>";
	break;
	case 18:
		return  "<p class='cross'>File upload failed!</p>";
	break;
	case 19:
		return  "<p class='cross'>File upload failed! File too large!</p>";
	break;
	case 20:
		return  "<p class='cross'>Paperwork is in use by activities! Cannot delete!</p>";
	break;
	case 21:
		return  "<p class='cross'>Your account is not ready yet. Please try again later after we have verified you as an admin.</p>";
	break;
	case 22:
		return  "<p class='cross'>System is over capacity! Please try again.</p>";
	break;
	case 24:
		return  "<p class='tick'>Imported account was activated sucessfully! It is now available for student use.</p>";
	break;
	case 25:
		return  "<p class='tick'>User recognition was updated.</p>";
	break;
	case 26:
		return "<p class='cross'>You forgot to enter your username!</p>";
	break;
	case 27:
		return "<p class='cross'>Some of the entered characters are not permitted in the username!</p>";
	break;
	case 28:
		return "<p class='cross'>Staff cannot login unless they are admin.</p>";
	break;
	case 29:
		return  "<p class='tick'>Users paperwork status was sucessfully updated.</p>";
	break;
	case 30:
		return "<p class='cross'>You cannot use the system at this time. This may be because you are blocked or you are already going on a trip and are not required to make a booking.</p>";
	break;
	case 31:
		return  "<p class='tick'>Users payment status was sucessfully updated.</p>";
	break;
	case 32:
		return  "<p class='cross'>System is currently closed for new bookings, sorry.</p>";
	break;
	case 33:
		return  "<p class='cross'>Max logins have been exceeded! If you login now you will be added to a booking queue and will have to wait until spaces are freed.</p>";
	break;
	case 34:
			return  "<p class='cross'>JavaScript is disabled! You must enable it in your browser to use this web application!</p>";
	break;
	case 35:
			return  "<p class='cross'>You are currently in a booking queue at position $val. Please wait... </p>";
	break;
	case 36:
			return  "<p class='cross'>Debug: $val</p>";
	break;
	case 37:
		return  "<p class='tick'>User was sucessfully unblocked!</p>";
	break;
	case 38:
		return  "<p class='tick'>User details sucessfully updated!</p>";
	break;
	case 39:
		return  "<p class='tick'>User was deleted.</p>";
	break;
	case 40:
		return  "<p class='tick'>Users were stored sucessfully!</p>";
	break;
	case 41:
		return  "<p class='cross'>CSV is in the wrong format!</p>";
	break;
	case 42:
		return  "<p class='tick'>Checkout screen text was updated!</p>";
	break;
	case 43:
		return  "<p class='cross'>Days selected aren't in order!</p>";
	break;
	case 44:
		return  "<p class='tick'>System was reset ready for next year!</p>";
	break;
	case 45:
		return  "<p class='tick'>Payment was deducted sucessfully!</p>";
	break;
	case 46:
		return  "<p class='cross'>Account already exists!</p>";
	break;
	case 47:
		return  "<p class='tick'>Users was added!</p>";
	break;
	case 48:
		return "<p class='cross'>Login token timeout, refresh the page!</p>";
	break;
	case 49:
		return "<p class='cross'>Cannot delete this group, it is currently in use by other years.</p>";
	break;
	case 50:
		return "<p class='tick'>Year was updated!</p>";
	break;
	case 51:
		return "<p class='cross'>Payment failed, please try again! Details: $val</p>";
	break;
	case 52:
		return "<p class='tick'>Payment was sucessful!</p>";
	break;
	
	case 53:
		return "<p class='cross'>The mail function failed!</p>";
	break;
	case 54:
		return "<p class='cross'>Please enter a valid email address!</p>";
	break;
	case 55:
		return "<p class='tick'>Reset E-Mail was sucessfully sent. Click on the link in the e-mail to change your password.</p>";
	break;
	case 56:
		return "<p class='tick'>Password was changed!</p>";
	break;
	
	case 57:
		return "<p class='cross'>Passwords don't match!</p>";
	break;
	case 58:
		return "<p class='cross'>Fields that are marked with an asterisk(*) are required!</p>";
	break;

	}
	}
}
?>