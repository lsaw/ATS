<?php
// for those that have default
$park = 		isset($_GET['park']) 		? ('' == $_GET['park'] 		? $park 	: $_GET['park'])	: $park;
$projNum = 		isset($_GET['projNum'])		? ('' == $_GET['projNum'] 	? $projNum 	: $_GET['projNum'])	: $projNum;
$year = 		isset($_GET['year']) 		? ('' == $_GET['year'] 	  	? $year 	: $_GET['year'])	: $year;
$fund = 		isset($_GET['fund']) 		? ('' == $_GET['fund'] 	  	? $fund 	: $_GET['fund'])	: $fund;
$lowYear = 		isset($_GET['lowYear']) 	? ('' == $_GET['lowYear'] 	? $lowYear 	: $_GET['lowYear']) : $lowYear;
$highYear = 	isset($_GET['highYear'])	? ('' == $_GET['highYear'] 	? $highYear : $_GET['highYear']) : $highYear;

// passed in from somewhere.php
$park = 		isset($_GET['park']) 		? $_GET['park'] 		: '';
$asset_id = 	isset($_GET['asset_id']) 	? $_GET['asset_id'] 	: '';
$asset_name = 	isset($_GET['asset_name'])	? $_GET['asset_name'] 	: '';
$projNum =		isset($_GET['projNum']) 	? $_GET['projNum']		: '';
$year = 		isset($_GET['year']) 		? $_GET['year'] 		: '';
$fund = 		isset($_GET['fund']) 		? $_GET['fund'] 		: '';
$region = 		isset($_GET['region'])		? $_GET['region'] 		: '';

// or as a post from this page
$park = 		isset($_POST['park']) 		? $_POST['park'] 		: $park;
$asset_id = 	isset($_POST['asset_id']) 	? $_POST['asset_id'] 	: $asset_id;
$asset_name = 	isset($_POST['asset_name'])	? $_POST['asset_name'] 	: $asset_name;
$projNum =		isset($_POST['projNum']) 	? $_POST['projNum']		: $projNum;
$year = 		isset($_POST['year']) 		? $_POST['year'] 		: $year;
$fund = 		isset($_POST['fund']) 		? $_POST['fund'] 		: $fund;
$region = 		isset($_POST['region'])		? $_POST['region'] 		: $region;

echo"
<table>
<tr>
<td><a href='index.htm' onclick='return confirmMove();'>Admin Menu</a></td>
<td><a href='manageAssets.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projNum=$projNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage Assets</a></td>
<td><a href='managePMIS.php?	park=$park&asset_id=$asset_id&asset_name=$asset_name&projNum=$projNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Manage PMIS</a></td>
<td><a href='tier.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projNum=$projNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Tier Assignment</a></td>
<td><a href='assets.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projNum=$projNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Asset Data</a></td>
<td><a href='FMSS.php?			park=$park&asset_id=$asset_id&asset_name=$asset_name&projNum=$projNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>FMSS Links</a></td>
<td><a href='funding.php?		park=$park&asset_id=$asset_id&asset_name=$asset_name&projNum=$projNum&year=$year&fund=$fund&region=$region' onclick='return confirmMove();'>Funding</a></td>
<td><a href='logout.php' onclick='return confirmMove();' >Log out</a></td>
</tr>
</table>

<td>
	<input type='hidden' id='idParkId' 		name='park' 			value='$park'>
	<input type='hidden' id='idAssetID' 	name='assetId' 			value='$asset_id'>
	<input type='hidden' id='idAssetName'	name='asset_name' 		value='$asset_name'>
	<input type='hidden' id='idProjNum' 	name='projNum' 			value='$projNum'>
	<input type='hidden' id='idYear' 		name='year' 			value='$year'>
	<input type='hidden' id='idfund' 		name='fund' 			value='$fund'>
	<input type='hidden' id='idregion' 		name='region' 			value='$region'>
	<input type='hidden' id='anyTable' 		name='anyTableChanged'	value='false'>
</td>
";							
								
								