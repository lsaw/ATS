<?php // login.php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

// $uid and $level come from accessControl.php
logUser('login');

if(!isset($_SESSION['uid']))
	session_start();
	
// Functions are used here because after change user name, that 
// page outputs the same page but with a diferent header
echo getHeaderTextGeneral();
echo loginTextTop();
echo "<th>Welcome to the ATS Management System navigation page: {$_SESSION['user']}</th>
";

if($level == '1')
{
	echo loginTextAllowSignUp();
}
echo loginTextAllowChange();
echo loginTextBottom();


?>