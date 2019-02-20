<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('assets');

if(!isset($_SESSION['uid']))
	session_start();

if(isset($_POST['update'])){// update all tables
	$assID = $_POST['asset_id'];
	if($_POST['assetChanged']== 'true'){
		$cap = stripCommas($_POST['capCost']);
		$op = stripCommas($_POST['opCost']);
		$query = 'UPDATE assets SET 
		         	park_id = ?,
					asset_name = ?,
					asset_type = ?,
					asset_status = ?,
					owner = ?,
					lifespan = ?,
					cap_cost = ?,
					op_cost = ?,
					year_new = ?
				WHERE asset_id = ?';
		$paramTypes = 'ssssssssss';
		$params = array(	$_POST['parkId'] ,
							$_POST['assetName'],
							$_POST['assetType'],
							$_POST['assetStatus'],
							$_POST['owner'],
							$_POST['lifespan'],
							$cap,
							$op,
							$_POST['yearNew'],
							$assID);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}

	if($_POST['rankingChanged']== 'true'){
		$query = "SELECT * FROM rankings WHERE asset_id='$assID'";
		$paramTypes = 's';
		$params = array($assID);
		$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
		$num = count($result);

		if($num){
			$query = 'UPDATE rankings SET ' . 
			         	'assetpriority_park = ?,
						 assetpriority_project = ?,
						 funding_status = ?,
						 asset_action = ?,
						 asset_tier = ?
					 WHERE asset_id = ?';
			$paramTypes = 'ssssss';
			$params = array(	$_POST['assPriorPark'] ,
								$_POST['assPriorProj'],
								$_POST['fundingStat'],
								$_POST['assetAction'],
								$_POST['assetTier'],
								$assID);
			$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
		else{
			$query = 'INSERT INTO rankings SET ' .
			            'asset_id = ?,
						 assetpriority_park = ?,
						 assetpriority_project = ?,
						 funding_status = ?,
						 asset_action = ?,
						 asset_tier = ?';
			$paramTypes = 'ssssss';
			$params = array(	$assID,
								$_POST['assPriorPark'] ,
								$_POST['assPriorProj'],
								$_POST['fundingStat'],
								$_POST['assetAction'],
								$_POST['assetTier']);
			$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
	}

	if($_POST['sysLinkChanged']== 'true'){
		// first test for existence of asset_id in this table
		$query = "SELECT * FROM systemlinks WHERE asset_id=?";
		$paramTypes = 's';
		$params = array($assID);
		$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
		$num = count($result);
		$dol = stripCommas($_POST['pmisDollars']);
		$dol = '' == $dol ? null : $dol;
		$pnum = '' == $_POST['pmisNum'] ? null : $_POST['pmisNum'];
		$pPart = '' == $_POST['pmisPart'] ? null : $_POST['pmisPart'];
		$pName = '' == $_POST['pmisName'] ? null : $_POST['pmisName'];
		$fund = '' == $_POST['pmisFundName'] ? null : $_POST['pmisFundName'];
		$pmisyear = '' == $_POST['pmisYear'] ? null : $_POST['pmisYear'];
		$rRank = '' == $_POST['pmisRegRank'] ? null : $_POST['pmisRegRank'];
		if($num){ // this syslink exists so modify
			$query = 'UPDATE systemlinks SET ' . 
			         	'pmis_number = ?,
						 pmis_part = ?,
			         	 pmis_name = ?,
						 pmis_dollars = ?,
						 pmis_fund_name = ?,
						 pmis_year = ?,
						 pmis_region_rank = ?		
					  WHERE asset_id = ?';
			$paramTypes = 'ssssssss';
			$params = array(	$pnum,
								$pPart,
								$pName,
								$dol,
								$fund,
								$pmisyear,
								$rRank,
								$assID);
			$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
		else{ // this syslink does not exist, so create
			$query = 'INSERT INTO systemlinks SET ' .
								'asset_id = ?,
								 pmis_number = ?,
								 pmis_part = ?,
								 pmis_name = ?,
								 pmis_dollars = ?,
								 pmis_fund_name = ?,
								 pmis_year = ?,
								 pmis_region_rank = ?';
			$paramTypes = 'ssssssss';
			$params = array(	$assID,
								$pnum ,
								$pPart,
								$pName,
								$dol,
								$fund,
								$pmisyear,
								$rRank);
			$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
	}

	if($_POST['projectAssetChanged']== 'true'){
		// this is the number of projects in the project_assets table for this asset
		if(isset($_POST['numProjects']))
			$numProjects = $_POST['numProjects'];
		for($i=0; $i<$numProjects; $i++)
		{
			$temp1 = 'projectPercent' . $i;
			$val = $_POST[$temp1]/100;
			$temp2 = 'assetSplit' . $i;
			$val2 = $_POST[$temp2]/100;
			$temp3 = 'projectNumber' . $i;
			$query = 'REPLACE INTO project_assets SET ' .
			         'asset_id = ?, 
			          project_id = ?,
					  project_percent = ?,
					  asset_split = ?';
			$paramTypes = 'ssss';
			$params = array(	$assID,
								$_POST[$temp3],
								$val,
								$val2);
			$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
	}
}

if(isset($_POST['new'])){// update all tables
	if($_POST['projectAssetChanged']== 'true'){
		// this is the number of projects in the project_assets table for this asset
		if(isset($_POST['numProjects']))
			$numProjects = $_POST['numProjects'];
		// the above may seem weird, but we want the next one to be new
		$temp1 = 'projectPercent' . $numProjects;
		$val = $_POST[$temp1]/100;
		$temp2 = 'assetSplit' . $numProjects;
		$val2 = $_POST[$temp2]/100;
		$temp3 = 'projectNumber' . $numProjects;
		$query = 'REPLACE INTO project_assets SET ' .
		         'asset_id = ?,
		          project_id = ?,
		          project_percent = ?,
				  asset_split = ?';
		$paramTypes = 'ssss';
		$params = array(	$_POST['assetId'],
							$_POST[$temp3],
							$val,
							$val2);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}
}

if(isset($_POST['delete'])){// update all tables
	if(isset($_POST['deleteThisProjectAsset']))
		$thisAsset = $_POST['deleteThisProjectAsset'];
	$temp2 = 'projectNumber' . $thisAsset;
	$query = 'DELETE FROM project_assets WHERE ' .
						 'asset_id = ? AND ' .
						 'project_id = ?' ;
	$paramTypes = 'ss';
	$params = array(	$_POST['assetId'],
						$_POST[$temp2]);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
}
// passed in from somewhere.php
$region = 		isset($_GET['region'])		? $_GET['region'] 		: '';
$park = 		isset($_GET['park']) 		? $_GET['park'] 		: '';
$asset_id = 	isset($_GET['asset_id']) 	? $_GET['asset_id'] 	: '';
$asset_name = 	isset($_GET['asset_name'])	? $_GET['asset_name'] 	: '';
$projectNum =	isset($_GET['projectNum']) 	? $_GET['projectNum']	: '';
$year = 		isset($_GET['year']) 		? $_GET['year'] 		: '';
$fund = 		isset($_GET['fund']) 		? $_GET['fund'] 		: '';

// or as a post from this page
$region = 		isset($_POST['region'])		? $_POST['region'] 		: $region;
$park = 		isset($_POST['park']) 		? $_POST['park'] 		: $park;
$asset_id = 	isset($_POST['asset_id']) 	? $_POST['asset_id'] 	: $asset_id;
$asset_name = 	isset($_POST['asset_name'])	? $_POST['asset_name'] 	: $asset_name;
$projectNum =	isset($_POST['projectNum']) ? $_POST['projectNum']	: $projectNum;
$year = 		isset($_POST['year']) 		? $_POST['year'] 		: $year;
$fund = 		isset($_POST['fund']) 		? $_POST['fund'] 		: $fund;

if('' == $asset_id){ echo"	Asset required to enter this page.";  exit;}
// get table data
$rowAssets 		= getWholeTableQueryMysqli($mysqli, 'assets', 'asset_id', $asset_id);
$rowRankings 	= getWholeTableQueryMysqli($mysqli, 'rankings', 'asset_id', $asset_id);
$rowSysLinks 	= getWholeTableQueryMysqli($mysqli, 'systemlinks', 'asset_id', $asset_id);

// get data for drop down lists
$resParks 			= getSingleColumnQueryDistinctOrderedAscMysqli($mysqli, 'park_id', 'parks');
$resAssetType 		= getSingleColumnQueryMysqli($mysqli, 'asset_type_name', 'asset_types');
$resAssetAction 	= getSingleColumnQueryMysqli($mysqli, 'asset_action', 'asset_actions');
$resAssetOwner 		= getSingleColumnQueryMysqli($mysqli, 'asset_owner', 'asset_owners');
$resAssetPriority 	= getSingleColumnQueryMysqli($mysqli, 'asset_priority', 'asset_prioritites');
$resAssetStatus 	= getSingleColumnQueryMysqli($mysqli, 'asset_status', 'asset_status');
$resAssetTier 		= getSingleColumnQueryMysqli($mysqli, 'asset_tier', 'asset_tiers');
$resFundingStatus 	= getSingleColumnQueryMysqli($mysqli, 'funding_status', 'funding_status');

// projects in this park
$query = "SELECT project_id, project_name FROM projects WHERE park_id=?";
$paramTypes = 's';
$params = array($park);
$resProj = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
$availableProjects = count($resProj);

// projects for this asset_id
$query = "SELECT project_id, project_percent, asset_split FROM project_assets WHERE asset_id=?";
$paramTypes = 's';
$params = array($asset_id);
$resProjectPercent = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
$numProjects = count($resProjectPercent);

echo getHeaderTextGeneral();
echo javascriptHeaderText();
echo commonFunctionsText();
echo commaSeperatorFunctionsText();

echo <<<_END
		<script>
		function onLoadFunction()
		{
			setSelectField('idParkId','{$rowAssets['park_id']}')
			setSelectField('idAssetType','{$rowAssets['asset_type']}')
			setSelectField('idAssetStatus','{$rowAssets['asset_status']}')
			setSelectField('idAssetOwner','{$rowAssets['owner']}')
			setSelectField('idAssetPriorityPark','{$rowRankings['assetpriority_park']}')
			setSelectField('idAssetPriorityProj','{$rowRankings['assetpriority_project']}')
			setSelectField('idFundingStatus','{$rowRankings['funding_status']}')
			setSelectField('idAssetAction','{$rowRankings['asset_action']}')
			setSelectField('idAssetTier','{$rowRankings['asset_tier']}')
			document.getElementById("assetForm").onkeypress = function(evt) {
				evt = evt || window.event;
				var charCode = evt.keyCode || evt.which;
				if (charCode == 13) {
					// Suppress default action of the keypress
					if (evt.preventDefault) {
						evt.preventDefault();
					}
				evt.returnValue = false;
				}
			};
		}
		function editFunctionProj(fieldPointer, table){
			if('true' == $('#anyTable').val()){
				alert("Save changes to Asset, System Links and Ranking data before you make changes to project percentages.")
			}
			else{
				$("#idUpdateAll2").css("visibility","visible")
				fieldPointer.style.color="red"
				$("#idReset2").css("visibility","visible")
				$("#" + table).val("true")
			}
		}
		function editFunctionProjNew(fieldPointer, table){
			if('true' == $('#anyTable').val()){
				alert("Save Asset, System Links and Ranking data before you make changes to project percentages or you will lose those changes.")
			}
			else{
				fieldPointer.style.color="red"
				$("#idNewProjAss").css('color','red')
				$("#idReset2").css("visibility","visible")
				$("#" + table).val("true")
			}
		}
		function chooseProject(proj){
			$("#" + "idThisProj").val(proj)
		}
</script>
_END;
echo "
		<title>ATS Management System</title>
	</head>
	<body onload=onLoadFunction()>
";	
echo "		<div id='container'>
			<p>Asset Table</p>
			<p>Asset $asset_id: $asset_name</p>
			<div id='assets1'>
			<table><tr>
			<td><a href='index.htm' onclick='return confirmMove();'>Admin Menu</a></td>
			<td><a href='manageAssets.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage Assets</a></td>
			<td><a href='managePMIS.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage PMIS</a></td>
			<td><a href='tier.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Tier Assignment</a></td>
			<td><a href='FMSS.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>FMSS Links</a></td>
			<td><a href='funding.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Funding</a></td>
			<td><a href='logout.php' onclick='return confirmMove();'>Log out</a></td></tr>
			</table>
			</div> <!--  End assets1 -->
			<form id='assetForm' method='post' action='$_SERVER[SCRIPT_NAME]'>
				<div id='assets2'>
";
// Load up the Assets table
echo "					<table class='assetTable'>
						<tr><th></th><th>Asset</th></tr>
";
echo generateSelect('Park ID', 'idParkId', 'parkId', $resParks, 'assetC', $level, $rowAssets['park_id']);
echo generateInputField('Asset Name', 'assetName', $rowAssets['asset_name'], 'assetC', $level);
echo generateSelect('Asset Type', 'idAssetType', 'assetType', $resAssetType, 'assetC', $level, $rowAssets['asset_type']);
echo generateSelect('Status', 'idAssetStatus', 'assetStatus', $resAssetStatus, 'assetC', $level, $rowAssets['asset_status']);
echo generateSelect('Owner', 'idAssetOwner', 'owner', $resAssetOwner, 'assetC', $level, $rowAssets['owner']);
echo generateInputField('Lifespan', 'lifespan',$rowAssets['lifespan'], 'assetC', $level);
echo generateInputFieldNum('Capital Cost', 'capCost',$rowAssets['cap_cost'], 'assetC', $level);
echo generateInputFieldNum('Operational Cost', 'opCost',$rowAssets['op_cost'], 'assetC', $level);
echo generateInputField('Year New', 'yearNew',$rowAssets['year_new'], 'assetC', $level);
echo"						<tr>	<td></td>
							<td><input type='submit' id='idReset' name='reset' value='Reset' style='visibility:hidden'><input type='submit' id='idUpdateAll' name='update' value='Update' style='visibility:hidden;color:red'></td></tr>
						<tr><td><input type='hidden' id='idParkId' 		name='park' 			value='$park'>
								<input type='hidden' id='idAssetID' 	name='asset_id' 			value='$asset_id'>
								<input type='hidden' id='idAssetName'	name='asset_name' 		value='$asset_name'>
								<input type='hidden' id='idprojectNum' 	name='projectNum' 		value='$projectNum'>
								<input type='hidden' id='idYear' 		name='year' 			value='$year'>
								<input type='hidden' id='idfund' 		name='fund' 			value='$fund'>
								<input type='hidden' id='idregion' 		name='region' 			value='$region'>
								<input type='hidden' id='assetC' name='assetChanged' value='false'>
								<input type='hidden' id='rankC' name='rankingChanged' value='false'>
								<input type='hidden' id='syslinkC' name='sysLinkChanged' value='false'>
								<input type='hidden' id='projC' name='projectAssetChanged' value='false'>
								<input type='hidden' id='anyTable' name='anyTableChanged' value='false'></td></tr>
					</table>
";

// Load up the System Links table
if($level == '3'){
	echo "					<table class='assetTable'>
";
}else{
	echo "					<table class='sysLinkTable'>
";
}
echo "						<tr><th></th><th>System Links</th></tr>
";
echo generateSysLinkInputField('PMIS Number', 'pmisNum', $rowSysLinks['pmis_number'], 'syslinkC', $level);
echo generateSysLinkInputField('PMIS Part', 'pmisPart', $rowSysLinks['pmis_part'], 'syslinkC', $level);
echo generateSysLinkInputField('PMIS Name', 'pmisName', $rowSysLinks['pmis_name'], 'syslinkC', $level);
echo generateSysLinkInputFieldNum('PMIS Dollars', 'pmisDollars', $rowSysLinks['pmis_dollars'], 'syslinkC', $level);
echo generateSysLinkInputField('PMIS Fund Name', 'pmisFundName', $rowSysLinks['pmis_fund_name'], 'syslinkC', $level);
echo generateSysLinkInputField('PMIS Year', 'pmisYear', $rowSysLinks['pmis_year'], 'syslinkC', $level);
echo generateSysLinkInputField('PMIS Region Rank', 'pmisRegRank', $rowSysLinks['pmis_region_rank'], 'syslinkC', $level);
echo"					</table>
";
echo"				</div> <!--  End assets2 -->
";
echo "				<div id='assets3'>
";

// Load up the Rankings table
echo "					<table class='rankingTable'>
						<tr><th></th><th>Ranking</th></tr>
";
echo generateSelect('Importance for Park', 'idAssetPriorityPark', 'assPriorPark', $resAssetPriority, 'rankC', $level, $rowRankings['assetpriority_park']);

$resAssetPriority->data_seek(0); //reset Priority list for next fields
echo generateSelect('Importance for Project', 'idAssetPriorityProj', 'assPriorProj', $resAssetPriority, 'rankC', $level, $rowRankings['assetpriority_project']);
echo generateSelect('Funding Status', 'idFundingStatus', 'fundingStat', $resFundingStatus, 'rankC', $level, $rowRankings['funding_status']);
echo generateSelect('Asset Action', 'idAssetAction', 'assetAction', $resAssetAction, 'rankC', $level, $rowRankings['asset_action']);
echo generateSelect('Asset Tier', 'idAssetTier', 'assetTier', $resAssetTier, 'rankC', $level, $rowRankings['asset_tier']);
echo"					</table>
";

echo "					<table class='projectAssetTable'>
						<tr>	<td></td><td>What portion</td><td>How is ATS</td><td></td></tr>
						<tr>	<td></td><td>of this asset</td><td>usage divided</td><td></td></tr>
						<tr>	<td></td><td>is used by</td><td>among</td><td></td></tr>
						<tr>	<td></td><td>this project?</td><td> projects</td><td></td></tr>
						<tr>	<td><input type='submit' id='idReset2' name='reset' value='Reset' style='visibility:hidden'><input type='submit' id='idUpdateAll2' name='update' value='Update' style='visibility:hidden;color:red'></td><td>%</td><td>%</td><td></td></tr>
";
$usedProj = '';
if($level == '3'){
	for($i=0; $i<$numProjects;$i++){
		$resProjectPercent[$i]['project_percent'] *= 100;
		$resProjectPercent[$i]['asset_split'] *= 100;
		// projects in this park
		$query = "SELECT project_name FROM projects WHERE project_id=?";
		$paramTypes = 's';
		$params = array($resProjectPercent[$i]['project_id']);
		$resProjName = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);

		echo"						<tr>	<td>{$resProjName[0]['project_name']}</td>
							<td>{$resProjectPercent[$i]['project_percent']}</td>
							<td>{$resProjectPercent[$i]['asset_split']}</td></tr>
";
	}
}

else {
	for($i=0; $i<$numProjects;$i++){
		foreach ($resProj as $rowProj){
			$thisProj = '';
			if($rowProj['project_id'] == $resProjectPercent[$i]['project_id']){
				$thisProj = $rowProj['project_name'];
				$usedProj[]=$rowProj['project_id'];
				break;
			}
		}
		echo "						<tr>	<td style='text-align:left;'>$thisProj</td>
";

		$resProjectPercent[$i]['project_percent'] *= 100;
		$resProjectPercent[$i]['asset_split'] *= 100;
		echo "							<td><input type='text' style='width: 3em;' name='projectPercent$i' value={$resProjectPercent[$i]['project_percent']} onKeyUp=editFunctionProj(this,'projC')></td>
							<td><input type='text' style='width: 3em;' name='assetSplit$i' value={$resProjectPercent[$i]['asset_split']} onKeyUp=editFunctionProj(this,'projC')></td>
							<td><input type='submit' id='idDeleteProjAss$i' name='delete' value='Delete' onclick='chooseProject($i)'></td></tr>
							<input type='hidden' name='projectNumber$i' value='{$resProjectPercent[$i]['project_id']}'>
						<script type='text/javascript'>
						$(\"#\" + \"idProjectNum$i\").val({$resProjectPercent[$i]['project_id']})
						</script>
";
	}
	echo"							<input type='hidden' id='idThisProj' name='deleteThisProjectAsset' value=''>
							<input type='hidden' id='idNumProjects' name='numProjects' value='$numProjects'>
";

	// this is for the last row for a new project asset
	if($availableProjects > $numProjects){
		echo"							<td><select id='idProjectNum$i' name='projectNumber$i' style='width:100%;' onChange=editFunctionProjNew(this,'projC')>
";

		foreach($resProj as $rowProj){
			if(!in_array($rowProj['project_id'],$usedProj, true)){
				echo"								<option value='{$rowProj['project_id']}'> {$rowProj['project_name']} </option>
";
			}
		}

	echo"							</select></td>
							<td><input type='text' style='width: 3em;' name='projectPercent$i' value='' onKeyUp=editFunctionProjNew(this,'projC')></td>
							<td><input type='text' style='width: 3em;' name='assetSplit$i' value='' onKeyUp=editFunctionProjNew(this,'projC')></td>
							<td><input type='submit' id='idNewProjAss' name='new' value='new'></td></tr>
";
	}
}

echo "							<tr><td></td><td colspan='3'>First column can total to <100%</td></tr>
							<tr><td></td><td colspan='3'>ATS usage column should total to 100%</td></tr>
					</table>
";

echo "				</div> <!--  End assets3 -->
			</form>
		</div> <!--  End container2 -->
";

echo assetTextBottom();
?>