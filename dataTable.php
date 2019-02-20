<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';

logUser('dataTable');

if(!isset($_SESSION['uid']))
session_start();

$haveProject = false;
$projectName = '';

$region 	= isset($_POST['region'])		? $_POST['region']		: 'NERO';
$park 		= isset($_POST['park'])			? $_POST['park']		: '';
$projectNum = isset($_POST['projectNum'])	? $_POST['projectNum']	: '';
$year		= isset($_POST['year'])			? $_POST['year']		: '2015';
$fund		= isset($_POST['fund'])			? $_POST['fund']		: 'All';

$region 	= isset($_GET['region'])		? $_GET['region']		: $region;
$park 		= isset($_GET['park'])			? $_GET['park']			: $park;
$projectNum = isset($_GET['projectNum'])	? $_GET['projectNum']	: $projectNum;
$year 		= isset($_GET['year'])			? $_GET['year']			: $year;
$fund		= isset($_GET['fund'])			? $_GET['fund']			: 'All';

// need to deal with situation where you have a project selected for park A, but you 
// then switch to park B, and that park does not have that project.  Sol'n is to 
// query that park for that project. If not there force to 'All'.
if($park && $projectNum != '') {
	$query= "SELECT project_name FROM projects WHERE project_id = ? AND park_id = ?";
	$paramTypes = 'ss';
	$params = array($projectNum, $park);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$projectName = $result[0]['project_name'];

	if(empty($result))
		$projectNum = '';
}
if($park && $projectNum == '') {// project should default to default for that park
	$query= "SELECT projects.project_id, project_name FROM parks_project_defaults 
											JOIN projects ON parks_project_defaults.project_id=projects.project_id
											WHERE parks_project_defaults.park_id = ?";
	$paramTypes = 's';
	$params = array($park);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	if(!empty($result)){
		$projectNum = $result[0]['project_id'];
		$projectName = $result[0]['project_name'];
	}
}
if('' == $park && ''!= projectNum ){
	$query= "SELECT project_name FROM projects WHERE project_id = ?";
	$paramTypes = 's';
	$params = array($projectNum);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$projectName = $result[0]['project_name'];
	
	if(empty($result))
		$projectNum = '';
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

$haveProject = '' == $projectNum ? false : true;

echo getHeaderTextGeneral();
echo javascriptHeaderText();
?>	
	<link href="dataTables/css/demo_table.css" rel="stylesheet" type="text/css" />
	<link href="dataTables/css/NPSdataTable.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="dataTables/js/jquery.dataTables.js"></script>
	<script type="text/javascript" src="dataTables/js/grouping.js"></script>
	<script type="text/javascript">
	
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
		function setSelectField(selectName, fieldName) {
			$("#" + selectName).val(fieldName)
		}
		function refScreen(){
			location.reload();
		}
		function changeYear() {
		    var txt;
		    var yearChange = prompt("Enter year:\nNOTE:\nThe total cost year should\nbe changed only after component\nATS asset costs have been updated\nin the 'Manage Assets' screen.", "");
			var pid = '<?php echo"$projectNum";?>';
			var pName = '<?php echo"$projectName";?>';
		    if (yearChange == null || yearChange == "") {		        
		    } else {
		    	$.ajax({
					type: 'POST',
					url: 'changeYear.php',
					data: {	year: yearChange,project:pid},
					dataType: 'json',
					success: function(result){
						$('#cYear').html("Year for project '" + pName + "' changed to " + yearChange + ".");
						$('#refBut').html("<input type='button' value='Refresh table' onclick='refScreen()'>");
					},
					error: function(result){
						$('#cYear').html("Year change failed.");
					}
				});
		    }
		}
		$(document).ready( function () {
<?php
	echo "			setSelectField('idRegion','$region');
			setSelectField('idParkId','$park');
			setSelectField('idProjectNum','$projectNum');
";
?>
				var p = new Array();
				p.push('<?php echo"$park";?>');
				var s = new Array();
				s.push('current');
				s.push('temporary');
				var pr = new Array();
				pr.push('<?php echo"$projectName";?>');
				
	<?php if($haveProject)echo"makeTables(p, s, pr);
";?>
			} 
		); // document ready function()

			function getParksData() {
				// get all of the checkboxes
				var parksChecked = $('.prkName:checked')
					parksList=new Array();

				// 'All' is checked just make that the first value
				if($('#parkNameAll').prop('checked')) {
					parksList.push('All');
				}else{ // otherwise, need to make list from those that are checked
					for(var i=0; i<parksChecked.length; i++) {
						parksList.push(parksChecked[i].value);
					}
				}
				return parksList
			}
			function getStatusData() {
				// reset hidden field
				$('#statusChecked').val('false');
				// get all of the checkboxes
				var statusChecked = $('.statusName:checked')
					statusList=new Array();

				// 'All' is checked just make that the first value
				if($('#statusNameAll').prop('checked')) {
					statusList.push('All');
				}else{ // otherwise, need to make list from those that are checked
					for(var i=0; i<statusChecked.length; i++) {
						statusList.push(statusChecked[i].value);
					}
				}
				return statusList
			}
			function getProjectData() {
				// reset hidden field
				$('#projectChecked').val('false');
				// get all of the checkboxes
				var projectChecked = $('.projectName:checked')
					projectList=new Array();

				// 'All' is checked just make that the first value
				if($('#projectNameAll').prop('checked')) {
					projectList.push('All');
				}else{ // otherwise, need to make list from those that are checked
					for(var i=0; i<projectChecked.length; i++) {
						projectList.push(projectChecked[i].value);
					}
				}
				return projectList
			}
			function makeTables(parksList,statusList,projectList) {
				if(parksList.length > 0 && statusList.length > 0 && projectList.length > 0){
					$.ajax({
							type: 'POST',
							url: 'getParkTotalCostData.php',
							data: {	parks: parksList, status: statusList, project:projectList},
							dataType: 'json',
							success: function(result){
								$('#costTable').html(result.data)
								$('#costTotRow').html(result.totals)
								$('#projectsTable').html(result.projects)
								$('#costTable').dataTable({
									"bSort":false,
									"bFilter":false,
									"bLengthChange": false,
									"bPaginate": false,
									"bDestroy":true,
									"bAutoWidth":true,
									"bInfo":false
								} ).rowGrouping({bExpandableGrouping: true});
								// fix names so they match those produced by dataTable
								for(var t in result.pNamesData){
									result.pNamesData[getCleanedGroup(t)]=result.pNamesData[t];
									delete result.pNamesData[t];
								}
								fixGroupHeaders(result.pNamesData);
								adjustTotalCostTable();
								makeRevenueTable(parksList,statusList,projectList);
							}
						}
					);
				}
				else {
					$('#costTable').html('No Data Present')
					$('#costTotRow').html('')
				}
			}
			function makeRevenueTable(parksList,statusList,projectList,th) {
				if(parksList.length > 0 && statusList.length > 0 && projectList.length > 0){
					$.ajax({
							type: 'POST',
							url: 'getParkTotalRevenueData.php',
							data: {	parks: parksList, status: statusList, project:projectList},
							dataType: 'json',
							success: function(result){
								$('#revenueTable').html(result.data)
								$('#revenueTotRow').html(result.totals)
								$('#revenueTable').dataTable({
									"bSort":false,
									"bFilter":false,
									"bLengthChange": false,
									"bPaginate": false,
									"bDestroy":true,
									"bAutoWidth":true,
									"bInfo":false
								} );
								adjustTotalRevenueTable();
								$("th").css('cursor','default')
								$('.display').css("width","100%")
							}
						}
					);
				}else{
					$('#revenueTable').html('No Data Present')
					$('#revenueTotRow').html('')
				}
			}
			// get the widths from the cost table, copy to totals row, return these widths
			function adjustTotalCostTable() {
				var thHolder = []
				var i=0;
				$('#costTable tr:first > th').each(function(){
					thHolder.push( $(this).width() );					
				});
				i=0;
				$('#costTotRow tr:first > td').each(function(){
					$(this).width(thHolder[i]);
					i++
				});
			};
			// get the widths from the revenue table, copy to totals row
			function adjustTotalRevenueTable() {
				var thHolder = []
				var i=0;
				$('#costTable tr:first > th').each(function(){
					thHolder.push( $(this).width() );					
				});
				i=0;
				$('#revenueTable tr:first > th').each(function(){
					$(this).width(thHolder[i]);
					i++
				});
				i=0;
				$('#revenueTotRow tr:first > td').each(function(){
					$(this).width(thHolder[i]);
					i++
				});
			};
			function fixGroupHeaders(projectNamesData) {
				$('#costTable tbody:first tr[id^="group-id"] >td').attr('colspan',1);
				var name;
				for(name in projectNamesData){
					$('#costTable tbody:first td[data-group="' + name + '"]').parent().append("<td style='text-align:right' id='cap' class='group'>" + projectNamesData[name].annual_cap_cost + "</td><td style='text-align:right' id='op' class='group'>" + projectNamesData[name].annual_op_cost + "</td><td style='text-align:right' id='tot' class='group'>" + projectNamesData[name].total_cost + "</td>");
				}
			}
			function setCheckedFields(fieldPointer) {
				switch(fieldPointer.classList[0]) {
					case 'prkName':
						$('#parksChecked').val('true');
						var prkList = $('.prkName')
						prkChecked = $('.prkName:checked')
						
						if(prkChecked.length == prkList.length) {
							$('#parkNameAll').prop('checked', true);
						}else if(prkChecked.length < prkList.length) {
							$('#parkNameAll').prop('checked', false);
						}
						break;
					case 'statusName':
						$('#statusChecked').val('true');
						var stList = $('.statusName')
						stChecked = $('.statusName:checked')
						
						if(stChecked.length == stList.length){
							$('#statusNameAll').prop('checked', true);
						}else if(stChecked.length < stList.length){
							$('#statusNameAll').prop('checked', false);
						}
						break;
					case 'projectName':
						$('#projectChecked').val('true');
						var ptList = $('.projectName')
						ptChecked = $('.projectName:checked')
						
						if(ptChecked.length == ptList.length){
							$('#projectNameAll').prop('checked', true);
						}else if(ptChecked.length < ptList.length){
							$('#projectNameAll').prop('checked', false);
						}
						break;
				}
			}
			function setAll(fieldPointer){
				switch(fieldPointer.id)	{
					case 'parkNameAll':
						$('#parksChecked').val('true');
						if(fieldPointer.checked){
							$('.prkName').prop('checked', true)
						}else{
							$('.prkName').prop('checked', false)
						}
						break;
					case 'statusNameAll':
						$('#statusChecked').val('true');
						if(fieldPointer.checked){
							$('.statusName').prop('checked', true)
						}else{
							$('.statusName').prop('checked', false)
						}
						break;
					case 'projectNameAll':
						$('#projectChecked').val('true');
						if(fieldPointer.checked){
							$('.projectName').prop('checked', true)
						}else{
							$('.projectName').prop('checked', false)
						}
						break;
				}
			};
			// this function from the grouping plug-in
            function getCleanedGroup(sGroup) {
                if (sGroup === "") return "-";
                return sGroup.toLowerCase().replace(/[^a-zA-Z0-9\u0080-\uFFFF]+/g, "-"); //fix for unicode characters (Issue 23)
                //return sGroup.toLowerCase().replace(/\W+/g, "-"); //Fix provided by bmathews (Issue 7)
            };
    	</script>
	</head>
	<body>
		<div id='container'>
		<p class='p2'>Total Annual Cost of ATS Ownership</p>
		<div id='manAssets2'>
			<table>
				<tr>
					<td><p><a href='index.htm'>Admin Menu</a></p></td>
				</tr>
			</table>
		</div><!-- manAssets2 -->
<?php

// Get list of project names form projects database and load into select field
echo "			<div id='manAssets3'>
				<form method='post' action='$_SERVER[SCRIPT_NAME]'>
				<input type='hidden' name='year' value=$year>
				<input type='hidden' name='fund' value=$fund>
				<input id='parksChecked' type='hidden' value='false'>
				<input id='projectChecked' type='hidden' value='false'>
				<input type='hidden' id='fieldSelected' name='' value='true'>
				<table class='manAssTop'>
					<tr>
						<td><p>Region (pick one)</p></td><td><p>Park Code (pick one)</p></td><td><p>Project (pick one)</p></td>
					</tr>
					<tr>
						<td><select id='idRegion' name='region' style='width: 15em;' onChange=submitForm(this)>
							<option value=''></option>
";
foreach($resRegions as $row){
	echo"							<option value='{$row['region_id']}'> {$row['region_name']} </option>
";
}
echo"						</select></td>
						<td><select id='idParkId' name='park' style='width: 6em;' onChange=submitForm(this)>
							<option value=''> </option>
";

foreach($resParks as $row) {
	echo"						<option value='{$row['park_id']}'> {$row['park_id']} </option>
";
}
echo"						</select></td>
";	
echo"					<td><select id='idProjectNum' name='projectNum' style='width: 20em;' onChange=submitForm(this)>
";
	echo "							<option value=''></option>
";
foreach($resProjects as $row) {
	echo"							<option value='{$row['project_id']}'> {$row['project_name']} </option>
";
}
echo"						</select></td>
					<tr>
				</table>
			</div>
";
// LS 29 Sep 2016
// add total cost reporting year
$query = "SELECT total_cost_year FROM projects WHERE project_id=?";
$paramTypes = 's';
$params = array($projectNum);
$resProj = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
$val = $resProj[0]['total_cost_year'] == null ? 'NULL' : $resProj[0]['total_cost_year'];
echo "		<div><p>Total Cost Report for $val</p></div>
";
?>
		<div class="clear"></div>
	</form>
	</div>
		<div id="super">
			<div id="table_container">
				<table cellpadding="0" cellspacing="0" border="0" class="display dataTable" id="costTable">
				</table> <!-- costTable -->
			</div> <!-- container -->
			<div id='totals'>
				<table cellpadding="0" cellspacing="0" border="0" id='costTotRow' class='display dataTable'>
				</table> <!-- costTotRow -->
			</div> <!-- totals -->
			<div id="table_container">
				<table cellpadding="0" cellspacing="0" border="0" class="display dataTable" id="revenueTable">
				</table> <!-- revenueTable -->
			</div> <!-- container -->
			<div id='totals'>
				<table cellpadding="0" cellspacing="0" border="0" id='revenueTotRow' class='display dataTable2'>
				</table> <!-- revenueTotRow -->
			</div> <!-- totals -->
			<div id='yc'>
				<input id='y' type="button" value="Change reporting year" onclick="changeYear()"><div id="refBut"></div>
				<p id='cYear'></p>
				<p id='cNote'>NOTE: This only changes the year that is displayed for this report.  The values remain the same.</p>
			</div>
			<div id="bottom"></div>
		</div> <!-- super -->
		</div> <!-- container -->
	</body>
</html>
