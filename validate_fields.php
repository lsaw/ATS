<?php // validate_fields.php
// These first 2 were ripped off from Robin Nixon and his
// PHP, MySQL & JavaScript book
function validate_username($field) {
	if ($field == "") return "No Username was entered<br />";
	else if (strlen($field) < 5)
		return "Usernames must be at least 5 characters<br />";
	else if (preg_match("/[^a-zA-Z0-9!@#%&*+=?_-]/", $field))
		return "Only letters, numbers, and _-!@#%&*+=? in usernames<br />";
	return "";		
}

function validate_password($field) {
	if ($field == "") return "No Password was entered<br />";
	else if (strlen($field) < 6)
		return "Passwords must be at least 6 characters<br />";
	else if (!preg_match("/[a-z]/", $field) ||
			 !preg_match("/[A-Z]/", $field) ||
			 !preg_match("/[0-9]/", $field))
		return "Passwords require 1 each of a-z, A-Z and 0-9<br />";
	return "";
}
	
function validateUP1IsUP2($U1, $U2, $P1, $P2) {
	// All fields must be filled out
	if ($U1 == "" || $U2 == "" || $P1 == "" || $P2 == "")
		return "New user name and password must be entered twice<br />";
	// Fields must match
	else if ($U1 != $U2 || $P1 != $P2)
		return "Verification user name or password did not match<br />";
	return "";		
}
// No datafile name warning
function validate_datafile($f) {
	if ($f == "" )
		return "Data file name must be entered<br />";
	return "";
}
?>