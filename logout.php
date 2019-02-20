<?php // logout.php
require_once 'authorize.php';
require_once 'mySqli.php';
require_once 'helper.php';

if(!isset($_SESSION['uid']))
	session_start();
logUser('logout');

$auth = new Auth();
$auth->logout();

?>