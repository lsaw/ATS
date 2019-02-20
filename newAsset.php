<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('new asset');

if(!isset($_SESSION['uid']))
	session_start();

if(isset($_POST['new'])){// update all tables
	$cap = stripCommas($_POST['capCost']);
	$op = stripCommas($_POST['opCost']);
	$query = 'INSERT INTO assets SET ' .
         	'park_id = ?,'.
			'asset_name = ?,'.
			'asset_type = ?,'.
			'asset_status = ?,'.
			'owner = ?,'.
			'lifespan = ?,'.
			'cap_cost = ?,'.
			'op_cost = ?,'.
			'year_new = ?';
	$paramTypes = 'sssssssss';
	$params = array(	$_POST['parkId'] ,
						$_POST['assetName'],
						$_POST['assetType'],
						$_POST['assetStatus'],
						$_POST['owner'],
						$_POST['lifespan'],
						$cap,
						$op,
						$_POST['yearNew']);
	$result = queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);	
	echo "<meta http-equiv='refresh' content='0;url=assets.php?park={$_POST['parkId']}&asset_id={$mysqli->insert_id}&asset_name={$_POST['assetName']}'>";
}

// passed in from manageAssets.php
$park = isset($_GET['park']) ? $_GET['park'] : '';

// or as a post from this page
$park = isset($_POST['park']) ? $_POST['park'] : '';
$asset_id = isset($_POST['assetId']) ? $_POST['assetId'] : '';

$resParks 			= getSingleColumnQueryMysqli($mysqli, 'park_id', 'parks');
$resAssetType 		= getSingleColumnQueryMysqli($mysqli, 'asset_type_name', 'asset_types');
$resAssetStatus 	= getSingleColumnQueryMysqli($mysqli, 'asset_status', 'asset_status');
$resAssetOwner 		= getSingleColumnQueryMysqli($mysqli, 'asset_owner', 'asset_owners');

echo getHeaderTextGeneral();
echo javascriptHeaderText();
echo <<<_END
		<script>
		function getAssetData(park){	
			$.ajax({
		   type: "POST",
		   url: "getAssetData.php",
		   data: "park=" + park.value,
		   success: function(result){
			document.getElementById('info').innerHTML = result
		   }
			});
		}
		function setSelectField(selectName, fieldName){
			$("#" + selectName).val(fieldName)
		}
		function changeColor(p){
			p.style="color:red";
		}
		function onLoadFunction(){
			setSelectField('idParkId','$park')
		}
		function editFunction(fieldPointer, table){
			fieldPointer.style.color="red"
			$("#idNew").css("visibility","visible")
			$("#idReset").css("visibility","visible")
			$("#idCancel").css("visibility","visible")
			$("#" + table).val("true")
		}
		function editFunctionProj(fieldPointer, table){
			fieldPointer.style.color="red"
			$("#idReset").css("visibility","visible")
			$("#" + table).val("true")
		}
		function chooseProject(proj){
			$("#" + "idThisProj").val(proj)
		}
		function stripCommas(num){
			var rg = /,/g;
			var num1 = num.replace(rg, '');
			return num1;
		}
		function addCommas(nStr){
			nStr += '';
			var x = nStr.split('.');
			var x1 = x[0];
			var x2 = x.length > 1 ? '.' + x[1] : '';
			var rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {
				x1 = x1.replace(rgx, '$1' + ',' + '$2');
			}
			return x1 + x2;
		}
		function setCommas(fieldPointer, table){
			editFunction(fieldPointer, table);
			var num = fieldPointer.value;
			var num1 = stripCommas(num);
			var numCom = addCommas(num1);
			fieldPointer.value = numCom;
		}
		function pressCancel(){
			window.location.href = "manageAssets.php?park=" + $('#idCancel').attr('name')
		}
	</script>
_END;
echo "
		<title>ATS Management System</title>
	</head>
	<body onload=onLoadFunction()>
";	
echo "		<div id='container'>
			<p>Enter New Asset</p>
			<div id='assets1'>
			<table>
			<td><a href='index.htm'>Admin Menu</a></td>
			<td><a href='manageAssets.php?park=$park'>Manage Assets</a></td>
			<td><a href='logout.php' >Log out</a></td>
			</table>
			</div> <!--  End assets1 -->
			<form method='post' action='$_SERVER[SCRIPT_NAME]'>
				<div id='assets2'>
";
// Load up the Assets table
echo "					<table class='newAssetTable'>
						<tr><th></th><th>Asset</th></tr>
";

echo generateSelect('Park ID', 'idParkId', 'parkId', $resParks, 'assetC', $level, '');
echo generateInputField('Asset Name', 'assetName', '', 'assetC', $level);
echo generateSelect('Asset Type', 'idAssetType', 'assetType', $resAssetType, 'assetC', $level, '');
echo generateSelect('Status', 'idAssetStatus', 'assetStatus', $resAssetStatus, 'assetC', $level, '');
echo generateSelect('Owner', 'idAssetOwner', 'owner', $resAssetOwner, 'assetC', $level, '');
echo generateInputField('Lifespan', 'lifespan','', 'assetC', $level);
echo generateInputFieldNum('Capital Cost', 'capCost','', 'assetC','', $level);
echo generateInputFieldNum('Operational Cost', 'opCost','', 'assetC','', $level);
echo generateInputField('Year New', 'yearNew','', 'assetC', $level);
echo"						<tr>	<td></td>
							<td><input type='submit' id='idReset' name='reset' value='Reset' style='visibility:hidden'/><input type='submit' id='idNew' name='new' value='New' style='visibility:hidden;color:red'/><input type='button' id='idCancel' name='$park' value='Cancel' onClick='pressCancel();' style='visibility:hidden'/></td></tr>
								<input type='hidden' id='idParkId' name='park' value='$park'>
								<input type='hidden' id='projC' name='assetAdded' value='false'></td></tr>
					</table>
";

echo "			</form>
		</div> <!--  End container2 -->
";

echo assetTextBottom();

?>