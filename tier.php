<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('tier');

if(!isset($_SESSION['uid']))
	session_start();

// passed in from somewhere.php
$region = 		isset($_GET['region'])		? $_GET['region'] 		: 'NERO';
$park = 		isset($_GET['park']) 		? $_GET['park'] 		: '';
$asset_id = 	isset($_GET['asset_id']) 	? $_GET['asset_id'] 	: '';
$asset_name = 	isset($_GET['asset_name'])	? $_GET['asset_name'] 	: '';
$projectNum = 	isset($_GET['projectNum'])	? $_GET['projectNum'] 	: 'All';
$year = 		isset($_GET['year']) 		? ('' == $_GET['year'] ? '2015' : $_GET['year']) : '2015';
$fund = 		isset($_GET['fund']) 		? ('' == $_GET['fund'] ? 'All'  : $_GET['fund']) : 'All';
$lowYear = 		isset($_GET['lowYear']) 	? $_GET['lowYear']	 	: '';
$highYear = 	isset($_GET['highYear']) 	? $_GET['highYear'] 	: '';

// or as a post from this page
$region = 		isset($_POST['region'])		? $_POST['region'] 		: $region;
$park = 		isset($_POST['park']) 		? $_POST['park'] 		: $park;
$asset_id = 	isset($_POST['asset_id']) 	? $_POST['asset_id'] 	: $asset_id;
$asset_name = 	isset($_POST['asset_name'])	? $_POST['asset_name'] 	: $asset_name;
$projectNum =	isset($_POST['projectNum']) ? $_POST['projectNum']	: $projectNum;
$year = 		isset($_POST['year']) 		? $_POST['year'] 		: $year;
$fund = 		isset($_POST['fund']) 		? $_POST['fund'] 		: $fund;
$lowYear = 		isset($_POST['lowYear']) 	? $_POST['lowYear'] 	: $lowYear;
$highYear = 	isset($_POST['highYear'])	? $_POST['highYear'] 	: $highYear;

if(isset($_POST['update'])){
	if($_POST['update'] == 'true'){
		if($_POST['tier']) {
			$query = 'UPDATE rankings SET ' .
					 'asset_tier = ? ' . 
					 'WHERE asset_id = ?' ;
			$paramTypes = 'ss';
			$params = array(	$_POST['tier'],
								$_POST['asset']);
		}elseif($_POST['yearChanged']){
			$query = 'UPDATE systemlinks SET ' .
					'pmis_year = ? ' .
					'WHERE asset_id = ?' ;
			$paramTypes = 'ss';
			$params = array(	$_POST['yearChanged'],
								$_POST['asset']);
		}
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}
}
	
echo getHeaderTextGeneral();
echo javascriptHeaderText();
echo commonFunctionsText();
echo <<<_END
		<script type='text/javascript'>
			function setSelectField(selectName, fieldName){
				$("#" + selectName).val(fieldName)
			}

			function onLoadFunction(){
				setSelectField('idYear','$year')
				setSelectField('idFund','$fund')
			}
		</script>

_END;
echo "		<script type='text/javascript'>
			function editFunctionTier(fieldPointer, asset){
				$('#idAsset').val(asset)
				$('#idUpdate').val('true')
				$('#idTier').val(fieldPointer.options[fieldPointer.value].text)
			}
			function editFunctionYear(fieldPointer, asset){
				$('#idAsset').val(asset)
				$('#idUpdate').val('true')
				$('#idYearChanged').val(fieldPointer.options[fieldPointer.value].text)
			}
		</script>
";
echo "
		<title>Tier Management Page</title>
	</head>
	<body onload=onLoadFunction()>
";

echo "		<div id='container'>
			<form method='post' action='$_SERVER[SCRIPT_NAME]'>
	<input type='hidden' id='idRegion' 		name='region' 			value='$region'>
	<input type='hidden' id='idParkId' 		name='park' 			value='$park'>
	<input type='hidden' id='idAssetID' 	name='asset_id' 		value='$asset_id'>
	<input type='hidden' id='idAssetName'	name='asset_name' 		value='$asset_name'>
	<input type='hidden' id='idprojectNum' 	name='projectNum' 		value='$projectNum'>
	<input type='hidden' 					name='lowYear' 			value='$lowYear'>
	<input type='hidden' 					name='highYear' 		value='$highYear'>
			<p>Manage Tier Assignments</p>
			<div id='manAssets2'>
				<table>
					<tr><td><a href='index.htm'>Admin Menu</a></td>
<td><a href='manageAssets.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage Assets</a></td>
<td><a href='managePMIS.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage PMIS</a></td>
<td><a href='assets.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Asset Data</a></td>
<td><a href='FMSS.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>FMSS Links</a></td>
<td><a href='funding.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Funding</a></td>
<td><a href='tierFunding.php?	year=$year&fund=$fund&lowYear=$lowYear&highYear=$highYear'>Tier Funding Report</a></td>
						<td><a href='logout.php' >Log out</a></td>
					</tr>
				</table>
			</div> <!--  End manAssets2 -->
";

echo "			<div id='manAssets3'>
				<table class='manAssTop'>
					<tr>
						<td><p>Choose Year</p></td><td><p>Funding Source</p></td>
					</tr>
					<tr>
						<td><select id='idYear' name='year' style='width: 6em;' onChange=this.form.submit()>
";
$query = "SELECT DISTINCT pmis_year FROM systemlinks ORDER BY pmis_year ASC";
$resPMISYear = queryMysqli($mysqli, $query);
if(!$resPMISYear) die ("Database not available at this time:" . mysql_error());

while(	$row = $resPMISYear->fetch_row()){
	if($row[0]){// this gets rid of extra empty cells
		echo"						<option value='$row[0]'> $row[0] </option>
";
	}
}
echo"						</select></td>
";
$resPMISYear->data_seek(0);
$arrResPMISYear = arrayFromResultNotNullMysqli($resPMISYear);

echo "						<td><select id='idFund' name='fund' style='width: 6em;' onChange=this.form.submit()>
<option value='All'>All</option>
";
$resFundSource = getSingleColumnQueryDistinctMysqli($mysqli, 'pmis_fund_name', 'systemlinks');
$fundSource = arrayFromResultNotNullMysqli($resFundSource);

foreach ($fundSource as $row){
	echo"						<option value='$row'> $row </option>
";

}
echo"						</select></td>
					</tr>
				</table>
			</div>
";

	$assetT = getSingleColumnQueryMysqli($mysqli, 'asset_tier', 'asset_tiers');
	$assetTier = arrayFromResultMysqli($assetT);
	
	if('All' == $fund){
	// get table data
	$query = "	SELECT 	assets.asset_id, assets.park_id, project_name, projects.project_id, access_type, asset_name,
						pmis_number, pmis_dollars, pmis_fund_name, pmis_year, asset_action, asset_tier, pmis_region_rank
				FROM assets INNER JOIN systemlinks ON assets.asset_id = systemlinks.asset_id
							LEFT JOIN project_assets ON assets.asset_id = project_assets.asset_id
							LEFT JOIN projects ON projects.project_id = project_assets.project_id
							INNER JOIN rankings ON rankings.asset_id = assets.asset_id
				WHERE pmis_year = '$year'
				ORDER BY asset_tier ASC, systemlinks.pmis_region_rank ASC ";
	}else{
		// get table data
		$query = "	SELECT 	assets.asset_id, assets.park_id, project_name, projects.project_id, access_type, asset_name,
							pmis_number, pmis_dollars, pmis_fund_name, pmis_year, asset_action, asset_tier, pmis_region_rank
					FROM assets INNER JOIN systemlinks ON assets.asset_id = systemlinks.asset_id
								LEFT JOIN project_assets ON assets.asset_id = project_assets.asset_id
								LEFT JOIN projects ON projects.project_id = project_assets.project_id
								INNER JOIN rankings ON rankings.asset_id = assets.asset_id
					WHERE pmis_year = '$year' AND pmis_fund_name = '$fund'
					ORDER BY asset_tier ASC, -pmis_region_rank DESC ";
	}	
	$resTier = queryMysqli($mysqli, $query);
	if(!$resTier) die ("Database not available at this time:" . mysql_error());
	
echo "			<div class='fmssAndFunding'>
				<table class='tierTableTotals'>
					<td>Totals Tier 1:</td><td><span id='tier1Total'></span></td><td>Tier 2:</td><td><span id='tier2Total'></span></td><td>Tier 3:</td><td><span id='tier3Total'></span></td><td>Tier 4:</td><td><span id='tier4Total'></span></td>
				</table>
				<table class='tierTable'>
					<tr><th>Asset ID</th><th>Park ID</th><th style='text-align:left;'>Project Name</th><th>Access Type</th>
					<th style='text-align:left;'>Asset Name</th><th>PMIS Num</th><th>PMIS Dollars</th><th>Fund</th>
					<th>Year</th><th>Asset Action</th><th>Priority</th><th>Asset Tier</th><th></th></tr>
";
$sClass = 'even';
$lastID = '';
$totals = array('tier1'=>0, 'tier2'=>0, 'tier3'=>0, 'tier4'=>0);
while ($rowTier = $resTier->fetch_assoc()){
	if($lastID != $rowTier['asset_id']){
		$sClass = $sClass == 'even' ? 'odd' : 'even';
		echo generateFieldTier($rowTier, $level, $assetTier, $year, $fund, $arrResPMISYear, $sClass);
		switch($rowTier['asset_tier']){
			case 'Tier 1':
				$totals['tier1'] += $rowTier['pmis_dollars'];
				break;
			case 'Tier 2':
				$totals['tier2'] += $rowTier['pmis_dollars'];
				break;
			case 'Tier 3':
				$totals['tier3'] += $rowTier['pmis_dollars'];
				break;
			case 'Tier 4':
				$totals['tier4'] += $rowTier['pmis_dollars'];
				break;
			case 'decommisioned':
				break;
		}
	}
	$lastID = $rowTier['asset_id'];
}
foreach($totals as $t=>&$v){
	$v = addCommas($v);
}
unset($v);
echo"<tr><td>
<input type='hidden' id='idAsset' name='asset' value=''>
<input type='hidden' id='idUpdate' name='update' value=''>
<input type='hidden' id='idTier' name='tier' value=''>
<input type='hidden' id='idYearChanged' name='yearChanged' value=''></td></tr>
</table>
	<script type='text/javascript'>
		$(\"#tier1Total\").text(\"{$totals['tier1']}\")
		$(\"#tier2Total\").text(\"{$totals['tier2']}\")
		$(\"#tier3Total\").text(\"{$totals['tier3']}\")
		$(\"#tier4Total\").text(\"{$totals['tier4']}\")
	</script>
";

echo "			</form>
		</div>
		</div> <!--  End container2 -->
";
echo assetTextBottom();

?>