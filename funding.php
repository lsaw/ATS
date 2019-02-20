<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('Funding');

if(!isset($_SESSION['uid']))
	session_start();
$resSource 	= getSingleColumnQueryMysqli($mysqli, 'fund_source', 'funding_sources');
$resName 	= getSingleColumnQueryMysqli($mysqli, 'fund_name', 'funding_sources');
$arraySource = arrayFromResultMysqli($resSource);
$arrayName 	 = arrayFromResultMysqli($resName);
$numSources = $resSource->num_rows;

if(isset($_POST['update'])){// update all tables
	if(isset($_POST['asset_id']))
	$asset_id = $_POST['asset_id'];
		
	if($_POST['fundChanged']== 'true'){
		for($i=0; $i<$numSources; $i++){
			$temp0 = $arrayName[$i] . '0';
			$temp1 = $arrayName[$i] . '1';
			$temp2 = $arrayName[$i] . '2';
			$query = 'REPLACE INTO funding SET ' .
					 'asset_id = ?,' . 
					 'fund_source = ?,' . 
					 'cap_percent = ?,' . 
					 'op_percent = ?,' . 
					 'futurecap_percent = ?';
			$paramTypes = 'sssss';
			$st0 = str_replace('%', '', $_POST[$temp0])/100;
			$st1 = str_replace('%', '', $_POST[$temp1])/100;
			$st2 = str_replace('%', '', $_POST[$temp2])/100;
			$params = array(	$_POST['asset_id'],
								$arraySource[$i],
								$st0,
								$st1,
								$st2);
			$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
	}
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

// get table data
$query = "SELECT * FROM funding WHERE asset_id=?";
$paramTypes = 's';
$params = array($asset_id);
$resFunding = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	
echo getHeaderTextGeneral();
echo javascriptHeaderText();
echo commonFunctionsText();
echo "<script type='text/javascript'>
		
		function editFunding(fieldPointer, table){
		calcTotal(fieldPointer);
		editFunction(fieldPointer, table);
	}
	function calcTotal(fieldPointer){
		var c = ''
		c = $(fieldPointer).hasClass('cap') ? 'cap' : c
		c = $(fieldPointer).hasClass('op') ? 'op' : c
		c = $(fieldPointer).hasClass('future') ? 'future' : c
		var sum = 0;
		switch(c){
			case 'cap':
				$('.cap').each(function( index ) {
					sum += Number($(this).val());
				});
				$('#captot').html(sum);
				if(sum > 100 || sum < 100){
					$('#captot').css('color','red');
				} else{
					$('#captot').css('color','black');
				}
				break;
			case 'op':
				$('.op').each(function( index ) {
					sum += Number($(this).val());
				});
				$('#optot').html(sum);
				if(sum > 100 || sum < 100){
					$('#optot').css('color','red');
				} else{
					$('#optot').css('color','black');
				}
				break;
			case 'future':
				$('.future').each(function( index ) {
					sum += Number($(this).val());
				});
				$('#futuretot').html(sum);
				if(sum > 100 || sum < 100){
					$('#futuretot').css('color','red');
				} else{
					$('#futuretot').css('color','black');
				}
				break;
		}
	}
		</script>";
echo "
		<title>ATS Management System</title>
	</head>
	<body>
";
echo "		<div id='container'>
			<p>Funding</p>
			<p>Asset $asset_id: $asset_name</p>
			<div id='assets1'>
			<table><tr>
			<td><a href='index.htm' onclick='return confirmMove();'>Admin Menu</a></td>
			<td><a href='manageAssets.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage Assets</a></td>
			<td><a href='managePMIS.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage PMIS</a></td>
			<td><a href='tier.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Tier Assignment</a></td>
			<td><a href='assets.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Asset Data</a></td>
			<td><a href='FMSS.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projectNum=$projectNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>FMSS Links</a></td>
			<td><a href='logout.php' onclick='return confirmMove();' >Log out</a></td></tr>
			</table>
			</div> <!--  End assets1 -->
			<form method='post' action='$_SERVER[SCRIPT_NAME]'>
				<div class='fmssAndFunding'>
";

echo "					<table class='fundTable'>
						<tr><th>Source</th><th>Capital</th><th>Operating</th><th>Future Capital</th></tr>
";
	$rowFunding = array(0,0,'0','0',0);
	$haveData = count($resFunding);
	$totals = array('cap_percent'=>'0','op_percent'=>'0','futurecap_percent'=>'0');
	for($i=0; $i<$numSources;$i++){
		if($haveData){
			$rowFunding = $resFunding[$i]; // $rowAssets contains all of the data
		}
		echo generateInputFieldFunding($arraySource[$i], $arrayName[$i], $rowFunding, 'fundC', $level, $totals);
	}
	
	echo "<tr><td>Totals:</td>
";
	if($totals['cap_percent']<100 || $totals['cap_percent']>100){
		echo "<td id='captot' style='color:red;'>{$totals['cap_percent']}%</td>";
	} else{
		echo "<td id='captot'>{$totals['cap_percent']}%</td>";
	}
	if($totals['op_percent']<100 || $totals['op_percent']>100){
		echo "<td id='optot' style='color:red;'>{$totals['op_percent']}%</td>";
	} else{
		echo "<td id='optot'>{$totals['op_percent']}%</td>";
	}
	if($totals['futurecap_percent']<100 || $totals['futurecap_percent']>100){
		echo "<td id='futuretot' style='color:red;'>{$totals['futurecap_percent']}%</td>";
	} else{
		echo "<td id='futuretot'>{$totals['futurecap_percent']}%</td>";
	}
	echo "</tr>
";
echo"						<tr>	<td></td>
							<td><input type='submit' id='idReset' name='reset' value='Reset' style='visibility:hidden'/><input type='submit' id='idUpdateAll' name='update' value='Update' style='visibility:hidden;color:red'/></td></tr>
							<tr><td></td><td colspan='3' style='text-align:center;'>Each column should total to 100%</td></tr>
						<tr><td><input type='hidden' id='idParkId' 		name='park' 			value='$park'>
								<input type='hidden' id='idAssetID' 	name='asset_id' 		value='$asset_id'>
								<input type='hidden' id='idAssetName'	name='asset_name' 		value='$asset_name'>
								<input type='hidden' id='idprojectNum' 	name='projectNum' 		value='$projectNum'>
								<input type='hidden' id='idYear' 		name='year' 			value='$year'>
								<input type='hidden' id='idfund' 		name='fund' 			value='$fund'>
								<input type='hidden' id='idregion' 		name='region' 			value='$region'>
								<input type='hidden' id='fundC' name='fundChanged' value='false'>
								<input type='hidden' id='anyTable' name='anyTableChanged' value='false'></td></tr>
					</table>
";

echo "			</form>
		</div> <!--  End container2 -->
";

echo assetTextBottom();
?>