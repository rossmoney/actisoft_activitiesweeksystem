<?php
$inputwidth = 200;
require("includes/config.php");
?>
<div id="contentloader" style="display: none;"></div>
<div id="paperworklist" style="float:left;">
	<u>Manage Paperwork Details</u><br /><br />
<?php
	mysql_connect($mysql_host, $mysql_user, $mysql_pass) or die("Could not connect to MySQL server!");
	mysql_select_db($mysql_db) or die("Could not select $site_title_text database!");
	$paperwork_result = mysql_query("SELECT * FROM `paperwork` ORDER BY `name` ASC");
	while($paperwork = mysql_fetch_object($paperwork_result))
	{
		?>
		<div id="form_<?php print $paperwork->id; ?>">
		<input style="width: 200px;" onclick="beginPaperworkEdit('<?php print $paperwork->id; ?>');" type="button" value="<?php print $paperwork->name; ?>">
		<a id="deletebtn" href="javascript:deletePaperwork('<?php print $paperwork->id; ?>');"></a>
		<br />
		</div>
		<?php
	}
?>
<input class="sysbutton" onclick="addPaperwork();" type="button" value="Add New Form">
</div>

<form name="paperworkedit" action="formsubmit.php" method="POST" enctype="multipart/form-data" style="width: 50%; display: inline-block; float: right;">
<div id="adminform" class="paperworkform" style="display: none;">
<label for="name">Name:</label><input style="width: <?php print $inputwidth; ?>px;" id="name" name="name" type="text" maxlength="50" /><br />
<label for="loc_link">Link:<br />(must start with http)</label><input type="radio" id="loc_link" name="loc" value="Link" onchange="dispURLBox();"><input style="width: 182px; display: none; margin-left: 5px;" id="url" name="url" type="text" maxlength="100" /><br />
<label for="loc_embed">Embed</label><input type="radio" id="loc_embed" name="loc" value="Embed" onchange="dispUploadBox();"><input style="width: 182px; display: none; margin-left: 5px;" id="formfile" name="formfile" type="file"/><br />
<input id="papid" name="papid" type="hidden" value="">
<input class="sysbutton" id="paperworksubmit" onclick="return validatePaperworkForm();" name="submit" type="submit">
</div>
</form>

<div style="clear:both;"></div>