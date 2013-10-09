<?php
	$user_details = $_SESSION['userdetails'];
	if($user_details->admin == 0 && $user_details != NULL)
	{
		ob_end_clean();
		if(isset($user_bookings)) {
			header("Location: checkout");
			exit();
		} else {
			header("Location: browse");
			exit();
		}
	}
    include_once("includes/config.php");
    mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
    mysql_select_db($mysql_db) or die("Could not select $site_title_text database!");
	$usernametext = "Username or E-Mail";
	$passwordtext = "Password";
?>
<div style="margin: 0 auto; width: 450px;">
<form id="login" name="login" method="post" action="formsubmit.php">
	<input id="username" class="login_form_fields" name="username" type="text" value="<?php print $usernametext; ?>" maxlength="50" tabindex="1" onFocus="if (this.value=='<?php print $usernametext; ?>') { this.value=''; $(this).removeClass('login_form_fields'); }" onBlur="if ('' == this.value) {  this.value = '<?php print $usernametext; ?>'; $(this).addClass('login_form_fields'); }" onKeyDown="if (event.keyCode == 13) document.getElementById('btnLogin').click();" />
		
	<input id="password_field_text" class="login_form_fields" type="text" value="<?php print $passwordtext; ?>" maxlength="50" tabindex="2" onFocus="$('#password_field_text').hide(); $('#password_field_password').show(); $('#password_field_password').focus(); $('#password_field_password').val('');" />
		
	<input id="password_field_password" style="display: none;" name="password" type="password" maxlength="50" tabindex="2" onFocus="if (this.value=='<?php print $passwordtext; ?>') { this.value=''; $(this).removeClass('login_form_fields'); this.setAttribute('type','password'); }" onBlur="if ('' == this.value) { $('#password_field_text').show(); $('#password_field_password').hide(); }" onKeyDown="if (event.keyCode == 13) document.getElementById('btnLogin').click();" />

	<a id="resetpassword" href="passwordreset">forgotten your details?</a>
	<input type="submit" name="submit" id="btnLogin" value="Go!" />
</form>
</div>
