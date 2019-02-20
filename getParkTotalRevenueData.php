<?php // getParkTotalRevenueData.php
// gets single park data from vwtotalrevenue view

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
	$query = "SELECT fund_source, SUM(annual_cap_cost) AS 'annual_cap_cost', SUM(annual_op_cost) AS 'annual_op_cost', SUM(total_cost) AS 'total_cost' FROM vwtotalrevenue ";
	if('All' == $parks[0] && 'All' == $status[0] && 'All' == $project[0]){
		$query .= "GROUP BY fund_source ORDER BY fund_source";
		$res = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	}
	else {
		$query .= "WHERE (";
		
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
		$query .= " GROUP BY fund_source ORDER BY fund_source";
		$res = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	}
		
		$txt = $totals = '';
		//generateHTMLTotalRevenueDataTable($res, $projResData, $txt, $totals, $projRes, $projTable);
		generateHTMLTotalRevenueDataTable($res, $txt, $totals);
		
		$mysqli->close();
}

//echo json_encode(array("data" => $txt, "totals" => $totals, "projects" => $projTable, "pNamesData" => $projResData, "query" => $query, "queryParams" => $params, "q" => $q,"arr" => $pRes));
echo json_encode(array("data" => $txt, "totals" => $totals));
?>
