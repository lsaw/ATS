<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';
include 'annFleetMaker.php';

logUser('transportation systems');

if(!isset($_SESSION['uid']))
session_start();

$region 	= isset($_POST['region'])		? $_POST['region'] 	   : 'NERO';
$park 		= isset($_POST['park'])			? $_POST['park'] 	   : '';
$projectNum = isset($_POST['projectNum']) 	? $_POST['projectNum'] : '';
$yearPicked = isset($_POST['yearPicked'])	? $_POST['yearPicked'] : '';
$yearSet = isset($_POST['yearSet'])	? $_POST['yearSet'] : '';
// $showReport defaults to 'false', or whatever is its current value
//$showReport	= isset($_POST['showReport']) 	? $_POST['showReport'] : 'false';
// if showReportButton is pressed, flip t/f value of showReport
//$showReport = isset($_POST['showReportButton'])	? 'false' == $_POST['showReport'] ? 'true' : 'false' : $showReport;

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
$assetTypes = array('boat' =>[],
					'bus' => [],
					'railroad' => [],
					'tram' => [],
					'van' => []	);
$fuelTypes = array(	'Diesel',
					'Electric',
					'Ethanol',
					'Gasoline',
					'Hybrid',
					'Propane');

if(isset($_POST['post'])) {
	foreach ($assetTypes as $asT=>$y){
		foreach ($fuelTypes as $ft=>$t){
			$name = $asT . $t;
			$nameOld = $name . 'Old';
			if('' != $_POST[$name]){
				$val = $_POST[$name];
				$oldVal = $_POST[$nameOld];
				$query = "INSERT INTO fleet(project_id, project_year, fuel_type, vehicle_type, vehicle_count, old_vehicle_count)
						  VALUES($projectNum, $yearSet, '$t', '$asT', ?, ?)
						  ON DUPLICATE KEY UPDATE vehicle_count = ?, old_vehicle_count = ?";
				$paramTypes = 'iiii';
				$params = array($val, $oldVal, $val, $oldVal);
				$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
			}
		}
	}
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
/*
$resAT = getSingleColumnQueryMysqli($mysqli, 'asset_type_name', 'asset_types');
$resVFT = getSingleColumnQueryMysqli($mysqli, 'fuel_type', 'vehicle_fuel_types');

while($row = $resAT->fetch_assoc()){
	$assetTypes[$row['asset_type_name']] = [];
}

foreach ($assetTypes as $at => $y){
	while($r = $resVFT->fetch_assoc()){
		$VFT[$r['fuel_type']]=0;
	}
	$assetTypes[$at] = $VFT;
}
*/

foreach  ($assetTypes as $at => $y){
	$assetTypes[$at] = array('Diesel' => array(count=>'',old=>''),
			'Electric' => array(count=>'',old=>''),
			'Ethanol' => array(count=>'',old=>''),
			'Gasoline' => array(count=>'',old=>''),
			'Hybrid' => array(count=>'',old=>''),
			'Propane' => array(count=>'',old=>'')
	);
}





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
	$query = "	SELECT *
				FROM fleet
				WHERE project_id=? AND project_year=?";
	$paramTypes = 'ii';
	$params = array($projectNum, $yearPicked);
	$resFleetData = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	foreach ($resFleetData as $r){
		$t = 4;
	}

	// set peak season values
	echo "			<table id='' class='projData' style='float:left;'>
				<thead>
					<tr>
						<th style='width:8em'></th>
						<th style='width:8em'></th>
						<th style='width:8em'></th>
						<th style='width:8em'>Count of</th>
					</tr>
					<tr>
						<th></th>
						<th></th>
						<th>Vehicle</th>
						<th>Overage</th>
					</tr>
					<tr>
						<th></th>
						<th></th>
						<th>Count</th>
						<th>Vehicles</th>
					</tr>
			</thead>
				<tbody>
";
	foreach ($assetTypes as $asT=>$y){
		foreach ($resFleetData as $v=>$h){
			$haveType = false;
			$haveFuel = false;
			if($asT == $h['vehicle_type']){
				$haveType = true;
			}
			if(true == $haveType){
				foreach ($y as $t=>$b){
					if($t == $h['fuel_type']){
						$haveFuel = true;
					}
					if($haveFuel){
						$assetTypes[$asT][$t]['count'] = $h['vehicle_count'];
						$assetTypes[$asT][$t]['old'] = $h['old_vehicle_count'];
						$haveFuel = false;
					}
					if('Propane' == $t)
						unset($resFleetData[$v]);
				}
			}
		}
		if($haveType){
			$haveType = false;
		}
	}
	foreach ($assetTypes as $asT=>$y){
		echo "				<tr><td>$asT</td></tr>";
		foreach ($fuelTypes as $ft){
			echo "					<tr>
						<td></td>
						<td>$ft</td>
						<td><input class='FleetCell' id='idOldV' style='width:100%;text-align:right;' name='$asT$ft' type='text' value='{$assetTypes[$asT][$ft]['count']}'></td>
						<td><input class='FleetCell' id='idOldV' style='width:100%;text-align:right;' name='{$asT}{$ft}Old' type='text' value='{$assetTypes[$asT][$ft]['old']}'></td></tr>
";
								}	
	}
	
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
						<tr></tr>
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