<?php // helper.php

// Load javascript
function javascriptHeaderText()
{
/*	global $javaScriptSource;*/
	$txt = "		<script type='text/javascript' src=jquery-2.1.3.min.js></script>
";
	return $txt;
} // end: javascriptHeaderText()

// as of 6-21-12 includes:
//		function setSelectField(selectName, fieldName)
//		function editFunction(fieldPointer, table)
function commonFunctionsText()
{
	/*	global $javaScriptSource;*/
	$txt = "		<script type='text/javascript' src=commonFunctions.js></script>
";
	return $txt;
} // end: commonFunctionsText()

// as of 6-21-12 includes:
//		function stripCommas(num)
//		function addCommas(nStr)
//		function setCommas(fieldPointer, table)
function commaSeperatorFunctionsText()
{
	/*	global $javaScriptSource;*/
	$txt = "		<script type='text/javascript' src=commaSeperatorFunctions.js></script>
";
	return $txt;
} // end: commaSeperatorFunctionsText()

// generic header for all html pages
//	Don't forget to change the style sheet name to match
function getHeaderTextGeneral()
{
$txt = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>
	
		<!--style sheet-->
		<link href='stylesheet/ats.css' rel='stylesheet' type='text/css' />
";
return $txt;
} // end: getHeaderTextGeneral()

// Top of login.php
function loginTextTop()
{

$txt = "		<title>ATS Management System</title>
	</head>
	<body style='text-align:center'>
		<table class='login' cellspacing='5'>
";
	return $txt;
} // end: loginTextTop()

// top of asset page
function assetTextTop()
{
	$txt = "		<title>ATS Management System</title>
	</head>
	<body>
";
	return $txt;
} // end: assetTextTop()

// adds ability to change username/password to login.php
function loginTextAllowChange()
{
	$txt = '			<tr align="center">
				<td><input align="center" type="button" onClick="parent.location=\'change_user_name.php\'"
					value="Change user name/password"></td>
			</tr>
';
	return $txt;
} // end: loginTextAllowChange()

// adds ability to sign up users to login page
function loginTextAllowSignUp()
{
	$txt = '			<tr align="center">
				<td><input align="center" type="button" onClick="parent.location=\'signup.php\'"
					value="Sign Up User"></td>
			</tr>
';
	return $txt;
} // end: loginTextAllowSignUp()

// bottom of login.php
function loginTextBottom()
{
	$txt = '			<tr align="center">
				<td><input align="center" type="button" onClick="parent.location=\'index.htm\'"
						  value="Management System"></td>
			</tr>
			<tr align="center">
				<td><button type="button" onclick="parent.location=\'logout.php\'">Log out</button></td>
			</tr>
		</table>
	</body>
</html>
';
	return $txt;
} // end: loginTextBottom()

// bottom of asset page
function assetTextBottom()
{
	$txt = '	</body>
</html>
';
	return $txt;
} // end: assetTextBottom()


// If this is called with 'true' then it returns a boolean.
// 'true' = timed out
// and the calling code handles the result.  If called with 'false'
// then this function handles the time out details and exits from here
function manageSessionTime($returnValue)
{
	//           sec        min           hr
	$duration =   0 + (60 * 30) + (3600 * 4); // (set for 30 minutes)
//	$duration =   10 + (60 * 0) + (3600 * 0); // (set for 10 seconds)
	
	if(sessionTimedOut($_SESSION['startTime'],  $duration))
	{
		// let calling function handle time out if $returnValue is 'true'
		if($returnValue)
			return true;

//		destroy_session_and_data();
		reDirectHome('timeOut.php');
		exit;
	}
	// session not timed out
	return false;
} // end: manageSessionTime($returnValue)

// sends user to $page
function reDirectHome($page)
{
	echo "<meta http-equiv='refresh' content='0;url=$page'>";
} // end: reDirectHome($page)

// This function taken directly from php manual
function destroy_session_and_data()
{
	if(isset($_SESSION['sessId']))
	{
		removeSessionId($_SESSION['sessId']);
	}
	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get("session.use_cookies"))
	{
	    $params = session_get_cookie_params();
	    setcookie(session_name(), '', time() - 42000,
	        $params["path"], $params["domain"],
	        $params["secure"], $params["httponly"]);
	}

	// Finally, destroy the session.
	session_destroy();
}//end: destroy_session_and_data()

// This function taken directly from php manual
function destroy_session_and_data_mysqli($mySqli)
{
	if(isset($_SESSION['sessId']))
	{
		removeSessionIdMysqli($mySqli, $_SESSION['sessId']);
	}
	$_SESSION = array();

	// If it's desired to kill the session, also delete the session cookie.
	// Note: This will destroy the session, and not just the session data!
	if (ini_get("session.use_cookies"))
	{
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
		$params["path"], $params["domain"],
		$params["secure"], $params["httponly"]);
	}

	// Finally, destroy the session.
	session_destroy();
}//end: destroy_session_and_data()

// returns true if timed out, else false
function sessionTimedOut($sessionStartTime, $delta)
{
	$nowTime = time();
	if($nowTime - $sessionStartTime > $delta)
		return true;
	return false;
} // end: sessionTimedOut($sessionStartTime, $delta)

// fires query and checks for failure
function queryMysql($query)
{
	$result = mysql_query($query) or die(mysql_error());
	return $result;
} // end: queryMysql($query)

// exectue a prepared SELECT statement to database
// taken from php website and modified
function queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params)
{
	$column = '';
//	$stmt =  $mysqli->stmt_init();
//	if ($stmt->prepare($query))
	if ($stmt = $mysqli->prepare($query))
	{
		if(!empty($params))
		{
			/* bind parameters for markers */
			$bindStatement="\$stmt->bind_param('" . $paramTypes . "',";
			$bindList = '';
			for($i=0; $i<count($params);$i++)
			{
				if(empty($bindList)){
					$bindList .= '$params[' . $i . ']';
				}else{
					$bindList.=', $params[' . $i . ']';
				}
			}
			$bindStatement .= $bindList . ");";
			eval($bindStatement);
		}
		/* execute query */
		$err = $stmt->execute();
		//$stmt->store_result();
		/* bind result variables */
		$metaResults = $stmt->result_metadata();
		$fields = $metaResults->fetch_fields();
		$statementParams='';
		//build the bind_results statement dynamically so I can get the results in an array
		foreach($fields as $field){
			if(empty($statementParams)){
				$statementParams.="\$column['".$field->name."']";
			}else{
				$statementParams.=", \$column['".$field->name."']";
			}
		}
		$statment="\$stmt->bind_result($statementParams);";
		eval($statment);
		$i=0;
		$res=array();
		while($stmt->fetch()){
			foreach($column as $k=>$v)
			{
				$res[$i][$k]=$v;
			}
			$i++;
		}
		/* close statement */
		$stmt->close();
	}
	return $res;
}
// exectue a prepared SELECT statement to database
// taken from php website and modified
function queryMysqliPreparedNonSelect($mysqli, $query, $paramTypes, $params)
{
	$column = '';
//	$stmt =  $mysqli->stmt_init();
//	if ($stmt->prepare($query))
	
	$err='';
//	$stmt =  $mysqli->stmt_init();
	if ($stmt = $mysqli->prepare($query))
	{
		if(!empty($params))
		{
			/* bind parameters for markers */
			$bindStatement="\$stmt->bind_param('" . $paramTypes . "',";
			$bindList = '';
			for($i=0; $i<count($params);$i++)
			{
				if(empty($bindList)){
					$bindList .= '$params[' . $i . ']';
				}else{
					$bindList.=', $params[' . $i . ']';
				}
			}
			$bindStatement .= $bindList . ");";
			eval($bindStatement);
		}
		/* execute query */
		$err = $stmt->execute();
		/* close statement */
		$stmt->close();
	}
	return $err;
}

function sanitizeString($var)
{
	$var = strip_tags($var);
	$var = htmlentities($var);
	return stripslashes($var);
}

function sanitizeStringMySql($var)
{
	$var = strip_tags($var);
	$var = htmlentities($var);
	$var = stripslashes($var);
	return mysql_real_escape_string($var);
}

function sanitizeStringMySqli($mySqli, $var)
{
	$var = strip_tags($var);
	$var = htmlentities($var);
	$var = stripslashes($var);
	return $mySqli->real_escape_string($var);
}

function removeSessionId($oldSessId)
{
	include_once 'mySqlData.php';
	
	$query = "UPDATE $dbtable SET sessId='' WHERE sessId='$oldSessId'";
	
	if(!queryMysql($query)) die ("Database not available at this time: helper line: 328" . mysql_error());
}

function removeSessionIdMysqli($mySqli, $oldSessId)
{
	$query = "UPDATE users SET sessId='' WHERE sessId='$oldSessId'";
	$paramTypes = '';
	//	$params = array($parkIn, $subUnitIn, $parkNameIn, $subUnitNameIn, $regionIn, $park, $subUnit);
	$params = array();
	$result = queryMysqliPreparedNonSelect($mySqli, $query, $paramTypes, $params);
	
//	include_once 'mySqlData.php';

//	$query = "UPDATE $dbtable SET sessId='' WHERE sessId='$oldSessId'";

//	if(!queryMysql($query)) die ("Database not available at this time:" . mysql_error());
}

// load data from userLog.txt into showUse.php for display of use statistics
function loadUserData()
{
	$txt = "		<table class='showList'>
			<th>User Data</th>
			<tr><td>User</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Time</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Where</td></tr>
";
	
	// initialize array and load with data from file
	$userData = array();
	$userData = unserialize(file_get_contents("userLog.txt"));
	
	// needed for date() function
	date_default_timezone_set('America/New_York');
	
	if(count( $userData ) )
	{
		foreach ($userData as $val)
		{
			$u = $val['user'];
			$t = $val['time'];
			$now = date("y-m-j   H:i:s", $t);
			$w = $val['where'];
			$txt .= "			<tr><td>$u</td><td>$now</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$w</td></tr>
";
		}
	}
	$txt .= "		</table>
";

	return $txt;
} // end: loadUserData()

// call this function to log entering a php file must be called after session start to 
// get uid Need to fire time() in here so we get different times for each php
// $where is passed in from calling function
function logUser($where)
{	
	if(!isset($_SESSION['uid']))
	session_start();
	
	$u = $_SESSION['user'];
	
	// if no file, create new one and nload with start date of file
	if(!file_exists('userLog.txt'))
	{
		$handle = fopen('userLog.txt', 'w');
		fclose($handle);

		$uD = array();
		$uD[0] = array('user' => 'Start Log', 'time' => time(), 'where' => '');
		file_put_contents('userLog.txt', serialize($uD), LOCK_EX);
	}
	
	// now put in info for this time through
	$userData = array();
	$userData = unserialize(file_get_contents('userLog.txt'));
	$userData[] = array('user' => $u, 'time' => time(), 'where' => $where);
	
	file_put_contents('userLog.txt', serialize($userData), LOCK_EX);
}
// standard query functin with error control
function queryMysqli($link, $query)
{
	$result = $link->query($query);
	if ($link->connect_errno) {
		echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
	}
	if (!$result) {
//		echo "Error code: $link->errno:  $link->error";
		echo "Error code: $link->errno:  $query";
		}

	return $result;
} // end: queryMysqli($link, $query)

// returns column "$col" from table "$table"
function getSingleColumnQuery($col, $table)
{
	$query = "SELECT " . $col . " FROM " . $table;
	$result = queryMysql($query);
	if(!$result) die ("Database not available at this time: helper line: 428" . mysql_error());
	return $result;
}

// returns column "$col" from table "$table"
// rewritten for mysqli LS 4/7/2014
function getSingleColumnQueryMysqli($mysqli, $col, $table)
{
	$query = "SELECT " . $col . " FROM " . $table;
//	$result = queryMysql($query);
	$result = queryMysqli($mysqli, $query);
	if(!$result) die ("Database not available at this time: helper line: 438" . mysql_error());
	return $result;
}

// returns column "$col" from table "$table", no duplicate values
function getSingleColumnQueryDistinct($col, $table)
{
	$query = "SELECT DISTINCT " . $col . " FROM " . $table;
	$result = queryMysql($query);
	if(!$result) die ("Database not available at this time: helper line: 448" . mysql_error());
	return $result;
}

// returns column "$col" from table "$table", no duplicate values
function getSingleColumnQueryDistinctMysqli($mysqli, $col, $table)
{
	$query = "SELECT DISTINCT " . $col . " FROM " . $table;
	$result = queryMysqli($mysqli, $query);
	if(!$result) die ("Database not available at this time: helper line: 457" . mysql_error());
	return $result;
}

// returns column "$col" from table "$table", no duplicate values, ascending order
function getSingleColumnQueryDistinctOrderedAscMysqli($mysqli, $col, $table)
{
	$query = "SELECT DISTINCT " . $col . " FROM " . $table . " ORDER BY " . $col . " ASC";
	$result = queryMysqli($mysqli, $query);
	if(!$result) die ("Database not available at this time: helper line: 466" . mysql_error());
	return $result;
}
// returns column "$col" from table "$table", no duplicate values, ascending order
function getPreparedSingleColumnQueryDistinctOrderedAscMysqli($mysqli, $col, $table)
{
	$query = "SELECT DISTINCT " . $col . " FROM " . $table . " ORDER BY " . $col . " ASC";
	
	$paramTypes = '';
	$params = array();
	$res = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);

//	$result = queryMysqli($mysqli, $query);
	if(!$res) die ("Database not available at this time: helper line: 466" . mysql_error());

	return $res;
}


// returns whole table "$table" where field "$field" is "fieldValue"
function getWholeTableQuery($table, $field, $fieldValue)
{
	$query = "SELECT * FROM " . $table . " WHERE " . $field . " = " . $fieldValue;
	$result = queryMysql($query);
	if(!$result) die ("Database not available at this time: helper line: 475" . mysql_error());
	$rowAssets = mysql_fetch_row($result); // $rowAssets contains all of the data
	
	return $rowAssets;
}

// returns whole table "$table" where field "$field" is "fieldValue"
// rewritten for mySqli LS 4/7/2014
function getWholeTableQueryMysqli($mysqli,$table, $field, $fieldValue)
{
	$query = "SELECT * FROM " . $table . " WHERE " . $field . " = " . $fieldValue;
//	$result = queryMysql($query);
	$result = queryMysqli($mysqli, $query);
	if(!$result) die ("Database not available at this time: helper line: 487" . mysql_error());
//	$rowAssets = mysql_fetch_row($result); // $rowAssets contains all of the data
	$rowAssets = $result->fetch_assoc(); // $rowAssets contains all of the data

	return $rowAssets;
}

// generates <select> field
// $title	= title displayed for this input field
// $id		= html ID for this field
// $name	= html name for this field (gets paassed in POST)
// &$result = query result containing option values
// $table	= which table is this field in?
// $level	= security level of user
// $value	= value for this field (only needed for level 3 users)
function generateSelect($title, $id, $name, &$result, $table, $level=3, $value='') {

	$txt = '';
	$txt .= "						<tr><td>" . $title . ":	</td>
";
	// level  3, only show value, do not allow changes
	if($level == '3')
	{
		$txt .= "							<td>$value</td></tr>
";
	}
	// levels 1 and 2, allow changes
	else
	{
		$txt .= "							<td><select id='" . $id . "' name='" . $name. "' style='width:100%;' onChange=editFunction(this,'$table')>
";

//		while(	$row = mysql_fetch_row($result))
		while(	$row = $result->fetch_row()){
			$txt .= "							<option value='$row[0]'> $row[0] </option>
";
		};
	
		$txt .= "							</select></td></tr>
";
	}
	
	return $txt;
}

// generates <select> field for $result from prepared sql statement
// $title	= title displayed for this input field
// $id		= html ID for this field
// $name	= html name for this field (gets paassed in POST)
// &$result = query result containing option values
// $table	= which table is this field in?
// $level	= security level of user
// $value	= value for this field (only needed for level 3 users)
function generateSelectPrepared($title, $id, $name, &$result, $table, $level=3, $value='') {

	$txt = '';
	$txt .= "						<tr><td>" . $title . ":	</td>
";
	// level  3, only show value, do not allow changes
	if($level == '3')
	{
		$txt .= "							<td>$value</td></tr>
		";
	}
	// levels 1 and 2, allow changes
	else
	{
		$txt .= "							<td><select id='" . $id . "' name='" . $name. "' style='width:100%;' onChange=editFunction(this,'$table')>
		";

		//		while(	$row = mysql_fetch_row($result))
		//		while(	$row = $result->fetch_row())
		foreach ($result as $row){
			$t2 = array_keys($row)[0];
			$r = $row["$t2"];
			$txt .= "							<option value='$r'> $r </option>
			";
	};

	$txt .= "							</select></td></tr>
	";
}

return $txt;
}

// generates <select> field inside a td element
// $id		= html ID for this field
// $name	= html name for this field (gets paassed in POST)
// &$result = query result containing option values
function generateSelectTd($id, $name, &$result) {

	$txt = '';
	// levels 1 and 2, allow changes
	$txt .= "							<td><select id='" . $id . "' name='" . $name. "' style='width:100%;' onChange=this.form.submit()>
";

		while(	$row = $result->fetch_row())
		{
			$txt .= "							<option value='$row[0]'> $row[0] </option>
";
		};
	
		$txt .= "							</select></td></tr>
";
	
	return $txt;
}

//	generateSelectTd uses row[0] for lboth display and internal value
// generateSelectTd2 uses row[0] for internal and row[1] for display
// generates <select> field inside a td element
// $id		= html ID for this field
// $name	= html name for this field (gets paassed in POST)
// &$result = query result containing option values
function generateSelectTd2($id, $name, &$result) {

	$txt = '';
	// levels 1 and 2, allow changes
	$txt .= "							<td><select id='" . $id . "' name='" . $name. "' style='width:100%;' onChange=this.form.submit()>
";

	while(	$row = $result->fetch_row())
	{
		$txt .= "							<option value='$row[0]'> $row[1] </option>
		";
	};

	$txt .= "							</select></td></tr>
";

	return $txt;
}


// generates <select> field whose first value is blank
// $title	= title displayed for this input field
// $id		= html ID for this field
// $name	= html name for this field (gets paassed in POST)
// &$result = query result containing option values
// $table	= which table is this field in?
// $level	= security level of user
// $value	= value for this field (only needed for level 3 users)
function generateSelectFirstBlank($title, $id, $name, &$result, $table, $level=3, $value='') {

	$txt = '';
	$txt .= "						<tr><td>" . $title . ":	</td>
";
	// level  3, only show value, do not allow changes
	if($level == '3')
	{
		$txt .= "							<td>$value</td></tr>
		";
	}
	// levels 1 and 2, allow changes
	else
	{
		$txt .= "							<td><select id='" . $id . "' name='" . $name. "' style='width:100%;' onChange=editFunction(this,'$table')>
";
		$txt .= "								<option value=''></option>
";

		while(	$row = $result->fetch_row())
		{
			$txt .= "							<option value='$row[0]'> $row[0] </option>
";
	};

	$txt .= "							</select></td></tr>
";
}

return $txt;
}

// generates sysLinkTable <input> field
// $title	= title displayed for this input field
// $name	= html name for this field (gets paassed in POST)
// $val 	= what value should the input start with
// $table	= which table is this field in?
// $level	= security level of user
function generateSysLinkInputField($title, $name, $val, $table, $level) {
	$txt = "						<tr><td>" . $title . ":	</td>
";
	// level  3, only show value, do not allow changes
	if($level == '3')
	{
		$txt .= "							<td>$val</td></tr>
";
	}
	// levels 1 and 2, allow changes
	else
	{
		$txt .= "							<td><input type='text' style='width:100%' name='" . $name . "' value='" . $val . "' style='width:100%' onKeyUp=editFunction(this,'$table')></td></tr>
";
	}
	return $txt;
}

// generates sysLinkTable <input> number field
// $title	= title displayed for this input field
// $name	= html name for this field (gets paassed in POST)
// $val 	= what value should the input start with
// $table	= which table is this field in?
// $level	= security level of user
// same as above, except need to deal with commas
function generateSysLinkInputFieldNum($title, $name, $val, $table, $level) {
	$val = addCommas($val);
	$txt = "						<tr><td>" . $title . ":	</td>
";
	// level  3, only show value, do not allow changes
	if($level == '3')
	{
		$txt .= "							<td>$val</td></tr>
";
	}
	// levels 1 and 2, allow changes
	else 
	{
		$txt .= "							<td><input type='text' style='width:100%' name='" . $name . "' value='" . $val . "' style='width:100%' onKeyUp=setCommas(this,'$table')></td></tr>
";
	}
	return $txt;
}

// generates <input> field
// $title	= title displayed for this input field
// $name	= html name for this field (gets paassed in POST)
// $val 	= what value should the input start with
// $table	= which table is this field in?
// $level	= security level of user
function generateInputField($title, $name, $val, $table, $level) {
	$txt = "						<tr><td>" . $title . ":	</td>
";
	// level  3, only show value, do not allow changes
	if($level == '3')
	{
		$txt .= "							<td>$val</td></tr>
";
	}
	// levels 1 and 2, allow changes
	else
	{
		$txt .= "							<td><input type='text' name='" . $name . "' value='" . $val . "' style='width:100%' onKeyUp=editFunction(this,'$table')></td></tr>
";
	}
	return $txt;
}

// generates <input> field with number format (adds commas)
// $title	= title displayed for this input field
// $name	= html name for this field (gets paassed in POST)
// $val 	= what value should the input start with
// $table	= which table is this field in?
// $level	= security level of user
function generateInputFieldNum($title, $name, $val, $table='', $level) {
	$val = addCommas($val);
	
	$txt = "						<tr><td>" . $title . ":	</td>
";
	// level  3, only show value, do not allow changes
	if($level == '3')
	{
		$txt .= "							<td>$val</td></tr>
";
	}
	// levels 1 and 2, allow changes
	else
	{
		$txt .= "							<td><input type='text' name='" . $name . "' value='" . $val . "' style='width:100%' onKeyUp=setCommas(this,'$table')></td></tr>
";
	}
	return $txt;
}

// deals with funding table's strange data format
// $title	= name for this row of the table
// $name	= html name for this field (gets passed in POST)
// $val 	= what value should the input start with
// $table	= which table is this field in?
// $level	= security level of user
function generateInputFieldFunding($title, $name, $vals, $table, $level, &$tots) {
	$txt = "						<tr><td>" . $title . ":	</td>
";

	for($i=2, $j=0; $i<5; $i++, $j++)
	{
		$type = '';
		$class = '';
		switch ($i)
		{
			case 2:
			  $type = 'cap_percent';
			  $class = 'cap';
			  break;
			case 3:
			  $type = 'op_percent';
			  $class = 'op';
			  break;
			case 4:
			  $type = 'futurecap_percent';
			  $class = 'future';
			  break;
		}
		$val = $vals[$type]*100;
		$tots[$type]+=$val;
		if('0' == $val)
		{
			if($level == '3')
			{
				$txt .= "							<td>$val%</td>
";
			}
			else
			{
//				$txt .= "							<td><class='$class' input type='text' name='" . $name . $j . "' value='" . $val . "' onKeyUp=editFunction(this,'$table')></td>
//";
				$txt .= "							<td><input class='$class' type='text' name='" . $name . $j . "' value='" . $val . "%' onKeyUp=editFunding(this,'$table')></td>
";
			}
		}
		else
		{
			if($level == '3')
			{
				$txt .= "							<td style='color:blue'>$val%</td>
";
			}
			else
			{
//				$txt .= "							<td><class='$class' input type='text' style='color:blue' name='" . $name . $j . "' value='" . $val . "' onKeyUp=editFunction(this,'$table')></td>
//";
				$txt .= "							<td><input class='$class' type='text' style='color:blue' name='" . $name . $j . "' value='" . $val . "%' onKeyUp=editFunding(this,'$table')></td>
";
			}
		}
	}
	$txt .= "						</tr>
";
	return $txt;
}

// generates row for tier.php
// $row			= array from mysql query containing fields for this row
// $level 		= useage level for this user
// $assetTier 	= array of asset tiers
// $year		= year
// LS 10 Nov 2014 added $display for odd/even class names for table shading
// LS 24 Nov 2014 changed to assoc array
function generateFieldTier($row, $level, &$assetTier, $year, $fund, &$PMISYear, $display) {
	$row['pmis_dollars'] = addCommas($row['pmis_dollars']);

	// level 3, only show value, do not allow changes
	if($level == '3')
	{
		$txt = "					<tr class='$display'><td style='text-align:right;'>{$row['assset_id']}</td><td style='text-align:left;'>{$row['park_id']}</td><td style='text-align:left;'>{$row['project_name']}</td>
						<td style='text-align:left;'>{$row['access_type']}</td><td style='text-align:left;'>{$row['asset_name']}</td><td>{$row['pmis_number']}</td><td style='text-align:right;'>{$row['pmis_dollars']}</td><td style='width:4em; text-align:left;'>{$row['pmis_fund_name']}</td>
						<td>{$row['pmis_year']}</td><td style='text-align:left;'>{$row['asset_action']}</td><td>{$row['pmis_region_rank']}</td><td>{$row['asset_tier']}</td></tr>
";
	}
	// levels 1 and 2, allow changes
	else
	{
		$txt = "					<tr class='$display'><td style='text-align:right;'>{$row['asset_id']}</td><td style='text-align:left;'>{$row['park_id']}</td><td style='text-align:left;'>{$row['project_name']}</td>
						<td style='text-align:left;'>{$row['access_type']}</td><td style='text-align:left;'>{$row['asset_name']}</td><td>{$row['pmis_number']}</td><td style='text-align:right;'>{$row['pmis_dollars']}</td><td style='width:4em; text-align:left;'>{$row['pmis_fund_name']}</td>
";
		
		$txt .= "						<td><select id='idY" . $row['asset_id'] . "' name='Y" . $row['asset_id']. "' style='width:6em;' onChange=\"editFunctionYear(this, '" . $row['asset_id']. "');this.form.submit()\">
";
		$i = 0;
		foreach($PMISYear as $value)
		{
			$txt .= "							<option value='$i'>$value</option>
";
			$i++;
		};
		
		$txt .= "						</select></td>
";		
		// find which value in the $assetTier array is the tier for his asset
		$val = '';
		$j = 0;
		foreach($PMISYear as $value)
		{
			if($value == $row['pmis_year'])
			{
				$val = $j;
				break;
			}
			$j++;
		};
		$txt .= "					<script type='text/javascript'>
						$(\"#\" + \"idY{$row['asset_id']}\").val($val)
						</script>
";
		
		$txt .= "						<td style='text-align:left;'>{$row['asset_action']}</td>
						<td>{$row['pmis_region_rank']}</td>
";
		
		$txt .= "						<td><select id='id" . $row['asset_id'] . "' name='" . $row['asset_id']. "' style='width:6em;' onChange=\"editFunctionTier(this, '" . $row['asset_id']. "');this.form.submit()\">
";
		$i = 0;
		foreach($assetTier as $value)
		{
			$txt .= "							<option value='$i'>$value</option>
";
			$i++;
		};
		
		$txt .= "						</select></td>
";
		$txt .= "<td style='text-align:center'><a href=\"assets.php?park={$row['park_id']}&asset_id={$row['asset_id']}&asset_name={$row['asset_name']}&projNum={$row['project_id']}&year=$year&fund=$fund\">EDIT</a></td>
";
		
		$txt .= "				</tr>
";		
		// find which value in the $assetTier array is the tier for his asset
		$val = '';
		$j = 0;
		foreach($assetTier as $value)
		{
			if($value == $row['asset_tier'])
			{
				$val = $j;
				break;
			}
			$j++;
		};
		$txt .= "					<script type='text/javascript'>
						$(\"#\" + \"id{$row['asset_id']}\").val($val)
						</script>
";

	}
	return $txt;
}

// generates <input> field like a select box, but which you can also type into
// $title	= title displayed for this input field
// $name	= html name for this field (gets paassed in POST)
// $table	= which table is this field in?
function generateComboSelect($title, $id, $name, &$res, $ulId) {
	$txt = "						<tr><td>" . $title . ":	</td>
";
	// levels 1 and 2, allow changes
	$txt .= "							<td><input type='text' id='$id' name='$name' value='' onKeyUp=editFunction(this,'parkC') style='width:100%' >
							<ul id='$ulId'>
";
	
	while(	$row = $res->fetch_row()){
		$txt .= "							<li>$row[0]</li>
";
	}
			echo"</ul></td></tr>
";
	return $txt;
}


// this is a js function (found in commaSeperatorFunctions.js) ported to php
// removes commas from displayed number
function stripCommas($num)
{
	$num1 = str_replace(',', '', $num);
	return $num1;
}

// ported from commaSeperatorFunctions.js, original from:
// http://www.mredkj.com/javascript/nfbasic.html
// adds commas to a number for display
function addCommas($nStr) {
//	$nStr .= '';
//	$x = preg_split("/./",$nStr );
	$x = explode(".",$nStr );
	$x1 = $x[0];
	$x2 = '';
	if(count($x)>1)
		$x2 = '.' . $x[1];
	$rgx = "/(\d+)(\d{3})/";
//	while ($rgx.test($x1)) {
	$replacePat = '$1' . ',' . '$2';
	while (preg_match($rgx,$x1)) {
//		$x1 = $x1.replace($rgx, '$1' + ',' + '$2');
		$x1 = preg_replace($rgx, $replacePat, $x1);
	}
	return $x1 . $x2;
}

// makes an array from a mysql result
// only works for results with one field
function arrayFromResult(&$result){
	$numRows = mysql_num_rows($result);
	for($i=0;$i<$numRows;$i++)
	{
		$t = mysql_fetch_row($result);
		$arr[] = $t[0];
	}
	return $arr;
}

// makes an array from a mysql result
// only works for results with one field
// removes any nulls found
function arrayFromResultNotNull(&$result){
	$numRows = mysql_num_rows($result);
	for($i=0;$i<$numRows;$i++)
	{
		$t = mysql_fetch_row($result);
		if($t[0])
			$arr[] = $t[0];
	}
	return $arr;
}

// makes an array from a mysql result
// only works for results with one field
// removes 'discontinue' & 'replace' from list of tiers
function tierArrayFromResult(&$result){
	$numRows = mysql_num_rows($result);
	for($i=0;$i<$numRows;$i++)
	{
		$t = mysql_fetch_row($result);
		if('discontinue' != $t[0] && 'replace' != $t[0])
		$arr[] = $t[0];
	}
	return $arr;
}

// makes an array from a mysql result
// only works for results with one field
function arrayFromResultMysqli($result){
	$numRows = $result->num_rows;
	for($i=0;$i<$numRows;$i++)
	{
		$t = $result->fetch_row();
		$arr[] = $t[0];
	}
	return $arr;
}

// makes an array from a mysql result
// only works for results with one field
// removes any nulls found
function arrayFromResultNotNullMysqli($result){
	$numRows = $result->num_rows;
	for($i=0;$i<$numRows;$i++)
	{
		$t = $result->fetch_row();
		if($t[0])
			$arr[] = $t[0];
	}
	return $arr;
}

// makes an array from a mysql result
// only works for results with one field
// removes 'discontinue' & 'replace' from list of tiers
function tierArrayFromResultMysqli($result){
	$numRows = $result->num_rows;
	for($i=0;$i<$numRows;$i++)
	{
		$t = $result->fetch_row();
		if('discontinue' != $t[0] && 'replace' != $t[0])
			$arr[] = $t[0];
	}
	return $arr;
}

// Create a JS array from a php array
// $arr	= query result from table
// $jsArrayName = name of array in JS
function makeJSArray2($arr, $jsArrayName)
{
	$val = '';
	$i = 0;
	foreach($arr as $v)
	{
//		$val .= "				$jsArrayName" . "[$i] =";
//		$val .= "\"$v\"
//";
		$val .= "			" . $jsArrayName . "[" . $i . "] = \"" . $v . "\";
";
		$i++;
	}
	return $val;
}// end makeJSArray2($res, $str)

// generate html for dataTable
// $res is result from prepared stmt to mysqli
function generateHTMLdataTable($res, &$txt, &$totals )
{
	$fields = array_keys($res[0]);
	
	$txt = '';
	$txt .= "		<thead>
		<tr>
";
	foreach($fields as $field)
	{
		$txt .= "						<th>$field</th>
";
	}
	$txt .= "					</tr>
				</thead>
				<tbody>
";
	$acc = $oc = $tc = 0;
	foreach($res as $r)
	{
	$txt .= "					<tr class=''>
";
		foreach($r as $k=>$v)
		{
			switch($k)
			{
				case ('annual_cap_cost'):
					$acc += $v;
					break;
				case ('op_cost'):
					$oc += $v;
					break;
				case ('total_cost'):
					$tc += $v;
					break;
				default:
			}
			$bacc = number_format($acc);
			$boc = number_format($oc);
			$btc = number_format($tc);
	
			if(is_numeric($v))
			{
				$val = number_format($v);
				$txt .= "						<td style='text-align:right'>$val</td>
";
			}
			else
			{
				$txt .= "						<td>$v</td>
";
			}
	
		}
		$txt .= "					</tr>
";
	}
	$txt .= "				</tbody>
";
	
	$totals = '';
	$totals .= "					<tr class='even'>
						<td>Totals</td>
							<td></td>
	<td></td>
	<td></td>
	<td>$bacc</td>
	<td>$boc</td>
	<td>$btc</td>
	</tr>
";
}

// generate html for dataTable
// $res is result from prepared stmt to mysqli
// &$project_names		array of project names with an array attached to each name for totals
// &$txt				main table
// &$totals				totals table
// $projR				array of project names
// &$projT				project table
// total cost fields are:
//		park_id
//		project_name
//		asset_name
//		asset_status
//		asset_type
//		annual_cap_cost
//		op_cost
//		total_cost
function generateHTMLTotalCostDataTable($res, &$project_names, &$txt, &$totals, $projR, &$projT )
{
	//	first make main data table
	// headers
	$txt = '';
	$txt .= "		<thead>
		<tr>
			<th>Project Name</th>
			<th style='text-align:left'>Asset Name</th>
			<th style='text-align:right;'>Annual Cap Cost</th>
			<th style='text-align:right;'>Annual Op Cost</th>
			<th style='text-align:right;'>Total Cost</th>
		</tr>
	</thead>
	<tbody>
";
	// body
	$acc = $oc = $tc = 0;
	foreach($res as $r)
	{
		$txt .= "					<tr class=''>
						<td>{$r['project_name']}</td>
						<td style='text-align:left'>{$r['asset_name']}</td>
						<td style='text-align:right'>" . number_format($r['annual_cap_cost']) . "</td>
						<td style='text-align:right'>" . number_format($r['annual_op_cost']) . "</td>
						<td style='text-align:right'>" . number_format($r['total_cost']) . "</td>
					</tr>
";
		// while going through data, accumulate totals
		$project_names[$r['project_name']]['annual_cap_cost'] += $r['annual_cap_cost'];
		$project_names[$r['project_name']]['annual_op_cost'] += $r['annual_op_cost'];
		$project_names[$r['project_name']]['total_cost'] += $r['total_cost'];
		$acc += $r['annual_cap_cost'];
		$oc += $r['annual_op_cost'];
		$tc += $r['total_cost'];
	}
	
	// fill project names totals data
	foreach($project_names as &$proj)
	{
		foreach($proj as &$p)
		{
			$p = number_format($p);
		}
	}
	$acc = number_format($acc);
	$oc = number_format($oc);
	$tc = number_format($tc);
	
	$txt .= "				</tbody>
";

	// make totals table
	$totals = '';
	$totals .= "					<tr class='even totals'>
	<td>Totals</td>
	<td>$acc</td>
	<td>$oc</td>
	<td>$tc</td>
	</tr>
";
	
	// make project names table
	$projT = makeGroupSelect('part',$projR, '', 'projectName', 'prjName', 'projectNameAll', 'projectChecked');
}

// generate html for dataTable
// $res is result from prepared stmt to mysqli
// &$project_names		array of project names with an array attached to each name for totals
// &$txt				main table
// &$totals				totals table
// $projR				array of project names
// &$projT				project table
// total cost fields are:
//		park_id
//		project_name
//		asset_name
//		asset_status
//		asset_type
//		annual_cap_cost
//		op_cost
//		total_cost
function generateHTMLTotalRevenueDataTable($res, &$txt, &$totals){
	//	first make main data table
	// headers
	$txt = '';
	$txt .= "		<thead>
		<tr>
			<th style='text-align:left'>Fund Source</th>
			<th style='text-align:right;'>Annual Cap Revenue</th>
			<th style='text-align:right;'>Annual Op Revenue</th>
			<th style='text-align:right;'>Total Revenue</th>
		</tr>
	</thead>
	<tbody>
";
	// body
	$acc = $oc = $tc = 0;
	foreach($res as $r){
		$txt .= "					<tr class=''>
		<td style='text-align:left'>{$r['fund_source']}</td>
		<td style='text-align:right'>" . number_format($r['annual_cap_cost']) . "</td>
				<td style='text-align:right'>" . number_format($r['annual_op_cost']) . "</td>
						<td style='text-align:right'>" . number_format($r['total_cost']) . "</td>
						</tr>
";
		// while going through data, accumulate totals
		$acc += $r['annual_cap_cost'];
		$oc += $r['annual_op_cost'];
		$tc += $r['total_cost'];
	}

	// fill project names totals data
	$acc = number_format($acc);
	$oc = number_format($oc);
	$tc = number_format($tc);

	$txt .= "				</tbody>
";

	// make totals table
	$totals = '';
	$totals .= "					<tr class='even totals'>
						<td>Totals</td>
						<td>$acc</td>
						<td>$oc</td>
						<td>$tc</td>
						</tr>
";
}

function makeGroupSelect($type='All', $fieldArray, $tableId, $className, $allClassName, $allIdName, $indicatorFieldName)
{
	if('All' == $type)
	{
		$t = "			<table id='$tableId'>
";
	}
	else 
	{
		$t = '';
	}
	
	$t .= "				<tr>
					<td class=''>
						<input type='checkbox' class='$allClassName' id='$allIdName' value='All' checked onclick='setAll(this)'>
					</td>
					<td>All</td>
				</tr>
";
	$i = 1;
	foreach($fieldArray as $f)
	{
		$t .= "				<tr>
					<td><input type='checkbox' class='$className' name='$i' value='$f' checked onClick='setCheckedFields(this)'></td>
			<td>$f</td>
		</tr>
";
	}
	$t .= "				<tr><input type='hidden' id='$indicatorFieldName' value='false'>
";
	if('All' == $type)
	{
		$t .= "			</table>
";
	}

	return $t;
}

// generate html for dataTable
// $res 				result from mysqli query
// &$park_names			array of park names with an array attached to each name for totals
// &$totals				totals table
// $places				decial places for displayed data
// $years				years to output
function generateReportTableHTML($res, &$park_names, &$totals,$places=0,$years)
{
	// this makes $val2010 and $t2010 for each year
	foreach($years as $y)
	{
		$v = 'val'.$y;
		$t = 't'.$y;
		$$v = 0;
		$$t = 0;
		$vals[]=$v;
		$tots[]=$t;
	}
	reset($years);
	
	// headers
	$txt = '';
	$txt .= "		<thead>
		<tr>
			<th>Park</th>
			<th style='text-align:left'>Park Unit and Project</th>
";
	foreach($years as $y){
		$txt .="	<th style='text-align:right;'>$y</th>
";
	}
	reset($years);

	$txt .= "		</tr>
	</thead>
	<tbody>
";
	// body
	
	$c = count($res);
	$park = $proj = $year = '';
	for($i=0; $i<$c; $i++){
		$writeData=false;
		if(($res[$i]['park_id'] != $park)) { // check for new park
			// have new park so reset $park and $proj
			$park = $res[$i]['park_id'];
			$proj = $res[$i]['project_name'];
			$r = 'val' . $res[$i]['project_year'];
			$$r = $res[$i]['data'];
			$f = 'have' . $res[$i]['project_year'];
			$$f = true;
			$writeData = true;
			// check next row to see if it is same park and proj
			if($i < $c-1){// when $i = $c-1 have last row
				if( $res[$i+1]['park_id'] == $park && $res[$i+1]['project_name'] == $proj ){// same park and proj, so collect data, and get next row
					$writeData=false;// get next row
				}
			}
		}
		elseif(!($res[$i]['project_name'] == $proj)) // same park, check for new proj
		{									// have new proj in this park
			$proj = $res[$i]['project_name'];
			$r = 'val' . $res[$i]['project_year'];
			$$r = $res[$i]['data'];
			$f = 'have' . $res[$i]['project_year'];
			$$f = true;
			$writeData = true;
			// check next row to see if it is same park and proj
			if($i < $c-1){// when $i = $c-1 have last row
				if( $res[$i+1]['park_id'] == $park && $res[$i+1]['project_name'] == $proj ){// same park and proj, so collect data, and get next row
					$writeData=false;// get next row
				}
			}
		}
		else { // not a new park or proj so should be another year for this proj
			$r = 'val' . $res[$i]['project_year'];
			$$r = $res[$i]['data'];
			$f = 'have' . $res[$i]['project_year'];
			$$f = true;
			$writeData = true;
			if($i < $c-1){// when $i = $c-1 have last row
				if( $res[$i+1]['park_id'] == $park && $res[$i+1]['project_name'] == $proj ){// same park and proj, so collect data, and get next row
					$writeData=false;// get next row
				}
			}
		}
		if($writeData){
			$txt .= "		<tr class=''>
			<td>$park</td>
			<td>$proj</td>
";
			foreach($years as $y){
				$val = 'val' . $y;
				$t = 't' . $y;
				$txt .= "<td style='text-align:right'>" . number_format($$val,$places) . "</td>
";
				$park_names[$park][$y] += $$val;
				$$t += $$val;
				$$val = 0;
			}
			reset($years);
			$txt .= "		</tr>
";
		}
	}

	$txt .= "	</tbody>
";
	foreach($park_names as &$park) {
		foreach ($years as $y){
			$park[$y] = number_format($park[$y]);
		}
	}
	reset($years);

	// make totals table
	$totals = "			<tr class='even totals'>
				<td>Totals</td>
";
	foreach ($tots as $t){
		$$t = number_format($$t);
		$totals .= "				<td>{$$t}</td>
";
	}
//	reset($tots);
$txt .= "				</tr>
";
	return $txt;
}

function getProjects($mysqli, $park='', $region=''){
	if('' != $park){// have park, get associated projects
		$query = "SELECT DISTINCT project_id, project_name FROM projects WHERE park_id=? ORDER BY project_name ASC";
		$paramTypes = 's';
		$params = array($park);
	}elseif ('' != $region){// have region, get associated projects
		$query = "	SELECT DISTINCT project_id, project_name FROM projects
					INNER JOIN parks ON projects.park_id=parks.park_id WHERE region_id=?
					ORDER BY project_name ASC";
		$paramTypes = 's';
		$params = array($region);
	}else{// neither park nor region, get all projects
		$query = "SELECT DISTINCT project_id, project_name FROM projects ORDER BY project_name ASC";
		$paramTypes = '';
		$params = array();
	}
	return queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
}
function getRegions($mysqli){
	$query = "SELECT region_id, region_name FROM regions";
	$paramTypes = '';
	$params = array();
	$res = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	return $res;
}
// if you just want to select parks from a particular region, use $reg
function getParks($mysqli, $reg=''){
	if('' == $reg){
		$query = "SELECT DISTINCT park_id, park_name FROM parks";
		$paramTypes = '';
		$params = array();
		$res = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	}else{
		$query = "SELECT DISTINCT park_id, park_name FROM parks WHERE region_id = ?";
		$paramTypes = 's';
		$params = array($reg);
		$res = queryMysqliPreparedSelect($mysqli, $query, $paramTypes, $params);
	}
	return $res;
}
?>