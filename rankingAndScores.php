<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';
include 'annPerfMaker.php';

logUser('rankingAndScores');

if(!isset($_SESSION['uid']))
session_start();

$yearPicked = isset($_POST['yearPicked'])	? $_POST['yearPicked'] : '';
$categoryId = isset($_POST['categoryId'])	? $_POST['categoryId'] : '1';

$yearPicked = isset($_GET['yearPicked'])	? $_GET['yearPicked'] : $yearPicked;
$categoryId = isset($_GET['categoryId'])	? $_GET['categoryId'] : $categoryId;

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

_END;
echo <<<_END
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
$b = $_SESSION["uid"];
echo"					</tr>
				</table>
			</div>";

$query = "SELECT * FROM categories";
$resCategories = queryMysqli($mysqli, $query);
if(!$resCategories) die ("Database not available at this time:" . mysql_error());

echo"			<div id='manAssets3'>
				<form method='post' name='myform' action='$_SERVER[SCRIPT_NAME]'>
				<input type='hidden' name='action' value=''>
				<table class='manAssTop'>
					<tr>
						<td><p>Year (pick one)</p></td>
						<td><p>Category (pick one)</p></td>
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

echo generateSelectTd2('categoryId', 'categoryId', $resCategories);
echo "		<script type='text/javascript'>
			$('#categoryId').val($categoryId)
		</script>";
echo"
					<tr>
				</table>
			</div>
";

if($yearPicked){
	$query = "	SELECT	projects.park_id AS park,
						projects.project_name AS project,
						categories.category AS category,
						scores.score AS score,
						annualprojectdata.project_status AS status
				FROM 	projects
					INNER JOIN scores 			 ON projects.project_id = scores.project_id
					INNER JOIN categories 		 ON scores.category_id 	= categories.category_id
					INNER JOIN annualprojectdata ON projects.project_id = annualprojectdata.project_id
				WHERE scores.category_id = ?
					AND scores.project_year = ?
					AND annualprojectdata.project_year = ?
				ORDER BY 	score DESC,
							park ASC,
							project ASC
				LIMIT 0 , 30";
	$paramTypes = 'iii';
	$params = array($categoryId,$yearPicked,$yearPicked);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);

	$sClass = 'even';
	echo "			<div class='fmssAndFunding'>
				<table class='reportCard'>
					<tr>
						<th>Park</th>
						<th>Project</th>
						<th>Score</th>
						<th style='width:60px;'>Project Status</th>
";
	echo "</tr>
";
	foreach ($result as $r){
		$sClass = $sClass=='even' ? 'odd' : 'even';
		echo"					<tr class='$sClass'>
						<td>{$r['park']}</td>
						<td>{$r['project']}</td>
						<td>{$r['score']}</td>
						<td>{$r['status']}</td>
					</tr>
";
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