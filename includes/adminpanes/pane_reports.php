<div id="reportplaceholder">
	<div align="center">
		<div style="background: url(images/icons/icon-reports.png) center top no-repeat; width: 200px; height: 100px; display:block; color: #FFFFFF; font-size: 18px; font-weight: bold; line-height: 160px;">Reports Wizard</div>
		<form id="formbody" style="width: 50%; text-align:right; ">
			<label for="reporttype">Template:</label>
			<select id="reporttype">
				<?php
					require("includes/config.php");
					for($i = 0; $i < count($reports); $i++)
					{
						?>
						<option value="<?php print $i; ?>"><?php print $reports[$i]; ?></option>
						<?php
					}	
				?>
			</select><br />
			<label for="reportformat">Format:</label>
			<select id="reportformat">
				<option value="html">HTML (Embedded)</option>
				<option value="htmldirect">HTML (Direct Download)</option>
				<option value="csv">CSV</option>
			</select><br />
			<input class="sysbutton" type="button" onclick="showReport();" value="Generate" />
		</form>
	</div>
</div>