<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('managePMIS');

if(!isset($_SESSION['uid']))
session_start();

// passed in from somewhere.php
$region = 		isset($_GET['region'])		? $_GET['region'] 		: 'NERO';
$park = 		isset($_GET['park']) 		? $_GET['park'] 		: '';
$asset_id = 	isset($_GET['asset_id']) 	? $_GET['asset_id'] 	: '';
$asset_name = 	isset($_GET['asset_name'])	? $_GET['asset_name'] 	: '';
$projectNum = 	isset($_GET['projectNum'])	? $_GET['projectNum'] 	: 'All';
$year = 		isset($_GET['year']) 		? $_GET['year'] 	  	: '';
$fund = 		isset($_GET['fund']) 		? $_GET['fund']			: '';

// or as a post from this page
$region = 		isset($_POST['region'])		? $_POST['region'] 		: $region;
$park = 		isset($_POST['park']) 		? $_POST['park'] 		: $park;
$asset_id = 	isset($_POST['asset_id']) 	? $_POST['asset_id'] 	: $asset_id;
$asset_name = 	isset($_POST['asset_name'])	? $_POST['asset_name'] 	: $asset_name;
$projectNum =	isset($_POST['projectNum']) ? $_POST['projectNum']	: $projectNum;
$year = 		isset($_POST['year']) 		? $_POST['year'] 		: $year;
$fund = 		isset($_POST['fund']) 		? $_POST['fund'] 		: $fund;

if(isset($_GET['allowDeletes'])){// flips $allowDeletes
	$test = $_GET['allowDeletes'];
	$allowDeletes = $test ? 0 : 1;
}else{// if $allowDeletes not yet set (firsst time in) set to '0'
	$allowDeletes = 0;
}

// need to deal with situation where you have a project selected for park A, but you 
// then switch to park B, and that park does not have that project.  Sol'n is to 
// query that park for that project. If not there force to 'All'.
if($park && $projectNum != 'All'){	$query= "SELECT project_name FROM projects WHERE project_id = ? AND park_id = ?";
	$paramTypes = 'ss';
	$params = array($projectNum, $park);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);

	if(empty($result))
		$projectNum = 'All';
}

// only 'action' is delete
if(isset($_GET['action'])){
	if(isset($_GET['asset_id']))
		$asset_id = $_GET['asset_id'];

	// delete systemlinks
	$query = "DELETE FROM systemlinks WHERE asset_id=?";
	$paramTypes = 's';
	$params = array($asset_id);
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
echo <<<_END
		<script>
			function setSelectField(selectName, fieldName){
				$("#" + selectName).val(fieldName)
			}

			function onLoadFunction(){
				setSelectField('idRegion','$region')
				setSelectField('idParkId','$park')
				setSelectField('idProjectNum','$projectNum')
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
		</script>
_END;
echo"		<title>ATS Management System</title>
	</head>
	<body onload=onLoadFunction()>
";
echo "		<div id='container'>
			<p>Manage PMIS</p>
			<div id='manAssets2'>
				<table>
					<tr>
						<td><p><a href='index.htm'>Admin Menu</a></p></td>
						<td><a href='manageAssets.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage Assets</a></td>
						<td><a href='tier.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Tier Assignment</a></td>
						<td><a href='assets.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Asset Data</a></td>
						<td><a href='FMSS.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>FMSS Links</a></td>
						<td><a href='funding.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Funding</a></td>						
						<td><p><a href='logout.php' >Log out</a></p></td>
";
if ($level == '1'){// must be level '1' and also have poked 'Allow Deletes' field to delete
	if($allowDeletes){
		echo"						<td><a href = managePMIS.php?park=$park&projectNum=$projectNum&year=$year&fund=$fund&allowDeletes=$allowDeletes>No Deletes</a></td>
";
	}else{
		echo"						<td><a href = managePMIS.php?park=$park&projectNum=$projectNum&year=$year&fund=$fund&allowDeletes=$allowDeletes>Allow Deletes</a></td>
";
	}
}

echo"					</tr>
				</table>
			</div>
";
echo "			<div id='manAssets3'>
				<form method='post' action='$_SERVER[SCRIPT_NAME]'>
				<input type='hidden' id='idAssetID' 	name='assetId' 			value='$asset_id'>
				<input type='hidden' id='idAssetName'	name='asset_name' 		value='$asset_name'>
				<input type='hidden' id='idYear' 		name='year' 			value='$year'>
				<input type='hidden' id='idfund' 		name='fund' 			value='$fund'>
				<input type='hidden' id='fieldSelected' name='' 				value='true'>
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
foreach ($resParks as $row){
echo"						<option value='{$row['park_id']}'> {$row['park_id']} </option>
";
}
echo"						</select></td>
						<td><select id='idProjectNum' name='projectNum' style='width: 20em;' onChange=submitForm(this)>
							<option value='All'>All</option>
";
foreach($resProjects as $row){
echo"							<option value='{$row['project_id']}'> {$row['project_name']} </option>
";
}
echo"						</select></td>
					<tr>
				</table>
				</form>
			</div>
";
echo"			<table id='pmisTable' class='manAsset'>
";

// if we have a park selected, display it's data
if($park){
	$query = "	SELECT	systemlinks.pmis_year,
						systemlinks.pmis_number,
						systemlinks.pmis_part,
						systemlinks.pmis_name,
						systemlinks.pmis_dollars,
						systemlinks.pmis_region_rank,
						systemlinks.pmis_fund_name,
						assets.park_id,
						assets.asset_id,
						assets.asset_name,
						assets.cap_cost,
						rankings.asset_action,
						rankings.funding_status,
						rankings.asset_tier
				FROM assets LEFT JOIN systemlinks ON assets.asset_id=systemlinks.asset_id
							LEFT JOIN rankings ON assets.asset_id=rankings.asset_id
				WHERE 
					assets.park_id='$park'
				ORDER BY -pmis_number DESC, pmis_part ASC";
	$paramTypes = '';
	$params = '';
	$resAssData = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	
	echo "				<thead>
					<tr>
						<th style='width:4em'>PMIS Number</th>
						<th style='width:4em'>PMIS Part</th>
						<th style='width:4em'>PMIS Year</th>
						<th style='width:4em'>Park ID</th>
						<th style=>PMIS Name</th>
						<th>Capital Cost</th>
						<th style='width:4em'>PMIS Dollars</th>
						<th style='width:4em'>Rank</th>
						<th style='width:8em'>Funding Status</th>
						<th style='width:4em'>Asset Action</th>
						<th style='width:5em'>Asset Tier</th>
						<th style='width:5em'>PMIS Fund Name</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
";
	echo "					<tr class='odd'>
						<td style='text-align:left'>NEW ASSET</td>
						<td colspan='4'style='text-align:center'>To add new asset, press 'new' button at right</td>
						<td style='text-align:center'></td>
						<td style='text-align:center'></td>
						<td style='text-align:center'></td>
						<td style='text-align:center'></td>
						<td style='text-align:center'></td>
						<td style='text-align:center'></td>
						<td style='text-align:center'></td>
						<td style='text-align:center'></td>
						<td style='text-align:center'></td>
						<td style='text-align:center'></td>
						<td style='text-align:center'><a href=\"newAsset.php?park=$park\">New</a></td>
					</tr>
";
	$sClass = 'even';
	$pNum = '';
	$firstTime = true;
	foreach($resAssData as $rowAssData){
		$show = 1; // display everything...
		if('All' != $projectNum){
			$query = "SELECT project_id FROM project_assets WHERE asset_id={$rowAssData['asset_id']}";
			$resProj2 = queryMysqli($mysqli, $query);
			if(!$resProj2) die ("Database not available at this time:" . mysql_error());
			$rowProj2 = $resProj2->fetch_assoc();
			if($projectNum != $rowProj2['project_id'])
				$show = 0;// unless not in project
		}
		if($show){
			if($firstTime){
				$sClass = 'even';
				$pNum = $rowAssData['pmis_number'];
				$firstTime = false;
			}elseif($pNum != $rowAssData['pmis_number']){
				$pNum = $rowAssData['pmis_number'];
				$sClass = $sClass == 'even' ? 'odd' : 'even';
			}
			$pmisName = '';
			$cost = addCommas($rowAssData['cap_cost']);
			$dol = addCommas($rowAssData['pmis_dollars']);
			echo "					<tr class='$sClass'>
						<td style='text-align:left'>{$rowAssData['pmis_number']}</td>
						<td style='text-align:left'>{$rowAssData['pmis_part']}</td>
						<td style='text-align:right'>{$rowAssData['pmis_year']}</td>
						<td style='text-align:left'>{$rowAssData['park_id']}</td>
						<td style='text-align:left'>{$rowAssData['pmis_name']}</td>
						<td style='text-align:right'>$cost</td>
						<td style='text-align:right'>$dol</td>
						<td style='text-align:right'>{$rowAssData['pmis_region_rank']}</td>
						<td style='text-align:left'>{$rowAssData['funding_status']}</td>
						<td style='text-align:left'>{$rowAssData['asset_action']}</td>
						<td style='text-align:left'>{$rowAssData['asset_tier']}</td>
						<td style='text-align:left'>{$rowAssData['pmis_fund_name']}</td>
";
			// low level only gets to see, not edit and no delete
			if($level == '3'){
				echo "						<td style='text-align:center'><a href=\"assets.php?park={$rowAssData['park_id']}&asset_id={$rowAssData['asset_id']}&asset_name={$rowAssData['asset_name']}&projectNum=$projectNum\">SHOW</a></td>
";
			}else{	// level 2 gets to edit but no delete
				echo "						<td style='text-align:center'><a href=\"assets.php?park={$rowAssData['park_id']}&asset_id={$rowAssData['asset_id']}&asset_name={$rowAssData['asset_name']}&projectNum=$projectNum\">EDIT</a></td>
";
			}
			// level 1 gets to delete, if 'Allow Deletes' poked
			if($level == '1' && $allowDeletes == '1'){
				echo "						<td style='text-align:center'><a href=\"managePMIS.php?park=$park&projectNum=$projectNum&year=$year&fund=$fund&asset_id={$rowAssData['asset_id']}&action=delete\">DELETE</a></td>
";
			}
			echo "					</tr>
";
		}
	}
	echo "				<tbody>
";

	echo "			</table>
";
}
echo"		</div>
";
echo assetTextBottom();
?>