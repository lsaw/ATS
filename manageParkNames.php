<?php
// due to changes in conception, park here gets saved as subunit, and subunit gets saved as admin (park in database)
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('manage park names');

if(!isset($_SESSION['uid']))
session_start();

$numParkStates = 0;

$mode 			= isset($_POST['mode']) 		? $_POST['mode'] 		: '';
$reset 			= isset($_POST['reset']) 		? $_POST['reset'] 		: '';
$action 		= isset($_POST['action']) 		? $_POST['action'] 		: '';
$park 			= isset($_POST['park']) 		? $_POST['park'] 		: '';
$parkName		= isset($_POST['parkName']) 	? $_POST['parkName'] 	: '';
$region 		= isset($_POST['region']) 		? $_POST['region'] 		: '';
$theStates 		= isset($_POST['theStates'])	? explode(',', $_POST['theStates']) 	: '';
$allowDeletes 	= isset($_POST['allowDeletes']) ? $_POST['allowDeletes'] : '0';

$mode 			= isset($_GET['mode']) 			? $_GET['mode'] 		: $mode;
$park 			= isset($_GET['park']) 			? $_GET['park'] 		: $park;
$parkName 		= isset($_GET['parkName']) 		? $_GET['parkName'] 	: $parkName;
$region			= isset($_GET['region']) 		? $_GET['region'] 		: $region;
$action 		= isset($_GET['action']) 		? $_GET['action'] 		: $action;
$allowDeletes 	= isset($_GET['allowDeletes'])	? $_GET['allowDeletes'] : $allowDeletes;

if('update' == $action){
	$parkIn 	= isset($_POST['parkIn']) 		? $_POST['parkIn'] 		: '';
	$parkNameIn	= isset($_POST['parkNameIn']) 	? $_POST['parkNameIn'] 	: '';
	$regionIn 	= isset($_POST['regionIn']) 	? $_POST['regionIn'] 	: '';
	$state 		= isset($_POST['state']) 		? $_POST['state'] 		: '';
	$query = "UPDATE parks SET  park_id=?,
								park_name=?,
								region_id=?
						WHERE 	park_id=?";
	$paramTypes = 'ssss';
	$params = array($parkIn, $parkNameIn, $regionIn, $park);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
			
	$query = "INSERT INTO park_states SET park_id=?, state_id=?";
	$paramTypes = 'ss';
	$params = array($parkIn, $state);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
}
if('newPark' == $action) {// update all tables
	$parkIn 	= isset($_POST['parkIn']) 		? $_POST['parkIn'] 		: '';
	$parkNameIn	= isset($_POST['parkNameIn']) 	? $_POST['parkNameIn'] 	: '';
	$regionIn 	= isset($_POST['regionIn']) 	? $_POST['regionIn'] 	: '';
	$parkIn = strtoupper($parkIn);
	$query = "INSERT INTO parks SET park_id=?,
									park_name=?,
									region_id=?";
	$paramTypes = 'sss';
	$params = array($parkIn, $parkNameIn, $regionIn);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		
	foreach ($theStates as $state){
		$query = "INSERT INTO park_states SET park_id=?, state_id=?";
		$paramTypes = 'ss';
		$params = array($parkIn, $state);
		$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	}
}
if('newState' == $action){
	$state = isset($_POST['newStatePicked']) ? $_POST['newStatePicked'] : '';
	$query = "INSERT INTO park_states SET park_id=?, state_id=?";
	$paramTypes = 'ss';
	$params = array($park, $state);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
}
if('deleteState' == $action){
	$state = isset($_POST['delState']) ? $_POST['delState'] : '';
	$query = "DELETE FROM park_states WHERE park_id=? AND state_id=?";
	$paramTypes = 'ss';
	$params = array($park,$state);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
}

if('deletePark' == $action){
	$query = "DELETE FROM parks WHERE park_id=?";
	$paramTypes = 's';
	$params = array($park);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	
	// delete park_states
	$query = "DELETE FROM park_states WHERE park_id=?";
	$paramTypes = 's';
	$params = array($park);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	
	$allowDeletes = 1;
}
if('edit' == $mode){ // get park name, and generate data for displaying that park in edit mode
	$query = "	SELECT state_name, states.state_id
			FROM park_states JOIN states ON park_states.state_id=states.state_id
			WHERE park_id=?";
	$paramTypes = 's';
	$params = array($park);
	$resParkStates = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$numParkStates = count($resParkStates);
	// get the data from $park
	$query = "SELECT * FROM parks WHERE park_id=?";
	$paramTypes = 's';
	$params = array($park);
	$resPark = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	if($resPark){
		$parkName = $resPark[0]['park_name'];
		$subUnit = $resPark[0]['subunit_id'];
		$subUnitName = $resPark[0]['subunit_name'];
		$region = $resPark[0]['region_id'];
	}
}
$query = "SELECT * FROM states ORDER BY state_name ASC";
$resStates = queryMysqli($mysqli, $query);
$numStates = $resStates->num_rows;

echo getHeaderTextGeneral();
echo javascriptHeaderText();
?>
		<script>
			var states=[]; // create variable to hold states
			function setSelectField(selectName, fieldName) {
				$("#" + selectName).val(fieldName)
			}
			function editFunction(fieldPointer){
				fieldPointer.style.color="red"
				$("#idUpdate").css("visibility","visible")
				$("#idReset").css("visibility","visible")
			}
			function editFunctionNew(fieldPointer){
				fieldPointer.style.color="red"
				$("#idReset").css("visibility","visible")
				$('#idNewStateButton').css("color", "red")
			}
			function chooseState(fieldPointer,id){
				$("#delState").val(id)
				setAction(fieldPointer)
			}
			function setAction(fieldPointer){
				switch (fieldPointer.name) {
					case 'Reset':
						$("#action").val("reset")
						break;			
					case 'newPark':
						$("#action").val("newPark")
						break;
					case 'updatePark':
						$("#action").val("update")
						break;			
					case 'newStatePicked':
						$("#action").val("newState")
						break;
					case 'deleteState':
						$("#action").val("deleteState")
						break;
					case 'allowDeletes0':
						$("#allowDeletes").val(1)
						break;
					case 'allowDeletes1':
						$("#allowDeletes").val(0)
						break;
					case 'deletePark':
						$("#action").val("deletePark")
						$("#idPark").val(fieldPointer.id)
						break;
					default:
						break;
				}
				$("#myForm").submit();
			}
			function deleteThisState(field) {
				$('#tr' + field.name).remove() // take away the row with this value
				$('#idState option[value=' + field.name + ']').css("display","block"); // show this value in drop down list again
				states.splice(states.indexOf(field.name),1) // remove this value from the array
				$("#theStates").val(states); // make this array value of hidden input variable
			};
			function onLoadFunction() {
				$('#idState').append(states); // append this variable to the <input> idState
				$('#idState').change(function() { // do this when you add a state
					states.push($(this).val()) // add to array
					$("#theStates").val(states); // make this array value of hidden input variable
					f=$('#idState option:selected').text(); // this is like "Maryland" // next line adds row with this state
					$('#regionRow').after('<tr id="tr' + $(this).val() + '"><td>State:</td><td style="font-size:10pt;" colspan="3">' + f + '</td><td><button id="idDeleteState" onclick="deleteThisState(this)" type="button" name="' + $(this).val() + '">Delete</button></td></tr>')
					$('#idState option[value=' + $(this).val() + ']').css("display","none"); // remove from drop down display
					$("#idState")[0].selectedIndex = 0; // select show top line (blank)
				});

<?php
	// always set mode
	echo "				$('#mode').val('$mode')
				$('#allowDeletes').val('$allowDeletes')
		
";
	if('edit' == $mode) {
	echo "				$('#idPark').val('$park')
				$('#idParkNameIn').val('$parkName')
				$('#idParkIn').val('$park')
				$('#idRegionIn').val('$region')
";
	}
?>
			}
		</script>
		<title>ATS Management System</title>
	</head>
	<body onload=onLoadFunction()>
<?php
// Get list of region names
$resRegions = getRegions($mysqli);

// below only needed for 'show parks'
$query = "	SELECT	parks.park_id,
					park_name,
					region_name,
					regions.region_id,
					states.state_id,
					state_name
					FROM parks	JOIN regions ON regions.region_id=parks.region_id
								JOIN park_states ON parks.park_id=park_states.park_id
								JOIN states ON states.state_id=park_states.state_id
					ORDER BY parks.park_id ASC;";
$resParkData = queryMysqli($mysqli, $query);

?>
		<div id='container'>
			<p>Manage Park Names</p>
			<div id='manAssets2'>
				<table>
					<tr>
						<td><p><a href='index.htm'>Admin Menu</a></p></td>
						<td><p><a href='logout.php' >Log out</a></p></td>
<?php
if('show' == $mode){
	if ($level == '1') {// must be level '1' and also have poked 'Allow Deletes' field to delete
		if('1' == $allowDeletes) {
			echo"						<td><a href='#!' name='allowDeletes$allowDeletes' onclick='setAction(this)'>No Deletes</a></td>
";
		}else {
			echo"						<td><a href='#!' name='allowDeletes$allowDeletes' onclick='setAction(this)'>Allow Deletes</a></td>
";
		}
	}
}
?>
					</tr>
				</table>
			</div><!-- manAsset2 -->
			<div id='manAssets2'>
				<table>
					<tr>
						<td><p><a href='manageParkNames.php?mode=new'>Add New Park</a></p></td>
						<td style='visibility:hidden'><p><a href='manageParkNames.php?mode=edit'>Edit Park</a></p></td>
						<td><p><a href='manageParkNames.php?mode=show'>Show Parks</a></p></td>
					</tr>
				</table>
			</div><!-- manAsset2 -->
				<form id='myForm' method='post' action='<?php echo "$_SERVER[SCRIPT_NAME]"?>'>
					<input type='hidden' id='mode'			name='mode' 		value=''>
					<input type='hidden' id='action' 		name='action' 		value=''>
					<input type='hidden' id='idPark' 		name='park' 		value=''>
					<input type='hidden' id='delState' 		name='delState' 	value=''>
					<input type='hidden' id='theStates'		name='theStates' 	value=''>
					<input type='hidden' id='allowDeletes' 	name='allowDeletes'	value=''>

<?php
if('new' == $mode || 'edit' == $mode){
?>
			<div id='manParks'>
				<div class='newPark'>
					
				<table id='parkDetail' class='parkNamesTable'>
						<thead>
							<tr>
								<th></th>
								<th colspan='11'>
<?php
	if('edit' == $mode){
		echo "						Edit Park
";
	}else{
		echo "						New Park
";
	}
?>
								</th>
							</tr></thead><tbody>
							<tr><td>Park Name:</td>
								<td colspan='7'><input type='text' id='idParkNameIn' name='parkNameIn' value='' style='' onKeyUp=editFunction(this)></td>
							</tr>
							<tr><td>Park ID:</td>
								<td><input type='text' id='idParkIn' name='parkIn' value='' onKeyUp=editFunction(this) style=''></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td></tr>
							<tr id='regionRow'><td>Region:</td>
								<td colspan='4'><select id='idRegionIn' name='regionIn' style='width:100%;' onChange=editFunction(this)>

<?php
foreach ($resRegions as $row){
	echo "									<option value='{$row['region_id']}'> {$row['region_name']} </option>
";
}
?>
									</select></td>
							</tr>
<?php

if('edit' == $mode){
	echo "							<tr><td id='statesTd'>State:</td>
";
	$usedState = [];
	for($i=0; $i<$numParkStates;$i++){
		foreach ($resStates as $rowState){
			$thisState = '';
			if($rowState['state_name'] == $resParkStates[$i]['state_name']){
				$thisState = $rowState['state_name'];
				$usedState[]=$rowState['state_id'];
				break;
			}
		}
		if($i != 0)
			echo "							<tr><td></td>";
		echo "								<td colspan='3' style='font-size:10pt;'>$thisState</td>
								<td><input type='button' id='idDeleteState' name='deleteState' value='Delete' onclick=chooseState(this,'{$resParkStates[$i]['state_id']}')></td></tr>
";
		
	}

	// this is for the last row for a new state
	if($numStates > $numParkStates) {
		echo"								<td></td>
								<td colspan='3'><select id='idNewState' name='newStatePicked' style='width:100%;' onChange=setAction(this)>
										<option value=''></option>
";
		foreach($resStates as $rowState){
			if(!in_array($rowState['state_id'], $usedState, true)){
				echo"									<option value='{$rowState['state_id']}'> {$rowState['state_name']} </option>
";
			}
		}

		echo "									</select></td>
								<td colspan='4' style='font-size:10pt'>Select to add new state</td></tr>
";
	} // end if( numStates > $numParkStates)
}// end if('edit' == $action)
else{// below is not edit
	echo "							<tr><td id='statesTd'>State(select):</td>
";
	echo"								<td colspan='3'><select id='idState' name='state' style='width:100%;' onChange=editFunction(this)>
									<option value=''></option>
";
	foreach($resStates as $rowState) {
		echo"									<option value='{$rowState['state_id']}'> {$rowState['state_name']} </option>
";
	}
	echo"									</select></td></tr>
";
}

	echo"							<tr><td></td>
								<td>
									<input type='button' id='idReset' name='Reset' onclick='setAction(this)' value='Reset' style='visibility:hidden'></td>
";

if('edit' == $mode){
	echo"								<td colspan='3'><input type='button' id='idUpdate' name='updatePark' value='Update Park Data' onclick='setAction(this)' style='visibility:hidden;color:red'></td>
							</tr>
";
}else { // new park
	echo"								<td colspan='2'><input type='button' id='idUpdate' name='newPark' value='Enter New Park' onclick='setAction(this)' style='visibility:hidden;color:red'></td>
							</tr>
";
}
?>
						</tbody>
					</table><!-- parkNamesTable -->
				</div><!-- newPark -->
<?php }
//								<th>Admin</th>

if('show' == $mode){?>
				<div id='parksTable'>
					<table id='manAss' class='manAsset' style='float:left;'>
						<thead>
							<tr>
								<th>Park Name</th>
								<th>Park ID</th>
								<th>Region</th>
								<th>State</th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
<?php
	$sClass = 'even';
	$currentPark = '';
	$showEdit = true;
	while($rowPark=$resParkData->fetch_assoc()) {
		$sClass = $sClass == 'even' ? 'odd' : 'even';
		echo "							<tr class='$sClass'>
";
		if($currentPark == $rowPark['park_name']){
			echo "								<td></td>
";
			$showEdit = false;
		}else{
			echo "								<td style='text-align:left'>{$rowPark['park_name']}</td>
";
			$currentPark = $rowPark['park_name'];
			$showEdit = true;
		}
		
		echo "								<td style='text-align:left'>{$rowPark['park_id']}</td>
								<td style='text-align:left'>{$rowPark['region_name']}</td>
								<td style='text-align:left'>{$rowPark['state_name']}</td>
";
			// low level only gets to see, not edit and no delete
		if($level == '3') {
			echo "								<td style='text-align:center'></td>
";
		}
			// level 2 gets to edit but no delete
		else {
			echo "								<td style='text-align:center";
			if(!$showEdit) {				
				echo ";visibility:hidden";
			}
			echo "'><a href=\"manageParkNames.php?park={$rowPark['park_id']}&mode=edit\">EDIT</a></td>
";				
		}
		// level 1 gets to delete, if 'Allow Deletes' poked
		if($level == '1' && $allowDeletes == '1' && $showEdit) {
			echo "								<td style='text-align:center'><a href='#!' name='deletePark' id='{$rowPark['park_id']}' onclick='setAction(this)'>DELETE</a></td>
";
		}
		echo "							</tr>
";
	}
	echo "						<tbody>
					</table>
				</div><!-- parksTable -->
				</form>
			</div><!-- manParks -->
";
}
echo "
		</div><!-- container -->
";
echo assetTextBottom();
?>