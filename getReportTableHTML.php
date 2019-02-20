<?php // getReportTableHTML.php
// gets single park data from vwtotalcost view
include 'helper.php';
include 'mySqli.php';

$report = isset($_POST['report']) ? $_POST['report']:'';
$years = isset($_POST['years']) ? $_POST['years']:'';
$totals = '';
$places = 0;
switch ($report) {
	case 'dailyRides':
		$query = "	SELECT projects.`park_id`, projects.project_name, seasonalprojectdata.project_year,
						  (SUM(riders)/`seasonalprojectdata`.`service_days`) AS data
					FROM projects INNER JOIN `seasonalprojectdata`
					ON projects.project_id = seasonalprojectdata.project_id
					WHERE peak_season = 'true'
			 		GROUP BY project_name, project_year
			 		ORDER BY park_id, project_name,project_year";

		$query2 = "	SELECT DISTINCT projects.`park_id`
				  	FROM projects INNER JOIN `seasonalprojectdata`
				  	ON projects.project_id = seasonalprojectdata.project_id
				  	WHERE peak_season = 'true'
			 	  	ORDER BY park_id";
		$places = 0;
		break;
	case 'annualRides':
		$query = "	SELECT projects.`park_id`, projects.project_name, seasonalprojectdata.project_year,
						   SUM(riders) AS data
					FROM projects INNER JOIN `seasonalprojectdata`
					ON projects.project_id = seasonalprojectdata.project_id
			 		GROUP BY project_name, project_year
	 				ORDER BY park_id,project_name,project_year";

		$query2 = "	SELECT DISTINCT projects.`park_id`
				  	FROM projects INNER JOIN `seasonalprojectdata`
					ON projects.project_id = seasonalprojectdata.project_id
			 		ORDER BY park_id";
		$places = 0;
		break;
	case 'annualAutoMilesRemoved':
		$query = "	SELECT 	`projects`.`park_id` AS park_id,
							`projects`.`project_name` AS project_name,
							`seasonalprojectdata`.`project_year`,
							sum((seasonalprojectdata.`riders`/`annualprojectdata`.`group_size`)	
							* `annualprojectdata`.`miles_per_trip`) AS `data`
					FROM `projects` INNER JOIN `seasonalprojectdata`
						ON `projects`.`project_id` = `seasonalprojectdata`.`project_id`
					INNER JOIN `annualprojectdata`
						ON `seasonalprojectdata`.`project_id` = `annualprojectdata`.`project_id` 
					WHERE `seasonalprojectdata`.`project_year` = `annualprojectdata`.`project_year`
					GROUP BY park_id,project_name,project_year";

		$query2 = "	SELECT 	`projects`.`park_id` AS park_id
					FROM `projects` INNER JOIN `seasonalprojectdata`
						ON `projects`.`project_id` = `seasonalprojectdata`.`project_id`
					INNER JOIN `annualprojectdata`
						ON `seasonalprojectdata`.`project_id` = `annualprojectdata`.`project_id` 
					WHERE `seasonalprojectdata`.`project_year` = `annualprojectdata`.`project_year`";
		$places = 0;
		break;
	case 'averageDailyRidersPerBus':
		$query = "	SELECT projects.`park_id`, projects.project_name, seasonalprojectdata.project_year, 
							(SUM(riders)/`seasonalprojectdata`.`service_days`)/`seasonalprojectdata`.`average_daily_vehicles` AS data
					FROM projects INNER JOIN `seasonalprojectdata`
					ON projects.project_id = seasonalprojectdata.project_id
					WHERE peak_season = 'true'
						AND `projects`.`mode` = 'bus'	
			 		GROUP BY project_name, project_year
			 		ORDER BY park_id,project_name,project_year";

		$query2 = "	SELECT projects.`park_id`
					FROM projects INNER JOIN `seasonalprojectdata`
					ON projects.project_id = seasonalprojectdata.project_id
					WHERE peak_season = 'true'
						AND `projects`.`mode` = 'bus'	
			 		ORDER BY park_id";
		$places = 0;
		break;
	case 'annualNPSCost':
		$query = "	SELECT projects.`park_id`, projects.project_name, seasonalprojectdata.project_year, 
						   SUM((`vwop_costs`.`op_cost` - (`seasonalprojectdata`.`riders` * `annualprojectdata`.`average_fare`))
								* (1 - `annualprojectdata`.`partner_percent`)) AS data
					FROM projects INNER JOIN `seasonalprojectdata`
										ON projects.project_id = seasonalprojectdata.project_id
								  INNER JOIN `annualprojectdata`
										ON projects.project_id = `annualprojectdata`.`project_id`
						AND `seasonalprojectdata`.`project_year` = `annualprojectdata`.`project_year`
								  INNER JOIN `vwop_costs`
										ON projects.project_id = `vwop_costs`.`project_id`
						AND `seasonalprojectdata`.`project_season` = `vwop_costs`.`project_season`
						AND `seasonalprojectdata`.`project_year` = `vwop_costs`.`year`
					GROUP BY park_id,project_name,year
					ORDER BY park_id, project_name, project_year ";

		$query2 = "	SELECT projects.`park_id`
					FROM projects INNER JOIN `seasonalprojectdata`
										ON projects.project_id = seasonalprojectdata.project_id
								  INNER JOIN `annualprojectdata`
										ON projects.project_id = `annualprojectdata`.`project_id`
						AND `seasonalprojectdata`.`project_year` = `annualprojectdata`.`project_year`
								  INNER JOIN `vwop_costs`
										ON projects.project_id = `vwop_costs`.`project_id`
						AND `seasonalprojectdata`.`project_season` = `vwop_costs`.`project_season`
						AND `seasonalprojectdata`.`project_year` = `vwop_costs`.`year`
					ORDER BY park_id ";
		$places = 0;
		break;
	case 'annualNPSCostPerRide':
		$query = "SELECT projects.`park_id`, projects.project_name, seasonalprojectdata.project_year, 
						   (SUM((`vwop_costs`.`op_cost` - (`seasonalprojectdata`.`riders` * `annualprojectdata`.`average_fare`))
								* (1 - `annualprojectdata`.`partner_percent`)))/ SUM(riders) AS data
					FROM projects INNER JOIN `seasonalprojectdata`
										ON projects.project_id = seasonalprojectdata.project_id
								  INNER JOIN `annualprojectdata`
										ON projects.project_id = `annualprojectdata`.`project_id`
						AND `seasonalprojectdata`.`project_year` = `annualprojectdata`.`project_year`
								  INNER JOIN `vwop_costs`
										ON projects.project_id = `vwop_costs`.`project_id`
						AND `seasonalprojectdata`.`project_season` = `vwop_costs`.`project_season`
						AND `seasonalprojectdata`.`project_year` = `vwop_costs`.`year`
					GROUP BY park_id,project_name,year
					ORDER BY park_id, project_name, project_year ";

		$query2 = "	SELECT projects.`park_id`
					FROM projects INNER JOIN `seasonalprojectdata`
										ON projects.project_id = seasonalprojectdata.project_id
								  INNER JOIN `annualprojectdata`
										ON projects.project_id = `annualprojectdata`.`project_id`
						AND `seasonalprojectdata`.`project_year` = `annualprojectdata`.`project_year`
								  INNER JOIN `vwop_costs`
										ON projects.project_id = `vwop_costs`.`project_id`
						AND `seasonalprojectdata`.`project_season` = `vwop_costs`.`project_season`
						AND `seasonalprojectdata`.`project_year` = `vwop_costs`.`year`
					ORDER BY park_id ";
		$places = 2;
		break;
}
$paramTypes = '';
$params = '';
$res = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
$resParks = queryMysqliPreparedSelect($mysqli, $query2, $paramTypes, $params);

foreach($resParks as $p){
	foreach ($years as $y){
		$parkTotData[$p['park_id']][$y] = 0;
	}
}

$t = generateReportTableHTML($res, $parkTotData, $totals, $places,$years);

$mysqli->close();

echo json_encode(array("data" => $t, "totals" => $totals, "parkTotals" => $parkTotData));
?>
