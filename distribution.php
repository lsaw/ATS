<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';
include 'annPerfMaker.php';

logUser('report card');

if(!isset($_SESSION['uid']))
session_start();

$yearPicked = isset($_POST['yearPicked'])	? $_POST['yearPicked'] : '';
$yearPicked = isset($_GET['yearPicked'])	? $_GET['yearPicked'] : $yearPicked;

$assetTypes = array(	'Buildings' =>[],
		'Buses & Trams' => [],
		'Bus Stops' => [],
		'Docks' =>[],
		'Ferryboats' => [],
		'ITS' => [],
		'Other' => [],
		'Pilots' => [],
		'Parking' => [],
		'Railroad Infrastructure' => [],
		'Roadways' => [],
		'Seawalls & Dredging' => [],
		'Study' => [],
		'Trails' => [],
		'Totals' => []						);
// if $_POST is empty, then first time in: set vals to true (default is checked)
// next time in: use checkbox values
$val = empty($_POST) ? 'true' : 'false';

foreach ($assetTypes as $at => $y){
	$assetTypes[$at] = array(	'municipal' => 0,
			'NPS' => 0,
			'private' => 0,
			'state' => 0,
			'transit' => 0,
			'Total' => 0,
			'show' =>$val
	);
}
$assetTypes['Totals']['show'] = 'true';
$assetTypes['Buildings']['show'] 		= isset($_POST['buildings'])	? 'true' : $assetTypes['Buildings']['show'];
$assetTypes['Buses & Trams']['show'] 	= isset($_POST['busestrams'])	? 'true' : $assetTypes['Buses & Trams']['show'];
$assetTypes['Bus Stops']['show'] 		= isset($_POST['busstops'])		? 'true' : $assetTypes['Bus Stops']['show'];
$assetTypes['Docks']['show'] 			= isset($_POST['docks'])		? 'true' : $assetTypes['Docks']['show'];
$assetTypes['Ferryboats']['show'] 		= isset($_POST['ferryboats'])	? 'true' : $assetTypes['Ferryboats']['show'];
$assetTypes['Other']['show'] 			= isset($_POST['other'])		? 'true' : $assetTypes['Other']['show'];
$assetTypes['Railroad Infrastructure']['show'] = isset($_POST['railroadinfrastructure'])	? 'true' : $assetTypes['Railroad Infrastructure']['show'];
$assetTypes['Seawalls & Dredging']['show'] = isset($_POST['seawallsdredging'])				? 'true' : $assetTypes['Seawalls & Dredging']['show'];
$assetTypes['Trails']['show'] 			= isset($_POST['trails'])		? 'true' : $assetTypes['Trails']['show'];

echo getHeaderTextGeneral();
echo javascriptHeaderText();
echo commaSeperatorFunctionsText();
echo <<<_END
		<script>
			function setSelectField(selectName, fieldVal) {
				$("#" + selectName).val(fieldVal)
			}
			function setCheckBox(fieldName, fieldValue){
				if(1 == fieldValue){
					$('fieldName').prop('checked', true)
				}else{
					$('fieldName').prop('checked', false)
				}
			}
			function onLoadFunction() {
				setSelectField('idParkId','$park')
				setSelectField('idProjectNum','$projectNum')

_END;
echo <<<_END
				$('#showReport').val($showReport)
				$('#buildings').prop('checked', {$assetTypes['Buildings']['show']})
				$('#busestrams').prop('checked', {$assetTypes['Buses & Trams']['show']})
				$('#busstops').prop('checked', {$assetTypes['Bus Stops']['show']})
				$('#docks').prop('checked', {$assetTypes['Docks']['show']})
				$('#ferryboats').prop('checked', {$assetTypes['Ferryboats']['show']})
				$('#other').prop('checked', {$assetTypes['Other']['show']})
				$('#railroadinfrastructure').prop('checked', {$assetTypes['Railroad Infrastructure']['show']})
				$('#seawallsdredging').prop('checked', {$assetTypes['Seawalls & Dredging']['show']})
				$('#trails').prop('checked', {$assetTypes['Trails']['show']})
			}
			function submitForm(th){
				var t = $(th).attr('name')
				
				switch(t){
					case 'post':
						var b = 'post'
						break;
				}
						
				$('#fieldSelected').attr('name',b)
				$('#fieldSelected').val('true')
				document.forms['myform'].submit();
			}
		</script>
_END;
echo"		<title>ATMS Report Card</title>
	</head>
	<body onload=onLoadFunction()>
";

echo "		<div id='container'>
			<p>Distribution of ATS Assets by Owner</p>
			<div id='manAssets2'>
				<table>
					<tr>
						<td><p><a href='index.htm'>Admin Menu</a></p></td>
						<td><p><a href='logout.php' >Log out</a></p></td>
";

echo"					</tr>
				</table>
			</div>
			<div id='manAssets3'>
				<form method='post' name='myform' action='$_SERVER[SCRIPT_NAME]'>
				<input type='hidden' name='action' value=''>
			</div>
";

$query = "SELECT * FROM vwasset_inventory";
$result = queryMysqli($mysqli, $query);
if(!$result) die ("Database not available at this time:" . mysql_error());

while($rowAssets = $result->fetch_assoc()){

// Here we want only those that are 'current' or 'temporary', and no 'study', 'pilot' or 'partnership'
if(	('current' == $rowAssets['asset_status'] ||
	 'temporary' == $rowAssets['asset_status'] )
	&&
	'study' != $rowAssets['asset_type'] 
	&&
	'pilot' != $rowAssets['asset_type']
	&&
	'partnership' != $rowAssets['owner']      
		){
	if('true' == $assetTypes[$rowAssets['asset_class']]['show'] ||
		('ITS' == $rowAssets['asset_class'] && 'true' == $assetTypes['Other']['show'])||
		('Parking' == $rowAssets['asset_class'] && 'true' == $assetTypes['Other']['show']) ||
		('Transit Stops' == $rowAssets['asset_class'] && 'true' == $assetTypes['Bus Stops']['show']) ||
		('Waterways' == $rowAssets['asset_class'] && 'true' == $assetTypes['Seawalls & Dredging']['show'])		){
		
	
		// grand total
		$assetTypes['Totals']['Total']+=$rowAssets['cost'];
		//gather column data
		switch ($rowAssets['owner']) {
			case 'municipal':
				$assetTypes['Totals']['municipal']+=$rowAssets['cost'];
			break;
			case 'NPS':
				$assetTypes['Totals']['NPS']+=$rowAssets['cost'];
			break;
			case 'private':
				$assetTypes['Totals']['private']+=$rowAssets['cost'];
			break;
			case 'state':
				$assetTypes['Totals']['state']+=$rowAssets['cost'];
			break;
			case 'transit':
				$assetTypes['Totals']['transit']+=$rowAssets['cost'];
			break;
//			case 'partnership':
//				$assetTypes['Totals']['partnership']+=$rowAssets['cost'];
//			break;
			default:
				;
			break;
		}
		
		
		switch ($rowAssets['asset_class']) {
			case 'Buildings':
				$assetTypes['Buildings'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Buildings']['Total']+= $rowAssets['cost'];
			break;
			case 'Buses & Trams':
				$assetTypes['Buses & Trams'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Buses & Trams']['Total']+= $rowAssets['cost'];
			break;
			case 'Transit Stops':
				$assetTypes['Bus Stops'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Bus Stops']['Total']+= $rowAssets['cost'];
			break;
			case 'Docks':
				$assetTypes['Docks'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Docks']['Total']+= $rowAssets['cost'];
			break;
			case 'Ferryboats':
				$assetTypes['Ferryboats'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Ferryboats']['Total']+= $rowAssets['cost'];
			break;
//			case 'ITS':
//				$assetTypes['ITS'][$rowAssets['owner']]+= $rowAssets['cost'];
//				$assetTypes['ITS']['Total']+= $rowAssets['cost'];
//			break;
			case 'ITS':
			case 'Other':
			case 'Parking':
				$assetTypes['Other'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Other']['Total']+= $rowAssets['cost'];
			break;
			case 'Pilots':
//				$assetTypes['Pilots'][$rowAssets['owner']]+= $rowAssets['cost'];
//				$assetTypes['Pilots']['Total']+= $rowAssets['cost'];
//			break;
//			case 'Parking':
//				$assetTypes['Parking'][$rowAssets['owner']]+= $rowAssets['cost'];
//				$assetTypes['Parking']['Total']+= $rowAssets['cost'];
//			break;
			case 'Railroad Infrastructure':
				$assetTypes['Railroad Infrastructure'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Railroad Infrastructure']['Total']+= $rowAssets['cost'];
			break;
			case 'Roadways':
				$assetTypes['Roadways'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Roadways']['Total']+= $rowAssets['cost'];
			break;
			case 'Waterways':
				$assetTypes['Seawalls & Dredging'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Seawalls & Dredging']['Total']+= $rowAssets['cost'];
			break;
			case 'Study':
//				$assetTypes['Study'][$rowAssets['owner']]+= $rowAssets['cost'];
//				$assetTypes['Study']['Total']+= $rowAssets['cost'];
			break;
			case 'Trails':
				$assetTypes['Trails'][$rowAssets['owner']]+= $rowAssets['cost'];
				$assetTypes['Trails']['Total']+= $rowAssets['cost'];
			break;
			
			default:
				;
			break;
		}
		}
	}
}
	
	
	echo "			<div class='distribution'>
				<table class='reportCard'>
					<tr><th>OWNER</th>
						<th>Municipal</th>
						<th>NPS</th>
						<th>Private</th>
						<th>State</th>
						<th>Regional Transit</th>
						<th>Total</th>
";
	echo "</tr>
";
	
	$sClass = 'even';
	foreach ($assetTypes as $at=>$vals){
		if(	'Study' != $at &&
			'Pilots' != $at &&
			'ITS' != $at &&
			'Parking' != $at &&
			'Roadways' != $at){
			if('true' == $vals['show']){
				$sClass = $sClass == 'even' ? 'odd' : 'even';
				echo "							<tr class='$sClass'>
								<td>$at</td>";
				foreach ($vals as $v =>$p){
					if('show' != $v){
						$p = addCommas($p);
						echo "<td style='text-align:right;'>$p</td>";
					}
				}
			}
			echo "					</tr>
";
		}
	}
	echo "</table>";

echo"		</div>
";
echo "		<div class='dist-side'>
			<table>
				<thead></thead>
				<tbody>
					<tr>
						<td>
							<input class='' type='checkbox' onclick='' id='buildings' value='false' name='buildings'>
						</td>
						<td>Buildings</td>
					</tr>
					<tr>
						<td>
							<input class='' type='checkbox' onclick='' id='busestrams' value='busestrams' name='busestrams'>
						</td>
						<td>Buses & Trams</td>
					</tr>
					<tr>
						<td>
							<input class='' type='checkbox' onclick='' id='busstops' value='busstops' name='busstops'>
						</td>
						<td>Bus Stops</td>
					</tr>
					<tr>
						<td>
							<input class='' type='checkbox' onclick='' id='docks' value='docks' name='docks'>
						</td>
						<td>Docks</td>
					</tr>
					<tr>
						<td>
							<input class='' type='checkbox' onclick='' id='ferryboats' value='ferryboats' name='ferryboats'>
						</td>
						<td>Ferryboats</td>
					</tr>
						<td>
							<input class='' type='checkbox' onclick='' id='other' value='other' name='other'>
						</td>
						<td>Other</td>
					</tr>
						<td>
							<input class='' type='checkbox' onclick='' id='railroadinfrastructure' value='railroadinfrastructure' name='railroadinfrastructure'>
						</td>
						<td>Railroad Infrastructure</td>
					</tr>
						<td>
							<input class='' type='checkbox' onclick='' id='seawallsdredging' value='seawallsdredging' name='seawallsdredging'>
						</td>
						<td>Seawalls & Dredging</td>
					</tr>
						<td>
							<input class='' type='checkbox' onclick='' id='trails' value='trails' name='trails'>
						</td>
						<td>Trails</td>
					</tr>
					<tr>
						<td>
							<input class='send' type='submit' onclick='' value='submit' name='submit'>
						</td>
					</tr>
		
				</tbody>
			</table>
		</div>
";
echo assetTextBottom();
?>