<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('manageProjects');

if(!isset($_SESSION['uid']))
session_start();

$region 	= isset($_POST['region'])		? $_POST['region'] 		: 'NERO';
$park 		= isset($_POST['park'])			? $_POST['park'] 		: '';
$projectNum = isset($_POST['projectNum'])	? $_POST['projectNum'] 	: '';
$year	 	= isset($_POST['year'])			? $_POST['year'] 		: '2015';
$fund 		= isset($_POST['fund'])			? $_POST['fund']	 	: 'All';

$region 	= isset($_GET['region'])		? $_GET['region'] 		: $region;
$park 		= isset($_GET['park'])			? $_GET['park'] 		: $park;
$projectNum = isset($_GET['projectNum'])	? $_GET['projectNum'] 	: $projectNum;
$year 		= isset($_GET['year'])			? $_GET['year'] 		: $year;
$fund 		= isset($_GET['fund'])			? $_GET['fund'] 		: $fund;

if(isset($_GET['allowDeletes'])) {// flips $allowDeletes
	$test = $_GET['allowDeletes'];
	$allowDeletes = $test ? 0 : 1;
}
else {// if $allowDeletes not yet set (firsst time in) set to '0'
	$allowDeletes = 0;
}

// need to deal with situation where you have a project selected for park A, but you 
// then switch to park B, and that park does not have that project.  Sol'n is to 
// query that park for that project. If not there force to 'All'.
if($park && $projectNum != '' && $projectNum != 'New') {
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

if(isset($_POST['update'])){
	// if field is blank, save as NULL
	// if field is not set, save as NULL
	// otherwise load what is in field
	$selectPark 	= isset($_POST['parkId']) 			? $_POST['parkId'] : '';
	$projectName 	= isset($_POST['projectName']) 		? $_POST['projectName'] : '';
	$mode 			= isset($_POST['mode']) 			? '' == $_POST['mode'] 			? NULL : $_POST['mode'] 		 : NULL;
	$accessType 	= isset($_POST['accessType']) 		? '' == $_POST['accessType'] 	? NULL : $_POST['accessType']    : NULL;
	$agreementType 	= isset($_POST['agreementType']) 	? '' == $_POST['agreementType'] ? NULL : $_POST['agreementType'] : NULL;
	$operator 		= isset($_POST['operator']) 		? '' == $_POST['operator'] 		? NULL : $_POST['operator'] 	 : NULL;
	$projectAction 	= isset($_POST['projectAction'])	? '' == $_POST['projectAction'] ? NULL : $_POST['projectAction'] : NULL;
	$description 	= isset($_POST['description']) 		? $_POST['description'] : '';
	$query = "	UPDATE projects SET
					park_id			= ?,
					project_name	= ?,
					mode			= ?,
					access_type		= ?,
					agreement_type	= ?,
					operator		= ?,
					project_action	= ?
				WHERE project_id	= ?";
	$paramTypes = 'ssssssss';
	$params = array(	$selectPark,
						$projectName,
						$mode,
						$accessType,
						$agreementType,
						$operator,
						$projectAction,
						$projectNum);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	
	if('' != $description){ // we have some words
		$query = "	INSERT INTO project_descriptions (project_id, project_description)
							VALUES (?,?)
					ON DUPLICATE KEY UPDATE project_description=?";
		$paramTypes = 'sss';
		$params = array(	$projectNum,
							$description,
							$description);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}else{// description is empty
		$query = "SELECT * FROM project_descriptions WHERE project_id=?";
		$paramTypes = 's';
		$params = array($projectNum);
		$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
		if($result){
			$query = "DELETE FROM project_descriptions WHERE project_id=?";
			$paramTypes = 's';
			$params = array($projectNum);
			$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
	}
}
if(isset($_POST['new'])){
	$selectPark = isset($_POST['parkId']) ? $_POST['parkId'] : '';
	$projectName = isset($_POST['projectName']) ? $_POST['projectName'] : '';
	$mode = isset($_POST['mode']) ? '' == $_POST['mode'] ? NULL : $_POST['mode'] : NULL;
	$accessType = isset($_POST['accessType']) ? '' == $_POST['accessType'] ? NULL : $_POST['accessType'] : NULL;
	$agreementType = isset($_POST['agreementType']) ? '' == $_POST['agreementType'] ? NULL : $_POST['agreementType'] : NULL;
	$operator = isset($_POST['operator']) ? '' == $_POST['operator'] ? NULL : $_POST['operator'] : NULL;
	$projectAction = isset($_POST['projectAction'])	? '' == $_POST['projectAction'] ? NULL : $_POST['projectAction'] : NULL;
	$description = isset($_POST['description']) ? $_POST['description'] : '';

	$query = "INSERT INTO projects SET
				park_id = ?,
				project_name = ?,
				mode = ?,
				access_type = ?,
				agreement_type = ?,
				operator = ?,
				project_action = ?";
	$paramTypes = 'sssssss';
	$params = array(	$selectPark ,
			$projectName,
			$mode,
			$accessType,
			$agreementType,
			$operator,
			$projectAction);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	
	$projectNum = $mysqli->insert_id;
	if('' != $description){ // we have some words
		$query = "	INSERT INTO project_descriptions (project_id, project_description)
							VALUES (?,?)";
		$paramTypes = 'ss';
		$params = array(	$projectNum,
							$description);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}
}

if(isset($_POST['delete'])) {
	// delete from projects table
	$query = "DELETE FROM projects WHERE project_id=?";
	$paramTypes = 's';
	$params = array($projectNum);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	
	// delete from project descriptions table
	$query = "DELETE FROM project_descriptions WHERE project_id=?";
	$paramTypes = 's';
	$params = array($projectNum);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	// reset $projectNum for proper display
	$projectNum = '';
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

// data for this project
if($projectNum != '' && $projectNum != 'New'){
	$resProject = getWholeTableQueryMysqli($mysqli, 'projects', 'project_id', $projectNum);
}

echo getHeaderTextGeneral();
echo javascriptHeaderText();
?>
		<script>
			function setSelectField(selectName, fieldName) {
				$("#" + selectName).val(fieldName)
			}
			function editFunction(fieldPointer){
				fieldPointer.style.color="red"
				$("#idUpdate").css("visibility","visible")
				$("#idNew").css("visibility","visible")
				$("#idReset").css("visibility","visible")
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
<?php
echo "				setSelectField('idRegion','$region')
				setSelectField('idParkId','$park')
				setSelectField('idProjectNum','$projectNum')
";
if($projectNum != '' && $projectNum != 'New'){
echo "				setSelectField('idSelectParkId', '{$resProject['park_id']}')
				setSelectField('idMode', '{$resProject['mode']}');
				setSelectField('idAccessType', '{$resProject['access_type']}');
				setSelectField('idAgreementType', '{$resProject['agreement_type']}');
				setSelectField('idOperator', '{$resProject['operator']}');
				setSelectField('idProjectAction', '{$resProject['project_action']}');
";
 }else{
	echo "				setSelectField('idSelectParkId', '$park')
";
 }
?>
	}
		</script>
		<title>ATS Management System</title>
	</head>
	<body onload=onLoadFunction()>
<?php

echo "		<div id='container'>
			<p>Manage Projects</p>
			<div id='manAssets2'>
				<table>
					<tr>
						<td><p><a href='index.htm'>Admin Menu</a></p></td>
						<td><p><a href='annualPerformance.php?park=$park&projectNum=$projectNum&region=$region'>Annual Data</a></p></td>
						<td><p><a href='logout.php' >Log out</a></p></td>
";
if ($level == '1') {// must be level '1' and also have poked 'Allow Deletes' field to delete 
	if($allowDeletes) {
		echo"						<td><a href = manageProjects.php?park=$park&projectNum=$projectNum&year=$year&fund=$fund&allowDeletes=$allowDeletes>No Deletes</a></td>
";
	}
	else {
		echo"						<td><a href = manageProjects.php?park=$park&projectNum=$projectNum&year=$year&fund=$fund&allowDeletes=$allowDeletes>Allow Deletes</a></td>
";
	}
}

echo"					</tr>
				</table>
			</div>
";
echo "			<div id='manAssets3'>
				<form method='post' action='$_SERVER[SCRIPT_NAME]'>
				<input type='hidden' name='year' value=$year>
				<input type='hidden' name='fund' value=$fund>
				<table class='manAssTop'>
					<tr>
						<td><p>Region (pick one)</p></td><td><p>Park Code (pick one)</p></td><td><p>Project Name (pick one)</p><td>
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

foreach($resParks as $row) {
	echo"						<option value='{$row['park_id']}'> {$row['park_id']} </option>
";
}

echo"						</select></td>
						<td><select id='idProjectNum' name='projectNum' style='width: 20em;' onChange=submitForm(this)>
							
";
if('' != $park){
	echo "							<option value='New'>New</option>
";
}
foreach($resProjects as $row) {
	echo"							<option value='{$row['project_id']}'> {$row['project_name']} </option>
";
}
echo"						</select></td><td><input type='hidden' id='fieldSelected' name='' value='true'></td>
					<tr>
				</table>
			</div>
";

if('' != $park){
	// get data for drop down lists
	$resModes 			= getSingleColumnQueryMysqli($mysqli, 'mode', 'project_modes');
	$resAccess		 	= getSingleColumnQueryMysqli($mysqli, 'project_access', 'project_access');
	$resAgreement 		= getSingleColumnQueryMysqli($mysqli, 'agreement_type', 'project_agreements');
	$resOperators	 	= getSingleColumnQueryMysqli($mysqli, 'project_operator', 'project_operators');
	$resActions		 	= getSingleColumnQueryMysqli($mysqli, 'project_action', 'project_actions');

	if($projectNum != '' && $projectNum != 'New'){// we have a project so display its data
		echo "			<table class='assetTable'>
					<tr><th></th><th>Project Data</th></tr>
";
		$query = "SELECT project_description FROM project_descriptions WHERE project_id=?";
		$paramTypes = 's';
		$params = array($projectNum);
		$resDesc = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
		$desc = $resDesc ? $resDesc[0]['project_description'] : '';
		
		echo "						<tr><td>Park ID:</td>
";
		// level  3, only show value, do not allow changes
		if($level == '3') {
			echo "							<td>{$resProject['park_id']}</td></tr>
";
		}
		// levels 1 and 2, allow changes
		else {
			echo "							<td><select id='idSelectParkId' name='parkId' style='width:100%;' onChange=editFunction(this,'')>
";
			foreach ($resParks as $row){
				echo "							<option value='{$row['park_id']}'> {$row['park_id']} </option>
";
			};
			echo "							</select></td>";
		}
		if($allowDeletes){
			echo"<td colspan='3'><input type='submit' id='idDelete' name='delete' value='Delete this project' onclick='' style=''></td>";
		}else{
			echo "</tr>
";
		}
		
	
		echo generateInputField('Project Name', 'projectName', $resProject['project_name'], '', $level);
		echo generateSelectFirstBlank('Mode', 'idMode', 'mode', $resModes, '', $level, $resProject['mode']);
		echo generateSelectFirstBlank('Access Type', 'idAccessType', 'accessType', $resAccess, '', $level, $resProject['access_type']);
		echo generateSelectFirstBlank('Agreement Type', 'idAgreementType', 'agreementType', $resAgreement, '', $level, $resProject['agreement_type']);
		echo generateSelectFirstBlank('Operator', 'idOperator', 'operator', $resOperators, '', $level, $resProject['operator']);
		echo generateSelectFirstBlank('Project Action', 'idProjectAction', 'projectAction', $resActions, '', $level, $resProject['project_action']);
		echo "<tr><td>Description:</td><td><textarea onkeyup=\"editFunction(this,'')\" name='description' rows='4' cols='50'>$desc</textarea></td></tr>
";
		echo"<tr><td><input type='submit' id='idReset' value='Reset' style='visibility:hidden'></td>
		<td colspan='3'><input type='submit' id='idUpdate' name='update' value='Update Project Data' onclick='' style='visibility:hidden;color:red'></td>
		</tr>
";
		echo "</table>
";
	}
	elseif('New' == $projectNum){// want to make a new one
		echo "			<table class='assetTable'>
					<tr><th></th><th>Project Data</th></tr>
";
		echo generateSelectPrepared('Park ID', 'idSelectParkId', 'parkId', $resParks, '', $level, $park);
		echo generateInputField('Project Name', 'projectName', '', '', $level);
		echo generateSelectFirstBlank('Mode', 'idMode', 'mode', $resModes, '', $level, '');
		echo generateSelectFirstBlank('Access Type', 'idAccessType', 'accessType', $resAccess, '', $level, '');
		echo generateSelectFirstBlank('Agreement Type', 'idAgreementType', 'agreementType', $resAgreement, '', $level, '');
		echo generateSelectFirstBlank('Operator', 'idOperator', 'operator', $resOperators, '', $level, '');
		echo generateSelectFirstBlank('Project Action', 'idProjectAction', 'projectAction', $resActions, '', $level, '');
		echo "<tr><td>Description:</td><td><textarea onkeyup=\"editFunction(this,'')\" name='description' rows='4' cols='50'></textarea></td></tr>
";
		echo"<tr><td><input type='submit' id='idReset' value='Reset' style='visibility:hidden'></td>
		<td colspan='3'><input type='submit' id='idNew' name='new' value='New Project' onclick='' style='visibility:hidden;color:red'></td>
		</tr>
";
				echo "</table>
";
	}
}// have a park picked

echo"			</form>
		</div>
";
echo assetTextBottom();
?>