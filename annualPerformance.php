<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';
include 'annPerfMaker.php';

logUser('annualPerformance');

if(!isset($_SESSION['uid']))
session_start();

$region 	= isset($_POST['region'])		? $_POST['region'] 	   : 'NERO';
$park 		= isset($_POST['park'])			? $_POST['park'] 	   : '';
$projectNum = isset($_POST['projectNum']) 	? $_POST['projectNum'] : '';
$yearPicked = isset($_POST['yearPicked'])	? $_POST['yearPicked'] : '';
// $showReport defaults to 'false', or whatever is its current value
$showReport	= isset($_POST['showReport']) 	? $_POST['showReport'] : 'false';
// if showReportButton is pressed, flip t/f value of showReport
$showReport = isset($_POST['showReportButton'])	? 'false' == $_POST['showReport'] ? 'true' : 'false' : $showReport;

$region 	= isset($_GET['region'])		? $_GET['region'] 	   : $region;
$park 		= isset($_GET['park'])			? $_GET['park'] 	   : $park;
$projectNum = isset($_GET['projectNum']) 	? $_GET['projectNum']  : $projectNum;
$yearPicked = isset($_POST['yearPicked'])	? $_POST['yearPicked'] : $yearPicked;

//if(isset($_GET['allowDeletes'])) {// flips $allowDeletes
//	$test = $_GET['allowDeletes'];
//	$allowDeletes = $test ? 0 : 1;
//}
//else {// if $allowDeletes not yet set (firsst time in) set to '0'
//	$allowDeletes = 0;
//}

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

if(isset($_POST['post'])) {
	// if field is blank, save as NULL
	// if field is not set, save as NULL
	// otherwise load what is in field
	$year = isset($_POST['yearSet']) ? $_POST['yearSet'] : '';
	$serviceDaysSpring = isset($_POST['serviceDaysSpring']) ? $_POST['serviceDaysSpring'] : '';
	$serviceDaysSummer = isset($_POST['serviceDaysSummer']) ? $_POST['serviceDaysSummer'] : '';
	$serviceDaysFall = isset($_POST['serviceDaysFall']) ? $_POST['serviceDaysFall'] : '';
	$serviceDaysWinter = isset($_POST['serviceDaysWinter']) ? $_POST['serviceDaysWinter'] : '';
	$oneWayRidesSpring = isset($_POST['oneWayRidesSpring']) ? str_replace(',', '', $_POST['oneWayRidesSpring']) : '';
	$oneWayRidesSummer = isset($_POST['oneWayRidesSummer']) ? str_replace(',', '', $_POST['oneWayRidesSummer']) : '';
	$oneWayRidesFall = isset($_POST['oneWayRidesFall']) ? str_replace(',', '', $_POST['oneWayRidesFall']) : '';
	$oneWayRidesWinter = isset($_POST['oneWayRidesWinter']) ? str_replace(',', '', $_POST['oneWayRidesWinter']) : '';
	$tripsPerDaySpring = isset($_POST['tripsPerDaySpring']) ? $_POST['tripsPerDaySpring'] : '';
	$tripsPerDaySummer = isset($_POST['tripsPerDaySummer']) ? $_POST['tripsPerDaySummer'] : '';
	$tripsPerDayFall = isset($_POST['tripsPerDayFall']) ? $_POST['tripsPerDayFall'] : '';
	$tripsPerDayWinter = isset($_POST['tripsPerDayWinter']) ? $_POST['tripsPerDayWinter'] : '';
	$peakVehiclesSpring = isset($_POST['peakVehiclesSpring']) ? $_POST['peakVehiclesSpring'] : '';
	$peakVehiclesSummer = isset($_POST['peakVehiclesSummer']) ? $_POST['peakVehiclesSummer'] : '';
	$peakVehiclesFall = isset($_POST['peakVehiclesFall']) ? $_POST['peakVehiclesFall'] : '';
	$peakVehiclesWinter = isset($_POST['peakVehiclesWinter']) ? $_POST['peakVehiclesWinter'] : '';
	$hoursPerTripSpring = isset($_POST['hoursPerTripSpring']) ? $_POST['hoursPerTripSpring'] : '';
	$hoursPerTripSummer = isset($_POST['hoursPerTripSummer']) ? $_POST['hoursPerTripSummer'] : '';
	$hoursPerTripFall = isset($_POST['hoursPerTripFall']) ? $_POST['hoursPerTripFall'] : '';
	$hoursPerTripWinter = isset($_POST['hoursPerTripWinter']) ? $_POST['hoursPerTripWinter'] : '';
	$deadheadPerVehicleSpring = isset($_POST['deadheadPerVehicleSpring']) ? $_POST['deadheadPerVehicleSpring'] : '';
	$deadheadPerVehicleSummer = isset($_POST['deadheadPerVehicleSummer']) ? $_POST['deadheadPerVehicleSummer'] : '';
	$deadheadPerVehicleFall = isset($_POST['deadheadPerVehicleFall']) ? $_POST['deadheadPerVehicleFall'] : '';
	$deadheadPerVehicleWinter = isset($_POST['deadheadPerVehicleWinter']) ? $_POST['deadheadPerVehicleWinter'] : '';
	$peakSeason = isset($_POST['peakSeason']) ? '' == $_POST['peakSeason'] ? NULL : $_POST['peakSeason'] : NULL;
	
	$costPerHour = isset($_POST['costPerHour']) ? '' == $_POST['costPerHour'] ? NULL : $_POST['costPerHour'] : NULL;
	$averageFare = isset($_POST['averageFare']) ? '' == $_POST['averageFare'] ? NULL : $_POST['averageFare'] : NULL;
	$partnerPercent = isset($_POST['partnerPercent']) ? '' == $_POST['partnerPercent'] ? NULL : $_POST['partnerPercent']/100 : NULL;
	$seatsPerVehicle = isset($_POST['seatsPerVehicle']) ? '' == $_POST['seatsPerVehicle'] ? NULL : $_POST['seatsPerVehicle'] : NULL;
	$milesPerTrip = isset($_POST['milesPerTrip']) ? '' == $_POST['milesPerTrip'] ? NULL : $_POST['milesPerTrip'] : NULL;
	$groupSize = isset($_POST['groupSize']) ? '' == $_POST['groupSize'] ? NULL : $_POST['groupSize'] : NULL;
	$projectStatus = isset($_POST['status']) ? '' == $_POST['status'] ? NULL : $_POST['status'] : NULL;
	
	// set peak season values
	$peakSP = $peakSU = $peakFA = $peakWI = 'false';
	switch ($peakSeason) {
		case 'spring':
			$peakSP = 'true';
			break;
		case 'summer':
			$peakSU = 'true';
			break;
		case 'fall':
			$peakFA = 'true';
			break;
		case 'winter':
			$peakWI = 'true';
			break;
	}
	$query = "SELECT * FROM seasonalprojectdata WHERE project_id=$projectNum AND project_year=$year";
	$res = queryMysqli($mysqli, $query);
	$row = $res->fetch_row();
	if($row){ // already exist, so update
		$querySP = " UPDATE seasonalprojectdata SET project_id=?, project_year=?,
					project_season='spring', peak_season=?, service_days=?, riders=?,
					vehicle_trips_per_day=?, average_daily_vehicles=?, hours_per_trip=?, deadhead_hours_per_vehicle=?
					WHERE project_id=? AND project_year=? AND project_season='spring'";
		$querySU = " UPDATE seasonalprojectdata SET project_id=?, project_year=?,
					project_season='summer', peak_season=?, service_days=?, riders=?,
					vehicle_trips_per_day=?, average_daily_vehicles=?, hours_per_trip=?, deadhead_hours_per_vehicle=?
					WHERE project_id=? AND project_year=? AND project_season='summer'";
		$queryFA = " UPDATE seasonalprojectdata SET project_id=?, project_year=?,
					project_season='fall', peak_season=?, service_days=?, riders=?,
					vehicle_trips_per_day=?, average_daily_vehicles=?, hours_per_trip=?, deadhead_hours_per_vehicle=?
					WHERE project_id=? AND project_year=? AND project_season='fall'";
		$queryWI = " UPDATE seasonalprojectdata SET project_id=?, project_year=?,
					project_season='winter', peak_season=?, service_days=?, riders=?,
					vehicle_trips_per_day=?, average_daily_vehicles=?, hours_per_trip=?, deadhead_hours_per_vehicle=?
					WHERE project_id=? AND project_year=? AND project_season='winter'";
		$paramTypes = 'iisiiddddii';
		$paramsSP = array($projectNum, $year, $peakSP, $serviceDaysSpring, $oneWayRidesSpring, $tripsPerDaySpring, $peakVehiclesSpring, $hoursPerTripSpring, $deadheadPerVehicleSpring, $projectNum, $year);
		$paramsSU = array($projectNum, $year, $peakSU, $serviceDaysSummer, $oneWayRidesSummer, $tripsPerDaySummer, $peakVehiclesSummer, $hoursPerTripSummer, $deadheadPerVehicleSummer, $projectNum, $year);
		$paramsFA = array($projectNum, $year, $peakFA, $serviceDaysFall, $oneWayRidesFall, $tripsPerDayFall, $peakVehiclesFall, $hoursPerTripFall, $deadheadPerVehicleFall, $projectNum, $year);
		$paramsWI = array($projectNum, $year, $peakWI, $serviceDaysWinter, $oneWayRidesWinter, $tripsPerDayWinter, $peakVehiclesWinter, $hoursPerTripWinter, $deadheadPerVehicleWinter, $projectNum, $year);
		$result = queryMysqliPreparedNonSelect($mysqli, $querySP, $paramTypes, $paramsSP);
		$result = queryMysqliPreparedNonSelect($mysqli, $querySU, $paramTypes, $paramsSU);
		$result = queryMysqliPreparedNonSelect($mysqli, $queryFA, $paramTypes, $paramsFA);
		$result = queryMysqliPreparedNonSelect($mysqli, $queryWI, $paramTypes, $paramsWI);
	}else{
		$querySP = "	INSERT INTO seasonalprojectdata
					(project_id, project_year,
					project_season, peak_season, service_days, riders, vehicle_trips_per_day,
					average_daily_vehicles, hours_per_trip, deadhead_hours_per_vehicle)
					VALUES (?,?,'spring',?,?,?,?,?,?,?) ";
		$querySU = "	INSERT INTO seasonalprojectdata
					(project_id, project_year,
					project_season, peak_season, service_days, riders, vehicle_trips_per_day,
					average_daily_vehicles, hours_per_trip, deadhead_hours_per_vehicle)
					VALUES (?,?,'summer',?,?,?,?,?,?,?) ";
		$queryFA = "	INSERT INTO seasonalprojectdata
					(project_id, project_year,
					project_season, peak_season, service_days, riders, vehicle_trips_per_day,
					average_daily_vehicles, hours_per_trip, deadhead_hours_per_vehicle)
					VALUES (?,?,'fall',?,?,?,?,?,?,?) ";
		$queryWI = "	INSERT INTO seasonalprojectdata
					(project_id, project_year,
					project_season, peak_season, service_days, riders, vehicle_trips_per_day,
					average_daily_vehicles, hours_per_trip, deadhead_hours_per_vehicle)
					VALUES (?,?,'winter',?,?,?,?,?,?,?) ";
		$paramTypes = 'iisiidddd';
		$paramsSP = array($projectNum, $year, $peakSP, $serviceDaysSpring, $oneWayRidesSpring, $tripsPerDaySpring, $peakVehiclesSpring, $hoursPerTripSpring, $deadheadPerVehicleSpring);
		$paramsSU = array($projectNum, $year, $peakSU, $serviceDaysSummer, $oneWayRidesSummer, $tripsPerDaySummer, $peakVehiclesSummer, $hoursPerTripSummer, $deadheadPerVehicleSummer);
		$paramsFA = array($projectNum, $year, $peakFA, $serviceDaysFall, $oneWayRidesFall, $tripsPerDayFall, $peakVehiclesFall, $hoursPerTripFall, $deadheadPerVehicleFall);
		$paramsWI = array($projectNum, $year, $peakWI, $serviceDaysWinter, $oneWayRidesWinter, $tripsPerDayWinter, $peakVehiclesWinter, $hoursPerTripWinter, $deadheadPerVehicleWinter);
		$result = queryMysqliPreparedNonSelect($mysqli, $querySP, $paramTypes, $paramsSP);
		$result = queryMysqliPreparedNonSelect($mysqli, $querySU, $paramTypes, $paramsSU);
		$result = queryMysqliPreparedNonSelect($mysqli, $queryFA, $paramTypes, $paramsFA);
		$result = queryMysqliPreparedNonSelect($mysqli, $queryWI, $paramTypes, $paramsWI);
	}
	
	$query = "SELECT * FROM annualprojectdata WHERE project_id=$projectNum AND project_year=$year";
	$res = queryMysqli($mysqli, $query);
	$row = $res->fetch_row();
	if($row){ // already exist, so update
		$query = " UPDATE annualprojectdata SET project_id=?, project_year=?,
					project_status=?, cost_per_hour=?, average_fare=?,
					partner_percent=?,seats_vehicle=?, miles_per_trip=?,group_size=?
					WHERE project_id=? AND project_year=?";
		$paramTypes = 'iisdddiddii';
		$params = array($projectNum, $year, $projectStatus, $costPerHour, $averageFare, $partnerPercent, $seatsPerVehicle, $milesPerTrip, $groupSize,$projectNum, $year);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}else{
		$query = "	INSERT INTO annualprojectdata
					(project_id, project_year,
					 project_status, cost_per_hour, average_fare,
					 partner_percent, seats_vehicle, miles_per_trip, group_size)
					VALUES (?,?,?,?,?,?,?,?,?) ";
		$paramTypes = 'iisdddidd';
		$params = array($projectNum, $year, $projectStatus, $costPerHour, $averageFare, $partnerPercent, $seatsPerVehicle, $milesPerTrip, $groupSize);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}
	$yearPicked = $year;
}		
	
if(isset($_GET['action'])) // only action is delete
{
	$year = $_GET['year'];
	$query = "DELETE FROM annualprojectdata WHERE project_id=? AND project_year=?";
	$paramTypes = 'ii';
	$params = array($projectNum, $year);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
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
			function editFunction(fieldPointer){
				fieldPointer.style.color="red"
				$("#postRes").css("color","red")
				$("#idReset").css("visibility","visible")
			}
			function updateTotal(field){
				var clas = $(field).attr('class')
				var tot=0
				switch(clas){
					case 'ServiceDays':
						$('.ServiceDays').each(function() {
							tot += parseInt($(this).val().replace(/\,/g,''),10)
						});
						$('#id'+ clas + 'Total').text(addCommas(tot))
						break;
					case 'OneWayRides':
						$('.OneWayRides').each(function() {
							tot += parseInt($(this).val().replace(/\,/g,''),10)
						});
						$('#id'+ clas + 'Total').text(addCommas(tot))
						break;
				}
				editFunction(field)
			}
			function setAction(field) {
				switch (field.value) {
					case 'add':
						$("#action").val('add')
						break;
					case 'delete':
						$("#action").val('delete')
						break;
				}
				$("form").submit()
				updateCalculatedFields()
			}
			function showReportClick(){
			    if (confirm("Press 'OK' to save data first!") == true) {
			        event.preventDefault();
			        var t = 1;
			    } else {
			        $("#postRes").css("color","green")
			    }
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
					case 'post':
						var b = 'post'
						break;
				}
						
				$('#fieldSelected').attr('name',b)
				$('#fieldSelected').val('true')
				$("form").submit()
			}
			function onLoadFunction() {
				setSelectField('idParkId','$park')
				setSelectField('idProjectNum','$projectNum')
				setSelectField('idYear','$yearPicked')
				setSelectField('idRegion','$region')

_END;
if('false' == $showReport){
	echo "				$('.report').hide()
";
}else{
	echo "				$('.report').show()
";
}
echo <<<_END
				$('#showReport').val($showReport)
				$('#idShowReport').click(function(e) {
					if('visible' == $('#idReset').css("visibility")) {
							if (confirm("Press 'OK' to save data first!") == true) {
							$('#postRes').click()
							e.preventDefault();
				    	} else {
				    	    e.preventDefault();
				    	}
					}
				});
 				$('#postRes').click(function(e) {
						var t = $('#idYear2').val()
					if('' == t) {
						alert("Please pick a year first")
						e.preventDefault();
			    	} else {
						$('#fieldSelected').attr('name','post')
						$('#fieldSelected').val('true')
						$("form").submit()
					}
					
				});
			}
		</script>
_END;
echo"		<title>ATS Management System</title>
	</head>
	<body onload=onLoadFunction()>
";

echo "		<div id='container'>
			<p>Annual Data</p>
			<div id='manAssets2'>
				<table>
					<tr>
						<td><p><a href='index.htm'>Admin Menu</a></p></td>
						<td><p><a href='manageProjects.php?park=$park&projectNum=$projectNum&region=$region'>Manage Projects</a></p></td>
						<td><p><a href='logout.php' >Log out</a></p></td>
";
/*if ($level == '1')// must be level '1' and also have poked 'Allow Deletes' field to delete
{
	if($allowDeletes)
	{
		echo"						<td><a href = annualPerformance.php?park=$park&projectNum=$projectNum&allowDeletes=$allowDeletes>No Deletes</a></td>
";
	}
	else
	{
		echo"						<td><a href = annualPerformance.php?park=$park&projectNum=$projectNum&allowDeletes=$allowDeletes>Allow Deletes</a></td>
";
	}
}*/
$b = $_SESSION["uid"];
echo"					</tr>
				</table>
			</div>
			<div id='manAssets3'>
				<form method='post' name='myform' action='$_SERVER[SCRIPT_NAME]'>
				<input type='hidden' name='action' value=''>
				<table class='manAssTop'>
					<tr>
						<td><p>Region (pick one)</p></td><td><p>Park Code (pick one)</p></td><td><p>Project Name (pick one)</p></td><td><p>Year (pick one)</p></td>
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
foreach($resProjects as $row)
{
	echo"							<option value='{$row['project_id']}'> {$row['project_name']} </option>
";
}
echo"						</select></td>
							<td><select id='idYear' name='yearPicked' style='width: 6em;' onChange=this.form.submit()>
								<option value=''> </option>
";
echo"								<option value='2010'> 2010 </option>
								<option value='2011'> 2011 </option>
								<option value='2012'> 2012 </option>
								<option value='2013'> 2013 </option>
								<option value='2014'> 2014 </option>
								<option value='2015'> 2015 </option>
								<option value='2016'> 2016 </option>
								<option value='2017'> 2017 </option>
";
echo"							</select></td><td><input type='hidden' id='fieldSelected' name='' value='true'></td>
					<tr>
				</table>
			</div>
";

// if we have a park selected, display it's data
if($park && $projectNum && $yearPicked)
{
	$query = "	SELECT project_year, project_status, cost_per_hour, average_fare, partner_percent, seats_vehicle, miles_per_trip, group_size
				FROM annualprojectdata
				WHERE project_id=? AND project_year=?";
	$paramTypes = 'ii';
	$params = array($projectNum, $yearPicked);
	$resAnnPData = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$numAnnP = count($resAnnPData);
	$annPFields = array('project_status', 'cost_per_hour', 'average_fare', 'partner_percent', 'seats_vehicle', 'miles_per_trip', 'group_size');

	$query = "	SELECT project_year, project_season, peak_season, service_days, riders, vehicle_trips_per_day, average_daily_vehicles, hours_per_trip, deadhead_hours_per_vehicle
				FROM seasonalprojectdata
				WHERE project_id=? AND project_year=?
				ORDER BY project_season ASC";
	$paramTypes = 'ii';
	$params = array($projectNum, $yearPicked);
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
	// set peak season values
	
	
	echo "			<table id='' class='projData' style='float:left;'>
				<thead>
					<tr>
						<th style='width:15em'></th>
						<th style='width:7em'>Spring</th>
						<th style='width:6em'>Summer</th>
						<th style='width:6em'>Fall</th>
						<th style='width:6em'>Winter</th>
						<th style='width:6em'>Total</th>
					</tr>
				</thead>
				<tbody>
";
	// seasonal data
	echo makeAnnPerfRowSeas('Service days', 'ServiceDays', 'service_days', 'serviceDays', $resSeasPData, $spring, $summer, $fall, $winter, true);
	echo makeAnnPerfRowSeas('One-way rides', 'OneWayRides', 'riders', 'oneWayRides', $resSeasPData, $spring, $summer, $fall, $winter, true,true,0);
	echo makeAnnPerfRowSeas('Trips per day', 'TripsPerDay', 'vehicle_trips_per_day', 'tripsPerDay', $resSeasPData, $spring, $summer, $fall, $winter, false, true, 1);
	echo makeAnnPerfRowSeas('Peak vehicles', 'PeakVehicles', 'average_daily_vehicles', 'peakVehicles', $resSeasPData, $spring, $summer, $fall, $winter, false, true, 1);
	echo makeAnnPerfRowSeas('Hours per round trip', 'HoursPerTrip', 'hours_per_trip', 'hoursPerTrip', $resSeasPData, $spring, $summer, $fall, $winter, false, true, 2);
	echo makeAnnPerfRowSeas('Deadhead per vehicle', 'DeadheadPerVehicle', 'deadhead_hours_per_vehicle', 'deadheadPerVehicle', $resSeasPData, $spring, $summer, $fall, $winter, false, true, 2);
	echo"						<tr><td>Peak season</td>
";
	$seasons = array('spring', 'summer', 'fall', 'winter');
	foreach($seasons as $season){
		if($season == $peakS){
			echo "						<td><input type='radio' onclick='editFunction(this)' class='aRadio' name='peakSeason' value='$season' checked='true'></td>
";
		}else {
			echo "						<td><input type='radio' onclick='editFunction(this)' class='aRadio' name='peakSeason' value='$season'></td>
";
		}
	}
	echo "					</tr>
";

	// annual data
	echo makeAnnPerfRowAnn('Cost per Hour', 'CostPerHour', 'cost_per_hour', 'costPerHour', $resAnnPData);
	echo makeAnnPerfRowAnn('Average Fare', 'AverageFare', 'average_fare', 'averageFare', $resAnnPData);
	echo makeAnnPerfRowAnn('Percent Partner Funding', 'PartnerPercent', 'partner_percent', 'partnerPercent', $resAnnPData);
	echo makeAnnPerfRowAnn('Seats per Vehicle', 'SeatsPerVehicle', 'seats_vehicle', 'seatsPerVehicle', $resAnnPData);
	echo makeAnnPerfRowAnn('Average Trip Length', 'MilesPerTrip', 'miles_per_trip', 'milesPerTrip', $resAnnPData);
	echo makeAnnPerfRowAnn('Average Group Size', 'GroupSize', 'group_size', 'groupSize', $resAnnPData);
	echo "					<tr><td style='text-align:right;'>Project Status</td><td><select onchange='editFunction(this)' id='idStatus' name='status' style='width:100%;'>
							<option value=''></option>
";
	while(	$row = $resStatus->fetch_row()) {
		echo "							<option value='$row[0]'> $row[0] </option>
		";
	}
	echo "						</select></td>
					<script type='text/javascript'>
						$('#idStatus').val('{$resAnnPData[0]['project_status']}')
					</script>
";
	echo "<td></td><td></td><td><input type='submit' id='idReset' value='Reset' style='visibility:hidden'></td></tr>
";
	$reports =array();
	$reportNames =array(	'Average daily rides',
							'Average riders per trip',
							'Daily riders per vehicle',
							'Service hours per day',
							'Service hours per season',
							'Operating cost',
							'Fare box revenue',
							'Operating deficit',
							'Percent NPS funding',
							'Partner funding',
							'NPS operating cost',
							'Cost per rider',
							'NPS cost per rider',
							'Riders per trip/capacity',
							'Est. car miles removed'
	);
	foreach ($reportNames as $rep){
		switch ($rep) {
			case 'Average daily rides':
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['service_days'] && '' != $resSeasPData[$$s]['service_days']){
						$reports[$rep][$s] = addCommas(round($resSeasPData[$$s]['riders']/$resSeasPData[$$s]['service_days'],0));
					}else{
						$reports[$rep][$s] = '';
					}
				}
				$totServiceDays = $resSeasPData[$spring]['service_days'] + $resSeasPData[$summer]['service_days'] + $resSeasPData[$fall]['service_days'] + $resSeasPData[$winter]['service_days'];
				if(0 != $totServiceDays && '' != $totServiceDays){
					$reports[$rep]['total'] = addCommas(round(($resSeasPData[$spring]['riders'] + $resSeasPData[$summer]['riders'] + $resSeasPData[$fall]['riders'] + $resSeasPData[$winter]['riders'])/$totServiceDays,0));
				}else{
					$reports[$rep]['total'] = '';
				}
			break;
			case 'Average riders per trip':
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['vehicle_trips_per_day'] && '' != $resSeasPData[$$s]['vehicle_trips_per_day']){
						$reports[$rep][$s] = round(stripCommas($reports['Average daily rides'][$s])/$resSeasPData[$$s]['vehicle_trips_per_day'],1);
					}else{
						$reports[$rep][$s] = '';
					}
				}
				$reports[$rep]['total'] = '';
			break;
			case 'Daily riders per vehicle':
				$drpv = 0;
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['average_daily_vehicles'] && '' != $resSeasPData[$$s]['average_daily_vehicles']){
						$reports[$rep][$s] = addCommas(round(stripCommas($reports['Average daily rides'][$s])/$resSeasPData[$$s]['average_daily_vehicles'],0));
					}else{
						$reports[$rep][$s] = '';
					}
					$drpv+=($resSeasPData[$$s]['average_daily_vehicles'] * $resSeasPData[$$s]['service_days']); 
				}
				if(0 != $totServiceDays && '' != $totServiceDays){
					$reports[$rep]['total'] = addCommas(round($drpv/$totServiceDays,0));
				}else{
					$reports[$rep]['total'] = '';
				}
			break;
			case 'Service hours per day':
				foreach ($seasons as $s){
					$reports[$rep][$s] = round(($resSeasPData[$$s]['vehicle_trips_per_day'] * $resSeasPData[$$s]['hours_per_trip']) + ($resSeasPData[$$s]['average_daily_vehicles'] * $resSeasPData[$$s]['deadhead_hours_per_vehicle']),1);
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : $reports[$rep][$s];
				}
				$reports[$rep]['total'] = '';
			break;
			case 'Service hours per season':
				$totShps = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = round(($reports['Service hours per day'][$s] * $resSeasPData[$$s]['service_days']),0);
					$totShps+=  $reports[$rep][$s];
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas($reports[$rep][$s]);
				}
				$reports[$rep]['total'] = 0 == $totShps ? '' : addCommas($totShps);
			break;
			case 'Operating cost':
				$totOpc = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = round(stripCommas($reports['Service hours per season'][$s]) * $resAnnPData[0]['cost_per_hour'],0);
					$totOpc+=  $reports[$rep][$s];
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas($reports[$rep][$s]);
				}
				$reports[$rep]['total'] = 0 == $totOpc ? '' : addCommas($totOpc);
			break;
			case 'Fare box revenue':
				$totFareBox = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = $resSeasPData[$$s]['riders'] * $resAnnPData[0]['average_fare'];
					$totFareBox+=  $reports[$rep][$s];
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas($reports[$rep][$s]);
				}
				$reports[$rep]['total'] = 0 == $totFareBox ? '' : addCommas($totFareBox);
			break;
			case 'Operating deficit':
				// LS 23 Sep 2016
				// make negative deficits show as 0
				$totOpDef = 0;
				foreach ($seasons as $s){
					$reports[$rep][$s] = stripCommas($reports['Operating cost'][$s]) - stripCommas($reports['Fare box revenue'][$s]);
					$totOpDef+=  $reports[$rep][$s];
					// LS 18 Apr 17
					// Only show total deficit
					$reports[$rep][$s] = '';//0 == $reports[$rep][$s] ? '' : $reports[$rep][$s] < 0 ? 0 : addCommas($reports[$rep][$s]);
				}
				$reports[$rep]['total'] = 0 == $totOpDef ? '' : $totOpDef < 0 ? 0 : addCommas($totOpDef);
			break;
			case 'Percent NPS funding':
				$val = (1 - $resAnnPData[0]['partner_percent']) * 100;
				foreach ($seasons as $s){
					$reports[$rep][$s] = $val;
					if( 0 == $reports['Operating deficit'][$s] || '' == $reports['Operating deficit'][$s])
						$reports[$rep][$s] = '';
				}
				if( 0 == $reports['Operating deficit']['total'] || '' == $reports['Operating deficit']['total']){
					$reports[$rep]['total'] = '';
				}else{
					$reports[$rep]['total'] = $val;
				}
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
				foreach ($seasons as $s){
					$reports[$rep][$s] = stripCommas($reports['Operating deficit'][$s]) * $reports['Percent NPS funding'][$s]/100;
					$totOpCost+=  $reports[$rep][$s];
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas(round($reports[$rep][$s],0));
				}
				$reports[$rep]['total'] = 0 == $totOpCost ? '' : addCommas(round($totOpCost),0);
			break;
			case 'Cost per rider':
				$totRiders = 0;
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['riders'] && '' != $resSeasPData[$$s]['riders']){
						$reports[$rep][$s] = round(stripCommas($reports['Operating cost'][$s])/$resSeasPData[$$s]['riders'],2);
					}else{
						$reports[$rep][$s] = '';
					}
					$totRiders+= $resSeasPData[$$s]['riders'];
				}
				if(0 != $totRiders && '' != $totRiders){
					$reports[$rep]['total'] = round(stripCommas($reports['Operating cost']['total'])/$totRiders,2);
				}else{
					$reports[$rep]['total'] = '';
				}
			break;
			case 'NPS cost per rider':
				$totRiders = 0;
				foreach ($seasons as $s){
					if(0 != $resSeasPData[$$s]['riders'] && '' != $resSeasPData[$$s]['riders']){
						$reports[$rep][$s] = round(stripCommas($reports['NPS operating cost'][$s])/$resSeasPData[$$s]['riders'],2);
					}else{
						$reports[$rep][$s] = '';
					}
					$totRiders+= $resSeasPData[$$s]['riders'];
				}
				if(0 != $totRiders && '' != $totRiders){
					$reports[$rep]['total'] = round(stripCommas($reports['NPS operating cost']['total'])/$totRiders,2);
				}else{
					$reports[$rep]['total'] = '';
				}
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
				$totMiles = 0;
				foreach ($seasons as $s){
					if(0 != $resAnnPData[0]['group_size'] && '' != $resAnnPData[0]['group_size']){
						$reports[$rep][$s] = round(($resSeasPData[$$s]['riders']/$resAnnPData[0]['group_size'])*$resAnnPData[0]['miles_per_trip'],0);
					}else{
						$reports[$rep][$s] = '';
					}
					$totMiles+= $reports[$rep][$s];
					$reports[$rep][$s] = 0 == $reports[$rep][$s] ? '' : addCommas(round($reports[$rep][$s],0));
				}
				$reports[$rep]['total'] = 0 == $totMiles ? '' : addCommas(round($totMiles,0));
			break;
		}
	}
	foreach ($reportNames as $r){
		echo"					<tr>
						<td class='report' style='text-align:right;'>$r</td><td class='report' style='font-size:11pt;text-align:right;'>{$reports[$r]['spring']}</td><td class='report' style='font-size:11pt;text-align:right;'>{$reports[$r]['summer']}</td><td class='report' style='font-size:11pt;text-align:right;'>{$reports[$r]['fall']}</td><td class='report' style='font-size:11pt;text-align:right;'>{$reports[$r]['winter']}</td><td class='report' style='font-size:11pt;text-align:right;'>{$reports[$r]['total']}</td>
					</tr>
";
	}
	$repShow = 'false' == $showReport ? 'Show' : 'Hide';
	echo "				</tbody>
			</table>
			<div id=yearBox2>
				<table class='projectScore'>
					<thead></thead>
					<tbody>
						<tr>
							<td>Post results<td><td><select id='idYear2' name='yearSet' style='width: 6em;'>
								<option value=''></option>
								<option value='2010'> 2010 </option>
								<option value='2011'> 2011 </option>
								<option value='2012'> 2012 </option>
								<option value='2013'> 2013 </option>
								<option value='2014'> 2014 </option>
								<option value='2015'> 2015 </option>
								<option value='2016'> 2016 </option>
								<option value='2017'> 2017 </option>
							</select></td><td><input type='button' id='postRes' name='' value='post'></td>
			<td style='width:300px;text-align:left;padding-left:20px;'>To use this page, select a park, project and year from above.  To edit this year or to add a new year, make the necessary changes and then select the year to update or add from the drop down (left).  Press post to affect change. </td>
						</tr>
						<tr><td><input type='submit' id='idShowReport' name='showReportButton' value='$repShow Calculations'></td></tr>
							<input type='hidden' id='showReport' name='showReport' value=''>
					</tbody>
				</table>
			</div>
				</form>
";
	
}
echo"		</div>
";
echo assetTextBottom();
?>