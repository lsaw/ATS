<?php

//$dbhost  = 'mariadb-002.wc1.ord1.stabletransit.com';
$dbhost  = 'mysql51-002.wc1.ord1.stabletransit.com';
$dbuser  = '351423_crikdb';
$dbpass  = 'CedarAve1';
$dbname  = '351423_crikelair';
$dbtable = 'users';
//$dbhost  = 'http://www.mysql51-002.wc1.ord1.stabletransit.com';


$salt1 = "&&L#@";
$salt2 = "^b{*!";
/*
$dbhost  = 'localhost';
$dbuser  = 'root';
$dbpass  = '';
$dbname = 'tom';
$dbengine = 'innodb';
*/

$mysqli = new mysqli($dbhost, $dbuser, $dbpass);
if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
};
$mysqli->select_db($dbname);
?>