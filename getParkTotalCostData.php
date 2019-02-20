<?php // getParkTotalCostData.php
// gets single park data from vwtotalcost view

include_once 'helper.php';
include_once 'mySqli.php';
$mysqli->select_db($dbname);

date_default_timezone_set('America/New_York');
$txt = 'no data';
$totals = 0;
$add = '';
$haveParks = $haveStatus = $haveProjects = false;
$projResData = array();
if (isset($_POST['parks']) && isset($_POST['status']) && isset($_POST['project'])){
	$parks = $_POST['parks'];
	$status = $_POST['status'];
	$project = $_POST['project'];
	
	$paramTypes = '';
	$params = array();
	$paramTypes2 = '';
	$params2 = array();
	if('All' == $parks[0] && 'All' == $status[0] && 'All' == $project[0]){
		$query = "SELECT * FROM vwtotalcost ORDER BY park_id, project_name, asset_name";
		$q = "SELECT DISTINCT project_name FROM vwtotalcost ORDER BY project_name";
		$pRes = queryMysqliPreparedSelect($mysqli, $q, $paramTypes, $params);
		$res = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	}
	else {
		$query = "SELECT * FROM vwtotalcost WHERE (";
		$q = "SELECT DISTINCT project_name FROM vwtotalcost WHERE (";
		
		if(!('All' == $parks[0])){
			$haveParks = true;
			$firstTime = true;
			foreach($parks as $park){
				if($firstTime){
					$firstTime = false;
				}
				else {
					$add .= " OR ";
				}
				$add .= "park_id=?";
				$paramTypes .= 's';
				$params[]=$park;
			}
			$add .= ")";
		}

		if(!('All' == $project[0])){
			$haveProjects = true;
			if($haveParks){
				$add .= " AND (";
			}
		
			$firstTime = true;
			foreach($project as $proj){
				if($firstTime){
					$firstTime = false;
				}
				else {
					$add .= " OR ";
				}
				$add .= "project_name=?";
				$paramTypes .= 's';
				$params[]=$proj;
			}
			$add .= ")";
		}
		
		$q .= $add;
		$q .= " ORDER BY project_name";
		$pRes = queryMysqliPreparedSelect($mysqli, $q, $paramTypes, $params);
		
		if(!('All' == $status[0])){
			$haveStatus = true;
			if($haveParks || $haveProjects){
				$add .= " AND (";
			}
		
			$firstTime = true;
			foreach($status as $stat){
				if($firstTime){
					$firstTime = false;
				}
				else {
					$add .= " OR ";
				}
				$add .= "asset_status=?";
				$paramTypes .= 's';
				$params[]=$stat;
			}
			$add .= ")";
		}
		
		$query .= $add;
		$query .= " ORDER BY park_id, project_name, asset_name";
		$res = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	}
		
		foreach($pRes as $p){
			$projRes[] = $p['project_name'];
			$projResData[$p['project_name']] = array('annual_cap_cost'=>0,'annual_op_cost'=>0,'total_cost'=>0);
		}

		$txt = $totals = '';
		generateHTMLTotalCostDataTable($res, $projResData, $txt, $totals, $projRes, $projTable);

		$mysqli->close();
}

//echo json_encode(array("data" => $txt, "totals" => $totals, "projects" => $projTable, "pNamesData" => $projResData, "query" => $query, "queryParams" => $params, "q" => $q,"arr" => $pRes));
echo json_encode(array("data" => $txt, "totals" => $totals, "projects" => $projTable, "pNamesData" => $projResData));
?>
