<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('tierFunding');

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
$lowYear = 		isset($_GET['lowYear']) 	? $_GET['lowYear'] 		: '2012';
$highYear = 	isset($_GET['highYear']) 	? $_GET['highYear'] 	: '2022';

// or as a post from this page
$region = 		isset($_POST['region'])		? $_POST['region'] 		: $region;
$park = 		isset($_POST['park']) 		? $_POST['park'] 		: $park;
$asset_id = 	isset($_POST['asset_id']) 	? $_POST['asset_id'] 	: $asset_id;
$asset_name = 	isset($_POST['asset_name'])	? $_POST['asset_name'] 	: $asset_name;
$projectNum =	isset($_POST['projectNum']) ? $_POST['projectNum']	: $projectNum;
$year = 		isset($_POST['year']) 		? $_POST['year'] 		: $year;
$fund = 		isset($_POST['fund']) 		? $_POST['fund'] 		: $fund;
$lowYear = 		isset($_POST['lowYear']) 	? $_POST['lowYear'] 	: $lowYear;
$highYear = 	isset($_POST['highYear']) 	? $_POST['highYear'] 	: $highYear;

$resYears = getSingleColumnQueryDistinctOrderedAscMysqli($mysqli, 'pmis_year', 'systemlinks');
$years = arrayFromResultNotNullMysqli($resYears);

if(!$lowYear)
	$lowYear = $years[0];
if(!$highYear)
	$highYear = $years[count($years)-1];
foreach($years as $yr){
	if($lowYear == $yr)
		break;
	array_shift($years);
}
for($i=count($years)-1; $i>0; $i--){
	if($highYear == $years[$i])
		break;
	array_pop($years);
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
				setSelectField('idLowYear','$lowYear')
				setSelectField('idHighYear','$highYear')
			}
		</script>

_END;
echo "		<script type='text/javascript'>
			function editFunctionTier(fieldPointer, asset){
				$('#idAsset').val(asset)
				$('#idUpdate').val('true')
				$('#idTier').val(fieldPointer.options[fieldPointer.value].text)
			}
		</script>
";
echo "
		<title>Tier Funding Page</title>
	</head>
	<body onload=onLoadFunction()>
";

echo "		<div id='container'>
			<form method='post' action='$_SERVER[SCRIPT_NAME]'>
			<input type='hidden' name='year' value='$year'>
			<input type='hidden' name='fund' value='$fund'>
			<p>Tier Funding Totals</p>
			<div id='manAssets2'>
				<table>
					<tr><td><a href='index.htm'>Admin Menu</a></td>
						<td><a href='manageAssets.php'>Manage Assets</a></td>
						<td><a href='tier.php?year=$year&fund=$fund&lowYear=$lowYear&highYear=$highYear'>Tier Assignments</a></td>
						<td><a href='logout.php' >Log out</a></td>
					</tr>
				</table>
			</div> <!--  End manAssets2 -->
";

echo "			<div id='manAssets3'>
				<table class='manAssTop'>
					<tr>
						<td><p>From</p></td><td><p>To</p></td>
					</tr>
					<tr>
						<td><select id='idLowYear' name='lowYear' style='width: 6em;' onChange=this.form.submit()>
";
$query = "SELECT DISTINCT pmis_year FROM systemlinks ORDER BY pmis_year ASC";
$resPMISYear = queryMysqli($mysqli, $query);
if(!$resPMISYear) die ("Database not available at this time:" . mysql_error());

while(	$row = $resPMISYear->fetch_row()){
	if($row[0]){// this gets rid of empty cells
		echo"						<option value='$row[0]'> $row[0] </option>
";
	}
}
echo"						</select></td>
";

echo "						<td><select id='idHighYear' name='highYear' style='width: 6em;' onChange=this.form.submit()>
";

$resPMISYear->data_seek(0); //reset list for next fields

while(	$row = $resPMISYear->fetch_row()){
	if($row[0]){// this gets rid of empty cells
		echo"						<option value='$row[0]'> $row[0] </option>
";
	}
}
echo"						</select></td>
";
echo"					</tr>
				</table>
			</div>
";

	$resAssetTier = getSingleColumnQueryMysqli($mysqli, 'asset_tier', 'asset_tiers');
	$assetTier = tierArrayFromResultMysqli($resAssetTier);
	
//	$resFundSource = getSingleColumnQueryDistinct('pmis_fund_name', 'systemlinks');
//	$fundSource = arrayFromResultNotNull($resFundSource);
//	The above is the proper way to do this, but the funding source really needs to have
//	its own table.  So for now, create the array manually, and leave out TIGER and PLHD
	$fundSource[] = 'Cat 3';
	$fundSource[] = 'TRIP';
	
	$sClass = 'even';
	foreach($fundSource as $source){
		echo "			<div class='fmssAndFunding'>
				<table class='tierFundingTable'>
					<tr>
						<th>$source</th>
";
		foreach($years as $year){
			echo"						<th>$year</th>
";
			$total[$year] = 0;
		}
		// end row and start next
		echo"						<th>Total</th>
					</tr>
";
		foreach($assetTier as $tier){
			if("decommisioned" != $tier){
				$sClass = $sClass == 'even' ? 'odd' : 'even';
				$tierTotal = 0;
				echo"					<tr class='$sClass'>
						<td>$tier</td>
";
				
				foreach($years as $year){
					$query = "SELECT pmis_dollars, pmis_fund_name, pmis_year, asset_tier
							FROM assets INNER JOIN systemlinks
								ON assets.asset_id = systemlinks.asset_id
							INNER JOIN project_assets
								ON assets.asset_id = project_assets.asset_id
							INNER JOIN projects
								ON projects.project_id = project_assets.project_id
							INNER JOIN rankings
								ON rankings.asset_id = assets.asset_id
							WHERE pmis_year = '$year' AND asset_tier = '$tier' AND pmis_fund_name = '$source'";
				
					$resTier = queryMysqli($mysqli, $query);
					if(!$resTier) die ("Database not available at this time:" . mysql_error());
				
					$sum = 0;
					while ($rowTier = $resTier->fetch_row()){
						$sum += $rowTier[0];
					}
					$total[$year] += $sum;// this goes towards column (year) total
					$tierTotal += $sum;// this goes towards row (tier) total
					$sum = addCommas($sum);
					echo"						<td style='width:6em; text-align:right'>$sum</td>
";
				}
				$tt = addCommas($tierTotal);
				echo "						<td style='text-align:right'>$tt</td>
					</tr>
";
			}
		}
			
		echo "					<tr>	<td>Totals</td>
";
		foreach($years as $year){
			$val = addCommas($total[$year]);
			echo "						<td style='text-align:right;'>$val</td>
";
		}
		echo "					</tr>
";
		echo "			</table>
		</div>
";
	}
echo "			</form>
		</div> <!--  End container2 -->
";

echo assetTextBottom();

?>