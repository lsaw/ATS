<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';
include 'annPerfMaker.php';

logUser('summaryReport');

if(!isset($_SESSION['uid']))
session_start();

$yearPicked = isset($_POST['yearPicked'])	? $_POST['yearPicked'] : '';
$yearPicked = isset($_POST['yearPicked'])	? $_POST['yearPicked'] : $yearPicked;

echo getHeaderTextGeneral();
echo javascriptHeaderText();
echo commaSeperatorFunctionsText();
echo <<<_END
		<script>
			function setSelectField(selectName, fieldName) {
				$("#" + selectName).val(fieldName)
			}
			function editFunction(fieldPointer){
				fieldPointer.style.color="red"
				$("#postRes").css("color","red")
				$("#idReset").css("visibility","visible")
			}
			function onLoadFunction() {
				setSelectField('idYear','$yearPicked')
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
			<p>Summary Report</p>
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
				<table class='manAssTop'>
					<tr>
						<td><p>Year (pick one)</p></td>
					</tr>
					<tr>
							<td><select id='idYear' name='yearPicked' style='width: 6em;' onChange=this.form.submit()>
								<option value=''> </option>
";
echo"								<option value='2010'> 2010 </option>
								<option value='2011'> 2011 </option>
								<option value='2012'> 2012 </option>
								<option value='2013'> 2013 </option>
								<option value='2014'> 2014 </option>
								<option value='2015'> 2015 </option>
								<option value='2016'> 2016 </option>
								<option value='2017'> 2017 </option>
";
echo"							</select></td>";

echo"									
					<tr>
				</table>
			</div>
";

// if we have a park selected, display it's data
if($yearPicked){
	$query = "	SELECT 
					-- projects.project_id,
					projects.park_id,
					projects.project_name,
					vwannual_scores.`score`,
					vwannual_costs.`op cost`,
					vwannual_costs.`nps cost`,
					vwannual_rides.`annual_rides`,
					`op cost`/annual_rides AS 'cost per ride',
					`nps cost`/annual_rides AS 'NPS cost per ride',
					annualprojectdata.`project_status`
				FROM projects	INNER JOIN vwannual_costs
									ON projects.project_id = vwannual_costs.project_id
								INNER JOIN vwannual_rides
									ON projects.project_id = vwannual_rides.project_id
								INNER JOIN vwannual_scores
									ON projects.project_id = vwannual_scores.project_id
								INNER JOIN annualprojectdata
									ON projects.project_id = annualprojectdata.project_id
				WHERE vwannual_costs.year = ?
					AND vwannual_rides.`project_year` = ?
					AND vwannual_scores.`year` = ?
					AND annualprojectdata.`project_year` = ?
				GROUP BY projects.project_id
				ORDER BY score DESC ;";
	$paramTypes = 'iiii';
	$params = array($yearPicked,$yearPicked,$yearPicked,$yearPicked);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	
	if(0 == count($result)){
		echo "			<div class='fmssAndFunding'>
				<table class='reportCard'>
					<tr>
						<th>There is no data for $yearPicked</th>
				</tr>
		";
		
	}else{

	$sClass = 'even';
	echo "			<div class='fmssAndFunding'>
				<table class='reportCard'>
					<tr>
						<th></th>
						<th></th>
						<th></th>
						<th>Annual</th>
						<th>Op</th>
						<th>NPS</th>
						<th>Cost</th>
						<th>NPS Cost</th>
						<th>Project</th>
					</tr>
					<tr>
						<th>Park</th>
						<th>Project</th>
						<th>Score</th>
						<th>Rides</th>
						<th>Cost</th>
						<th>Cost</th>
						<th>per Ride</th>
						<th>per Ride</th>
						<th>Status</th>
					</tr>
";
	foreach ($result as $r){
		$annRide = addCommas($r['annual_rides']);
		$opCost = addCommas(round($r['op cost'],0));
		$NPSCost = addCommas(round($r['nps cost'],0));
		$costRide = round($r['cost per ride'],2);
		$NPScostRide = addCommas(round($r['NPS cost per ride'],2));
		echo"					<tr>
						<td>{$r['park_id']}</td>
						<td>{$r['project_name']}</td>
						<td>{$r['score']}</td>
						<td style='text-align:right;'>$annRide</td>
						<td style='text-align:right;'>$opCost</td>
						<td style='text-align:right;'>$NPSCost</td>
						<td style='text-align:right;'>$$costRide</td>
						<td style='text-align:right;'>$$NPScostRide</td>
						<td>{$r['project_status']}</td>
					</tr>
";
	}
	}
	echo "</table>";
	
}
	echo "
				</form>
";
echo"		</div>
";
echo assetTextBottom();
?>