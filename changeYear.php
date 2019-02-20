<?php // checkuser.php
// This file used for signup.php only
include_once 'mySqli.php';
include_once 'helper.php';

if (isset($_POST['year']) && isset($_POST['project'])){
	$year = $_POST['year'];
	$project = $_POST['project'];

	$query = "UPDATE projects SET total_cost_year=$year WHERE project_id = ?";
	$paramTypes = 's';
	$params = array($project);
	$res = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	
	echo $res;
}