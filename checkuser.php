<?php // checkuser.php
// This file used for signup.php only
include_once 'mySqli.php';
include_once 'helper.php';

if (isset($_POST['user']))
{
	$user = $_POST['user'];
	$query = "SELECT * FROM users WHERE user=?";
	$paramTypes = 's';
	$params = array($user);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	
	if(count($result) > 0)
	
//	if (mysql_num_rows(queryMysql($query)))
		echo "<font color=red>&nbsp;&larr;
			 Sorry, already taken</font>";
	else echo "<font color=green>&nbsp;&larr;
			 Username available</font>";
}
?>
