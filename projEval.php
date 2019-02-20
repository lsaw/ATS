<?php
include 'loggy.php';// Control who sees this page
include_once 'helper.php';
include_once 'mySqli.php';

if(!isset($_SESSION['uid']))
	session_start();
logUser('project eval');

$region 	= isset($_POST['region'])		? $_POST['region'] 		: 'NERO';
$park 		= isset($_POST['parkName'])		? $_POST['parkName'] 	: '';
$projectNum = isset($_POST['projectNum'])	? $_POST['projectNum'] 	: '';
$yearPicked	= isset($_POST['yearPicked'])	? $_POST['yearPicked'] 	: '';
$yearSet	= isset($_POST['yearSet'])		? $_POST['yearSet'] 	: '';

$region 	= isset($_GET['region'])		? $_GET['region'] 		: $region;
$park 		= isset($_GET['parkName'])		? $_GET['parkName'] 	: $park;
$projectNum = isset($_GET['projectNum'])	? $_GET['projectNum'] 	: $projectNum;
$yearPicked = isset($_GET['yearPicked'])	? $_GET['yearPicked'] 	: $yearPicked;
$yearSet 	= isset($_GET['yearSet'])		? $_GET['yearSet'] 		: $yearSet;
$miles = $ridersPerV = $annualRiders = '';

// need to deal with situation where you have a project selected for park A, but you
// then switch to park B, and that park does not have that project.  Sol'n is to
// query that park for that project. If not there force to 'All'.
if($park && $projectNum != '' && $projectNum != 'New') {
	$query= "SELECT project_name FROM projects WHERE project_id = ? AND park_id = ?";
	$paramTypes = 'ss';
	$params = array($projectNum, $park);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);

	if(empty($result))
		$projectNum = '';
}
if($park && $projectNum == '') {// project should default to default for that park
	$query= "SELECT project_id FROM parks_project_defaults WHERE park_id = ?";
	$paramTypes = 's';
	$params = array($park);
	$result = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	if(!empty($result))
		$projectNum = $result[0]['project_id'];
}

if(isset($_POST['postSelected']) && $yearSet !=''){
	$yearPicked = $yearSet;
	
	$A1 = isset($_POST['A1']) ? $_POST['A1'] : '';
	$A2 = isset($_POST['A2']) ? $_POST['A2'] : '';
	$A3 = isset($_POST['A3']) ? $_POST['A3'] : '';
	$A4 = isset($_POST['A4']) ? $_POST['A4'] : '';
	$A5 = isset($_POST['A5']) ? $_POST['A5'] : '';
	$A6 = isset($_POST['A6']) ? $_POST['A6'] : '';
	$A7 = isset($_POST['A7']) ? $_POST['A7'] : '';

	$B1 = isset($_POST['B1']) ? $_POST['B1'] : '';
	$B2 = isset($_POST['B2']) ? $_POST['B2'] : '';
	$B3 = isset($_POST['B3']) ? $_POST['B3'] : '';
	$B4 = isset($_POST['B4']) ? $_POST['B4'] : '';
	$B5 = isset($_POST['B5']) ? $_POST['B5'] : '';
	$B6 = isset($_POST['B6']) ? $_POST['B6'] : '';

	$C1 = isset($_POST['C1']) ? $_POST['C1'] : '';
	$C2 = isset($_POST['C2']) ? $_POST['C2'] : '';
	$C3 = isset($_POST['C3']) ? $_POST['C3'] : '';
	$C4 = isset($_POST['C4']) ? $_POST['C4'] : '';
	$C5 = isset($_POST['C5']) ? $_POST['C5'] : '';
	$C6 = isset($_POST['C6']) ? $_POST['C6'] : '';

	$D1 = isset($_POST['D1']) ? $_POST['D1'] : '';
	$D2 = isset($_POST['D2']) ? $_POST['D2'] : '';
	$D3 = isset($_POST['D3']) ? $_POST['D3'] : '';
	$D4 = isset($_POST['D4']) ? $_POST['D4'] : '';
	$D5 = isset($_POST['D5']) ? $_POST['D5'] : '';
	$D6 = isset($_POST['D6']) ? $_POST['D6'] : '';

	$E1 = isset($_POST['E1']) ? $_POST['E1'] : '';
	$E2 = isset($_POST['E2']) ? $_POST['E2'] : '';
	$E3 = isset($_POST['E3']) ? $_POST['E3'] : '';
	$E4 = isset($_POST['E4']) ? $_POST['E4'] : '';

	$F1 = isset($_POST['F1']) ? $_POST['F1'] : '';
	$F2 = isset($_POST['F2']) ? $_POST['F2'] : '';
	$F3 = isset($_POST['F3']) ? $_POST['F3'] : '';
	$F4 = isset($_POST['F4']) ? $_POST['F4'] : '';
	$F5 = isset($_POST['F5']) ? $_POST['F5'] : '';
	$F6 = isset($_POST['F6']) ? $_POST['F6'] : '';
	$F7 = isset($_POST['F7']) ? $_POST['F7'] : '';
	$F8 = isset($_POST['F8']) ? $_POST['F8'] : '';
	$F9 = isset($_POST['F9']) ? $_POST['F9'] : '';
	$F10 = isset($_POST['F10']) ? $_POST['F10'] : '';

	$G1 = isset($_POST['G1']) ? $_POST['G1'] : '';
	$G2 = isset($_POST['G2']) ? $_POST['G2'] : '';
	$G3 = isset($_POST['G3']) ? $_POST['G3'] : '';
	$G4 = isset($_POST['G4']) ? $_POST['G4'] : '';
	$G5 = isset($_POST['G5']) ? $_POST['G5'] : '';
	$G6 = isset($_POST['G6']) ? $_POST['G6'] : '';

	$H1 = isset($_POST['H1']) ? $_POST['H1'] : '';
	$H2 = isset($_POST['H2']) ? $_POST['H2'] : '';
	$H3 = isset($_POST['H3']) ? $_POST['H3'] : '';

	$I1 = isset($_POST['I1']) ? $_POST['I1'] : '';
	$I2 = isset($_POST['I2']) ? $_POST['I2'] : '';
	$I3 = isset($_POST['I3']) ? $_POST['I3'] : '';
	$I4 = isset($_POST['I4']) ? $_POST['I4'] : '';
	$I5 = isset($_POST['I5']) ? $_POST['I5'] : '';
	
	$critAccScoreVal = isset($_POST['critAccScoreVal']) ? $_POST['critAccScoreVal'] : '';
	$resProtecScoreVal = isset($_POST['resProtecScoreVal']) ? $_POST['resProtecScoreVal'] : '';
	$safetyScoreVal = isset($_POST['safetyScoreVal']) ? $_POST['safetyScoreVal'] : '';
	$visExpScoreVal = isset($_POST['visExpScoreVal']) ? $_POST['visExpScoreVal'] : '';
	$visDivScoreVal = isset($_POST['visDivScoreVal']) ? $_POST['visDivScoreVal'] : '';
	$regEcScoreVal = isset($_POST['regEcScoreVal']) ? $_POST['regEcScoreVal'] : '';
	$recEdScoreVal = isset($_POST['recEdScoreVal']) ? $_POST['recEdScoreVal'] : '';
	$rideProdScoreVal = isset($_POST['rideProdScoreVal']) ? $_POST['rideProdScoreVal'] : '';
	$costEffScoreVal = isset($_POST['costEffScoreVal']) ? $_POST['costEffScoreVal']	 : '';
	// make scores array
	$scores = array(	$critAccScoreVal,
				$resProtecScoreVal,
				$safetyScoreVal,
				$visExpScoreVal,
				$visDivScoreVal,
				$regEcScoreVal,
				$recEdScoreVal,
				$rideProdScoreVal,
				$costEffScoreVal);
	
	$query = "SELECT * FROM scores_detail_2 WHERE project_id=$projectNum AND eval_year=$yearSet";
	$res = queryMysqli($mysqli, $query);
	$row = $res->fetch_row();
	if($row){
		$query = "UPDATE scores_detail_2 SET project_id=?, eval_year=?,
		A1=?, A2=?, A3=?, A4=?, A5=?, A6=?, A7=?,
		B1=?, B2=?, B3=?, B4=?, B5=?, B6=?,
		C1=?, C2=?, C3=?, C4=?, C5=?, C6=?,
		D1=?, D2=?, D3=?, D4=?, D5=?, D6=?,
		E1=?, E2=?, E3=?, E4=?,
		F1=?, F2=?, F3=?, F4=?, F5=?, F6=?, F7=?, F8=?, F9=?, F10=?,
		G1=?, G2=?, G3=?, G4=?, G5=?, G6=?,
		H1=?, H2=?, H3=?,
		I1=?, I2=?, I3=?, I4=?, I5=?
		WHERE project_id=$projectNum AND eval_year=$yearSet";
	}
	else{

		$query = "	INSERT INTO scores_detail_2
					(project_id, eval_year,
					A1, A2, A3, A4, A5, A6, A7,
					B1, B2, B3, B4, B5, B6,
					C1, C2, C3, C4, C5, C6,
					D1, D2, D3, D4, D5, D6,
					E1, E2, E3, E4,
					F1, F2, F3, F4, F5, F6, F7, F8, F9, F10,
					G1, G2, G3, G4, G5, G6,
					H1, H2, H3,
					I1, I2, I3, I4, I5)
				VALUES (
					?,?,
					?,?,?,?,?,?,?,
					?,?,?,?,?,?,
					?,?,?,?,?,?,
					?,?,?,?,?,?,
					?,?,?,?,
					?,?,?,?,?,?,?,?,?,?,
					?,?,?,?,?,?,
					?,?,?,
					?,?,?,?,?  ) ";
	}
	$paramTypes = 'iiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii';
	$params = array($projectNum, $yearSet,
			$A1, $A2, $A3, $A4, $A5, $A6, $A7,
			$B1, $B2, $B3, $B4, $B5, $B6,
			$C1, $C2, $C3, $C4, $C5, $C6,
			$D1, $D2, $D3, $D4, $D5, $D6,
			$E1, $E2, $E3, $E4,
			$F1, $F2, $F3, $F4, $F5, $F6, $F7, $F8, $F9, $F10,
			$G1, $G2, $G3, $G4, $G5, $G6,
			$H1, $H2, $H3,
			$I1, $I2, $I3, $I4, $I5);
	queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
	$resProj = getProjects($mysqli, $park);


	$query = "SELECT * FROM scores WHERE project_id=$projectNum AND project_year=$yearSet";
	$res = queryMysqli($mysqli, $query);
	$row = $res->fetch_row();

	if($row){
		$i=0;
		foreach ($scores as $val){
			$i++;
			$query = "	UPDATE scores SET score=?
						WHERE project_id=$projectNum AND project_year=$yearSet AND category_id=$i";
			$paramTypes = 'i';
			$params = array($val);
			queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
	}
	else{
		$i=0;
		foreach ($scores as $val){
			$i++;
			$query = "	INSERT INTO scores
						(project_id, project_year,category_id,score)
						VALUES (?,?,?,?) ";
			$paramTypes = 'iiii';
			$params = array($projectNum, $yearSet,$i,$val);
			queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params);
		}
	}
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

if(isset($_POST['showYearSelected']) || isset($_POST['postSelected'])){
	$resParks = getParks($mysqli,$region);
	$resProj = getProjects($mysqli, $park);
}

// get the detail scores for this combination of project and year
if($yearPicked){
	$query = "SELECT * FROM scores_detail_2 WHERE project_id=? AND eval_year=?";
	$paramTypes = 'ii';
	$params = array($projectNum, $yearPicked);
	$resScores = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
}
// get the years that have been scored already
if($projectNum != ''){
	$query = "SELECT DISTINCT `project_year` FROM `scores` WHERE `project_id`=?";
	$paramTypes = 's';
	$params = array($projectNum);
	$scoreYears = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
}
echo getHeaderTextGeneral();
echo javascriptHeaderText();
//					row = $(th).attr('name')
//					val = $(th).prop('value')

echo <<<_END
		<script  type="text/javascript">
			function setSelectField(selectName, fieldName)
			{
				$("#" + selectName).val(fieldName)
			}

			function onLoadFunction()
			{
				setSelectField('idRegion','$region')
				setSelectField('idParkName','$park')
				setSelectField('idProjectNum','$projectNum')
				setSelectField('idYear','$yearPicked')

_END;

if($yearPicked && !empty($resScores)){
	for($i=0;$i<7;$i++){
		$e = 'A';
		$e .= $i+1;
		if($resScores[0][$e]> 1){
			break;
		}
	}
echo <<<_END
				$("input[name='$e'][value='{$resScores[0][$e]}']").prop('checked', true)
				var t = $("input[name='$e'][value='{$resScores[0][$e]}']")
				aClicked(t)
				$("input[name='B1'][value='{$resScores[0]['B1']}']").prop('checked', true)
				$("input[name='B2'][value='{$resScores[0]['B2']}']").prop('checked', true)
				$("input[name='B3'][value='{$resScores[0]['B3']}']").prop('checked', true)
				$("input[name='B4'][value='{$resScores[0]['B4']}']").prop('checked', true)
				$("input[name='B5'][value='{$resScores[0]['B5']}']").prop('checked', true)
				$("input[name='B6'][value='{$resScores[0]['B6']}']").prop('checked', true)
				t = $("input[name='B6'][value='{$resScores[0]['B6']}']")
				aClicked(t)
				$("input[name='C1'][value='{$resScores[0]['C1']}']").prop('checked', true)
				$("input[name='C2'][value='{$resScores[0]['C2']}']").prop('checked', true)
				$("input[name='C3'][value='{$resScores[0]['C3']}']").prop('checked', true)
				$("input[name='C4'][value='{$resScores[0]['C4']}']").prop('checked', true)
				$("input[name='C5'][value='{$resScores[0]['C5']}']").prop('checked', true)
				$("input[name='C6'][value='{$resScores[0]['C6']}']").prop('checked', true)
				t = $("input[name='C6'][value='{$resScores[0]['C6']}']")
				aClicked(t)
				$("input[name='D1'][value='{$resScores[0]['D1']}']").prop('checked', true)
				$("input[name='D2'][value='{$resScores[0]['D2']}']").prop('checked', true)
				$("input[name='D3'][value='{$resScores[0]['D3']}']").prop('checked', true)
				$("input[name='D4'][value='{$resScores[0]['D4']}']").prop('checked', true)
				$("input[name='D5'][value='{$resScores[0]['D5']}']").prop('checked', true)
				$("input[name='D6'][value='{$resScores[0]['D6']}']").prop('checked', true)
				t = $("input[name='D6'][value='{$resScores[0]['D6']}']")
				aClicked(t)
				$("input[name='E1'][value='{$resScores[0]['E1']}']").prop('checked', true)
				$("input[name='E2'][value='{$resScores[0]['E2']}']").prop('checked', true)
				$("input[name='E3'][value='{$resScores[0]['E3']}']").prop('checked', true)
				$("input[name='E4'][value='{$resScores[0]['E4']}']").prop('checked', true)
				t = $("input[name='E4'][value='{$resScores[0]['E4']}']")
				aClicked(t)
				$("input[name='F1'][value='{$resScores[0]['F1']}']").prop('checked', true)
				$("input[name='F2'][value='{$resScores[0]['F2']}']").prop('checked', true)
				$("input[name='F3'][value='{$resScores[0]['F3']}']").prop('checked', true)
				$("input[name='F4'][value='{$resScores[0]['F4']}']").prop('checked', true)
				$("input[name='F5'][value='{$resScores[0]['F5']}']").prop('checked', true)
				$("input[name='F6'][value='{$resScores[0]['F6']}']").prop('checked', true)
				$("input[name='F7'][value='{$resScores[0]['F7']}']").prop('checked', true)
				$("input[name='F8'][value='{$resScores[0]['F8']}']").prop('checked', true)
				$("input[name='F9'][value='{$resScores[0]['F9']}']").prop('checked', true)
				$("input[name='F10'][value='{$resScores[0]['F10']}']").prop('checked', true)
				t = $("input[name='F10'][value='{$resScores[0]['F10']}']")
				aClicked(t)
				$("input[name='G1'][value='{$resScores[0]['G1']}']").prop('checked', true)
				$("input[name='G2'][value='{$resScores[0]['G2']}']").prop('checked', true)
				$("input[name='G3'][value='{$resScores[0]['G3']}']").prop('checked', true)
				$("input[name='G4'][value='{$resScores[0]['G4']}']").prop('checked', true)
				$("input[name='G5'][value='{$resScores[0]['G5']}']").prop('checked', true)
				$("input[name='G6'][value='{$resScores[0]['G6']}']").prop('checked', true)
				t = $("input[name='G6'][value='{$resScores[0]['G6']}']")
				aClicked(t)
				$("input[name='H1'][value='{$resScores[0]['H1']}']").prop('checked', true)
				$("input[name='H2'][value='{$resScores[0]['H2']}']").prop('checked', true)
				$("input[name='H3'][value='{$resScores[0]['H3']}']").prop('checked', true)
				t = $("input[name='H3'][value='{$resScores[0]['H3']}']")
				aClicked(t)
				$("input[name='I1'][value='{$resScores[0]['I1']}']").prop('checked', true)
				$("input[name='I2'][value='{$resScores[0]['I2']}']").prop('checked', true)
				$("input[name='I3'][value='{$resScores[0]['I3']}']").prop('checked', true)
				$("input[name='I4'][value='{$resScores[0]['I4']}']").prop('checked', true)
				$("input[name='I5'][value='{$resScores[0]['I5']}']").prop('checked', true)
				t = $("input[name='I5'][value='{$resScores[0]['I5']}']")
				aClicked(t)
				
_END;
}
else{
	echo "						var tot = 0
						$('.areaTotal').each(function(i){
							tot+= parseInt($(this).text())
						});
						$('#totalScore').html(tot)
";
}
echo <<<_END
			}
			function operateScoreCard(th){
				var t = $(th).parent().next().css('display');
				if('none' == t) {
					$(th).parent().next().show();
				}
				else {
					$(th).parent().next().hide();
				}
			}
			function aClicked(th){
					var area = ''
					var row = ''
					var t = ''	
				if($(th).hasClass('aRadio')){
					area = 'a'
					$('.aRadio').prop('checked', false)
					$('.aRadioNA').prop('checked', true)
					$(th).prop('checked', true)
					$('.aRadio').filter('input:checked').each(function(i){
						t+= $(this).attr('name') + '-' + this.value + ','
					});
				}
				else if ($(th).hasClass('bRadio')){
					area = 'b'
					$('.bRadio').filter('input:checked').each(function(i){
						t+= $(this).attr('name') + '-' + this.value + ','
					});
				}
				else if ($(th).hasClass('cRadio')){
					area = 'c'
					$('.cRadio').filter('input:checked').each(function(i){
						t+= $(this).attr('name') + '-' + this.value + ','
					});
				}
				else if ($(th).hasClass('dRadio')){
					area = 'd'
					$('.dRadio').filter('input:checked').each(function(i){
						t+= $(this).attr('name') + '-' + this.value + ','
					});
				}
				else if ($(th).hasClass('eRadio')){
					area = 'e'
					$('.eRadio').filter('input:checked').each(function(i){
						t+= $(this).attr('name') + '-' + this.value + ','
					});
				}
				else if ($(th).hasClass('fRadio')){
					area = 'f'
					$('.fRadio').filter('input:checked').each(function(i){
						t+= $(this).attr('name') + '-' + this.value + ','
					});
				}
				else if ($(th).hasClass('gRadio')){
					area = 'g'
					$('.gRadio').filter('input:checked').each(function(i){
						t+= $(this).attr('name') + '-' + this.value + ','
					});
				}
				else if ($(th).hasClass('hRadio')){
					area = 'h'
					$('.hRadio').filter('input:checked').each(function(i){
						t+= $(this).attr('name') + '-' + this.value + ','
					});
				}
				else if ($(th).hasClass('iRadio')){
					area = 'i'
					$('.iRadio').filter('input:checked').each(function(i){
						t+= $(this).attr('name') + '-' + this.value + ','
					});
				}
				row = t.slice(0,t.lastIndexOf(','))
				getScore(area, row)
			}
			function getScore(area, row, val){
				$.ajax({
					type: "POST",
					url: "getScore.php",
					data: {	area: area, row: row, val: val},
					dataType: "json",
					success: function(result){
						switch(area) {
							case 'a':
								$('#critAccScore').html(result)
								$("input[name='critAccScoreVal']").prop('value', result)
								break;
							case 'b':
								$('#resProtecScore').html(result)
								$("input[name='resProtecScoreVal']").prop('value', result)
								break;
							case 'c':
								$('#safetyScore').html(result)
								$("input[name='safetyScoreVal']").prop('value', result)
								break;
							case 'd':
								$('#visExpScore').html(result)
								$("input[name='visExpScoreVal']").prop('value', result)
								break;
							case 'e':
								$('#visDivScore').html(result)
								$("input[name='visDivScoreVal']").prop('value', result)
								break;
							case 'f':
								$('#regEcScore').html(result)
								$("input[name='regEcScoreVal']").prop('value', result)
								break;
							case 'g':
								$('#recEdScore').html(result)
								$("input[name='recEdScoreVal']").prop('value', result)
								break;
							case 'h':
								$('#rideProdScore').html(result)
								$("input[name='rideProdScoreVal']").prop('value', result)
								break;
							case 'i':
								$('#costEffScore').html(result)
								$("input[name='costEffScoreVal']").prop('value', result)
								break;
						}
						var tot = 0
						$('.areaTotal').each(function(i){
							tot+= parseInt($(this).text())
						});
						$('#totalScore').html(tot)
					}
				});
			}
			function submitForm(th){
				var t = $(th).attr('name')
				
				switch(t){
					case 'region':
						var b = 'regionSelected'
						break;
					case 'parkName':
						var b = 'parkSelected'
						break;
					case 'projectNum':
						var b = 'projectSelected'
						break;
					case 'yearPicked':
						var b = 'showYearSelected'
						break;
					case 'postResults':
						var b = 'postSelected'
						break;
				}
						
				$('#fieldSelected').attr('name',b)
				$('#fieldSelected').val('true')
				document.forms['myform'].submit();
			}
		</script>
_END;
echo"		<title>ATS Management System</title>
	</head>
	<body onload=onLoadFunction()>
";

echo "		<div id='container'>
			<p>Evaluation Matrix</p>
			<div id='manAssets2'>
				<table>
					<tr>
						<td><p><a href='index.htm'>Admin Menu</a></p></td>
						<td><p><a href='logout.php' >Log out</a></p></td>
";

echo"					</tr>
				</table>
			</div><!-- manAssets2 -->
			<div id='manAssets3'>
				<form method='post' id='myform' action='$_SERVER[SCRIPT_NAME]'>
					<table class='manAssTop'>
						<tr>
							<td><p>Region (pick one)</p></td><td><p>Park Code (pick one)</p></td><td><p>Project Name (pick one)</p></td><td><p>Year (pick one)</p></td>
						</tr>
						<tr>
							<td><select id='idRegion' name='region' style='width: 20em;' onChange=submitForm(this)>
							<option value=''></option>
";
foreach($resRegions as $row) {
	echo"							<option value='{$row['region_id']}'> {$row['region_name']} </option>
";
}
echo"						</select></td>
							<td><select id='idParkName' name='parkName' style='width: 6em;' onChange=submitForm(this)>
								<option value=''> </option>
";
foreach ($resParks as $row){
	echo"								<option value='{$row['park_id']}'> {$row['park_id']} </option>
";
}
echo"							</select></td>
							<td><select id='idProjectNum' name='projectNum' style='width: 20em;' onChange=submitForm(this)>
								<option value='All'></option>
";
foreach($resProjects as $row)
{
	echo"								<option value='{$row['project_id']}'> {$row['project_name']} </option>
";
}
echo"							</select></td>
							<td><select id='idYear' name='yearPicked' style='width: 6em;' onChange=submitForm(this)>
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

echo"							</select></td><td><input type='hidden' id='fieldSelected' name='' value='true'></td>
						<tr>
					</table>
			</div><!-- manAssets3 -->
		<div class=scoring>
";

// if we have a park, project and year selected, display it's data
if($yearPicked)
{	
	$query = "SELECT * FROM categories";
	$paramTypes = '';
	$params = array();
	$resCategories = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$numCategories = count($resCategories);
	
	$query = "SELECT * FROM scores WHERE project_id = ? AND project_year = ? ORDER BY category_id";
	$paramTypes = 'ii';
	$params = array($projectNum, $yearPicked);
	$resScoreDat = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	if(!$resScoreDat){
		for ($i=0; $i<$numCategories; $i++){
			$resScoreData[$i]=0;
		}
		$query = "SELECT name,val1 FROM matrix ORDER BY name";
		$paramTypes = '';
		$params = array();
		$resMatrix = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
		foreach($resMatrix as $mat){
			$r = substr($mat['name'],0,1);
			switch ($r) {
				case 'A':
					$resScoreData[0]+= $mat['val1'];
				break;
				case 'B':
					$resScoreData[0]+= $mat['val1'];
				break;
				case 'C':
					$resScoreData[0]+= $mat['val1'];
				break;
				case 'D':
					$resScoreData[0]+= $mat['val1'];
				break;
				case 'E':
					$resScoreData[0]+= $mat['val1'];
				break;
				case 'F':
					$resScoreData[0]+= $mat['val1'];
				break;
				case 'G':
					$resScoreData[0]+= $mat['val1'];
				break;
				case 'H':
					$resScoreData[0]+= $mat['val1'];
				break;
				case 'I':
					$resScoreData[0]+= $mat['val1'];
				break;
				default:
				break;
			}
		}
	}
	else{
		foreach ($resScoreDat as $r){
			$resScoreData[]=$r['score'];
		}
	}

	$query = "	SELECT 	sum( (seasonalprojectdata.`riders`/`annualprojectdata`.`group_size`)	
							  * `annualprojectdata`.`miles_per_trip`) AS `miles`
				FROM `projects` INNER JOIN `seasonalprojectdata`
					ON `projects`.`project_id` = `seasonalprojectdata`.`project_id`
				INNER JOIN `annualprojectdata`
					ON `seasonalprojectdata`.`project_id` = `annualprojectdata`.`project_id` 
					AND `seasonalprojectdata`.`project_year` = `annualprojectdata`.`project_year` 
				WHERE `seasonalprojectdata`.`project_id`=? AND `seasonalprojectdata`.`project_year`=?";
	$paramTypes = 'ii';
	$params = array($projectNum, $yearPicked);
	$resMiles = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$miles = (int)$resMiles[0]['miles'];
	
	$query = "	SELECT  SUM(riders) AS riders
				FROM projects INNER JOIN `seasonalprojectdata`
					ON projects.project_id = seasonalprojectdata.project_id
				WHERE projects.project_id=? AND project_year=? ";
	$paramTypes = 'ii';
	$params = array($projectNum, $yearPicked);
	$resRiders = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$annualRiders = (int)$resRiders[0]['riders'];
	
	$query = "	SELECT  (SUM(riders)/`seasonalprojectdata`.`service_days`)/`seasonalprojectdata`.`average_daily_vehicles` AS rides_per_veh
				FROM projects INNER JOIN `seasonalprojectdata`
				ON projects.project_id = seasonalprojectdata.project_id
				WHERE peak_season = 'true' AND projects.project_id=? AND seasonalprojectdata.project_year=?";
	$paramTypes = 'ii';
	$params = array($projectNum, $yearPicked);
	$resRidesPerV = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	$ridersPerV = (int)$resRidesPerV[0]['rides_per_veh'];
	
	echo "			<table id='projScore' class='projectScore' style='float:left;'>
				<thead>
					<tr>
						<th>Score Area</th>
						<th>Score</th>
					</tr>
				</thead>
				<tbody>
";
	$i = -1;
	$scoreTotal = 0;
	foreach($resCategories as $rowCategories)
	{
		$i++;
		$scoreTotal+= $resScoreData[$i];
		echo "					<tr>
						<td>{$rowCategories['category']}</td>
						<td>{$resScoreData[$i]}</td>
";
	}
	echo "					<tr><td>Total</td>
						<td>{$scoreTotal}</td></tr>";
	echo "				<tbody>
			</table>
";
	if(1 == $level) echo "			<div id=yearBox>
				<table class='projectScore'>
					<thead></thead>
					<tbody>
						<tr>
							<td>Post results<td><td><select id='idYear' name='yearSet' style='width: 6em;'>
								<option value=''></option>
								<option value='test'>test</option>
								<option value='2010'> 2010 </option>
								<option value='2011'> 2011 </option>
								<option value='2012'> 2012 </option>
								<option value='2013'> 2013 </option>
								<option value='2014'> 2014 </option>
								<option value='2015'> 2015 </option>
								<option value='2016'> 2016 </option>
								<option value='2017'> 2017 </option>
							</select></td><td><input type='button' id='postRes' name='postResults' value='post' onclick=submitForm(this)></td>
						</tr>
					</tbody>
				</table>
			</div>
";
} // end $yearPicked
// if there are years with scores, highlite these
$yearCount = count($scoreYears);
if($yearCount){
	echo "			<div id=scoredYears>
				<table class='projectScore'>
					<thead>
						<th>These years have scores:</th>
					</thead>
					<tbody>
		";
	for($i=0; $i<$yearCount; $i++){
		echo "						<tr><td>{$scoreYears[$i]['project_year']}</td></tr>
		";
	}
	echo "					</tbody>
				</table>
			</div>
";
}
echo "		<div class='clear'></div>
";

echo "			<div class=scorer>
				<div class=scoreTitleDiv>
					<p class=scoreTitleP onclick='operateScoreCard(this)'>a. critical access&nbsp;&nbsp;&nbsp;&nbsp;<span class=areaTotal id=critAccScore>0</span></p>
					<input type='hidden' name='critAccScoreVal' value=''>
				</div><!-- scoreTitleDiv -->
				<div class=scoreCard id=criticalAccess>
					<div class=scoreCardChoice>
						<p class=scoreText>Answer only one question in this section.</p>
						<p class=scoreCardTitle>A1.&nbsp;&nbsp;&nbsp;Does the ATS provide the primary access to the entire park?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='aRadio' name='A1' value='2' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='aRadio aRadioNA' name='A1' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>A2.&nbsp;&nbsp;&nbsp;Does the ATS provide the primary access to a park site?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='aRadio' name='A2' value='2' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='aRadio aRadioNA' name='A2' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>A3.&nbsp;&nbsp;&nbsp;Does the ATS provide access to a site where available parking is consistently filled during the peak season?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='aRadio' name='A3' value='2' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='aRadio aRadioNA' name='A3' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>A4.&nbsp;&nbsp;&nbsp;Does the ATS provide a ride or tour that is itself an important component of the park experience?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='aRadio' name='A4' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='aRadio' name='A4' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='aRadio' name='A4' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='aRadio' name='A4' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='aRadio aRadioNA' name='A4' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>A5.&nbsp;&nbsp;&nbsp;Does the ATS provide access to a site where parking is sometimes unavailable?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='aRadio' name='A5' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='aRadio' name='A5' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='aRadio' name='A5' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='aRadio' name='A5' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='aRadio aRadioNA' name='A5' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>A6.&nbsp;&nbsp;&nbsp;Does the ATS service serve as a \"feeder\" route, delivering visitors to a transfer hub where they can board vehicles heading to destinations within the park?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='aRadio' name='A6' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='aRadio' name='A6' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='aRadio' name='A6' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='aRadio' name='A6' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='aRadio aRadioNA' name='A6' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>A7.&nbsp;&nbsp;&nbsp;Does the ATS provide access to a site that is difficult to reach by car or on foot?</p>
						<table class=scoreCardTable>	
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='aRadio' name='A7' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='aRadio' name='A7' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='aRadio' name='A7' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='aRadio' name='A7' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='aRadio aRadioNA' name='A7' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
				</div><!-- scoreCard -->

				<div class=scoreTitleDiv>
					<p class=scoreTitleP onclick='operateScoreCard(this)'>b. resource protection&nbsp;&nbsp;&nbsp;&nbsp;<span class=areaTotal id=resProtecScore>0</span></p>
					<input type='hidden' name='resProtecScoreVal' value=''>
				</div><!-- scoreTitleDiv -->
				<div class=scoreCard id=resourceProtection>
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>B1.&nbsp;&nbsp;&nbsp;Does the ATS reduce visitor impacts on wildlife and vegetation?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='bRadio' name='B1' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='bRadio' name='B1' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='bRadio' name='B1' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='bRadio' name='B1' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='bRadio bRadioNA' name='B1' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>B2.&nbsp;&nbsp;&nbsp;Does the ATS reduce visitor impacts on cultural and historic landscapes?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='bRadio' name='B2' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='bRadio' name='B2' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='bRadio' name='B2' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='bRadio' name='B2' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='bRadio bRadioNA' name='B2' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>B3.&nbsp;&nbsp;&nbsp;Does the ATS allow the NPS to limit the size of parking lots so autos do not overwhelm natural and cultural resources?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='bRadio' name='B3' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='bRadio' name='B3' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='bRadio' name='B3' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='bRadio' name='B3' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='bRadio bRadioNA' name='B3' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>B4.&nbsp;&nbsp;&nbsp;Does the ATS keep cars out of landscapes where they do not belong?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='bRadio' name='B4' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='bRadio' name='B4' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='bRadio' name='B4' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='bRadio' name='B4' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='bRadio bRadioNA' name='B4' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>B5.&nbsp;&nbsp;&nbsp;Does the ATS provide an example of NPS commitment to environmental stewardship?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='bRadio' name='B5' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='bRadio' name='B5' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='bRadio' name='B5' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='bRadio' name='B5' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='bRadio bRadioNA' name='B5' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>B6.&nbsp;&nbsp;&nbsp;To what extent does the ATS contribute to improved air quality by reducing the number of vehicle miles traveled?</p>
						<table class='scoreCardTable fLeft'>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='bRadio' name='B6' value='5' onclick='aClicked(this)'>>300k</td>
									<td><input type='radio' class='bRadio' name='B6' value='4' onclick='aClicked(this)'>201k-300k</td>
									<td><input type='radio' class='bRadio' name='B6' value='3' onclick='aClicked(this)'>101k-200k</td>
									<td><input type='radio' class='bRadio' name='B6' value='2' onclick='aClicked(this)'>16k-100k</td>
									<td><input type='radio' class='bRadio bRadioNA' name='B6' value='1' checked='true' onclick='aClicked(this)'><16k</td>
								</tr>
							</tbody>
						</table>
						<table class=scoreCardValueTable><thead></thead><tbody><tr><td><span>$miles</span></td></tr></tbody></table>
						<div class=clear></div>
						<p style='font-size:12pt;'>NOTE:  Vehicle miles = riders / average group size x average trip length</p>
					</div><!-- scoreCardChoice -->
				</div><!-- scoreCard -->

				<div class=scoreTitleDiv>
					<p class=scoreTitleP onclick='operateScoreCard(this)'>c. safety&nbsp;&nbsp;&nbsp;&nbsp;<span class=areaTotal id=safetyScore>0</span></p>
					<input type='hidden' name='safetyScoreVal' value=''>
				</div><!-- scoreTitleDiv -->
				<div class=scoreCard id=safety>
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>C1.&nbsp;&nbsp;&nbsp;Does the ATS serve highly congested roadways and parking areas?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='cRadio' name='C1' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='cRadio' name='C1' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='cRadio' name='C1' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='cRadio' name='C1' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='cRadio cRadioNA' name='C1' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>C2.&nbsp;&nbsp;&nbsp;Does the ATS reduce safety hazards caused by confusing or dangerous roadways?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='cRadio' name='C2' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='cRadio' name='C2' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='cRadio' name='C2' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='cRadio' name='C2' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='cRadio cRadioNA' name='C2' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>C3.&nbsp;&nbsp;&nbsp;Does the ATS reduce safety hazards caused by overflow parking?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='cRadio' name='C3' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='cRadio' name='C3' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='cRadio' name='C3' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='cRadio' name='C3' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='cRadio cRadioNA' name='C3' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>C4.&nbsp;&nbsp;&nbsp;Does the ATS reduce auto traffic on high accident roadways?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='cRadio' name='C4' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='cRadio' name='C4' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='cRadio' name='C4' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='cRadio' name='C4' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='cRadio cRadioNA' name='C4' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>C5.&nbsp;&nbsp;&nbsp;To what extent does the ATS reduce total vehicle miles traveled?</p>
						<table class='scoreCardTable fLeft'>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='cRadio' name='C5' value='5' onclick='aClicked(this)'>>300k</td>
									<td><input type='radio' class='cRadio' name='C5' value='4' onclick='aClicked(this)'>201k-300k</td>
									<td><input type='radio' class='cRadio' name='C5' value='3' onclick='aClicked(this)'>101k-200k</td>
									<td><input type='radio' class='cRadio' name='C5' value='2' onclick='aClicked(this)'>16k-100k</td>
									<td><input type='radio' class='cRadio cRadioNA' name='C5' value='1' checked='true' onclick='aClicked(this)'><16k</td>
								</tr>
							</tbody>
						</table>
						<table class=scoreCardValueTable><thead></thead><tbody><tr><td><span>$miles</span></td></tr></tbody></table>
						<div class=clear></div>
						<p style='font-size:12pt;'>NOTE:  Vehicle miles = riders / average group sixe x average trip length</p>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>C6.&nbsp;&nbsp;&nbsp;Does the ATS address off-road (ferry or rail) safety concerns that are directly relevant foe NPS visitors?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='cRadio' name='C6' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='cRadio' name='C6' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='cRadio' name='C6' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='cRadio' name='C6' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='cRadio cRadioNA' name='C6' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
				</div><!-- scoreCard -->

				<div class=scoreTitleDiv>
					<p class=scoreTitleP onclick='operateScoreCard(this)'>d. visitor experience&nbsp;&nbsp;&nbsp;&nbsp;<span class=areaTotal id=visExpScore>0</span></p>
					<input type='hidden' name='visExpScoreVal' value=''>
				</div><!-- scoreTitleDiv -->
				<div class=scoreCard id=visitorExperience>
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>D1.&nbsp;&nbsp;&nbsp;Does the ATS allow visitors to relax and enjoy park sites and scenery?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='dRadio' name='D1' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='dRadio' name='D1' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='dRadio' name='D1' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='dRadio' name='D1' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='dRadio dRadioNA' name='D1' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>D2.&nbsp;&nbsp;&nbsp;Does the ATS provide visitors with an improves understanding of park features, destinations and activities?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='dRadio' name='D2' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='dRadio' name='D2' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='dRadio' name='D2' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='dRadio' name='D2' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='dRadio dRadioNA' name='D2' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>D3.&nbsp;&nbsp;&nbsp;Does the ATS provide visitors with a greater choice of NPS destinations and activities; choices that would be otherwise limited by uncertain parking availability?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='dRadio' name='D3' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='dRadio' name='D3' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='dRadio' name='D3' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='dRadio' name='D3' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='dRadio dRadioNA' name='D3' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>D4.&nbsp;&nbsp;&nbsp;Does the ATS reduce visitor confusion by assisting with way finding and orientation?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='dRadio' name='D4' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='dRadio' name='D4' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='dRadio' name='D4' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='dRadio' name='D4' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='dRadio dRadioNA' name='D4' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>D5.&nbsp;&nbsp;&nbsp;Does the ATS provide improved choices for groups with divergent interests?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='dRadio' name='D5' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='dRadio' name='D5' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='dRadio' name='D5' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='dRadio' name='D5' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='dRadio dRadioNA' name='D5' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>D6.&nbsp;&nbsp;&nbsp;Does the ATS provide NPS campers and other visitors with improved access to restaurants, shops and commercial attractions?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='dRadio' name='D6' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='dRadio' name='D6' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='dRadio' name='D6' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='dRadio' name='D6' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='dRadio dRadioNA' name='D6' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
				</div><!-- scoreCard -->

				<div class=scoreTitleDiv>
					<p class=scoreTitleP onclick='operateScoreCard(this)'>e. visitor diversity and car-free travel&nbsp;&nbsp;&nbsp;&nbsp;<span class=areaTotal id=visDivScore>0</span></p>
					<input type='hidden' name='visDivScoreVal' value=''>
				</div><!-- scoreTitleDiv -->
				<div class=scoreCard id=visitorDiversity>
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>E1.&nbsp;&nbsp;&nbsp;Does the ATS provide improved access to NPS resources by minorities and low-income groups?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='eRadio' name='E1' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='eRadio' name='E1' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='eRadio' name='E1' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='eRadio' name='E1' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='eRadio eRadioNA' name='E1' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>E2.&nbsp;&nbsp;&nbsp;Does the ATS provide improved access for people with disabilities and other special needs?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='eRadio' name='E2' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='eRadio' name='E2' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='eRadio' name='E2' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='eRadio' name='E2' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='eRadio eRadioNA' name='E2' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>E3.&nbsp;&nbsp;&nbsp;Does the ATS provide convenient connections with local or regional transit?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='eRadio' name='E3' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='eRadio' name='E3' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='eRadio' name='E3' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='eRadio' name='E3' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='eRadio eRadioNA' name='E3' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>E4.&nbsp;&nbsp;&nbsp;Does the ATS provide opportunities for car-free travelers?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='eRadio' name='E4' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='eRadio' name='E4' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='eRadio' name='E4' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='eRadio' name='E4' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='eRadio eRadioNA' name='E4' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
				</div><!-- scoreCard -->

				<div class=scoreTitleDiv>
					<p class=scoreTitleP onclick='operateScoreCard(this)'>f. regional economy and partnerships&nbsp;&nbsp;&nbsp;&nbsp;<span class=areaTotal id=regEcScore>0</span></p>
					<input type='hidden' name='regEcScoreVal' value=''>
				</div><!-- scoreTitleDiv -->
				<div class=scoreCard id=regionalEconomy>
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>F1.&nbsp;&nbsp;&nbsp;Is the ATS part of a regional or state-wide tourism initiative?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F1' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F1' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F1' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F1' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F1' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>F2.&nbsp;&nbsp;&nbsp;Is the ATS part of a strategy to reduce congestion in neighboring communities?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F2' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F2' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F2' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F2' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F2' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>F3.&nbsp;&nbsp;&nbsp;Is the ATS part of a strategy to address parking problems in neighboring communities?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F3' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F3' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F3' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F3' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F3' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>F4.&nbsp;&nbsp;&nbsp;Does the ATS involve a partnership with state government?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F4' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F4' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F4' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F4' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F4' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>F5.&nbsp;&nbsp;&nbsp;Does the ATS involve a partnership with neighboring municipalities?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F5' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F5' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F5' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F5' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F5' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>F6.&nbsp;&nbsp;&nbsp;Does the ATS involve a partnership with regional businesses?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F6' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F6' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F6' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F6' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F6' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>F7.&nbsp;&nbsp;&nbsp;Does the ATS support and promote NPS concessionaires?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F7' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F7' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F7' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F7' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F7' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
							<div class=scoreCardChoice>
						<p class=scoreCardTitle>F8.&nbsp;&nbsp;&nbsp;Does the ATS promote and support private businesses based outside the park?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F8' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F8' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F8' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F8' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F8' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
							<div class=scoreCardChoice>
						<p class=scoreCardTitle>F9.&nbsp;&nbsp;&nbsp;Does the ATS provide employment transportation for businesses located within the park?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F9' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F9' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F9' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F9' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F9' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
							<div class=scoreCardChoice>
						<p class=scoreCardTitle>F10.&nbsp;&nbsp;&nbsp;Does the ATS provide employment transportation for businesses located outside the park?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='fRadio' name='F10' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='fRadio' name='F10' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='fRadio' name='F10' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='fRadio' name='F10' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='fRadio fRadioNA' name='F10' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
				</div><!-- scoreCard -->

				<div class=scoreTitleDiv>
					<p class=scoreTitleP onclick='operateScoreCard(this)'>g. recreation and education&nbsp;&nbsp;&nbsp;&nbsp;<span class=areaTotal id=recEdScore>0</span></p>
					<input type='hidden' name='recEdScoreVal' value=''>
				</div><!-- scoreTitleDiv -->
				<div class=scoreCard id=recreationEducation>
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>G1.&nbsp;&nbsp;&nbsp;Does the ATS provide improved and expanded opportunities for hikers?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='gRadio' name='G1' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='gRadio' name='G1' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='gRadio' name='G1' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='gRadio' name='G1' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='gRadio gRadioNA' name='G1' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>G2.&nbsp;&nbsp;&nbsp;Does the ATS provide improved and expanded opportunities for cyclists?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='gRadio' name='G2' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='gRadio' name='G2' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='gRadio' name='G2' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='gRadio' name='G2' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='gRadio gRadioNA' name='G2' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>G3.&nbsp;&nbsp;&nbsp;Does the ATS provide improved and expanded opportunities for boaters?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='gRadio' name='G3' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='gRadio' name='G3' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='gRadio' name='G3' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='gRadio' name='G3' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='gRadio gRadioNA' name='G3' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>G4.&nbsp;&nbsp;&nbsp;Does the ATS provide improved and expanded opportunities for swimmers?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='gRadio' name='G4' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='gRadio' name='G4' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='gRadio' name='G4' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='gRadio' name='G4' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='gRadio gRadioNA' name='G4' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>G5.&nbsp;&nbsp;&nbsp;Does the ATS provide access to museums and other educational programs?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='gRadio' name='G5' value='5' onclick='aClicked(this)'>Critical</td>
									<td><input type='radio' class='gRadio' name='G5' value='4' onclick='aClicked(this)'>Very Important</td>
									<td><input type='radio' class='gRadio' name='G5' value='3' onclick='aClicked(this)'>Important</td>
									<td><input type='radio' class='gRadio' name='G5' value='2' onclick='aClicked(this)'>Potential</td>
									<td><input type='radio' class='gRadio gRadioNA' name='G5' value='1' checked='true' onclick='aClicked(this)'>N/A</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreText>FOR SPECIAL PURPOSE SHUTTLES ONLY:&nbsp;&nbsp;&nbsp;<span style='font-size:12pt;'>Occupancy or riders per round trip</span></p>
						<p class=scoreCardTitle>G6.&nbsp;&nbsp;&nbsp;Is the ATS a well-utilized special purpose recreational or educational shuttle?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='gRadio' name='G6' value='5' onclick='aClicked(this)'>100% or >30</td>
									<td><input type='radio' class='gRadio' name='G6' value='4' onclick='aClicked(this)'>75-99% or 25-30</td>
									<td><input type='radio' class='gRadio' name='G6' value='3' onclick='aClicked(this)'>50-74% or 12-24</td>
									<td><input type='radio' class='gRadio' name='G6' value='2' onclick='aClicked(this)'>25-49% or 6-12</td>
									<td><input type='radio' class='gRadio gRadioNA' name='G6' value='1' checked='true' onclick='aClicked(this)'><25% or <5</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
				</div><!-- scoreCard -->

				<div class=scoreTitleDiv>
					<p class=scoreTitleP onclick='operateScoreCard(this)'>h. ridership and productivity&nbsp;&nbsp;&nbsp;&nbsp;<span class=areaTotal id=rideProdScore>0</span></p>
					<input type='hidden' name='rideProdScoreVal' value=''>
				</div><!-- scoreTitleDiv -->
				<div class=scoreCard id=ridershipProductivity>
					<div class=scoreCardChoice>
						<p class=scorePOther>Daily riders per route or service</p>
						<p class=scoreCardTitle>H1.&nbsp;&nbsp;&nbsp;Does the ATS serve a large number of NPS visitors?</p>
						<table class='scoreCardTable fLeft'>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='hRadio' name='H1' value='5' onclick='aClicked(this)'>>750</td>
									<td><input type='radio' class='hRadio' name='H1' value='4' onclick='aClicked(this)'>500-749</td>
									<td><input type='radio' class='hRadio' name='H1' value='3' onclick='aClicked(this)'>150-499</td>
									<td><input type='radio' class='hRadio' name='H1' value='2' onclick='aClicked(this)'>25-149</td>
									<td><input type='radio' class='hRadio hRadioNA' name='H1' value='1' checked='true' onclick='aClicked(this)'><25</td>
								</tr>
							</tbody>
						</table>
						<table class=scoreCardValueTable><thead></thead><tbody><tr><td><span>$annualRiders</span></td></tr></tbody></table>
						<div class=clear></div>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scorePOther>Daily riders per bus or ferry</p>
						<p class=scoreCardTitle>H2.&nbsp;&nbsp;&nbsp;Does each vehicle carry a meaningful number of riders each day?</p>
						<table class='scoreCardTable fLeft'>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='hRadio' name='H2' value='5' onclick='aClicked(this)'>>200</td>
									<td><input type='radio' class='hRadio' name='H2' value='4' onclick='aClicked(this)'>150-199</td>
									<td><input type='radio' class='hRadio' name='H2' value='3' onclick='aClicked(this)'>100-149</td>
									<td><input type='radio' class='hRadio' name='H2' value='2' onclick='aClicked(this)'>50-99</td>
									<td><input type='radio' class='hRadio hRadioNA' name='H2' value='1' checked='true' onclick='aClicked(this)'>1-49</td>
								</tr>
							</tbody>
						</table>
						<table class=scoreCardValueTable><thead></thead><tbody><tr><td><span>$ridersPerV</span></td></tr></tbody></table>
						<div class=clear></div>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scorePOther>Percent filled (riders per scheduled round trip / seats per vehicle)</p>
						<p class=scoreCardTitle>H3.&nbsp;&nbsp;&nbsp;How does the average number of riders per round trip compare with the number of available seats per vehicle?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='hRadio' name='H3' value='5' onclick='aClicked(this)'>>90%</td>
									<td><input type='radio' class='hRadio' name='H3' value='4' onclick='aClicked(this)'>75-89%</td>
									<td><input type='radio' class='hRadio' name='H3' value='3' onclick='aClicked(this)'>50-74%</td>
									<td><input type='radio' class='hRadio' name='H3' value='2' onclick='aClicked(this)'>25-49%</td>
									<td><input type='radio' class='hRadio hRadioNA' name='H3' value='1' checked='true' onclick='aClicked(this)'><25%</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
				</div><!-- scoreCard -->

				<div class=scoreTitleDiv>
					<p class=scoreTitleP onclick='operateScoreCard(this)'>i. cost effectiveness&nbsp;&nbsp;&nbsp;&nbsp;<span class=areaTotal id=costEffScore>0</span></p>
					<input type='hidden' name='costEffScoreVal' value=''>
				</div><!-- scoreTitleDiv -->
				<div class=scoreCard id=costEffectiveness>
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>I1.&nbsp;&nbsp;&nbsp;What is the cost per rider?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='iRadio' name='I1' value='5' onclick='aClicked(this)'>$2</td>
									<td><input type='radio' class='iRadio' name='I1' value='4' onclick='aClicked(this)'>$2-$3</td>
									<td><input type='radio' class='iRadio' name='I1' value='3' onclick='aClicked(this)'>$3-$4</td>
									<td><input type='radio' class='iRadio' name='I1' value='2' onclick='aClicked(this)'>$4-$5</td>
									<td><input type='radio' class='iRadio iRadioNA' name='I1' value='1' checked='true' onclick='aClicked(this)'>>$5</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>I2.&nbsp;&nbsp;&nbsp;What is the NPS cost per rider?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='iRadio' name='I2' value='5' onclick='aClicked(this)'>$2</td>
									<td><input type='radio' class='iRadio' name='I2' value='4' onclick='aClicked(this)'>$2-$3</td>
									<td><input type='radio' class='iRadio' name='I2' value='3' onclick='aClicked(this)'>$3-$4</td>
									<td><input type='radio' class='iRadio' name='I2' value='2' onclick='aClicked(this)'>$4-$5</td>
									<td><input type='radio' class='iRadio iRadioNA' name='I2' value='1' checked='true' onclick='aClicked(this)'>>$5</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>I3.&nbsp;&nbsp;&nbsp;What percentage of ATS operating costs are covered by non-NPS funding sources?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='iRadio' name='I3' value='5' onclick='aClicked(this)'>100%</td>
									<td><input type='radio' class='iRadio' name='I3' value='4' onclick='aClicked(this)'>66-99%</td>
									<td><input type='radio' class='iRadio' name='I3' value='3' onclick='aClicked(this)'>33-65%</td>
									<td><input type='radio' class='iRadio' name='I3' value='2' onclick='aClicked(this)'>5-32%</td>
									<td><input type='radio' class='iRadio iRadioNA' name='I3' value='1' checked='true' onclick='aClicked(this)'><5%</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>I4.&nbsp;&nbsp;&nbsp;What percentage of ATS operating costs are covered by transit or entrance fees or by the park's regular operating budget?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='iRadio' name='I4' value='5' onclick='aClicked(this)'>100%</td>
									<td><input type='radio' class='iRadio' name='I4' value='4' onclick='aClicked(this)'>66-99%</td>
									<td><input type='radio' class='iRadio' name='I4' value='3' onclick='aClicked(this)'>33-65%</td>
									<td><input type='radio' class='iRadio' name='I4' value='2' onclick='aClicked(this)'>5-32%</td>
									<td><input type='radio' class='iRadio iRadioNA' name='I4' value='1' checked='true' onclick='aClicked(this)'><5%</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
					<div class=scoreCardChoice>
						<p class=scoreCardTitle>I5.&nbsp;&nbsp;&nbsp;In five years, what percentage of NPS operating costs will be covered by transit or entrance fees or by the park's regular operating budget?</p>
						<table class=scoreCardTable>
							<thead></thead>
							<tbody>
								<tr>
									<td><input type='radio' class='iRadio' name='I5' value='5' onclick='aClicked(this)'>100%</td>
									<td><input type='radio' class='iRadio' name='I5' value='4' onclick='aClicked(this)'>66-99%</td>
									<td><input type='radio' class='iRadio' name='I5' value='3' onclick='aClicked(this)'>33-65%</td>
									<td><input type='radio' class='iRadio' name='I5' value='2' onclick='aClicked(this)'>5-32%</td>
									<td><input type='radio' class='iRadio iRadioNA' name='I5' value='1' checked='true' onclick='aClicked(this)'><5%</td>
								</tr>
							</tbody>
						</table>
					</div><!-- scoreCardChoice -->
				</div><!-- scoreCard -->

				<div class=scoreTitleDiv>
					<p class=scoreTitleP>Total&nbsp;&nbsp;&nbsp;&nbsp;<span id=totalScore></span></p>
				</div><!-- scoreTitleDiv -->
				
			</div><!-- scorer -->
		</form>
		</div><!-- scoring -->
		</div><!-- container -->
"; 
echo assetTextBottom();
?>