<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('FMSS');

if(!isset($_SESSION['uid']))
	session_start();

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

if('' == $asset_id){
	header('Location: ' . $_SERVER['HTTP_REFERER']) . '?park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region';
//	echo "	Asset required to enter this page.";
	exit;
}
//if('' == $asset_id){ echo"	Asset required to enter this page.";  exit;}

if(isset($_POST['update'])){// update all tables
	$query = "SELECT * FROM fmsslinks WHERE asset_id=?";
	$paramTypes = 's';
	$params = array($asset_id);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$num = count($result);
	$val = stripCommas($_POST['fmssValue']);
	if($num){
	$query = 'UPDATE fmsslinks SET ' .
	         	'fmss_id = ?,'.
				'fmss_location_desc = ?,'.
				'fmss_value = ?,'.
				'fmss_api = ?,'.
				'fmss_fci = ?,'.
				'fmss_band = ?'.
			'WHERE asset_id = ?';
	$paramTypes = 'sssssss';
	$params = array(	$_POST['fmssId'] ,
						$_POST['fmssLocation'],
						$val,
						$_POST['fmssApi'],
						$_POST['fmssFci'],
						$_POST['fmssBand'],
						$_POST['assetId']);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}else{
		$query = 'INSERT INTO fmsslinks SET ' .
						'asset_id = "' 				. $_POST['assetId'] . '",'.
						'fmss_id = "' 				. $_POST['fmssId'] . '",'.
						'fmss_location_desc = "'	. $_POST['fmssLocation'] . '",'.
						'fmss_value = "' 			. $val . '",'.
						'fmss_api = "'				. $_POST['fmssApi'] . '",'.
						'fmss_fci = "' 				. $_POST['fmssFci'] . '",'.
						'fmss_band = "' 			. $_POST['fmssBand'] . '"';
		$paramTypes = 'sssssss';
		$params = array(	$_POST['assetId'],
							$_POST['fmssId'] ,
							$_POST['fmssLocation'],
							$val,
							$_POST['fmssApi'],
							$_POST['fmssFci'],
							$_POST['fmssBand']);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}
}

// get table data
$rowFmssLinks 	= getWholeTableQueryMysqli($mysqli, 'fmsslinks', 'asset_id', $asset_id);

echo getHeaderTextGeneral();
echo javascriptHeaderText();
echo commonFunctionsText();
echo commaSeperatorFunctionsText();
echo "
		<title>ATS Management System</title>
	</head>
	<body onload=onLoadFunction()>
";	
echo "		<div id='container'>
			<p>FMSS Links</p>
			<p>Asset $asset_id: $asset_name</p>
			<div id='assets1'>
			<table>
			<td><a href='index.htm' onclick='return confirmMove();'>Admin Menu</a></td>
			<td><a href='manageAssets.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage Assets</a></td>
			<td><a href='managePMIS.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage PMIS</a></td>
			<td><a href='tier.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Tier Assignment</a></td>
			<td><a href='assets.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Asset Data</a></td>
			<td><a href='funding.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Funding</a></td>
			<td><a href='logout.php' onclick='return confirmMove();' >Log out</a></td>
			</table>
			</div> <!--  End assets1 -->
			<form method='post' action='$_SERVER[SCRIPT_NAME]'>
				<div class='fmssAndFunding'>
";

// Load up the FMSS Links table
echo "					<table class='fmssLinksTable'>
						<tr><th></th><th>FMSS Links</th></tr>
";
echo generateInputField('FMSS ID', 'fmssId', $rowFmssLinks['fmss_id'], 'fmssC', $level);
echo generateInputField('FMSS Location', 'fmssLocation', $rowFmssLinks['fmss_location_desc'], 'fmssC', $level);
echo generateInputFieldNum('FMSS Value', 'fmssValue', $rowFmssLinks['fmss_value'], 'fmssC', $level);
echo generateInputField('FMSS API', 'fmssApi', $rowFmssLinks['fmss_api'], 'fmssC', $level);
echo generateInputField('FMSS FCI', 'fmssFci', $rowFmssLinks['fmss_fci'], 'fmssC', $level);
echo generateInputField('FMSS Band', 'fmssBand', $rowFmssLinks['fmss_band'], 'fmssC', $level);
echo"						<tr>	<td></td>
							<td><input type='submit' id='idReset' name='reset' value='Reset' style='visibility:hidden'/><input type='submit' id='idUpdateAll' name='update' value='Update' style='visibility:hidden;color:red'/></td></tr>
						<tr><td><input type='hidden' id='idParkId' 		name='park' 			value='$park'>
								<input type='hidden' id='idAssetID' 	name='assetId' 			value='$asset_id'>
								<input type='hidden' id='idAssetName'	name='asset_name' 		value='$asset_name'>
								<input type='hidden' id='idprojectNum' 	name='projectNum' 		value='$projectNum'>
								<input type='hidden' id='idYear' 		name='year' 			value='$year'>
								<input type='hidden' id='idfund' 		name='fund' 			value='$fund'>
								<input type='hidden' id='idregion' 		name='region' 			value='$region'>
								<input type='hidden' id='fmssC' name='fmssLinkChanged' value='false'>
								<input type='hidden' id='anyTable' name='anyTableChanged' value='false'></td></tr>
					</table>
";

echo "			</form>
		</div> <!--  End container2 -->
";

echo assetTextBottom();

?>