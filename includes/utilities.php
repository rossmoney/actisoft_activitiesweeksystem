<?php
	function stripspaces_andlower($strinput)
	{
		$valarray = explode(" ", strtolower($strinput));
		$stroutput = "";
		for($i = 0; $i < count($valarray); $i++)
		{
			$stroutput = $stroutput.$valarray[$i];
		}
		return $stroutput;
	}
?>