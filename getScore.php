<?php
include_once 'mySqli.php';
include_once 'helper.php';

if (isset($_POST['area']) && isset($_POST['row'])){
	
	$area = $_POST['area'];
	$row = $_POST['row'];
	
	$value = 0;
	switch ($area) {
		case 'a':
		case 'b':
		case 'c':
		case 'd':
		case 'e':
		case 'f':
		case 'g':
		case 'h':
		case 'i':
	        $v = explode(",", $row);
	        $value = 0;
	        foreach($v as $vs){
	        	$vals = explode("-", $vs);
				$query = "SELECT val$vals[1] FROM `matrix` WHERE name = '$vals[0]'";
				$res = queryMysqli($mysqli, $query);
				$row = $res->fetch_row();
				$value+= $row[0];
	        }
			break;
	}
}
$value = $value>50 ? 50 : $value;

echo $value;
?>