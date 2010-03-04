<?php
function sql_escape($value)
{
	$value = mysql_real_escape_string($value);
	$value = mb_ereg_replace('&', '\&', $value);
	return $value;
}
?>