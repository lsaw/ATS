<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';
include 'showReport.php';

logUser('multi year report');
if(!isset($_SESSION['uid']))
session_start();

// passed in from somewhere.php
$region = 		isset($_GET['region'])		? $_GET['region'] 		: 'NERO';
$park = 		isset($_GET['park']) 		? $_GET['park'] 		: '';
$asset_id = 	isset($_GET['asset_id']) 	? $_GET['asset_id'] 	: '';
$asset_name = 	isset($_GET['asset_name'])	? $_GET['asset_name'] 	: '';
$projectNum = 	isset($_GET['projectNum'])	? $_GET['projectNum'] 	: '';
$year = 		isset($_GET['year']) 		? $_GET['year'] 		: '';
$fund = 		isset($_GET['fund']) 		? $_GET['fund'] 		: '';
$yearStart = 	isset($_GET['yearStart']) 	? $_GET['yearStart'] 	: '2015';
$yearEnd = 		isset($_GET['yearEnd']) 	? $_GET['yearEnd'] 		: '2020';
$fundSource = 	isset($_GET['fundSource']) 	? $_GET['fundSource'] 	: 'All';

// or as a post from this page
$region = 		isset($_POST['region'])		? $_POST['region'] 		: $region;
$park = 		isset($_POST['park']) 		? $_POST['park'] 		: $park;
$asset_id = 	isset($_POST['asset_id']) 	? $_POST['asset_id'] 	: $asset_id;
$asset_name = 	isset($_POST['asset_name'])	? $_POST['asset_name'] 	: $asset_name;
$projectNum =	isset($_POST['projectNum']) ? $_POST['projectNum']	: $projectNum;
$year = 		isset($_POST['year']) 		? $_POST['year'] 		: $year;
$fund = 		isset($_POST['fund']) 		? $_POST['fund'] 		: $fund;
$yearStart = 	isset($_POST['yearStart']) 	? $_POST['yearStart'] 	: '2015';
$yearEnd = 		isset($_POST['yearEnd']) 	? $_POST['yearEnd'] 	: '2020';
$fundSource = 	isset($_POST['fundSource']) ? $_POST['fundSource'] 	: 'All';
$posted =		isset($_POST['posted'])		? true					: false;

$inTarget = 'true';
$overTarget = 'false';
$inT = 'true';
$overT = 'false';
$allTiers = false;

$years = getSingleColumnQueryDistinctOrderedAscMysqli($mysqli, 'pmis_year', 'systemlinks');

if('true' == $inTarget && 'true' == $overTarget)
	$allTiers = true;

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

echo getHeaderTextGeneral();
echo javascriptHeaderText();
echo <<<_END
		<script type='text/javascript'>
			function setSelectField(selectName, fieldName){
				$("#" + selectName).val(fieldName)
			}

			function onLoadFunction(){
				setSelectField('idYearStart','$yearStart')
				setSelectField('idYearEnd','$yearEnd')
				setSelectField('idFundSource','$fundSource')
				$('#inTarget').prop('checked', $inTarget)
				$('#inTarget').prop('value', $inT)
				$('#overTarget').prop('checked', $overTarget)
				$('#overTarget').prop('value', $overT)
			}
			function setTarget(fieldPointer){
				switch (fieldPointer.id) {
					case 'inTarget':
						if('true' == fieldPointer.value){
							$('#inTarget').prop('checked', false)
							$('#inTarget').prop('value', 'false')
						}else{
							$('#inTarget').prop('checked', true)
							$('#inTarget').prop('value', 'true')
						}
						break;
					case 'overTarget':
						if('true' == fieldPointer.value){
							$('#overTarget').prop('checked', false)
							$('#overTarget').prop('value', 'false')
						}else{
							$('#overTarget').prop('checked', true)
							$('#overTarget').prop('value', 'true')
						}
						break;
				}
				$('#reportsForm').submit()
			}
			function showSubTotals(fieldPointer){
				if(true == $(fieldPointer).prop('checked')){
					$('.pres').show()
				}else{
					$('.pres').hide()
				}
			}
		</script>
_END;
echo"		<title>ATS Management System</title>
	</head>
	<body onload=onLoadFunction()>
";
echo "		<div id='container'>
			<p>Multi Year Program of Projects</p>
			<div id='manAssets2'>
				<table>
					<tr>
						<td><p><a href='index.htm'>Admin Menu</a></p></td>
						<td><p><a href='logout.php' >Log out</a></p></td>
";
echo"					</tr>
				</table>
			</div>
";
echo "	<form method='post' id='reportsForm' action='$_SERVER[SCRIPT_NAME]'>
		<div id='manAssets3'>
			<table class='manAssTop'>
					<tr>
						<td><p>Year Start</p></td><td><p>Year End</p></td><td><p>Fund Source</p><td>
					</tr>
					<tr>
						<td><select id='idYearStart' name='yearStart' style='width: 6em;' onChange=this.form.submit()>
";
while(	$row = $years->fetch_row()){
	if($row[0]){// this gets rid of extra empty cells
		echo"							<option value='$row[0]'> $row[0] </option>
";
	}
}
$years->data_seek(0);
echo"						</select></td>
						<td><select id='idYearEnd' name='yearEnd' style='width: 6em;' onChange=this.form.submit()>
";
$years->data_seek(0);
while(	$row = $years->fetch_row()){
	if($row[0]){
		echo"							<option value='$row[0]'> $row[0] </option>
";
	}
}
echo"						</select></td>
";
echo "						<td><select id='idFundSource' name='fundSource' style='width: 6em;' onChange=this.form.submit()>
";
$resFundSource = getSingleColumnQueryDistinctMysqli($mysqli, 'pmis_fund_name', 'systemlinks');
$rowFundSource = arrayFromResultNotNullMysqli($resFundSource);
echo "							<option value='All'> All </option>
";
foreach ($rowFundSource as $row){
	echo"							<option value='$row'> $row </option>
";
}
echo"						</select></td>
";
echo"					</tr>
				</table>

			</div>
";
echo "			
			<div id='manAssets3'>
		<input type='hidden' id='posted' name='posted' value='true'>
				<table class='multiYearColors'>
					<tr>
						<td class='preserve'>preserve</td>
						<td></td>
<td><input id='subTotals' class='subTotal' type='checkbox' onclick='showSubTotals(this)' name='subTotals'></td>
						<td>Check box to display sub-totals by investment action type</td>
					</tr>
					<tr>
						<td class='enhance'>enhance</td>
					</tr>
					<tr>
						<td class='new'>new</td>
					</tr>
					<tr>
						<td class='plan'>plan</td>
					</tr>
				</table>
		</form>
			</div>
		<div class='tableDivider'></div>
		<div class='tableDivider'></div>		
";
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
					rankings.asset_action,
					rankings.asset_tier
			FROM 	systemlinks INNER JOIN assets ON assets.asset_id=systemlinks.asset_id
								INNER JOIN rankings ON assets.asset_id=rankings.asset_id
";
$paramTypes = '';
$params = '';
$query .= "			WHERE
";

if(!('All' == $fundSource)){
	$query .=  "			pmis_fund_name = ?
			AND
";
	$paramTypes .= 's';
	$params[] = $fundSource;
}
$show = false;
if(!$allTiers){
	if('true' == $inTarget){
		$query .=  "			rankings.asset_tier = ?
			AND
";
		$show = true;
	} elseif('true' == $overTarget) {
		$query .=  "			rankings.asset_tier != ?
			AND
";
		$show = true;
	}
	if($show){
		$paramTypes .= 's';
		$params[] = 'Tier 1';
	}
}else{
	$query2 = $query;
	$query .=  "			rankings.asset_tier = ?
			AND
";
	$query2 .=  "			rankings.asset_tier != ?
			AND
";
	$paramTypes .= 's';
	$params[] = 'Tier 1';
	$show = true;
}

$query .= "					pmis_year >= ? AND pmis_year <= ?
";
$paramTypes .= 'ss';
$params[] = $yearStart;
$params[] = $yearEnd;
$query .= "			ORDER BY pmis_year ASC, pmis_region_rank ASC";
$resAssData = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
if($allTiers){
	$query2 .= "					pmis_year >= ? AND pmis_year <= ?
";
	$query2 .= "			ORDER BY pmis_year ASC, pmis_region_rank ASC";
	$resAssData2 = queryMysqliPreparedSelect($mysqli, $query2, $paramTypes, $params);
}

if($show){
	$targetType = 'in';
	if(!$allTiers && 'true' == $overT)
		$targetType = 'over';
	showRep($fundSource, $yearStart, $resAssData, $targetType);
	if($allTiers)
		showRep($fundSource, $yearStart, $resAssData2, 'over');
} // show
echo"		</div>
";
echo assetTextBottom();
?>