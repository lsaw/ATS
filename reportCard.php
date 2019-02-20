<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';
include 'annPerfMaker.php';

logUser('report card');

if(!isset($_SESSION['uid']))
session_start();

$region 	= isset($_POST['region'])		? $_POST['region']		: 'NERO';
$park 		= isset($_POST['park'])			? $_POST['park']		: '';
$projectNum = isset($_POST['projectNum'])	? $_POST['projectNum']	: '';
$yearPicked = isset($_POST['yearPicked'])	? $_POST['yearPicked']	: '';

$region 	= isset($_GET['region'])		? $_GET['region']		: $region;
$park 		= isset($_GET['park'])			? $_GET['park']			: $park;
$projectNum = isset($_GET['projectNum'])	? $_GET['projectNum']	: $projectNum;
$yearPicked = isset($_POST['yearPicked'])	? $_POST['yearPicked']	: $yearPicked;


// need to deal with situation where you have a project selected for park A, but you 
// then switch to park B, and that park does not have that project.  Sol'n is to 
// query that park for that project. If not there force to 'All'.
if($park && $projectNum != '') {
	$query= "SELECT project_name FROM projects WHERE project_id = ? AND park_id = ?";
	$paramTypes = 'ss';
	$params = array($projectNum, $park);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	if(empty($result))
		$projectNum = '';
}

if($park && $projectNum == '') {// project should default to default for that park
	$query= "SELECT project_id FROM parks_project_defaults WHERE park_id = ?";
	$paramTypes = 's';
	$params = array($park);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	if(!empty($result))
		$projectNum = $result[0]['project_id'];
}


// if region is selected, set park and project accordingly
if(	isset($_POST['regionSelected'])){
	// reset to nothing picked
	$park = $projectNum = '';
}
// if park is selected, set project and region accordingly
if(isset($_POST['parkSelected'])){
	// set region to region for this park
	$query= "SELECT region_id FROM parks WHERE park_id = ?";
	$paramTypes = 's';
	$params = array($park);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	if(!empty($result))
		$region = $result[0]['region_id'];

	// if no park picked, set project to ''
	if('' == $park)
		$projectNum = '';
}
// if project is selected, set park and region accordingly
if(isset($_POST['projectSelected'])){
	if('' == $projectNum){// can't set region or park
	}else{
		// find park for this project
		$query= "SELECT park_id FROM projects WHERE project_id = ?";
		$paramTypes = 's';
		$params = array($projectNum);
		$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
		// set park
		if(!empty($result))
			$park = $result[0]['park_id'];

		// get region for this project
		$query= "SELECT region_id FROM projects
				 INNER JOIN parks ON projects.park_id=parks.park_id WHERE project_id = ? ";
		$paramTypes = 's';
		$params = array($projectNum);
		$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
		if(!empty($result))
			$region = $result[0]['region_id'];
	}
}
// get region, park and project arrays
$resRegions = getRegions($mysqli);
$resParks = getParks($mysqli, $region);
$resProjects = getProjects($mysqli,$park,$region);

echo getHeaderTextGeneral();
echo javascriptHeaderText();
echo commaSeperatorFunctionsText();
echo <<<_END
		<script>
			function setSelectField(selectName, fieldName) {
				$("#" + selectName).val(fieldName)
			}
			function submitForm(th){
				var t = $(th).attr('name')
				
				switch(t){
					case 'region':
						var b = 'regionSelected'
						break;
					case 'park':
						var b = 'parkSelected'
						break;
					case 'projectNum':
						var b = 'projectSelected'
						break;
				}
						
				$('#fieldSelected').attr('name',b)
				$('#fieldSelected').val('true')
				$("form").submit()
			}
			function onLoadFunction() {
				setSelectField('idRegion','$region')
				setSelectField('idParkId','$park')
				setSelectField('idProjectNum','$projectNum')

_END;
echo <<<_END
			}
		</script>
_END;
echo"		<title>ATMS Report Card</title>
	</head>
	<body onload=onLoadFunction()>
";

echo "		<div id='container'>
			<p>Report Card</p>
			<div id='manAssets2'>
				<table>
					<tr>
						<td><p><a href='index.htm'>Admin Menu</a></p></td>
						<td><p><a href='manageProjects.php?park=$park&projNum=$projectNum&region=$region'>Manage Projects</a></p></td>
						<td><p><a href='logout.php' >Log out</a></p></td>
";
echo"					</tr>
				</table>
			</div>
			<div id='manAssets3'>
				<form method='post' name='myform' action='$_SERVER[SCRIPT_NAME]'>
				<input type='hidden' name='action' value=''>
				<input type='hidden' id='fieldSelected' name='' value='true'>
				<table class='manAssTop'>
					<tr>
						<td><p>Region (pick one)</p></td><td><p>Park Code (pick one)</p></td><td><p>Project Name (pick one)</p></td>
					</tr>
					<tr>
					<td><select id='idRegion' name='region' style='width: 20em;' onChange=submitForm(this)>
							<option value=''></option>
";
foreach($resRegions as $row) {
	echo"							<option value='{$row['region_id']}'> {$row['region_name']} </option>
";
}
echo"						</select></td>
						<td><select id='idParkId' name='park' style='width: 6em;' onChange=submitForm(this)>
							<option value=''> </option>
";
foreach ($resParks as $row){
	echo"						<option value='{$row['park_id']}'> {$row['park_id']} </option>
";
}
echo"						</select></td>
						<td><select id='idProjectNum' name='projectNum' style='width: 20em;' onChange=submitForm(this)>
";
foreach($resProjects as $row){
	echo"							<option value='{$row['project_id']}'> {$row['project_name']} </option>
";
}
echo"						</select></td>
";
echo"									
					<tr>
				</table>
			</div>
";

// if we have a park selected, display it's data
if($park && $projectNum){
	$query = "SELECT DISTINCT project_year FROM seasonalprojectdata WHERE project_id=?";
	$paramTypes = 'i';
	$params = array($projectNum);
	$resYears = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$numYears = count($resYears);
	if($numYears > 0){
		foreach ($resYears as $ry){
			$years[] = $ry['project_year'];
		}
	$reports = array();
	$seasons = array('spring', 'summer', 'fall', 'winter');
	
	foreach ($years as $y){
	
	$query = "	SELECT project_year, project_status, cost_per_hour, average_fare, partner_percent, seats_vehicle, miles_per_trip, group_size
				FROM annualprojectdata
				WHERE project_id=? AND project_year=?";
	$paramTypes = 'ii';
	$params = array($projectNum, $y);
	$resAnnPData = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$numAnnP = count($resAnnPData);
	$annPFields = array('project_status', 'cost_per_hour', 'average_fare', 'partner_percent', 'seats_vehicle', 'miles_per_trip', 'group_size');

	$query = "	SELECT project_year, project_season, peak_season, service_days, riders, vehicle_trips_per_day, average_daily_vehicles, hours_per_trip, deadhead_hours_per_vehicle
				FROM seasonalprojectdata
				WHERE project_id=? AND project_year=?
				ORDER BY project_season ASC";
	$paramTypes = 'ii';
	$params = array($projectNum, $y);
	$resSeasPData = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$numSeasons = count($resSeasPData);
	$seasPFields = array('peak_season', 'service_days', 'riders', 'vehicle_trips_per_day', 'average_daily_vehicles', 'hours_per_trip', 'deadhead_hours_per_vehicle');
	$resStatus = getSingleColumnQueryMysqli($mysqli, 'project_status', 'project_status');
	
	if(!$numAnnP){ // set $resAnnPData to ''
		setDataToNull($resAnnPData, 0, $annPFields);
	}
	
	// Set $resSeasPData index for the seasons.
	// while going through, data, find which season is peak
	$spring = $summer = $fall = $winter = -1;
	$peakS = 'summer'; // default to summer
	$i = -1;
	foreach($resSeasPData as $row){
		$i++;
		switch ($resSeasPData[$i]['project_season']) {
			case 'spring':
				$spring = $i;
				if('true' == $resSeasPData[$i]['peak_season'])
					$peakS = 'spring';
				break;
			case 'summer':
				$summer = $i;
				if('true' == $resSeasPData[$i]['peak_season'])
					$peakS = 'summer';
				break;
			case 'fall':
				$fall = $i;
				if('true' == $resSeasPData[$i]['peak_season'])
					$peakS = 'fall';
				break;
			case 'winter':
				$winter = $i;
				if('true' == $resSeasPData[$i]['peak_season'])
					$peakS = 'winter';
				break;
		}
	} // if not full dataset, assign remainder and set to null
	if($i < 3){
		$i++;
		if(-1 == $spring){
			setDataToNull($resSeasPData, $i, $seasPFields);
			$spring = $i;
			$i++;
		}
		if(-1 == $summer){
			setDataToNull($resSeasPData, $i, $seasPFields);
			$summer = $i;
			$i++;
		}
		if(-1 == $fall){
			setDataToNull($resSeasPData, $i, $seasPFields);
			$fall = $i;
			$i++;
		}
		if(-1 == $winter){
			setDataToNull($resSeasPData, $i, $seasPFields);
			$winter = $i;
			$i++;
		}
	}
	$reportNames = array(	'Annual riders',
							'Average peak season riders per day',
//							'Average peak season riders per trip??',
							'Average daily riders',
//							'percent old buses',
							'Daily riders per vehicle',
							'Service hours per day',
							'Service hours per season',
							'Operating cost',
							'Fare box revenue',
							'Operating deficit',
							'Percent NPS funding',
							'Cost per rider',
							'NPS operating cost',
							'NPS cost per ride',// cost per rider
							'Est. car miles removed'
	);
	$reportsToShow = array(	'Annual riders',
							'Average peak season riders per day',
							'NPS operating cost',
							'NPS cost per ride',
							'Est. car miles removed',
							'Operating cost',
							'Cost per rider',
//							'ATS Rides / Park Visits',
//							'Average peak season riders per trip??',
//							'Average daily riders'
//							'percent old buses',
	);
	
	foreach ($reportNames as $rep){
		switch ($rep) {
			case 'Annual riders':
				$reports[$rep][$y] = 0;
				foreach ($seasons as $s){
					$reports[$rep][$y] += $resSeasPData[$$s]['riders'];
				}
				$reports[$rep][$y]=addCommas($reports[$rep][$y]);
			break;
			case 'Average peak season riders per day':
				$reports[$rep][$y] = '';
				if($resSeasPData[$$peakS]['service_days'] > 0){
					$reports[$rep][$y] = $resSeasPData[$$peakS]['riders']/$resSeasPData[$$peakS]['service_days'];
				}
				$reports[$rep][$y] = addCommas(round($reports[$rep][$y], 0));
			break;
			case 'Average daily riders':
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['service_days'] && '' != $resSeasPData[$$s]['service_days']){
						$reports[$rep][$s] = addCommas(round($resSeasPData[$$s]['riders']/$resSeasPData[$$s]['service_days'],0));
					}else{
						$reports[$rep][$s] = '';
					}
				}
				$totServiceDays = $resSeasPData[$spring]['service_days'] + $resSeasPData[$summer]['service_days'] + $resSeasPData[$fall]['service_days'] + $resSeasPData[$winter]['service_days'];
				if(0 != $totServiceDays && '' != $totServiceDays){
					$reports[$rep][$y] = round(($resSeasPData[$spring]['riders'] + $resSeasPData[$summer]['riders'] + $resSeasPData[$fall]['riders'] + $resSeasPData[$winter]['riders'])/$totServiceDays,0);
					$reports[$rep][$y]=addCommas($reports[$rep][$y]);
				}else{
					$reports[$rep][$y] = '';
				}
			break;
			case 'Average riders per trip':
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['vehicle_trips_per_day'] && '' != $resSeasPData[$$s]['vehicle_trips_per_day']){
						$reports[$rep][$s] = round($reports['Average daily riders'][$s]/$resSeasPData[$$s]['vehicle_trips_per_day'],1);
					}else{
						$reports[$rep][$s] = '';
					}
				}
				$reports[$rep]['total'] = '';
			break;
			case 'Daily riders per vehicle':
				$reports[$rep][$y] = 0;
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['average_daily_vehicles'] && '' != $resSeasPData[$$s]['average_daily_vehicles']){
						$reports[$rep][$s] = addCommas(round($reports['Average daily riders'][$s]/$resSeasPData[$$s]['average_daily_vehicles'],0));
					}else{
						$reports[$rep][$s] = '';
					}
					$reports[$rep][$y]+=($resSeasPData[$$s]['average_daily_vehicles'] * $resSeasPData[$$s]['service_days']); 
				}
				$totServiceDays = $resSeasPData[$spring]['service_days'] + $resSeasPData[$summer]['service_days'] + $resSeasPData[$fall]['service_days'] + $resSeasPData[$winter]['service_days'];
				if(0 != $totServiceDays && '' != $totServiceDays){
					$reports[$rep][$y] = addCommas(round($reports[$rep][$y]/$totServiceDays,0));
				}else{
					$reports[$rep][$y] = '';
				}
			break;
			case 'Service hours per day':
				foreach ($seasons as $s){
					$reports[$rep][$s] = round(($resSeasPData[$$s]['vehicle_trips_per_day'] * $resSeasPData[$$s]['hours_per_trip']) + ($resSeasPData[$$s]['average_daily_vehicles'] * $resSeasPData[$$s]['deadhead_hours_per_vehicle']),1);
//					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : $reports[$rep][$s];
				}
//				$reports[$rep]['total'] = '';
			break;
			case 'Service hours per season':
//				$totShps = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = round(($reports['Service hours per day'][$s] * $resSeasPData[$$s]['service_days']),0);
//					$totShps+=  $reports[$rep][$s];
//					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas($reports[$rep][$s]);
				}
//				$reports[$rep]['total'] = 0 == $totShps ? '' : addCommas($totShps);
			break;
			case 'Operating cost':
				$totOpc = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = round(stripCommas($reports['Service hours per season'][$s]) * $resAnnPData[0]['cost_per_hour'],0);
					$totOpc+=  $reports[$rep][$s];
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas($reports[$rep][$s]);
				}
				$reports[$rep][$y] = 0 == $totOpc ? '' : addCommas($totOpc);
				$reports[$rep]['total'] = 0 == $totOpc ? '' : addCommas($totOpc);
				break;
			case 'Fare box revenue':
//				$reports[$rep][$y] = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = $resSeasPData[$$s]['riders'] * $resAnnPData[0]['average_fare'];
//					$reports[$rep][$y]+=  $reports[$rep][$s];
//					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas($reports[$rep][$s]);
				}
//				$reports[$rep]['total'] = 0 == $totFareBox ? '' : addCommas($totFareBox);
			break;
			case 'Operating deficit':
//				$reports[$rep][$y] = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = stripCommas($reports['Operating cost'][$s]) - stripCommas($reports['Fare box revenue'][$s]);
//					$reports[$rep][$y]+=  $reports[$rep][$s];
//					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas($reports[$rep][$s]);
				}
//				$reports[$rep]['total'] = 0 == $totOpDef ? '' : addCommas($totOpDef);
			break;
			case 'Percent NPS funding':
				$val = (1 - $resAnnPData[0]['partner_percent']) * 100;
				foreach ($seasons as $s){
					$reports[$rep][$s] = $val;
					if( 0 == $reports['Operating deficit'][$s] || '' == $reports['Operating deficit'][$s])
						$reports[$rep][$s] = '';
				}
//				if( 0 == $reports['Operating deficit']['total'] || '' == $reports['Operating deficit']['total']){
//					$reports[$rep]['total'] = '';
//				}else{
//					$reports[$rep]['total'] = $val;
//				}
			break;
			case 'Partner funding':
				$totPf = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = round(stripCommas($reports['Operating deficit'][$s]) * $resAnnPData[0]['partner_percent'],0);
					$totPf+=  $reports[$rep][$s];
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas($reports[$rep][$s]);
				}
				$reports[$rep]['total'] = 0 == $totPf ? '' : addCommas($totPf);
			break;
			case 'NPS operating cost':
				$totOpCost = 0;
				$reports[$rep][$y] = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = stripCommas($reports['Operating deficit'][$s]) * $reports['Percent NPS funding'][$s]/100;
					$totOpCost+=  $reports[$rep][$s];
					$reports[$rep][$y]+=  $reports[$rep][$s];
//					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas(round($reports[$rep][$s],0));
				}
				$reports[$rep]['total'] = 0 == $totOpCost ? '' : addCommas(round($totOpCost),0);
				$reports[$rep][$y] = 0 == $reports[$rep][$y] ? '' : addCommas(round($reports[$rep][$y]),0);
			break;
			case 'Cost per rider':
				$totRiders = 0;
				$reports[$rep][$y] = 0;
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['riders'] && '' != $resSeasPData[$$s]['riders']){
						$reports[$rep][$s] = round(stripCommas($reports['Operating cost'][$s])/$resSeasPData[$$s]['riders'],2);
					}else{
						$reports[$rep][$s] = '';
					}
					$totRiders+= $resSeasPData[$$s]['riders'];
					$reports[$rep][$y]+= $resSeasPData[$$s]['riders'];
				}
				if(0 != $totRiders && '' != $totRiders){
					$reports[$rep]['total'] = round(stripCommas($reports['Operating cost']['total'])/$totRiders,2);
					$reports[$rep][$y] = addCommas(round(stripCommas($reports['Operating cost']['total'])/$reports[$rep][$y],2));
				}else{
					$reports[$rep]['total'] = '';
					$reports[$rep][$y] = '';
				}
			break;
			case 'NPS cost per ride':
				$totRiders = 0;
				$reports[$rep][$y] = 0;
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['riders'] && '' != $resSeasPData[$$s]['riders']){
						$reports[$rep][$s] = round(stripCommas($reports['NPS operating cost'][$s])/$resSeasPData[$$s]['riders'],2);
					}else{
						$reports[$rep][$s] = '';
					}
					$totRiders+= $resSeasPData[$$s]['riders'];
					$reports[$rep][$y]+= $resSeasPData[$$s]['riders'];
				}
				if(0 != $totRiders && '' != $totRiders){
					$reports[$rep]['total'] = round(stripCommas($reports['NPS operating cost']['total'])/$totRiders,2);
					$reports[$rep][$y] = round(stripCommas($reports['NPS operating cost'][$y])/$totRiders,2);
				}else{
					$reports[$rep]['total'] = '';
				}
//				if(0 != $reports['NPS cost per ride'][$y] && '' != $reports['NPS cost per ride'][$y]){
//					$reports[$rep][$y] = round(stripCommas($reports['NPS operating cost'][$y])/$reports['Cost per rider'][$y],2);
//					// this kluge is required because .0998 displays as $0.1, not $0.10 as desired
//					$reports[$rep][$y] = '$0' == $reports[$rep][$y] ? $reports[$rep][$y] : str_pad($reports[$rep][$y],5,"0",STR_PAD_RIGHT);
//					$reports[$rep][$y] = addCommas($reports[$rep][$y]);
//				}else{
//					$reports[$rep][$y] = '';
//				}
			break;
			case 'Riders per trip/capacity':
				foreach ($seasons as $s){
					if(0 != $resAnnPData[0]['seats_vehicle'] && '' != $resAnnPData[0]['seats_vehicle']){
						$reports[$rep][$s] = round($reports['Average riders per trip'][$s]/$resAnnPData[0]['seats_vehicle'],1);
					}else{
						$reports[$rep][$s] = '';
					}
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : round($reports[$rep][$s]*100,0).'%';
				}
					$reports[$rep]['total'] = '';
			break;

			case 'Est. car miles removed':
				$reports[$rep][$y] = 0;
				foreach ($seasons as $s){
					if(0 != $resAnnPData[0]['group_size'] && '' != $resAnnPData[0]['group_size']){
						$reports[$rep][$s] = round(($resSeasPData[$$s]['riders']/$resAnnPData[0]['group_size'])*$resAnnPData[0]['miles_per_trip'],0);
					}else{
						$reports[$rep][$s] = '';
					}
					$reports[$rep][$y]+= $reports[$rep][$s];
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas(round($reports[$rep][$s],0));
				}
				$reports[$rep][$y] = 0 == $reports[$rep][$y] ? '' : addCommas(round($reports[$rep][$y],0));
			break;
		}
	}
}
	$sClass = 'even';
	echo "			<div class='fmssAndFunding'>
				<table class='reportCard'>
					<tr><th></th>
";
	foreach($years as $y) {
		echo "					<th>$y</th>
";
	}
	echo "</tr>
";
	
	foreach ($reportsToShow as $r){
		echo"					<tr>
						<td>$r</td>";
		foreach ($years as $y){
			echo "<td style='text-align:right;'>{$reports[$r][$y]}</td>";
		}
		echo "					</tr>
";
	}
	echo "</table>";
}else{ 
	echo "No data to display";
}
}
	echo "
				</form>
";
	
echo"		</div>
";
echo assetTextBottom();
?>